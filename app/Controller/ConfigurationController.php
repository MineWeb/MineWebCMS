<?php

class ConfigurationController extends AppController {

	public $components = array('Session', 'RequestHandler');

	public function admin_index() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			$this->layout = "admin";

			$config = $this->Configuration->get_all()['Configuration'];
			$this->loadModel('Server');
			if(!empty($config['banner_server'])) {
				$config['banner_server'] = unserialize($config['banner_server']);
				foreach ($config['banner_server'] as $key => $value) {
					$d = $this->Server->find('first', array('conditions' => array('id' => $value)));
					$selected_server[] = $d['Server']['id'];
				}
			} else {
				$selected_server = array();
			}
			$this->set(compact('selected_server'));

			$search_servers = $this->Server->find('all');
			foreach ($search_servers as $v) {
				$servers[$v['Server']['id']] = $v['Server']['name'];
			}
			$this->set(compact('servers'));

			if($this->request->is('post')) {
				foreach ($this->request->data as $key => $value) {
					if($key != "version") {
						if($key == "banner_server") {
							$value = serialize($value);
						}
						$this->Configuration->set($key, $value);
						if($key == "mineguard") {
							if($value == "true") {
								$this->ServerComponent = $this->Components->load('Server');
								$this->ServerComponent->call(array('setMineguard' => 'true'), true);
							} else {
								$this->ServerComponent = $this->Components->load('Server');
								$this->ServerComponent->call(array('setMineguard' => 'false'), true);
							}
						}
					}
				}
				$this->History->set('EDIT_CONFIGURATION', 'configuration');
				 
				$this->Session->setFlash($this->Lang->get('EDIT_CONFIGURATION_SUCCESS'), 'default.success');
			}
		} else {
			$this->redirect('/');
		}
	}
}