<?php

class APIController extends AppController {

	public $components = array('Session', 'API');

	public function mineguard() {
		$this->autoRender = false;
		$this->response->type('json');
		$username = $_GET['user'];
		$ip = $_GET['ip'];
		$this->response->body(json_encode($this->API->verifIp($username, $ip)));
	}

	public function launcher($username, $password, $args = null) {
		$this->autoRender = false;
		$this->response->type('json');
		$args = explode(',', $args);
		$this->response->body(json_encode($this->API->get($username, $password, $args)));
	}

	public function delete_ip() {
		$this->autoRender = false;
		$this->response->type('json');
        if($this->isConnected) {
    		if($this->request->is('post')) {
    			if(isset($this->request->data['ip'])) {
    				if($this->API->removeIp($this->User->getKey('pseudo'), $this->request->data['ip'])) {
							$this->response->body(json_encode(array('statut' => true, 'msg' => '')));
    				} else {
    					$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__INTERNAL_ERROR'))));
    				}
    			} else {
    				$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));
    			}
    		} else {
    			$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST'))));
    		}
    	} else {
    		$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER__ERROR_MUST_BE_LOGGED'))));
    	}
	}

	public function add_ip() {
		$this->autoRender = false;
		$this->response->type('json');
      if($this->isConnected) {
    		if($this->request->is('post')) {
    			if(!empty($this->request->data['ip'])) {
    				if(filter_var($this->request->data['ip'], FILTER_VALIDATE_IP)) {
	    				if($this->API->setIp($this->User->getKey('pseudo'), $this->request->data['ip'])) {
	    					$this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('API__IP_ADD_SUCCESS'))));
	    				} else {
	    					$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__INTERNAL_ERROR'))));
	    				}
	    			} else {
	    				$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('API__IP_INVALID'))));
	    			}
    			} else {
    				$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));
    			}
    		} else {
    			$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST'))));
    		}
    	} else {
    		$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER__ERROR_MUST_BE_LOGGED'))));
    	}
	}

	public function disable_mineguard() {
		$this->autoRender = false;
		$this->response->type('json');
      if($this->isConnected) {
    		if($this->request->is('post')) {

    			$this->User->setKey('allowed_ip', '0');

    			$this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('API__MINEGUARD_DISABLE_SUCCESS'))));

    		} else {
    			$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST'))));
    		}
    	} else {
    		$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER__ERROR_MUST_BE_LOGGED'))));
    	}
	}

	public function enable_mineguard() {
		$this->autoRender = false;
		$this->response->type('json');
      if($this->isConnected) {
    		if($this->request->is('post')) {

    			$this->User->setKey('allowed_ip', serialize(array()));

    			$this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('API__MINEGUARD_ENABLE_SUCCESS'))));

    		} else {
    			$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST'))));
    		}
    	} else {
    		$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER__ERROR_MUST_BE_LOGGED'))));
    	}
	}

	public function admin_index() {
		if($this->isConnected AND $this->User->isAdmin()) {
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
		$this->response->type('png');
		$this->autoRender = false;
		$this->loadModel('ApiConfiguration');
		$config = $this->ApiConfiguration->find('first');
		$config = $config['ApiConfiguration'];
		if($config['skins']) {
			$filename = str_replace('{PLAYER}', $name, $config['skin_filename']);
			$filename = WWW_ROOT.$filename.'.png';
		} else {
			$filename = 'https://skins.minecraft.net/MinecraftSkins/'.$name.'.png';
		}

		echo $this->API->get_skin($filename);
	}

	public function get_head_skin($name, $size = 50) {
		$this->response->type('png');
		$this->autoRender = false;
		$this->loadModel('ApiConfiguration');
		$config = $this->ApiConfiguration->find('first');
		$config = $config['ApiConfiguration'];
		if($config['skins']) {
			$filename = str_replace('{PLAYER}', $name, $config['skin_filename']);
			$filename = WWW_ROOT.$filename.'.png';
		} else {
			$filename = 'https://skins.minecraft.net/MinecraftSkins/'.$name.'.png';
		}
		echo $this->API->get_head_skin($name, $size, $filename);
	}

}
