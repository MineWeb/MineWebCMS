<?php

class ConfigurationController extends AppController {

	public $components = array('Session', 'RequestHandler');

	public function admin_index() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			$this->layout = "admin";
			if($this->request->is('post')) {
				foreach ($this->request->data as $key => $value) {
					if($key != "version") {
						$this->Configuration->set($key, $value);
						if($key == "mineguard") {
							if($value == "true") {
								$this->Server->call(array('activateMineguard' => 'true'), true);
							} else {
								$this->Server->call(array('activateMineguard' => 'false'), true);
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