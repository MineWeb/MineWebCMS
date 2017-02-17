<?php
App::uses('CakeEvent', 'Event');

class History extends AppModel {

	public $belongsTo = 'User';

	public function getLastFromUser($user_id) {
		return $this->find('all', array('conditions' => array('user_id' => $user_id), 'limit' => '50', 'order' => 'id DESC'));
	}

	public function format($data, $lang) {

		if(empty($data)) {
			return array();
		}

		$return = array();

		$this->Lang = $lang;

		foreach ($data as $key => $value) {

			$category = 'HISTORY__CATEGORY_'.strtoupper($value['History']['category']);
			$category = ($this->Lang->get($category) != $category) ? $this->Lang->get($category) : $value['History']['category'];
			$string = '('.$category.') ';

			$string .= 'Le '.$this->Lang->date($value['History']['created']);

			$action = 'HISTORY__ACTION_'.strtoupper($value['History']['action']);
			$action = ($this->Lang->get($action) != $action) ? $this->Lang->get($action) : $value['History']['action'];
			$string .= ' : '.$action;

			// Autres
			switch ($value['History']['action']) {
				case 'SEND_MONEY':
					$other = explode('|', $value['History']['other']);
					$string .= ' pour un montant de '.$other[1].' à '.$this->User->getUsernameByID($other[0]);
					break;
				case 'BUY_MONEY':
					$other = explode('|', $value['History']['other']);
          if (empty($other) || !isset($other[1])) break;
					$string .= ' pour un montant de '.$other[1];
					if(isset($other[3])) {
						$string .= ' (Money : '.$other[3].')';
					}
					$string .= ' avec '.$other[0];
					break;
				case 'BUY_ITEM':
					$string .= ' l\'article "'.$value['History']['other'].'"';
					break;

				default:
					break;
			}

			$string .= ' par '.$value['History']['author'].'.';

			$return[$value['History']['id']] = $string;

		}

		return $return;

	}

	public function afterFind($results, $primary = false) {
		if(!isset($results[0][0]['count'])) {
			$users = ClassRegistry::init('User')->find('all', array('fields' => array('id', 'pseudo')));
			foreach ($users as $key => $value) {
				$users_list[$value['User']['id']] = $value['User']['pseudo'];
			}
			foreach ($results as $k => $v) {
				if(isset($v['History']['user_id']) && isset($users_list[$v['History']['user_id']])) {
					$results[$k]['History']['author'] = $users_list[$v['History']['user_id']];
				} else {
					$results[$k]['History']['author'] = 'N/A';
				}
			}
		}

		return $results;
	}

}
