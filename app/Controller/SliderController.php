<?php

class SliderController extends AppController {

	public $components = array('Session');

	public function admin_index() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_SLIDER')) {

			$this->set('title_for_layout',$this->Lang->get('SLIDER__ADD'));
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
					$this->Session->setFlash($this->Lang->get('SLIDER__DELETE_SUCCESS'), 'default.success');
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
					throw new NotFoundException();
				}
			} else {
				throw new NotFoundException();
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_edit_ajax() {
		$this->autoRender = false;
		$this->response->type('json');
		if($this->isConnected AND $this->Permissions->can('MANAGE_SLIDER')) {

			if($this->request->is('post')) {
				if(!empty($this->request->data['title']) AND !empty($this->request->data['subtitle']) AND !empty($this->request->data['id'])) {

					if(!isset($this->request->data['img_edit'])) {

						$checkIfImageAlreadyUploaded = (isset($this->request->data['img-uploaded']));
						if($checkIfImageAlreadyUploaded) {

							$url_img = Router::url('/').'img'.DS.'uploads'.$this->request->data['img-uploaded'];

						} else {

							$isValidImg = $this->Util->isValidImage($this->request, array('png', 'jpg', 'jpeg'));

							if(!$isValidImg['status']) {
								$this->response->body(json_encode(array('statut' => false, 'msg' => $isValidImg['msg'])));
								return;
							} else {
								$infos = $isValidImg['infos'];
							}

							$time = date('Y-m-d_His');

							$url_img = WWW_ROOT.'img'.DS.'uploads'.DS.'slider'.DS.$time.'.'.$infos['extension'];

							if(!$this->Util->uploadImage($this->request, $url_img)) {
								$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('FORM__ERROR_WHEN_UPLOAD'))));
								return;
							}

							$url_img = Router::url('/').'img'.DS.'uploads'.DS.'slider'.DS.$time.'.'.$infos['extension'];

						}

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
					$this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('SLIDER__EDIT_SUCCESS'))));
					$this->Session->setFlash($this->Lang->get('SLIDER__EDIT_SUCCESS'), 'default.success');
				} else {
					$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));
				}
			} else {
				$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST'))));
			}
		} else {
			throw new ForbiddenException();
		}
	}

	public function admin_add() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_SLIDER')) {
			$this->layout = 'admin';

			$this->set('title_for_layout', $this->Lang->get('SLIDER__ADD'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add_ajax() {
		$this->autoRender = false;
		$this->response->type('json');
		if($this->isConnected AND $this->Permissions->can('MANAGE_SLIDER')) {

			if($this->request->is('post')) {

				if(!empty($this->request->data['title']) AND !empty($this->request->data['subtitle'])) {

					$checkIfImageAlreadyUploaded = (isset($this->request->data['img-uploaded']));
					if($checkIfImageAlreadyUploaded) {

						$url_img = Router::url('/').'img'.DS.'uploads'.DS.'slider'.DS.$this->request->data['img-uploaded'];

					} else {

						$isValidImg = $this->Util->isValidImage($this->request, array('png', 'jpg', 'jpeg'));

						if(!$isValidImg['status']) {
							$this->response->body(json_encode(array('statut' => false, 'msg' => $isValidImg['msg'])));
							return;
						} else {
							$infos = $isValidImg['infos'];
						}

						$time = date('Y-m-d_His');

						$url_img = WWW_ROOT.'img'.DS.'uploads'.DS.'slider'.DS.$time.'.'.$infos['extension'];

						if(!$this->Util->uploadImage($this->request, $url_img)) {
							$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('FORM__ERROR_WHEN_UPLOAD'))));
							return;
						}

						$url_img = Router::url('/').'img'.DS.'uploads'.DS.'slider'.DS.$time.'.'.$infos['extension'];

					}

					$this->Slider->create();
					$this->Slider->set(array(
						'title' => $this->request->data['title'],
						'subtitle' => $this->request->data['subtitle'],
						'url_img' => $url_img
					));
					$this->Slider->save();

					$this->History->set('ADD_SLIDER', 'slider');

					$this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('SLIDER__ADD_SUCCESS'))));
					$this->Session->setFlash($this->Lang->get('SLIDER__ADD_SUCCESS'), 'default.success');
				} else {
					$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));
				}
			} else {
				$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('NOT_POST' ,$language))));
			}
		} else {
			throw new ForbiddenException();
		}
	}

}
