<?php

class StatisticsController extends AppController {

	public $components = array('Session', 'History');

	function admin_index() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->set('title_for_layout', $this->Lang->get('STATS__TITLE'));
			$this->layout = 'admin';

			$this->loadModel('Visit');
			$this->set('referers', $this->Visit->getGrouped('referer'));
			$this->set('pages', $this->Visit->getGrouped('page'));
			$this->set('language', $this->Visit->getGrouped('lang'));

		} else {
			$this->redirect('/');
		}
	}

	function admin_get_visits() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->response->type('json');

			$this->autoRender = false;
			$this->loadModel('Visit');

			$visits = $this->Visit->getVisitRange(15);

			foreach ($visits as $key => $value) {

				$date = strtotime($key);
				$date = $date * 1000;

				$visitsToFormatte[$date] = intval($value);

			}

			$i = 0;
			foreach ($visitsToFormatte as $key => $value) {
				$visitsFormatted[] = array($key, $value);
			}

			echo json_encode($visitsFormatted);

		} else {
			throw new ForbiddenException();
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
