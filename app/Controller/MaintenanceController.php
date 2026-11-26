<?php

class MaintenanceController extends AppController
{

    public $components = ['Session', 'Util'];

    public function beforeFilter()
    {
        $result = parent::beforeFilter();

        $this->set('title_for_layout', $this->Lang->get('MAINTENANCE__TITLE'));
        if (strpos($this->request->param('action'), 'admin_') === 0) {
            if (!$this->Permissions->can('MANAGE_MAINTENANCE'))
                throw new ForbiddenException();
            $this->layout = 'admin';
        }

        return $result;
    }

    public function index($url = "")
    {
        $this->loadModel("Maintenance");
        $check = $this->Maintenance->checkMaintenance("/" . $url, $this->Util);
        if ($this->Permissions->can("BYPASS_MAINTENANCE") || !$check)
            $this->redirect("/");
        $msg = $check["reason"];
        $this->set(compact('msg'));
    }

    public function admin_index()
    {
        $this->loadModel('Maintenance');
        $this->set('pages', $this->Maintenance->find('all'));
    }

    public function admin_add()
    {
        if ($this->request->is('post')) {
            $this->loadModel('Maintenance');
            $this->Maintenance->create();
            return $this->saveMaintenance('MAINTENANCE__ADD_SUCCESS');
        }
    }

    public function admin_edit($id = false)
    {
        if (!$id)
            throw new ForbiddenException();

        $this->loadModel('Maintenance');
        $page = $this->Maintenance->read(null, $id);
        if (!$page)
            throw new NotFoundException();

        $this->set('page', $page['Maintenance']);
        if ($this->request->is('post'))
            return $this->saveMaintenance('MAINTENANCE__EDIT_SUCCESS');
    }

    private function saveMaintenance($successMessage)
    {
        if (empty($this->request->data['reason']))
            return $this->sendJSON(['statut' => false, 'msg' => $this->Lang->get('MAINTENANCE__ADD_REASON_EMPTY')]);

        $data = array_intersect_key($this->request->data, array_flip(['reason', 'url', 'sub_url']));
        $saved = (bool)$this->Maintenance->save($data);
        return $this->sendJSON(['statut' => $saved, 'msg' => $this->Lang->get($saved ? $successMessage : 'ERROR__INTERNAL_ERROR')]);
    }

    public function admin_disable($id = false)
    {
        $this->autoRender = false;
        if (!$id)
            throw new ForbiddenException();

        $this->loadModel('Maintenance');
        $this->Maintenance->read(null, $id);
        $this->Maintenance->set(["active" => "0"]);
        $this->Maintenance->save();

        $pageUrl = $this->Maintenance->find('first', ["conditions" => ['id' => $id]])["Maintenance"]["url"];
        $this->Session->setFlash($this->Lang->get('MAINTENANCE__DISABLED_PAGE', [
            '{PAGE}' => $pageUrl,
        ]), 'default.success');
        $this->redirect(['controller' => 'maintenance', 'action' => 'index', 'admin' => true]);
    }

    public function admin_enable($id = false)
    {
        $this->autoRender = false;
        if (!$id)
            throw new ForbiddenException();

        $this->loadModel('Maintenance');
        $this->Maintenance->read(null, $id);
        $this->Maintenance->set(["active" => "1"]);
        $this->Maintenance->save();

        $pageUrl = $this->Maintenance->find('first', ["conditions" => ['id' => $id]])["Maintenance"]["url"];
        $this->Session->setFlash($this->Lang->get('MAINTENANCE__ENABLED_PAGE', [
            '{PAGE}' => $pageUrl,
        ]), 'default.success');
        $this->redirect(['controller' => 'maintenance', 'action' => 'index', 'admin' => true]);
    }

    public function admin_delete($id = false)
    {
        $this->autoRender = false;
        if (!$id)
            throw new ForbiddenException();

        $this->loadModel('Maintenance');
        $pageUrl = $this->Maintenance->find('first', ["conditions" => ['id' => $id]])["Maintenance"]["url"];

        $this->Maintenance->delete($id);

        $this->Session->setFlash($this->Lang->get('MAINTENANCE__DELETED_PAGE', [
            '{PAGE}' => $pageUrl,
        ]), 'default.success');
        $this->redirect(['controller' => 'maintenance', 'action' => 'index', 'admin' => true]);
    }
}
