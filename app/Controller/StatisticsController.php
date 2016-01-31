<?php

class StatisticsController extends AppController {

	public $components = array('Session', 'History');

	function admin_index() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->set('title_for_layout', $this->Lang->get('STATS__TITLE'));
			$this->layout = 'admin';

			$this->loadModel('Visit');
			$this->set('referers', $this->Visit->get('referer'));
			$this->set('pages', $this->Visit->get('page'));
			$this->set('language', $this->Visit->get('lang'));

		} else {
			$this->redirect('/');
		}
	}

	function admin_get_visits() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->response->type('json');

			$this->autoRender = false;
			$this->loadModel('Visit');

			$visits = $this->Visit->getVisits(15);

			foreach ($visits as $key => $value) {

				if($key != "count") {

					$date = $value['Visit']['created'];
					$date = explode(' ', $date)[0];
					$date = strtotime($date);
					$date = $date * 1000;


					if(isset($visitsToFormatte[$date])) {
						$visitsToFormatte[$date]++;
					} else {
						$visitsToFormatte[$date] = 1;
					}

				}

			}

			$limit = 15;
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
