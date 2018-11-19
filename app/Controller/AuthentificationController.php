<?php
class AuthentificationController extends AppController
{

    public function validLogin() {
        $this->response->type('json');
        $this->autoRender = false;
        // valid request
        if (!$this->request->is('post'))
          throw new NotFoundException('Not post');
        if (!$this->Session->read('user_id_two_factor_auth'))
          return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER__LOGIN_INFOS_NOT_FOUND'))));
        if (empty($this->request->data['code']))
          return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER__LOGIN_CODE_EMPTY'))));
        // find user
        $user = $this->User->find('first', array('conditions' => array('id' => $this->Session->read('user_id_two_factor_auth'))));
        if (empty($user))
          return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER__LOGIN_INFOS_NOT_FOUND'))));
        // get user infos
        $this->loadModel('Authentification');
        $infos = $this->Authentification->find('first', array('conditions' => array('user_id' => $user['User']['id'])));
        if (empty($infos) || !$infos['Authentification']['enabled'])
          return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER__LOGIN_INFOS_NOT_FOUND'))));
        // include library & init
        require ROOT.DS.'vendors'.DS.'auth'.DS.'GoogleAuthentificator.php';
        $ga = new PHPGangsta_GoogleAuthenticator();
        // check code
        $checkResult = $ga->verifyCode($infos['Authentification']['secret'], $this->request->data['code'], 2);    // 2 = 2*30sec clock tolerance
        if (!$checkResult)
          return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER__LOGIN_CODE_INVALID'))));
        // remove TwoFactorAuth session
        $this->Session->delete('user_id_two_factor_auth');
        // login
        if($this->request->data['remember_me'])
          $this->Cookie->write('remember_me', array('pseudo' => $user['User']['pseudo'], 'password' => $user['User']['password'], true, '1 week'));
        $this->Session->write('user', $user['User']['id']);
        $event = new CakeEvent('afterLogin', $this, array('user' => $this->User->getAllFromUser($user['User']['pseudo'])));
        $this->getEventManager()->dispatch($event);
        if($event->isStopped()) {
          return $event->result;
        }
        $this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('USER__REGISTER_LOGIN'))));
    }

    public function generateSecret() {
        $this->response->type('json');
        $this->autoRender = false;
        // valid request
        if (!$this->isConnected)
          throw new ForbiddenException('Not logged');
        // include library & init
        require ROOT.DS.'vendors'.DS.'auth'.DS.'GoogleAuthentificator.php';
        $ga = new PHPGangsta_GoogleAuthenticator();
        // generate and set into session
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($this->User->getKey('pseudo'), $secret, $this->Configuration->getKey('name'));
        $this->Session->write('two-factor-auth-secret', $secret);
        // send to user
        $this->response->body(json_encode(array('qrcode_url' => $qrCodeUrl, 'secret' => $secret)));
    }

    public function validEnable() {
        $this->response->type('json');
        $this->autoRender = false;
        // valid request
        if (!$this->request->is('post'))
          throw new NotFoundException('Not post');
        if (!$this->isConnected)
          throw new ForbiddenException('Not logged');
        if (empty($this->request->data['code']))
          return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER__LOGIN_CODE_EMPTY'))));
        if (!$this->Session->read('two-factor-auth-secret'))
          return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER__SECRET_NOT_FOUND'))));
        $secret = $this->Session->read('two-factor-auth-secret');
        // include library & init
        require ROOT.DS.'vendors'.DS.'auth'.DS.'GoogleAuthentificator.php';
        $ga = new PHPGangsta_GoogleAuthenticator();
        // check code
        $checkResult = $ga->verifyCode($secret, $this->request->data['code'], 2);    // 2 = 2*30sec clock tolerance
        if (!$checkResult)
          return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER__LOGIN_CODE_INVALID'))));
        // remove TwoFactorAuth session
        $this->Session->delete('two-factor-auth-secret');
        // save into db
        $this->loadModel('Authentification');
        if ($infos = $this->Authentification->find('first', array('conditions' => array('user_id' => $this->User->getKey('id')))))
          $this->Authentification->read(null, $infos['Authentification']['id']);
        else
          $this->Authentification->create();
        $this->Authentification->set(array('secret' => $secret, 'enabled' => true, 'user_id' => $this->User->getKey('id')));
        $this->Authentification->save();
        // send to user
        $this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('USER__SUCCESS_ENABLED_TWO_FACTOR_AUTH'))));
    }

    public function disable() {
        $this->response->type('json');
        $this->autoRender = false;
        // valid request
        if (!$this->isConnected)
          throw new ForbiddenException('Not logged');
        // save into db
        $this->loadModel('Authentification');
        $infos = $this->Authentification->find('first', array('conditions' => array('user_id' => $this->User->getKey('id'))));
        $this->Authentification->read(null, $infos['Authentification']['id']);
        $this->Authentification->set(array('enabled' => false));
        $this->Authentification->save();
        // send to user
        $this->response->body(json_encode(array('qrcode_url' => $qrCodeUrl, 'secret' => $secret)));
    }
}