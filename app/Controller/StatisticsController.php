<?php

class StatisticsController extends AppController {

	public $components = array('Session', 'History');

	function admin_index() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->set('title_for_layout', $this->Lang->get('STATISTICS'));
			$this->layout = 'admin';

			$this->set('referers', $this->Statistics->get_referers());
			$this->set('pages', $this->Statistics->get_pages());
			$this->set('language', $this->Statistics->get_language());

		} else {
			$this->redirect('/');
		}
	}

	function admin_reset() {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {
			$this->loadModel('Visit');
			$this->Visit->deleteAll(array('1 = 1'));

			$this->redirect(array('action' => 'index'));
		} else {
			$this->redirect('/');
		}
	}

}
