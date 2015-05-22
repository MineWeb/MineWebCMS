<?php 

class StatisticsController extends AppController {

	public $components = array('Session', 'History', 'Connect');

	function admin_index() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout', $this->Lang->get('STATISTICS'));
			$this->layout = 'admin';
			
			$this->set('referers', $this->Statistics->get_referers());

		} else {
			$this->redirect('/');
		}
	}

}