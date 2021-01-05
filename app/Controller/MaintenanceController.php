<?php

class MaintenanceController extends AppController
{

    public $components = ['Session'];

    public function index($banned = false)
    {
        if ($this->Configuration->getKey('maintenance') != '0') {
            $msg = $this->Configuration->getKey('maintenance');
        } else if ($banned) {
            $msg = $this->Lang->get('USER__BANNED_MSG');
        } else {
            $this->redirect('/');
        }
        $this->set(compact('msg'));
    }

    public function admin_index()
    {
        if ($this->isConnected and $this->Permissions->can('MANAGE_MAINTENANCE')) {
            $this->layout = "admin";

            $this->set('title_for_layout', $this->Lang->get('MAINTENANCE__TITLE'));
            if ($this->request->is('post')) {
                if ($this->request->data['state'] == 'enabled') {
                    $maintenance = $this->request->data['reason'];
                    $this->History->set('ADD_MAINTENANCE', 'maintenance');
                } else if ($this->request->data['state'] == 'disabled') {
                    $maintenance = '0';
                    $this->History->set('DELETE_MAINTENANCE', 'maintenance');
                }
                $this->Configuration->setKey('maintenance', $maintenance);
                $this->Session->setFlash($this->Lang->get('MAINTENANCE__EDIT_SUCCESS'), 'default.success');
            }
        } else {
            $this->redirect('/');
        }
    }

}
