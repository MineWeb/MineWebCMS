<?php

class MaintenanceController extends AppController
{

    public $components = ['Session', 'Util'];

    public function index($url = "")
    {
        $this->set('title_for_layout', $this->Lang->get('MAINTENANCE__TITLE'));
        $this->loadModel("Maintenance");
        $check = $this->Maintenance->checkMaintenance("/" . $url, $this->Util);
        if ($this->Permissions->can("BYPASS_MAINTENANCE") || !$check)
            $this->redirect("/");
        $msg = $check["reason"];
        $this->set(compact('msg'));
    }

    public function admin_index()
    {
        if (!$this->isConnected and !$this->Permissions->can('MANAGE_MAINTENANCE'))
            throw new ForbiddenException();

        $this->layout = "admin";
        $this->set('title_for_layout', $this->Lang->get('MAINTENANCE__TITLE'));

        $this->loadModel("Maintenance");
        $pagesInMaintenance = $this->Maintenance->find("all");

        $this->set("pages", $pagesInMaintenance);
    }

    public function admin_add()
    {
        if (!$this->isConnected and !$this->Permissions->can('MANAGE_MAINTENANCE'))
            throw new ForbiddenException();

        $this->layout = "admin";
        $this->set('title_for_layout', $this->Lang->get('MAINTENANCE__TITLE'));

        if ($this->request->is("post")) {
            $this->autoRender = false;
            $this->response->type('json');

            if (empty($this->request->data["reason"]))
                return $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('MAINTENANCE__ADD_REASON_EMPTY')]));

            $this->loadModel("Maintenance");

            $this->Maintenance->create();
            $this->Maintenance->set($this->request->data);
            $this->Maintenance->save();

            return $this->response->body(json_encode(['statut' => true, 'msg' => $this->Lang->get('MAINTENANCE__ADD_SUCCESS')]));
        }
    }

    public function admin_edit($id = false)
    {
        if (!$this->isConnected and !$this->Permissions->can('MANAGE_MAINTENANCE') | !$id)
            throw new ForbiddenException();

        $this->layout = "admin";
        $this->set('title_for_layout', $this->Lang->get('MAINTENANCE__TITLE'));

        $this->loadModel("Maintenance");
        $page = $this->Maintenance->find("first", ["conditions" => ["id" => $id]])["Maintenance"];

        $this->set("page", $page);

        if ($this->request->is("post")) {
            $this->autoRender = false;
            $this->response->type('json');

            if (empty($this->request->data["reason"]))
                return $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('MAINTENANCE__ADD_REASON_EMPTY')]));

            $this->Maintenance->read(null, $id);
            $this->Maintenance->set([
                "sub_url" => $this->request->data["sub_url"],
                "url" => $this->request->data["url"],
                "reason" => $this->request->data["reason"]
            ]);
            $this->Maintenance->save();

            return $this->response->body(json_encode(['statut' => true, 'msg' => $this->Lang->get('MAINTENANCE__EDIT_SUCCESS')]));
        }
    }

    public function admin_disable($id = false)
    {
        $this->autoRender = false;
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_MAINTENANCE') || !$id)
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
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_MAINTENANCE') || !$id)
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
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_MAINTENANCE') || !$id)
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
