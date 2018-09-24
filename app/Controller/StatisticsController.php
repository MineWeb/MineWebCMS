<?php

class StatisticsController extends AppController {

	public $components = array('Session', 'History');

	function admin_index() {
		if($this->isConnected AND $this->Permissions->can('VIEW_STATISTICS')) {

			$this->set('title_for_layout', $this->Lang->get('STATS__TITLE'));
			$this->layout = 'admin';

			$this->loadModel('Visit');
			$this->set('referers', $this->Visit->getGrouped('referer', 10));
			$this->set('pages', $this->Visit->getGrouped('page', 10));
			$this->set('language', $this->Visit->getGrouped('lang', 10));

		} else {
			$this->redirect('/');
		}
	}

	function admin_get_visits() {
		if($this->isConnected AND $this->Permissions->can('VIEW_STATISTICS')) {

			$this->response->type('json');

			$this->autoRender = false;
			$this->loadModel('Visit');

			$visits = $this->Visit->getVisitRange(15);

			if ($visits) {
				foreach ($visits as $key => $value) {

					$date = strtotime($key);
					$date = $date * 1000;

					$visitsToFormatte[$date] = intval($value);

				}

				$i = 0;
				foreach ($visitsToFormatte as $key => $value) {
					$visitsFormatted[] = array($key, $value);
				}

				$this->response->body(json_encode($visitsFormatted));
			}

		} else {
			throw new ForbiddenException();
		}
	}

	function admin_reset() {
		$this->autoRender = false;
		if($this->isConnected AND $this->Permissions->can('VIEW_STATISTICS')) {
			$this->loadModel('Visit');
			$this->Visit->deleteAll(array('1 = 1'));

			$this->redirect(array('action' => 'index'));
		} else {
			$this->redirect('/');
		}
	}

}
