<?php

class NavbarController extends AppController
{

    public $components = array('Session');

    public function admin_index()
    {
        if (!$this->Permissions->can('MANAGE_NAV'))
            throw new ForbiddenException();
        $this->set('title_for_layout', $this->Lang->get('NAVBAR__TITLE'));
        $this->layout = 'admin';

        $this->loadModel('Navbar');
        $navbars = $this->Navbar->find('all', array('order' => 'order'));

        $this->loadModel('Page');
        $pages = $this->Page->find('all', array('fields' => array('id', 'slug')));
        $pages_listed = [];
        foreach ($pages as $key => $value)
            $pages_listed[$value['Page']['id']] = $value['Page']['slug'];

        foreach ($navbars as $key => $value) {
            if ($value['Navbar']['url']['type'] == "plugin") {
                if (isset($value['Navbar']['url']['route']))
                    $plugin = $this->EyPlugin->findPlugin('slug', $value['Navbar']['url']['id']);
                else
                    $plugin = $this->EyPlugin->findPlugin('DBid', $value['Navbar']['url']['id']);
                if (!empty($plugin)) {
                    $navbars[$key]['Navbar']['url'] = (isset($value['Navbar']['url']['route'])) ? Router::url($value['Navbar']['url']['route']) : Router::url('/' . strtolower($plugin->slug));
                } else {
                    $navbars[$key]['Navbar']['url'] = false;
                }
            } else if ($value['Navbar']['url']['type'] == "page") {
                if (isset($pages_listed[$value['Navbar']['url']['id']])) {
                    $navbars[$key]['Navbar']['url'] = Router::url('/p/' . $pages_listed[$value['Navbar']['url']['id']]);
                } else {
                    $navbars[$key]['Navbar']['url'] = '#';
                }
            } else if ($value['Navbar']['url']['type'] == "custom") {
                $navbars[$key]['Navbar']['url'] = $value['Navbar']['url']['url'];
            } else {
                $navbars[$key]['Navbar']['url'] = '#';
            }
        }
        $this->set(compact('navbars'));
    }

    public function admin_save_ajax()
    {
        $this->autoRender = false;
        if ($this->isConnected AND $this->Permissions->can('MANAGE_NAV')) {

            if ($this->request->is('post')) {
                if (!empty($this->request->data)) {
                    $data = $this->request->data['nav'];
                    $data = explode('&', $data);
                    $i = 1;
                    foreach ($data as $key => $value) {
                        $data2[] = explode('=', $value);
                        $data3 = substr($data2[0][0], 0, -2);
                        $data1[$data3] = $i;
                        unset($data3);
                        unset($data2);
                        $i++;
                    }
                    $data = $data1;
                    $this->loadModel('Navbar');
                    foreach ($data as $key => $value) {
                        $find = $this->Navbar->find('first', array('conditions' => array('name' => $key)));
                        if (!empty($find)) {
                            $id = $find['Navbar']['id'];
                            $this->Navbar->read(null, $id);
                            $this->Navbar->set(array(
                                'order' => $value,
                                'url' => json_encode($find['Navbar']['url']),
                            ));
                            $this->Navbar->save();
                        } else {
                            $error = 1;
                        }
                    }
                    if (empty($error)) {
                        $this->History->set('EDIT_NAVBAR', 'navbar');
                        echo $this->Lang->get('NAVBAR__SAVE_SUCCESS') . '|true';
                    } else {
                        echo $this->Lang->get('ERROR__INTERNAL_ERROR') . '|false';
                    }
                } else {
                    echo $this->Lang->get('ERROR__FILL_ALL_FIELDS') . '|false';
                }
            } else {
                echo $this->Lang->get('ERROR__BAD_REQUEST') . '|false';
            }
        } else {
            $this->redirect('/');
        }
    }

    public function admin_delete($id = false)
    {
        $this->autoRender = false;
        if ($this->isConnected AND $this->Permissions->can('MANAGE_NAV')) {
            if ($id != false) {

                $this->loadModel('Navbar');
                if ($this->Navbar->delete($id)) {
                    $this->History->set('DELETE_NAV', 'navbar');
                    $this->Session->setFlash($this->Lang->get('NAVBAR__DELETE_SUCCESS'), 'default.success');
                    $this->redirect(array('controller' => 'navbar', 'action' => 'index', 'admin' => true));
                } else {
                    $this->redirect(array('controller' => 'navbar', 'action' => 'index', 'admin' => true));
                }
            } else {
                $this->redirect(array('controller' => 'navbar', 'action' => 'index', 'admin' => true));
            }
        } else {
            $this->redirect('/');
        }
    }

    public function admin_add()
    {
        if (!$this->Permissions->can('MANAGE_NAV'))
            throw new ForbiddenException();
        $this->layout = 'admin';
        $this->set('title_for_layout', $this->Lang->get('NAVBAR__ADD_LINK'));

        $this->loadModel('Page');
        $url_pages = $this->Page->find('all');
        foreach ($url_pages as $key => $value) {
            $url_pages2[$value['Page']['id']] = $value['Page']['title'];
        }
        $url_pages = (isset($url_pages2)) ? $url_pages2 : array();
        $this->set('url_plugins', $this->EyPlugin->findPluginsLinks());
        $this->set(compact('url_pages'));
    }

    public function admin_add_ajax()
    {
        if (!$this->Permissions->can('MANAGE_NAV'))
            throw new ForbiddenException();
        if (!$this->request->is('ajax'))
            throw new NotFoundException();
        $this->autoRender = false;
        $this->response->type('json');


        if (empty($this->request->data['name']) || empty($this->request->data['type']) || empty($this->request->data['url']) || $this->request->data['url'] === "undefined")
            return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));

        $this->loadModel('Navbar');
        $order = $this->Navbar->find('first', array('order' => array('order' => 'DESC')));
        $order = (empty($order)) ? 1 : intval($order) + 1;

        $open_new_tab = ($this->request->data['open_new_tab'] == 'true') ? 1 : 0;

        $this->Navbar->create();
        $data = array(
            'order' => $order,
            'name' => $this->request->data['name'],
			'icon' => $this->request->data['icon'],
            'type' => 1,
            'open_new_tab' => $open_new_tab
        );

        if ($this->request->data['type'] == "dropdown") {
            $data['type'] = 2;
            $data['url'] = json_encode(array('type' => 'submenu'));
            $data['submenu'] = json_encode($this->request->data['url']);
        } else {
            // URL
            $data['url'] = $this->request->data['url'];
        }

        $this->Navbar->set($data);
        $this->Navbar->save();

        $this->History->set('ADD_NAV', 'navbar');

        $this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('NAVBAR__ADD_SUCCESS'))));
        $this->Session->setFlash($this->Lang->get('NAVBAR__ADD_SUCCESS'), 'default.success');
    }

    public function admin_edit($id = false)
    {
        if (!$this->Permissions->can('MANAGE_NAV'))
            throw new ForbiddenException();
        if (!$id)
            throw new NotFoundException();
        $find = $this->Navbar->find('first', array('conditions' => array('id' => $id)));
        if (empty($find))
            throw new NotFoundException();
        $nav = $find['Navbar'];
        $this->layout = 'admin';
        $this->set('title_for_layout', $this->Lang->get('NAVBAR__EDIT_TITLE'));

        $this->loadModel('Page');
        $url_pages = $this->Page->find('all');
        foreach ($url_pages as $key => $value) {
            $url_pages2[$value['Page']['id']] = $value['Page']['title'];
        }
        $url_pages = (isset($url_pages2)) ? $url_pages2 : array();

        $this->set(compact('url_pages', 'nav'));
        $this->set('url_plugins', $this->EyPlugin->findPluginsLinks());
    }

    public function admin_edit_ajax($id)
    {
        if (!$this->Permissions->can('MANAGE_NAV'))
            throw new ForbiddenException();
        if (!$this->request->is('ajax'))
            throw new NotFoundException();
        $this->autoRender = false;
        $this->response->type('json');


        if (empty($this->request->data['name']) || empty($this->request->data['type']) || empty($this->request->data['url']) || $this->request->data['url'] === "undefined")
            return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));

        $this->loadModel('Navbar');
        $this->Navbar->read(null, $id);
        $data = array(
            'name' => $this->request->data['name'],
			'icon' => $this->request->data['icon'],
            'type' => 1,
            'open_new_tab' => ($this->request->data['open_new_tab'] == 'true') ? 1 : 0
        );
        if ($this->request->data['type'] == "dropdown") {
            $data['type'] = 2;
            $data['url'] = json_encode(array('type' => 'submenu'));
            $data['submenu'] = json_encode($this->request->data['url']);
        } else {
            // URL
            $data['url'] = $this->request->data['url'];
        }

        $this->Navbar->set($data);
        $this->Navbar->save();

        $this->History->set('EDIT_NAV', 'navbar');

        $this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('NAVBAR__EDIT_SUCCESS'))));
        $this->Session->setFlash($this->Lang->get('NAVBAR__EDIT_SUCCESS'), 'default.success');
    }

}
