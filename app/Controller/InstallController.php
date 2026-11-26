<?php

class InstallController extends AppController
{

    public function beforeFilter()
    {

        parent::beforeFilter();
        if (file_exists(ROOT . DS . 'config' . DS . 'installed.txt')) {
            return $this->redirect('/');
        }
        $lock = ROOT . DS . 'config' . DS . 'secure.txt';
        if (file_exists($lock)) {
            $secure = json_decode(file_get_contents($lock), true);
            $ip = $this->Util->getIP();
            if ($ip === '' || !isset($secure['ip']) || $secure['ip'] !== $ip) {
                throw new ForbiddenException($this->Lang->get('ERROR__IP_WRONG'));
            }
        }
    }

    public function index()
    {
        $this->layout = 'install';

        $this->set('title_for_layout', $this->Lang->get('INSTALL__INSTALL'));
        $this->loadModel('User');
        $admin = $this->User->find();
        if (!empty($admin)) {
            $this->set('admin_pseudo', $admin['User']['pseudo']);
            $this->set('admin_password', 1);
            $this->set('admin_email', $admin['User']['email']);
        }
    }

    public function step_1()
    {
        $this->request->allowMethod('post');
        $this->autoRender = false;
        $this->response->type('json');

        if (!$this->request->is('ajax'))
            throw new NotFoundException();
        if (empty($this->request->data['pseudo']) || empty($this->request->data['password']) || empty($this->request->data['password_confirmation']) || empty($this->request->data['email']))
            return $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')]));
        if ($this->request->data['password'] !== $this->request->data['password_confirmation'])
            return $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('USER__ERROR_PASSWORDS_NOT_SAME')]));
        if (!filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL))
            return $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('USER__ERROR_EMAIL_NOT_VALID')]));

        $this->request->data['ip'] = $this->Util->getIP();
        $this->request->data['rank'] = 4;
        $this->request->data['password'] = $this->Util->password($this->request->data['password'], $this->request->data['pseudo']);
        $this->request->data['password_hash'] = $this->Util->getPasswordHashType();

        $this->loadModel('User');
        $this->User->create();
        $this->User->set($this->request->data);
        if (!$this->User->save()) {
            throw new InternalErrorException();
        }

        $this->response->body(json_encode(['statut' => true, 'msg' => $this->Lang->get('USER__REGISTER_SUCCESS')]));
    }

    public function end()
    {
        $this->request->allowMethod('post');
        $this->loadModel('User');
        if (!$this->User->hasAny(['User.rank' => 4])) {
            throw new BadRequestException();
        }
        if (file_put_contents(ROOT . DS . 'config' . DS . 'installed.txt', "\n") === false) {
            throw new InternalErrorException();
        }
        return $this->redirect('/');
    }

}
