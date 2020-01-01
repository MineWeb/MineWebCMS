<?php

class MotdController extends AppController
{

    public $components = array('Session');

    public function admin_index()
    {
        if (!$this->Permissions->can('MANAGE_MOTD'))
            throw new ForbiddenException();
        $this->set('title_for_layout', $this->Lang->get('MOTD__TITLE'));
        $this->layout = 'admin';

        $this->loadModel('Server');
        $this->ServerComponent = $this->Components->load('Server');

        $get_all_servers = $this->Server->findSelectableServers(false);
        $get_servers = [];
        $calls = ['GET_MOTD' => []];

        foreach ($get_all_servers as $key => $value) {
            if (!$this->ServerComponent->online($key))
                continue;
            $call = array_values($this->ServerComponent->call($calls, $key));
            $get_servers[$key]['name'] = $value;
            $motd = explode("\n", $call[0]);
            $get_servers[$key]['motd_line1'] = $motd[0];
            $get_servers[$key]['motd_line2'] = $motd[1];


        }
        $this->set(compact('get_servers'));
    }


    public function admin_edit($server_id = false)
    {
        if (!$this->Permissions->can('MANAGE_MOTD'))
            throw new ForbiddenException();
        if (!$server_id)
            throw new NotFoundException();

        $this->set('title_for_layout', $this->Lang->get('MOTD__EDIT_TITLE'));
        $this->layout = 'admin';
        $this->loadModel('Server');
        $this->ServerComponent = $this->Components->load('Server');
        $calls = ['GET_MOTD' => []];
        $call = array_values($this->ServerComponent->call($calls, $server_id));
        $get_all_servers = $this->Server->findSelectableServers(false);
        $get['id'] = $server_id;
        $get['name'] = $get_all_servers[$server_id];
        $motd = explode("\n", $call[0]);
        $get['motd_line1'] = $motd[0];
        $get['motd_line2'] = $motd[1];
        $this->set(compact('get'));

    }

    public function admin_edit_ajax($server_id)
    {
        if (!$this->Permissions->can('MANAGE_MOTD'))
            throw new ForbiddenException();
        if (!$this->request->is('ajax'))
            throw new NotFoundException();
        $this->autoRender = false;
        $this->response->type('json');
        $this->ServerComponent = $this->Components->load('Server');
        $data = "";
        if (!empty($this->request->data['motd_line1']) || !empty($this->request->data['motd_line2']))
            $data = implode("\n", [$this->request->data['motd_line1'], $this->request->data['motd_line2']]);
        $this->ServerComponent->call(['SET_MOTD' => $data], $server_id);
        $this->History->set('EDIT_MOTD', 'motd');
        $this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('MOTD__EDIT_SUCCESS'))));
        $this->Session->setFlash($this->Lang->get('MOTD__EDIT_SUCCESS'), 'default.success');
    }

    public function admin_reset($server_id = false)
    {
        $this->autoRender = false;
        if (!$this->Permissions->can('MANAGE_MOTD'))
            throw new ForbiddenException();
        if (!$server_id)
            throw new NotFoundException();
        $this->ServerComponent = $this->Components->load('Server');
        $this->ServerComponent->call(['SET_MOTD' => ""], $server_id);
        $this->Session->setFlash($this->Lang->get('MOTD__RESET_SUCCESS'), 'default.success');
        $this->redirect('/admin/motd');


    }

}
