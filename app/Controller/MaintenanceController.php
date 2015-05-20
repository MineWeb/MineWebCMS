<?php

class MaintenanceController extends AppController {

	public $components = array('Session');

	public function index($banned = false) {
		if(!$banned) {
			$msg = $this->Configuration->get('maintenance');
		} else {
			 
			$msg = $this->Lang->get('YOUR_BANNED');
		}
		$this->set(compact('msg'));
	}

	public function admin_index() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			$this->layout = "admin";
			 
			$this->set('title_for_layout',$this->Lang->get('MAINTENANCE'));
			if($this->request->is('post')) {
				if($this->request->data['state'] == 'enabled') {
					$maintenance = $this->request->data['reason'];
					$this->History->set('ADD_MAINTENANCE', 'maintenance');
				} elseif($this->request->data['state'] == 'disabled') {
					$maintenance = '0';
					$this->History->set('DELETE_MAINTENANCE', 'maintenance');
				}
				$this->Configuration->set('maintenance', $maintenance);
				$this->Session->setFlash($this->Lang->get('EDIT_MAINTENANCE_SUCCESS'), 'default.success');
			}
		} else {
			$this->redirect('/');
		}
	}

}