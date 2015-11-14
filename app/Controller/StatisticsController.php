<?php 

class StatisticsController extends AppController {

	public $components = array('Session', 'History', 'Connect');

	function admin_index() {
		if($this->isConnected AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout', $this->Lang->get('STATISTICS'));
			$this->layout = 'admin';

			$this->set('referers', $this->Statistics->get_referers());
			$this->set('pages', $this->Statistics->get_pages());
			$this->set('language', $this->Statistics->get_language());

		} else {
			$this->redirect('/');
		}
	}

}