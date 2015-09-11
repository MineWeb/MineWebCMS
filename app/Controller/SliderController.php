<?php

class SliderController extends AppController {

	public $components = array('Session');

	public function admin_index() {
		if($this->Connect->connect() AND $this->Permissions->can('MANAGE_SLIDER')) {
			 
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
		if($this->Connect->connect() AND $this->Permissions->can('MANAGE_SLIDER')) {
			if($id != false) {
				 
				$this->layout = null;
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
		if($this->Connect->connect() AND $this->Permissions->can('MANAGE_SLIDER')) {
			$this->layout = 'admin';
			 
			if($id != false) {
				$this->loadModel('Slider');
				$search = $this->Slider->find('all', array('conditions' => array('id' => $id)));
				if(!empty($search)) {
					$slider = $search['0']['Slider'];
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
		if($this->Connect->connect() AND $this->Permissions->can('MANAGE_SLIDER')) {
			$this->layout = null;
			 
			if($this->request->is('post')) {
				if(!empty($this->request->data['title']) AND !empty($this->request->data['subtitle']) AND !empty($this->request->data['url_img']) AND !empty($this->request->data['id'])) {
					$this->loadModel('Slider');
					$this->Slider->read(null, $this->request->data['id']);
					$this->Slider->set(array(
						'title' => $this->request->data['title'],
						'subtitle' => $this->request->data['subtitle'],
						'url_img' => $this->request->data['url_img']
					));
					$this->Slider->save();
					$this->History->set('EDIT_SLIDER', 'slider');
					echo $this->Lang->get('SUCCESS_SLIDER_EDIT').'|true';
					$this->Session->setFlash($this->Lang->get('SUCCESS_SLIDER_EDIT'), 'default.success');
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST' ,$language).'|false';
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add() {
		if($this->Connect->connect() AND $this->Permissions->can('MANAGE_SLIDER')) {
			$this->layout = 'admin';
			 
			$this->set('title_for_layout', $this->Lang->get('ADD_SLIDER'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add_ajax() {
		if($this->Connect->connect() AND $this->Permissions->can('MANAGE_SLIDER')) {
			$this->layout = null;
			 
			if($this->request->is('post')) {
				if(!empty($this->request->data['title']) AND !empty($this->request->data['subtitle']) AND !empty($this->request->data['url_img'])) {
					$this->loadModel('Slider');
					$this->Slider->read(null, null);
					$this->Slider->set(array(
						'title' => $this->request->data['title'],
						'subtitle' => $this->request->data['subtitle'],
						'url_img' => $this->request->data['url_img']
					));
					$this->Slider->save();
					$this->History->set('ADD_SLIDER', 'slider');
					echo $this->Lang->get('SUCCESS_SLIDER_ADD').'|true';
					$this->Session->setFlash($this->Lang->get('SUCCESS_SLIDER_ADD'), 'default.success');
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST' ,$language).'|false';
			}
		} else {
			$this->redirect('/');
		}
	}

}