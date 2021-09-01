<?php

class SocialController extends AppController
{
    private $social_default = [
        ['title' => 'Discord', 'icon' => 'fab fa-discord', 'img' => null, 'color' => '#7289da'],
        ['title' => 'Twitter', 'icon' => 'fab fa-twitter', 'img' => null, 'color' => '#00acee'],
        ['title' => 'Youtube', 'icon' => 'fab fa-youtube', 'img' => null, 'color' => '#c4302b'],
        ['title' => 'FaceBook', 'icon' => 'fab fa-facebook', 'img' => null, 'color' => '#3b5998']
    ];

    function admin_index() {
        if (!$this->isConnected || !$this->Permissions->can("MANAGE_SOCIAL"))
            throw new ForbiddenException();

        $this->set('title_for_layout', $this->Lang->get("SOCIAL__HOME"));
        $this->layout = 'admin';

        $this->loadModel('SocialButton');
        $this->set('social_buttons', $this->SocialButton->find('all', ['order' => 'id desc']));
    }

    function admin_add() {
        if (!$this->isConnected || !$this->Permissions->can("MANAGE_SOCIAL"))
            throw new ForbiddenException();

        $this->set('title_for_layout', $this->Lang->get("SOCIAL__HOME"));
        $this->layout = 'admin';

        $this->set('social_default', $this->social_default);

        if ($this->request->is("post")) {
            $this->autoRender = false;
            $this->response->type('json');

            if (empty($this->request->data("url")))
                return $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')]));
            if(!empty($this->request->data("img")) && !empty($this->request->data("icon")) && empty($this->request->data("type")))
                return $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('SOCIAL__CANNOT_TOW_TYPE')]));
            
            // Vérify if having many input and select with radio:type
            $icon = $this->request->data("icon");
            $img = $this->request->data("img");

            if(!empty($this->request->data("type"))) {
                if($this->request->data("type") == "img") {
                    $icon = null;
                }

                if($this->request->data("type") == "icon") {
                    $img = null;
                }
            }

            $this->loadModel('SocialButton');
            $this->SocialButton->create();
            $this->SocialButton->set([
                "title" => $this->request->data("title"),
                "img" => $img,
                "icon" => $icon,
                "color" => $this->request->data("color"),
                "url" => $this->request->data("url")
            ]);
            $this->SocialButton->save();

            $this->response->body(json_encode(['statut' => true, 'msg' => $this->Lang->get('SOCIAL__BUTTON_SUCCESS')]));
        }
    }

    function admin_edit($id = false) {
        if (!$this->isConnected || !$this->Permissions->can("MANAGE_SOCIAL"))
            throw new ForbiddenException();

        if (!$id)
            throw new NotFoundException();

        $find = $this->SocialButton->find('first', ['order' => 'id desc', 'conditions' => ['id' => $id]]);
        if (empty($find))
            throw new NotFoundException();

        $this->set('title_for_layout', $this->Lang->get("SOCIAL__HOME"));
        $this->layout = 'admin';

        $this->set('social_button', $find['SocialButton']);
        $this->set('social_default', $this->social_default);

        if ($this->request->is("post")) {
            $this->autoRender = false;
            $this->response->type('json');

            if (empty($this->request->data("url")))
                return $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')]));
            if(!empty($this->request->data("img")) && !empty($this->request->data("icon")) && empty($this->request->data("type")))
                return $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('SOCIAL__CANNOT_TOW_TYPE')]));

            // Vérify if having many input and select with radio:type
            $icon = $this->request->data("icon");
            $img = $this->request->data("img");

            if(!empty($this->request->data("type"))) {
                if($this->request->data("type") == "img") {
                    $icon = null;
                }

                if($this->request->data("type") == "icon") {
                    $img = null;
                }
            }

            $this->loadModel('SocialButton');
            $this->SocialButton->read(null, $id);
            $this->SocialButton->set([
                "title" => $this->request->data("title"),
                "img" => $img,
                "icon" => $icon,
                "color" => $this->request->data("color"),
                "url" => $this->request->data("url")
            ]);
            $this->SocialButton->save();

            $this->response->body(json_encode(['statut' => true, 'msg' => $this->Lang->get('SOCIAL__BUTTON_EDIT_SUCCESS')]));
        }
    }

    public function admin_delete($id = false)
    {
        $this->autoRender = false;
        if ($this->isConnected and $this->Permissions->can('MANAGE_SOCIAL')) {
            if ($id != false) {

                $this->loadModel('SocialButton');
                if ($this->SocialButton->delete($id)) {
                    $this->History->set('DELETE_SOCIAL', 'réseaux sociaux');
                    $this->Session->setFlash($this->Lang->get('SOCIAL__BUTTON_DELETE_SUCCESS'), 'default.success');
                    $this->redirect(['controller' => 'social', 'action' => 'index', 'admin' => true]);
                } else {
                    $this->redirect(['controller' => 'social', 'action' => 'index', 'admin' => true]);
                }
            } else {
                $this->redirect(['controller' => 'social', 'action' => 'index', 'admin' => true]);
            }
        } else {
            $this->redirect('/');
        }
    }
}
