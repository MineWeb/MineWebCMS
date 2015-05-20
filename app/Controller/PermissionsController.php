<?php 

class PermissionsController extends AppController {

	public $components = array('Session', 'History', 'Connect');

	function admin_index() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout', $this->Lang->get('PERMISSIONS'));
			$this->layout = 'admin';

		} else {
			$this->redirect('/');
		}
	}

}