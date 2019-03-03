<?php

class InstallController extends AppController
{

    public function beforeFilter()
    {

        parent::beforeFilter();
        if (file_exists(ROOT . DS . 'config' . DS . 'installed.txt'))
            $this->redirect('/');
    }

    public function index()
    {
        $this->layout = 'install';

        $this->set('title_for_layout', $this->Lang->get('INSTALL__INSTALL'));
        $this->loadModel('User');
        $admin = $this->User->find('first');
        if (!empty($admin)) {
            $this->set('admin_pseudo', $admin['User']['pseudo']);
            $this->set('admin_password', 1);
            $this->set('admin_email', $admin['User']['email']);
        }
    }

    public function step_1()
    {
        $this->autoRender = false;
        $this->response->type('json');

        if (!$this->request->is('ajax'))
            throw new NotFoundException();
        if(file_exists(ROOT.DS.'config'.DS.'secure.txt')) {
            $secure = file_get_contents(ROOT . DS . 'config' . DS . 'secure.txt');
            $secure = json_decode($secure, true);
            if ($secure['ip'] != $_SERVER['HTTP_X_FORWARDED_FOR']) {
                return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__IP_WRONG'))));
            }
        }
        if (empty($this->request->data['pseudo']) || empty($this->request->data['password']) || empty($this->request->data['password_confirmation']) || empty($this->request->data['email']))
            return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));
        if ($this->request->data['password'] !== $this->request->data['password_confirmation'])
            return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER__ERROR_PASSWORDS_NOT_SAME'))));
        if (!filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL))
            return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER__ERROR_EMAIL_NOT_VALID'))));

        $this->request->data['ip'] = $_SERVER["REMOTE_ADDR"];
        $this->request->data['rank'] = 4;
        $this->request->data['password'] = $this->Util->password($this->request->data['password'], $this->request->data['pseudo']);

        $this->loadModel('User');
        $this->User->create();
        $this->User->set($this->request->data);
        $this->User->save();

        $this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('USER__REGISTER_SUCCESS'))));
    }

    public function end()
    {
        $this->autoRender = false;
        if (!file_exists(ROOT . DS . 'config' . DS . 'installed.txt')) {
            file_put_contents(ROOT . DS . 'config' . DS . 'installed.txt', "\n");
            $this->redirect('/');
        } else {
            $this->redirect(array('controller' => 'install', 'action' => 'index'));
        }
    }

}
