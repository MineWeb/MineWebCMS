<?php

class SocialController extends AppController
{
    private $social_default = [
        ['title' => 'Discord', 'extra' => 'fab fa-discord', 'color' => '#7289da'],
        ['title' => 'Twitter', 'extra' => 'fab fa-twitter', 'color' => '#00acee'],
        ['title' => 'Youtube', 'extra' => 'fab fa-youtube', 'color' => '#c4302b'],
        ['title' => 'FaceBook', 'extra' => 'fab fa-facebook', 'color' => '#3b5998']
    ];

    function admin_index() {
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_SOCIAL'))
            throw new ForbiddenException();

        $this->set('title_for_layout', $this->Lang->get('SOCIAL__HOME'));
        $this->layout = 'admin';

        $this->loadModel('SocialButton');
        $this->set('social_buttons', $this->SocialButton->find('all', ['order' => 'order']));
    }

    public function admin_save_ajax() {
        $this->autoRender = false;
        $this->response->type('json');
        if ($this->isConnected AND $this->Permissions->can('MANAGE_SOCIAL')) {
            if ($this->request->is('post')) {
                if (!empty($this->request->data)) {
                    $data = $this->request->data['social_button_order'];
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
                    $this->loadModel('SocialButton');
                    foreach ($data as $key => $value) {
                        $find = $this->SocialButton->find('first', array('conditions' => array('id' => $key)));
                        if (!empty($find)) {
                            $id = $find['SocialButton']['id'];
                            $this->SocialButton->read(null, $id);
                            $this->SocialButton->set(array(
                                'order' => $value,
                            ));
                            $this->SocialButton->save();
                        } else {
                            $error = 1;
                        }
                    }
                    if (empty($error)) {
                        return $this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('SOCIAL__SAVE_SUCCESS'))));
					} else{
                        return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));
                    }
                } else {
                    return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));
                }
            } else {
                return $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST'))));
            }
        } else {
            $this->redirect('/');
        }
    }

    function admin_add() {
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_SOCIAL'))
            throw new ForbiddenException();

        $this->set('title_for_layout', $this->Lang->get('SOCIAL__HOME'));
        $this->layout = 'admin';

        $this->set('social_default', $this->social_default);

        if ($this->request->is('post')) {
            $this->autoRender = false;
            $this->response->type('json');

            if (empty($this->request->data('url')))
                return $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')]));
            if(!empty($this->request->data('img')) && !empty($this->request->data('icon')) && empty($this->request->data('type')))
                return $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('SOCIAL__CANNOT_TOW_TYPE')]));
            
            $extra = null;
            if(!empty($this->request->data('type'))) {
                if($this->request->data('type') == "img") {
                    $extra = $this->request->data('img');;
                } else {
                    $extra = $this->request->data('icon');;
                }
            }

            $this->loadModel('SocialButton');
            $order = $this->SocialButton->find('first', ['order' => ['order' => 'DESC']])['SocialButton']['order'];
            $order = (empty($order)) ? 1 : $order+1;

            $this->SocialButton->create();
            $this->SocialButton->set([
                'order' => $order,
                'title' => $this->request->data('title'),
                'extra' => $extra,
                'color' => $this->request->data('color'),
                'url' => $this->request->data('url')
            ]);
            $this->SocialButton->save();

            $this->response->body(json_encode(['statut' => true, 'msg' => $this->Lang->get('SOCIAL__BUTTON_SUCCESS')]));
        }
    }

    function admin_edit($id = false) {
        if (!$this->isConnected || !$this->Permissions->can('MANAGE_SOCIAL'))
            throw new ForbiddenException();

        if (!$id)
            throw new NotFoundException();

        $find = $this->SocialButton->find('first', ['order' => 'id desc', 'conditions' => ['id' => $id]]);
        if (empty($find))
            throw new NotFoundException();

        $this->set('title_for_layout', $this->Lang->get('SOCIAL__HOME'));
        $this->layout = 'admin';

        $social_button_type = null;
        if(!empty($find['SocialButton']['extra'])) {
            if(strpos($find['SocialButton']['extra'], 'fa-')) {
                $social_button_type = 'fa';
            } else {
                $social_button_type = 'img';
            }
        }


        $this->set('social_button', $find['SocialButton']);
        $this->set('social_default', $this->social_default);
        $this->set('social_button_type', $social_button_type);

        if ($this->request->is('post')) {
            $this->autoRender = false;
            $this->response->type('json');

            if (empty($this->request->data('url')))
                return $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')]));
            if(!empty($this->request->data('img')) && !empty($this->request->data('icon')) && empty($this->request->data('type')))
                return $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('SOCIAL__CANNOT_TOW_TYPE')]));

            $extra = null;
            if(!empty($this->request->data('type'))) {
                if($this->request->data('type') == "img") {
                    $extra = $this->request->data('img');;
                } else {
                    $extra = $this->request->data('icon');;
                }
            }

            $this->loadModel('SocialButton');
            $this->SocialButton->read(null, $id);
            $this->SocialButton->set([
                'title' => $this->request->data('title'),
                'extra' => $extra,
                'color' => $this->request->data('color'),
                'url' => $this->request->data('url')
            ]);
            $this->SocialButton->save();

            $this->response->body(json_encode(['statut' => true, 'msg' => $this->Lang->get('SOCIAL__BUTTON_EDIT_SUCCESS')]));
        }
    }

    public function admin_delete($id = false) {
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
