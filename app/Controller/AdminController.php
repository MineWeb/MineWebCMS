<?php

class AdminController extends AppController {

	public $components = array('Session', 'History');

	function admin_index() {
		if($this->isConnected AND $this->Permissions->can('ACCESS_DASHBOARD')) {

			$this->set('title_for_layout', $this->Lang->get('GLOBAL__HOME'));
			$this->layout = 'admin';

			$this->loadModel('News');
			$nbr_news = $this->News->find('count');

			$this->loadModel('Comment');
			$nbr_comments = $this->Comment->find('count', array('conditions' => array('created LIKE' => date('Y-m-d').'%')));
			if(empty($nbr_comments)) {
				$nbr_comments = $this->Comment->find('count');
				$nbr_comments_type = "all";
			} else {
				$nbr_comments_type = "today";
			}

			$this->loadModel('User');
			$registered_users = $this->User->find('count');
			$registered_users_today = $this->User->find('count', array('conditions' => array('created LIKE' => date('Y-m-d').'%')));

			$this->loadModel('Visit');
			$count_visits = $this->Visit->getVisits(4)['count'];
			$count_visits_before_before_yesterday = $this->Visit->getVisitsByDay(date('Y-m-d', strtotime('-3 day')))['count'];
			$count_visits_before_yesterday = $this->Visit->getVisitsByDay(date('Y-m-d', strtotime('-2 day')))['count'];
			$count_visits_yesterday = $this->Visit->getVisitsByDay(date('Y-m-d', strtotime('-1 day')))['count'];
			$count_visits_today = $this->Visit->getVisitsByDay(date('Y-m-d'))['count'];

			if($this->EyPlugin->isInstalled('eywek.shop.1')) {

				$purchase = $this->History->get('shop', false, false, 'BUY_ITEM');
				$purchase = count($purchase);
				$purchase_today = $this->History->get('shop', false, date('Y-m-d'), 'BUY_ITEM');
				$purchase_today = count($purchase_today);

				$this->loadModel('History');
			  $items_solded = $this->History->find('all', array(
					'fields' => 'other,COUNT(*)',
		    	'order' => 'COUNT(other) DESC',
		    	'conditions' => array('action' => 'BUY_ITEM'),
			    'limit' => '5',
			    'group' => 'other',
				));
			}

			$this->loadModel('Server');
			$servers = $this->Server->find('all');

			if($this->request->is('post')) {
				if(!empty($this->request->data['cmd']) && !empty($this->request->data['server_id'])) {
					$this->ServerComponent = $this->Components->load('Server');
					$call = $this->ServerComponent->call(array('performCommand' => $this->request->data['cmd']), true, $this->request->data['server_id']);

					$this->Session->setFlash($this->Lang->get('SERVER__SEND_COMMAND_SUCCESS'), 'default.success');
				}
			}

			$this->set(compact(
				'nbr_news',
				'nbr_comments', 'nbr_comments_type',
				'registered_users', 'registered_users_today',
				'count_visits', 'count_visits_before_before_yesterday', 'count_visits_before_yesterday', 'count_visits_yesterday', 'count_visits_today',
				'purchase', 'purchase_today', 'items_solded',
				'servers'
			));

		} else {
			$this->redirect('/');
		}
	}

	function admin_stop() {
		if($this->isConnected AND $this->Permissions->can('ACCESS_DASHBOARD')) {
			if($this->Server->online()) {
				$this->layout = null;
				$this->Server->call(array('performCommand' => 'save-all'), true);
				$this->Server->call(array('performCommand' => 'stop'), true);
				$this->redirect(array('controller' => 'admin', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

}
