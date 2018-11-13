<?php

class APIController extends AppController {

	public $components = array('Session', 'API');

	public function launcher($username, $password, $args = null) {
		$this->autoRender = false;
		$this->response->type('json');
		$args = explode(',', $args);
		$this->response->body(json_encode($this->API->get($username, $password, $args)));
	}

	public function admin_index() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_API')) {
			$this->set('title_for_layout',$this->Lang->get('API__LABEL'));
			$this->layout = 'admin';

			$this->loadModel('ApiConfiguration');
			$config = $this->ApiConfiguration->find('first')['ApiConfiguration'];

			if($this->request->is('post')) {
				if(isset($this->request->data['skins']) AND isset($this->request->data['skin_free']) AND !empty($this->request->data['skin_filename']) AND isset($this->request->data['capes']) AND isset($this->request->data['cape_free']) AND !empty($this->request->data['cape_filename'])) {

					$this->loadModel('ApiConfiguration');
					$this->ApiConfiguration->read(null, 1);
					$this->ApiConfiguration->set($this->request->data);
					$this->ApiConfiguration->save();

					$config = $this->request->data;

					$this->History->set('EDIT_CONFIGURATION', 'api');
					$this->Session->setFlash($this->Lang->get('CONFIG__EDIT_SUCCESS'), 'default.success');
				} else {
					$this->Session->setFlash($this->Lang->get('ERROR__FILL_ALL_FIELDS'), 'default.error');
				}
			}

			$this->set('config', $config);
		} else {
			$this->redirect('/');
		}
	}

	public function get_skin($name) {
		header('Content-Type: image/png');
		$this->autoRender = false;
		echo $this->API->get_skin($name);
	}

	public function get_head_skin($name, $size = 50) {
		header('Content-Type: image/png');
		$this->autoRender = false;
		echo $this->API->get_head_skin($name, $size);
	}

}
