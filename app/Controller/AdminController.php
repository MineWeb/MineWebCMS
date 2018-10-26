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
			$count_visits = $this->Visit->getVisitsCount();
			$count_visits_before_before_yesterday = $this->Visit->getVisitsByDay(date('Y-m-d', strtotime('-3 day')))['count'];
			$count_visits_before_yesterday = $this->Visit->getVisitsByDay(date('Y-m-d', strtotime('-2 day')))['count'];
			$count_visits_yesterday = $this->Visit->getVisitsByDay(date('Y-m-d', strtotime('-1 day')))['count'];
			$count_visits_today = $this->Visit->getVisitsByDay(date('Y-m-d'))['count'];

			if($this->EyPlugin->isInstalled('eywek.shop')) {

				$this->loadModel('Shop.ItemsBuyHistory');
				$purchase = $this->ItemsBuyHistory->find('count', array('order' => 'id DESC'));
				$purchase_today = $this->ItemsBuyHistory->find('count', array('conditions' => array('created LIKE' => date('Y-m-d').'%'), 'order' => 'id DESC'));

				$this->loadModel('Shop.Item');
				$findItems = $this->Item->find('all');
				$itemsNameByID = array();
				foreach ($findItems as $key => $value) {
					$itemsNameByID[$value['Item']['id']] = $value['Item']['name'];
				}

				$find_items_solded = $this->ItemsBuyHistory->find('all', array(
					'fields' => 'COUNT(*),item_id',
					'order' => 'COUNT(id) DESC',
					'group' => 'item_id',
					'limit' => 5
				));
				$items_solded = array();
				$i = 0;

				foreach ($find_items_solded as $key => $value) {

					$items_solded[$i]['count'] = $value[0]['COUNT(*)'];
					$items_solded[$i]['item_name'] = @$itemsNameByID[$value['ItemsBuyHistory']['item_id']];

					$i++;

				}

			}

			$this->loadModel('Server');
			$servers = $this->Server->find('all');

            if($this->request->is('ajax') && $this->Permissions->can('SEND_SERVER_COMMAND_FROM_DASHBOARD')) {
                if(!empty($this->request->data['server_id'])) {
                    $this->ServerComponent = $this->Components->load('Server');
                    $this->autoRender = false;
                    $this->response->type('json');
                    if (!empty($this->request->data['cmd'])) {
                        $call = $this->ServerComponent->send_command($this->request->data['cmd'], $this->request->data['server_id']);
                    } else {
                        $call = $this->ServerComponent->send_command($this->request->data['cmd2'], $this->request->data['server_id']);
                    }

                    $this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('SERVER__SEND_COMMAND_SUCCESS'))));
                }
            }
            $this->loadModel('ServerCmd');
            $search_cmd = $this->ServerCmd->find('all');
            $this->set(compact(
                'nbr_news',
                'nbr_comments', 'nbr_comments_type',
                'registered_users', 'registered_users_today',
                'count_visits', 'count_visits_before_before_yesterday', 'count_visits_before_yesterday', 'count_visits_yesterday', 'count_visits_today',
                'purchase', 'purchase_today', 'items_solded',
                'servers',
                'search_cmd'
            ));

		} else {
			$this->redirect('/');
		}
	}

}
