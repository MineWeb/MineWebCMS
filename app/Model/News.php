<?php
App::uses('CakeEvent', 'Event');

class News extends AppModel {

	public $hasMany = array(
		'Comment' => array(
      'className' => 'Comment',
      'foreignKey' => 'news_id',
      'order' => 'Comment.created DESC',
      'dependent' => true
  	),
		'Like' => array(
      'className' => 'Like',
      'foreignKey' => 'news_id',
      'dependent' => true
  	)
	);

	private $userModel;
	private function __getUserModel() {
		if(empty($this->userModel)) {
			$this->userModel = ClassRegistry::init('User');
		}
		return $this->userModel;
	}

	private $usersByID = array();
	private function getUserFromID($id) {
		if(!isset($this->usersByID[$id])) {
			$findUser = $this->__getUserModel()->find('first', array('fields' => array('id', 'pseudo'), 'conditions' => array('id' => $id)));
			if(!empty($findUser)) {
				$this->usersByID[$findUser['User']['id']] = $findUser['User']['pseudo'];
			} else {
				$this->usersByID[$findUser['User']['id']] = 'N/A';
			}
		}
		return $this->usersByID[$id];
	}

	public function find($conditions = null, $fields = array(), $order = null, $recursive = null) {
		$result = Cache::read('news', 'data');
		$index = md5(serialize(func_get_args()));
		if($result === false || !isset($result[$index]) || Configure::read('debug') > 0) {
			$result[$index] = parent::find($conditions, $fields, $order, $recursive);

			if(!empty($result[$index])) {
				if($conditions != "count") {
					if($conditions == "first") {
						$result[$index]['News']['author'] = $this->getUserFromID($result[$index]['News']['user_id']);
					} else {
						foreach ($result[$index] as $key => $value) {
							$result[$index][$key]['News']['author'] = $this->getUserFromID($result[$index][$key]['News']['user_id']);
						}
					}
				}
				unset($key);
				unset($value);
			}

			if(!empty($result[$index]) && ($conditions == "all" || $conditions == "first")) {
				if($recursive || (isset($fields['recursive']) && $fields['recursive'])) {
					if($conditions == "all") {
						foreach ($result[$index] as $key => $value) {
							if(isset($result[$index][$key]['Comment'])) {
								$result[$index][$key]['News']['count_comments'] = count($result[$index][$key]['Comment']);
							}
							if(isset($result[$index][$key]['Like'])) {
								$result[$index][$key]['News']['count_likes'] = count($result[$index][$key]['Like']);
							}
							if(isset($result[$index][$key]['Comment'])) {
								foreach ($result[$index][$key]['Comment'] as $k => $v) {
									$result[$index][$key]['Comment'][$k]['author'] = $this->getUserFromID($v['user_id']);
								}
							}
						}
					} else {
						if(isset($result[$index]['Comment'])) {
							$result[$index]['News']['count_comments'] = count($result[$index]['Comment']);
						}
						if(isset($result[$index]['Like'])) {
							$result[$index]['News']['count_likes'] = count($result[$index]['Like']);
						}
						if(isset($result[$index]['Comment'])) {
							foreach ($result[$index]['Comment'] as $k => $v) {
								$result[$index]['Comment'][$k]['author'] = $this->getUserFromID($v['user_id']);
							}
						}
					}
				}
			}

			if(Configure::read('debug') <= 0) {
				Cache::write('news', $result, 'data');
			}

		}
		return $result[$index];
	}


	public function afterSave($created, $options = array()) {
		Cache::delete('news', 'data');
	}

	public function afterDelete($cascade = true) {
		Cache::delete('news', 'data');
	}

}
