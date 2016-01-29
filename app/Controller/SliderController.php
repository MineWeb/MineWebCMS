<?php

class SliderController extends AppController {

	public $components = array('Session');

	public function admin_index() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_SLIDER')) {

			$this->set('title_for_layout',$this->Lang->get('ADD_SLIDER'));
			$this->layout = 'admin';
			$this->loadModel('Slider');
			$sliders = $this->Slider->find('all');
			$this->set(compact('sliders'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_delete($id = false) {
		$this->autoRender = false;
		if($this->isConnected AND $this->Permissions->can('MANAGE_SLIDER')) {
			if($id != false) {
				$this->loadModel('Slider');
				$find = $this->Slider->find('all', array('conditions' => array('id' => $id)));
				if(!empty($find)) {
					$this->Slider->delete($id);
					$this->History->set('DELETE_SLIDER', 'slider');
					$this->Session->setFlash($this->Lang->get('DELETE_SLIDER_SUCCESS'), 'default.success');
					$this->redirect(array('controller' => 'slider', 'action' => 'index', 'admin' => true));
				} else {
					$this->Session->setFlash($this->Lang->get('UNKNONW_ID'), 'default.error');
					$this->redirect(array('controller' => 'slider', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'slider', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_edit($id = false) {
		if($this->isConnected AND $this->Permissions->can('MANAGE_SLIDER')) {
			$this->layout = 'admin';

			if($id != false) {
				$this->loadModel('Slider');
				$search = $this->Slider->find('all', array('conditions' => array('id' => $id)));
				if(!empty($search)) {
					$slider = $search['0']['Slider'];
					$slider['filename'] = explode('/', $slider['url_img']);
					$slider['filename'] = end($slider['filename']);
					$this->set(compact('slider'));
				} else {
					$this->Session->setFlash($this->Lang->get('UKNOWN_ID'), 'default.error');
					$this->redirect(array('controller' => 'slider', 'action' => 'admin_index', 'admin' => 'true'));
				}
			} else {
				$this->redirect(array('controller' => 'slider', 'action' => 'admin_index', 'admin' => 'true'));
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_edit_ajax() {
		$this->autoRender = false;
		if($this->isConnected AND $this->Permissions->can('MANAGE_SLIDER')) {

			if($this->request->is('post')) {
				if(!empty($this->request->data['title']) AND !empty($this->request->data['subtitle']) AND !empty($this->request->data['id'])) {

					if(!isset($this->request->data['img_edit'])) {

						$isValidImg = $this->Util->isValidImage($this->request, array('png', 'jpg', 'jpeg'));

						if(!$isValidImg['status']) {
							echo json_encode(array('statut' => false, 'msg' => $isValidImg['msg']));
							exit;
						} else {
							$infos = $isValidImg['infos'];
						}

						$url_img = WWW_ROOT.'img'.DS.'uploads'.DS.'slider'.DS.date('Y-m-d_His').'.'.$infos['extension'];

						if(!$this->Util->uploadImage($this->request, $url_img)) {
							echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('FORM__ERROR_WHEN_UPLOAD')));
							exit;
						}

						$url_img = Router::url('/').'img'.DS.'uploads'.DS.'slider'.DS.date('Y-m-d_His').'.'.$infos['extension'];

						$data = array(
							'title' => $this->request->data['title'],
							'subtitle' => $this->request->data['subtitle'],
							'url_img' => $url_img
						);

					} else {

						$data = array(
							'title' => $this->request->data['title'],
							'subtitle' => $this->request->data['subtitle'],
						);

					}

					$this->loadModel('Slider');
					$this->Slider->read(null, $this->request->data['id']);
					$this->Slider->set($data);
					$this->Slider->save();
					$this->History->set('EDIT_SLIDER', 'slider');
					echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('SUCCESS_SLIDER_EDIT')));
					$this->Session->setFlash($this->Lang->get('SUCCESS_SLIDER_EDIT'), 'default.success');
				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')));
				}
			} else {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('NOT_POST')));
			}
		} else {
			throw new ForbiddenException();
		}
	}

	public function admin_add() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_SLIDER')) {
			$this->layout = 'admin';

			$this->set('title_for_layout', $this->Lang->get('ADD_SLIDER'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add_ajax() {
		$this->autoRender = false;
		if($this->isConnected AND $this->Permissions->can('MANAGE_SLIDER')) {

			if($this->request->is('post')) {

				if(!empty($this->request->data['title']) AND !empty($this->request->data['subtitle'])) {

					$isValidImg = $this->Util->isValidImage($this->request, array('png', 'jpg', 'jpeg'));

					if(!$isValidImg['status']) {
						echo json_encode(array('statut' => false, 'msg' => $isValidImg['msg']));
						exit;
					} else {
						$infos = $isValidImg['infos'];
					}

					$url_img = WWW_ROOT.'img'.DS.'uploads'.DS.'slider'.DS.date('Y-m-d_His').'.'.$infos['extension'];

					if(!$this->Util->uploadImage($this->request, $url_img)) {
						echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('FORM__ERROR_WHEN_UPLOAD')));
						exit;
					}

					$url_img = Router::url('/').'img'.DS.'uploads'.DS.'slider'.DS.date('Y-m-d_His').'.'.$infos['extension'];

					$this->loadModel('Slider');
					$this->Slider->read(null, null);
					$this->Slider->set(array(
						'title' => $this->request->data['title'],
						'subtitle' => $this->request->data['subtitle'],
						'url_img' => $url_img
					));
					$this->Slider->save();
					$this->History->set('ADD_SLIDER', 'slider');
					echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('SUCCESS_SLIDER_ADD')));
					$this->Session->setFlash($this->Lang->get('SUCCESS_SLIDER_ADD'), 'default.success');
				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')));
				}
			} else {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('NOT_POST' ,$language)));
			}
		} else {
			throw new ForbiddenException();
		}
	}

}
