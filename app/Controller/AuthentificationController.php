<?php

use MineWeb\Vendor\PhpQrCode\QRCode;

class AuthentificationController extends AppController
{

    public function beforeFilter()
    {
        $this->response->disableCache();
        return parent::beforeFilter();
    }

    public function challenge()
    {
        if ($this->Session->read('TwoFactor.login.expires') <= time()) {
            return $this->redirect('/');
        }
        $this->set('verificationAction', 'validLogin');
    }

    public function validLogin()
    {
        if (!$this->request->is('post')) {
            throw new NotFoundException('Not post');
        }

        $this->autoRender = false;
        $this->response->type('json');
        $challenge = $this->Session->read('TwoFactor.login');
        if (empty($challenge['user_id']) || $challenge['expires'] <= time()) {
            return $this->verificationError('USER__LOGIN_INFOS_NOT_FOUND', 'challenge');
        }
        $code = $this->request->data('code');
        if ($code === null || $code === '') {
            return $this->verificationError('USER__LOGIN_CODE_EMPTY', 'challenge');
        }
        $userId = $challenge['user_id'];
        if (!$this->Authentification->verifyCode($userId, $code)) {
            return $this->verificationError('USER__LOGIN_CODE_INVALID', 'challenge');
        }
        $user = $this->User->findById($userId);
        if (!$user) {
            return $this->verificationError('USER__LOGIN_INFOS_NOT_FOUND', 'challenge');
        }

        $this->Session->delete('TwoFactor.login');
        if ($challenge['microsoft_id'] === null) {
            $event = new CakeEvent('onLogin', $this, ['user' => $user['User']]);
            $this->getEventManager()->dispatch($event);
            if ($event->isStopped()) {
                return $event->result;
            }
        }
        $this->renewUserSession($userId);
        if ($challenge['microsoft_id'] !== null) {
            $this->Cookie->write('microsoft_user_id', $challenge['microsoft_id']);
        }
        $event = new CakeEvent('afterLogin', $this, ['user' => $user['User']]);
        $this->getEventManager()->dispatch($event);
        if ($event->isStopped()) {
            return $event->result;
        }
        if ($this->request->data('standalone')) {
            return $this->redirect('/');
        }
        return $this->sendJSON(['statut' => true, 'msg' => $this->Lang->get('USER__REGISTER_LOGIN')]);
    }

    /**
     * @throws Exception
     */
    public function generateSecret()
    {
        if (!$this->isConnected) {
            throw new ForbiddenException('Not logged');
        }
        $userId = $this->User->getKey('id');
        if ($this->Authentification->requiresCode($userId)) {
            throw new ForbiddenException();
        }

        $secret = $this->Session->read('TwoFactor.secret.' . $userId);
        if (!$secret) {
            require_once ROOT . DS . 'vendors' . DS . 'auth' . DS . 'GoogleAuthentificator.php';
            $authenticator = new PHPGangsta_GoogleAuthenticator();
            $secret = $authenticator->createSecret();
            if (!$this->Session->write('TwoFactor.secret', [$userId => $secret])) {
                throw new InternalErrorException();
            }
        }
        return $this->sendJSON([
            'qrcode_url' => Router::url(['plugin' => null, 'controller' => 'authentification', 'action' => 'qrCode']),
            'secret' => $secret
        ]);
    }

    public function qrCode()
    {
        if (!$this->isConnected) {
            throw new ForbiddenException('Not logged');
        }
        $userId = $this->User->getKey('id');
        $secret = $this->Session->read('TwoFactor.secret.' . $userId);
        if (!$secret || $this->Authentification->requiresCode($userId)) {
            throw new NotFoundException();
        }

        require_once ROOT . DS . 'vendors' . DS . 'php-qrcode-0.1' . DS . 'QRCode.php';
        $uri = 'otpauth://totp/' . rawurlencode($this->User->getKey('pseudo'))
            . '?secret=' . $secret . '&issuer=' . rawurlencode($this->Configuration->getKey('name'));
        $this->response->type('svg');
        $this->response->body(QRCode::svg($uri, ['s' => 'qrm']));
        return $this->response;
    }

    public function validEnable()
    {
        if (!$this->request->is('post')) {
            throw new NotFoundException('Not post');
        }
        if (!$this->isConnected) {
            throw new ForbiddenException('Not logged');
        }
        $code = $this->request->data('code');
        if (empty($code)) {
            return $this->sendJSON(['statut' => false, 'msg' => $this->Lang->get('USER__LOGIN_CODE_EMPTY')]);
        }
        $userId = $this->User->getKey('id');
        $secret = $this->Session->read('TwoFactor.secret.' . $userId);
        if (!$secret) {
            return $this->sendJSON(['statut' => false, 'msg' => $this->Lang->get('USER__SECRET_NOT_FOUND')]);
        }

        $factor = $this->Authentification->find('first', [
            'conditions' => ['user_id' => $userId],
            'fields' => ['id', 'enabled'],
            'order' => ['enabled' => 'DESC']
        ]);
        if (!empty($factor['Authentification']['enabled'])) {
            throw new ForbiddenException();
        }
        require_once ROOT . DS . 'vendors' . DS . 'auth' . DS . 'GoogleAuthentificator.php';
        $authenticator = new PHPGangsta_GoogleAuthenticator();
        if (!is_string($code) || !$authenticator->verifyCode($secret, $code, 2)) {
            return $this->sendJSON(['statut' => false, 'msg' => $this->Lang->get('USER__LOGIN_CODE_INVALID')]);
        }

        $this->Authentification->id = $factor ? $factor['Authentification']['id'] : false;
        if (!$this->Authentification->save(['user_id' => $userId, 'secret' => $secret, 'enabled' => true])) {
            return $this->sendJSON(['statut' => false, 'msg' => $this->Lang->get('ERROR__INTERNAL_ERROR')]);
        }
        $this->Session->delete('TwoFactor.secret');
        return $this->sendJSON(['statut' => true, 'msg' => $this->Lang->get('USER__SUCCESS_ENABLED_TWO_FACTOR_AUTH')]);
    }

    public function disable()
    {
        if (!$this->isConnected) {
            throw new ForbiddenException('Not logged');
        }
        if ($this->request->is('get') && !$this->request->is('ajax')) {
            $this->set('verificationAction', 'disable');
            return $this->render('challenge');
        }
        if (!$this->request->is('post') || empty($this->request->data['code'])) {
            $this->response->statusCode(403);
            return $this->sendJSON([
                'statut' => false,
                'msg' => $this->Lang->get('USER__LOGIN_CODE_EMPTY'),
                'confirmTwoFactor' => $this->request->is('get'),
                'url' => Router::url(['plugin' => null, 'controller' => 'authentification', 'action' => 'disable'])
            ]);
        }
        $userId = $this->User->getKey('id');
        if (!$this->Authentification->verifyCode($userId, $this->request->data('code'))) {
            return $this->verificationError('USER__LOGIN_CODE_INVALID', 'disable');
        }

        $this->loadModel('RememberToken');
        if (!$this->RememberToken->deleteAll(['user_id' => $userId], false) ||
            !$this->Authentification->updateAll(['enabled' => 0], ['user_id' => $userId, 'enabled' => true])) {
            return $this->verificationError('ERROR__INTERNAL_ERROR', 'disable');
        }
        $this->Session->delete('TwoFactor.secret');
        if ($this->request->data('standalone')) {
            return $this->redirect(['controller' => 'user', 'action' => 'profile']);
        }
        return $this->sendJSON(['statut' => true, 'msg' => $this->Lang->get('GLOBAL__SUCCESS')]);
    }

    private function verificationError($key, $action)
    {
        $message = $this->Lang->get($key);
        if ($this->request->data('standalone')) {
            $this->Session->setFlash($message, 'default.error');
            return $this->redirect(['action' => $action]);
        }
        return $this->sendJSON(['statut' => false, 'msg' => $message]);
    }
}
