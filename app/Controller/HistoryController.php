<?php

class HistoryController extends AppController
{

    public function admin_index()
    {
        if (!$this->Permissions->can('VIEW_WEBSITE_HISTORY'))
            throw new ForbiddenException();
        $this->layout = 'admin';
        $this->set('title_for_layout', $this->Lang->get('HISTORY__VIEW_GLOBAL'));
    }

    public function admin_getAll()
    {
        if (!$this->Permissions->can('VIEW_WEBSITE_HISTORY'))
            throw new ForbiddenException();
        $this->autoRender = false;
        $this->response->type('json');

        $this->loadModel('History');
        $this->DataTable = $this->Components->load('DataTable');
        $this->modelClass = 'History';
        $this->DataTable->initialize($this);
        $this->paginate = array(
            'fields' => array('History.id', 'User.pseudo', 'History.action', 'History.user_id', 'History.category', 'History.created'),
            'order' => 'id DESC',
            'recursive' => 1
        );
        $this->DataTable->mDataProp = true;

        $response = $this->DataTable->getResponse();
        $this->response->body(json_encode($response));
    }

}