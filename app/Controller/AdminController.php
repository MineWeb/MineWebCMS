<?php

class AdminController extends AppController {

	public $components = array('Session', 'History', 'Connect');

	function admin_index() {
		if($this->isConnected AND $this->Permissions->can('ACCESS_DASHBOARD')) {

			$this->set('title_for_layout', $this->Lang->get('HOME'));
			$this->layout = 'admin';
			$this->loadModel('News');
			$nbr_news = $this->News->find('count');
			$this->set(compact('nbr_news'));
			$this->loadModel('Comment');
			$nbr_comments = $this->Comment->find('count', array('conditions' => array('created LIKE' => date('Y-m-d').'%')));
			if(empty($nbr_comments)) {
				$nbr_comments = $this->Comment->find('count');
				$nbr_comments_type = "all";
			} else {
				$nbr_comments_type = "today";
			}
			$this->set(compact('nbr_comments'));
			$this->set(compact('nbr_comments_type'));
			$this->loadModel('User');
			$registered_users = $this->User->find('count');
			$registered_users_today = $this->User->find('count', array('conditions' => array('created LIKE' => date('Y-m-d').'%')));
			$this->set(compact('registered_users'));
			$this->set(compact('registered_users_today'));

			$count_visits = $this->Statistics->get_all_visits(); $this->set(compact('count_visits'));
			$count_visits_before_before_yesterday = $this->Statistics->get_visits_by_day(date('Y-m-d', strtotime('-3 day')));
			$count_visits_before_yesterday = $this->Statistics->get_visits_by_day(date('Y-m-d', strtotime('-2 day')));
			$count_visits_yesterday = $this->Statistics->get_visits_by_day(date('Y-m-d', strtotime('-1 day')));
			$count_visits_today = $this->Statistics->get_visits_by_day(date('Y-m-d'));

			$this->set(compact('count_visits'));
			$this->set(compact('count_visits_before_before_yesterday'));
			$this->set(compact('count_visits_before_yesterday'));
			$this->set(compact('count_visits_yesterday'));
			$this->set(compact('count_visits_today'));

			$purchase = $this->History->get('shop', false, false, 'BUY_ITEM');
			$purchase = count($purchase);
			$purchase_today = $this->History->get('shop', false, date('Y-m-d'), 'BUY_ITEM');
			$purchase_today = count($purchase_today);
			$this->set(compact('purchase'));
			$this->set(compact('purchase_today'));

			$this->loadModel('History');
		    $items_solded = $this->History->find('all', array(
		    	'order' => 'COUNT(other) DESC',
		    	'conditions' => array('action' => 'BUY_ITEM'),
			    'limit' => '5',
			    'group' => 'other',
			));

		    foreach ($items_solded as $key => $value) {
		    	$how[$key] = $this->History->find('count', array('conditions' => array('other' => $value['History']['other'])));
		    }
		    $this->set(compact('how'));

			$this->set(compact('items_solded'));

			if($this->EyPlugin->isInstalled('eywek.shop.1')) {
				$this->loadModel('Shop.Item');
				$counts_items = $this->Item->find('count');

				if(count($items_solded) < 5) {
					$counts_items = 0;
				}

				$this->set(compact('counts_items'));
			} else {
				$this->set('counts_items', 0);
			}

			$this->loadModel('Server');
			$servers = $this->Server->find('all');
			$this->set(compact('servers'));

			if($this->request->is('post')) {
				if(!empty($this->request->data['cmd']) && !empty($this->request->data['server_id'])) {
					$this->ServerComponent = $this->Components->load('Server');
					$this->ServerComponent->call(array('performCommand' => $this->request->data['cmd']), true, $this->request->data['server_id']);
					$this->Session->setFlash($this->Lang->get('SUCCESS_SEND_COMMAND'), 'default.success');
				}
			}

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
