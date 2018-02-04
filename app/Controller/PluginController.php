<?php

class PluginController extends AppController
{

    function admin_index()
    {
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_PLUGINS'))
            throw new ForbiddenException();
        $this->set('title_for_layout', $this->Lang->get('PLUGIN__LIST'));
        $this->layout = 'admin';
    }

    function admin_delete($id = false)
    {
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_PLUGINS'))
            throw new ForbiddenException();
        if (!$id)
            throw new NotFoundException();
        $slug = $this->Plugin->find('first', array('conditions' => array('id' => $id)));

        if (isset($slug['Plugin']['name']) && !$this->EyPlugin->delete($slug['Plugin']['name']))
        {
            $this->History->set('DELETE_PLUGIN', 'plugin');
            $this->Session->setFlash($this->Lang->get('PLUGIN__DELETE_SUCCESS'), 'default.success');
        }
        else
            $this->Session->setFlash($this->Lang->get('ERROR__INTERNAL_ERROR'), 'default.error');

        Configure::write('Cache.disable', true);
        App::uses('Folder', 'Utility');
        $folder = new Folder(ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache');
        if (!empty($folder->path)) {
            $folder->delete();
        }

        $this->redirect(array('controller' => 'plugin', 'action' => 'index', 'admin' => true));
    }

    function admin_enable($id = false)
    {
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_PLUGINS'))
            throw new ForbiddenException();
        if (!$id)
            throw new NotFoundException();

        if ($this->EyPlugin->enable($id))
        {
            $this->History->set('ENABLE_PLUGIN', 'plugin');
            $this->Session->setFlash($this->Lang->get('PLUGIN__ENABLE_SUCCESS'), 'default.success');
        } else
            $this->Session->setFlash($this->Lang->get('ERROR__INTERNAL_ERROR'), 'default.error');
        $this->redirect(array('controller' => 'plugin', 'action' => 'index', 'admin' => true));
    }

    function admin_disable($id = false)
    {
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_PLUGINS'))
            throw new ForbiddenException();
        if (!$id)
            throw new NotFoundException();

        if ($this->EyPlugin->disable($id)) {
            $this->History->set('DISABLE_PLUGIN', 'plugin');
            $this->Session->setFlash($this->Lang->get('PLUGIN__DISABLE_SUCCESS'), 'default.success');
        } else
            $this->Session->setFlash($this->Lang->get('ERROR__INTERNAL_ERROR'), 'default.error');
        $this->redirect(array('controller' => 'plugin', 'action' => 'index', 'admin' => true));
    }

    function admin_install($slug = false)
    {
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_PLUGINS'))
            throw new ForbiddenException();
        if (!$slug)
            throw new NotFoundException();
        $this->autoRender = false;
        $this->response->type('json');

        $installed = $this->EyPlugin->download($slug, true);
        if ($installed !== true)
            return $this->response->body(json_encode(array('statut' => 'error', 'msg' => $this->Lang->get($installed))));

        $this->History->set('INSTALL_PLUGIN', 'plugin');

        Configure::write('Cache.disable', true);
        App::uses('Folder', 'Utility');
        $folder = new Folder(ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache');
        if (!empty($folder->path)) {
            $folder->delete();
        }

        $this->loadModel('Plugin');
        $this->Plugin->cacheQueries = false;
        $search = $this->Plugin->find('first', ['conditions' => ['name' => $slug]]);
        $this->response->body(json_encode(array(
            'statut' => 'success',
            'plugin' => array(
                'name' => $search['Plugin']['name'],
                'DBid' => $search['Plugin']['id'],
                'author' => $search['Plugin']['author'],
                'dateformatted' => $this->Lang->date($search['Plugin']['created']),
                'version' => $search['Plugin']['version']
            )
        )));
    }

    function admin_update($slug)
    {
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_PLUGINS'))
            throw new ForbiddenException();
        if (!$slug)
            throw new NotFoundException();
        $this->autoRender = false;

        $updated = $this->EyPlugin->update($slug);
        if ($updated === true)
        {
            App::uses('Folder', 'Utility');
            $folder = new Folder(ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache');
            if (!empty($folder->path)) {
                $folder->delete();
            }

            $this->History->set('UPDATE_PLUGIN', 'plugin');
            $this->Session->setFlash($this->Lang->get('PLUGIN__UPDATE_SUCCESS'), 'default.success');
        } else
            $this->Session->setFlash($this->Lang->get($updated), 'default.error');
        $this->redirect(array('controller' => 'plugin', 'action' => 'index', 'admin' => true));
    }

}
