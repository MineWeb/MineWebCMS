<?php

class SeoController extends AppController
{

    public $components = ['Session'];

    public function admin_index()
    {
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_SEO'))
            throw new ForbiddenException();
        $this->set('title_for_layout', $this->Lang->get('SEO__TITLE'));
        $this->layout = 'admin';
        $this->loadModel('Seo');
        $default = $this->Seo->find('first', ["conditions" => ['page' => null]])['Seo'];

        $seo_other = $this->Seo->find('all', ["conditions" => ['NOT' => ['page' => null]]]);
        $this->set(compact('default', 'seo_other'));
    }

    public function admin_edit_default()
    {
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_SEO') || !$this->request->is('post'))
            throw new ForbiddenException();
        $this->autoRender = false;
        $this->response->type('json');
        $this->loadModel('Seo');

        $default = $this->Seo->find('first', ["conditions" => ['page' => null]])['Seo'];

        if (!$this->request->data['img_edit']) {
            $already_uploaded = (isset($this->request->data['img-uploaded']));
            if ($already_uploaded) {
                $this->request->data['favicon_url'] = Router::url('/') . 'img' . DS . 'uploads' . DS . $this->request->data['img-uploaded'];
            } else {
                $isValidImg = $this->Util->isValidImage($this->request, ['png', 'jpg', 'jpeg']);
                if (!$isValidImg['status']) {
                    $this->response->body(json_encode(['statut' => false, 'msg' => $isValidImg['msg']]));
                    return;
                } else {
                    $infos = $isValidImg['infos'];
                }

                $time = date('Y-m-d_His');

                $url_img = WWW_ROOT . 'img' . DS . 'uploads' . DS . 'favicons' . DS . $time . '.' . $infos['extension'];

                if (!$this->Util->uploadImage($this->request, $url_img)) {
                    $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('FORM__ERROR_WHEN_UPLOAD')]));
                    return;
                }
                $this->request->data['favicon_url'] = Router::url('/') . 'img' . DS . 'uploads' . DS . 'favicons' . DS . $time . '.' . $infos['extension'];
            }


        }


        $this->Seo->read(null, $default['id']);
        $this->Seo->set($this->request->data);
        $this->Seo->save();

        $this->response->body(json_encode(['statut' => true, 'msg' => $this->Lang->get('SEO__EDIT_SUCCESS')]));

    }


    public function admin_add()
    {
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_SEO'))
            throw new ForbiddenException();
        $this->set('title_for_layout', $this->Lang->get('SEO__TITLE'));
        $this->layout = 'admin';
        if ($this->request->is('post')) {
            $this->autoRender = false;
            $this->response->type('json');
            if (!$this->request->data['img_edit']) {
                $already_uploaded = (isset($this->request->data['img-uploaded']));
                if ($already_uploaded) {
                    $this->request->data['favicon_url'] = Router::url('/') . 'img' . DS . 'uploads' . DS . $this->request->data['img-uploaded'];
                } else {
                    $isValidImg = $this->Util->isValidImage($this->request, ['png', 'jpg', 'jpeg']);
                    if (!$isValidImg['status']) {
                        $this->response->body(json_encode(['statut' => false, 'msg' => $isValidImg['msg']]));
                        return;
                    } else {
                        $infos = $isValidImg['infos'];
                    }

                    $time = date('Y-m-d_His');

                    $url_img = WWW_ROOT . 'img' . DS . 'uploads' . DS . 'favicons' . DS . $time . '.' . $infos['extension'];

                    if (!$this->Util->uploadImage($this->request, $url_img)) {
                        $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('FORM__ERROR_WHEN_UPLOAD')]));
                        return;
                    }
                    $this->request->data['favicon_url'] = Router::url('/') . 'img' . DS . 'uploads' . DS . 'favicons' . DS . $time . '.' . $infos['extension'];
                }
            }

            if (empty($this->request->data['page']) || (empty($this->request->data['title']) && empty($this->request->data['description']) && empty($this->request->data['favicon_url']) && empty($this->request->data['img-url'])))
                return $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')]));
            $this->Seo->create();
            $this->Seo->set($this->request->data);
            $this->Seo->save();

            $this->response->body(json_encode(['statut' => true, 'msg' => $this->Lang->get('SEO__PAGE_ADD_SUCCESS')]));

        }

    }


    public function admin_edit($id = false)
    {
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_SEO') || !$id)
            throw new ForbiddenException();
        $this->set('title_for_layout', $this->Lang->get('SEO__TITLE'));
        $this->layout = 'admin';
        $this->loadModel('Seo');
        $page = $this->Seo->find('first', ["conditions" => ['id' => $id]])["Seo"];
        $this->set(compact('page'));

        if ($this->request->is('post')) {
            $this->autoRender = false;
            $this->response->type('json');

            if (!$this->request->data['img_edit']) {
                $already_uploaded = (isset($this->request->data['img-uploaded']));
                if ($already_uploaded) {
                    $this->request->data['favicon_url'] = Router::url('/') . 'img' . DS . 'uploads' . DS . $this->request->data['img-uploaded'];
                } else {
                    $isValidImg = $this->Util->isValidImage($this->request, ['png', 'jpg', 'jpeg']);
                    if (!$isValidImg['status']) {
                        $this->response->body(json_encode(['statut' => false, 'msg' => $isValidImg['msg']]));
                        return;
                    } else {
                        $infos = $isValidImg['infos'];
                    }

                    $time = date('Y-m-d_His');

                    $url_img = WWW_ROOT . 'img' . DS . 'uploads' . DS . 'favicons' . DS . $time . '.' . $infos['extension'];

                    if (!$this->Util->uploadImage($this->request, $url_img)) {
                        $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('FORM__ERROR_WHEN_UPLOAD')]));
                        return;
                    }
                    $this->request->data['favicon_url'] = Router::url('/') . 'img' . DS . 'uploads' . DS . 'favicons' . DS . $time . '.' . $infos['extension'];
                }
            }

            if (empty($this->request->data['page']) || (empty($this->request->data['title']) && empty($this->request->data['description']) && empty($this->request->data['favicon_url']) && empty($this->request->data['img-url'])))
                return $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')]));


            $this->Seo->read(null, $page['id']);
            $this->Seo->set($this->request->data);
            $this->Seo->save();

            $this->response->body(json_encode(['statut' => true, 'msg' => $this->Lang->get('SEO__EDIT_SUCCESS')]));
        }

    }

    public function admin_delete($id = false)
    {
        $this->autoRender = false;
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_SEO') || !$id)
            throw new ForbiddenException();

        $this->loadModel('Seo');
        $this->Seo->delete($id);
        $this->Session->setFlash($this->Lang->get('SEO__PAGE_DELETE_SUCCESS'), 'default.success');
        $this->redirect(['controller' => 'seo', 'action' => 'index', 'admin' => true]);
    }

}
