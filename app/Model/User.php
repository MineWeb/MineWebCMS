<?php
App::uses('CakeEvent', 'Event');

class User extends AppModel {

	private $userData;

	public $hasMany = array(
		'Comment' => array(
      'className' => 'Comment',
      'foreignKey' => 'user_id',
      'order' => 'Comment.created DESC',
      'dependent' => true
  	),
		'Like' => array(
      'className' => 'Like',
      'foreignKey' => 'user_id',
      'dependent' => true
  	)
	);

	public function validRegister($data) {
		if(preg_match('`^([a-zA-Z0-9-_]{2,16})$`', $data['pseudo'])) {
			$data['password'] = password($data['password']);
			$data['password_confirmation'] = password($data['password_confirmation']);
			if($data['password'] == $data['password_confirmation']) {
				if(filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
					$search_member_by_pseudo = $this->find('all', array('conditions' => array('pseudo' => $data['pseudo'])));
					$search_member_by_email = $this->find('all', array('conditions' => array('email' => $data['email'])));
					if(empty($search_member_by_pseudo)) {
						if(empty($search_member_by_email)) {
							return true;
						} else {
							return 'EMAIL_ALREADY_EXIST';
						}
					} else {
						return 'PSEUDO_ALREADY_EXIST';
					}
				} else {
					return 'EMAIL_NOT_VALIDATE';
				}
			} else {
				return 'PASSWORD_NOT_SAME';
			}
		} else {
			return 'PSEUDO_NOT_GOOD_FORMAT';
		}
	}

	public function register($data) {

		$data_to_save = array();

		$data_to_save['pseudo'] = before_display($data['pseudo']);
		$data_to_save['email'] = before_display($data['email']);

		$data_to_save['ip'] = $_SERVER["REMOTE_ADDR"];
		$data_to_save['rank'] = 0;

		$data_to_save['password'] = password($data['password']);

		$this->create();
		$this->set($data_to_save);
		$this->save();
		return $this->getLastInsertId();
	}

	public function login($data, $need_email_confirmed = false) {
		$LoginRetryTable = ClassRegistry::init('LoginRetry');
		$findRetryWithIP = $LoginRetryTable->find('first', array(array('ip' => $_SERVER['REMOTE_ADDR'])));

		// si on trouve rien OU que il n'a pas encore essayé plus de 10 fois OU que la dernière date du retry est passé depuis 2h

		if(empty($findRetryWithIP) || $findRetryWithIP['LoginRetry']['count'] < 10 || strtotime('+2 hours', strtotime($findRetryWithIP['LoginRetry']['modified'])) < time()) {

			$search_user = $this->find('first', array('conditions' => array('pseudo' => $data['pseudo'], 'password' => password($data['password']))));
			if(!empty($search_user)) {

				if($need_email_confirmed && !empty($search_user['User']['confirmed']) && date('Y-m-d H:i:s', strtotime($search_user['User']['confirmed'])) != $search_user['User']['confirmed']) {
					// mail non confirmé
					return 'USER__MSG_NOT_CONFIRMED_EMAIL';
				}

				$this->getEventManager()->dispatch(new CakeEvent('onLogin', $data));

				return array('status' => true, 'session' => $search_user['User']['id']);

			} else {

				if(empty($findRetryWithIP)) { // si il avais rien fail encore

					$LoginRetryTable->create();
					$LoginRetryTable->set(array(
						'ip' => $_SERVER['REMOTE_ADDR'],
						'count' => 1
					));
					$LoginRetryTable->save();

				} else {

					$LoginRetryTable->read(null, $findRetryWithIP['LoginRetry']['id']);
					$LoginRetryTable->set(array(
						'ip' => $_SERVER['REMOTE_ADDR'],
						'count' => ($findRetryWithIP['LoginRetry']['count']+1),
						'modified' => date('Y-m-d H:i:s')
					));
					$LoginRetryTable->save();

				}

				return 'BAD_PSEUDO_OR_PASSWORD';
			}

		} else {
			return 'LOGIN__BLOCKED';
		}
	}

	public function resetPass($data) {
		$data['password'] = password($data['password']);
		$data['password2'] = password($data['password2']);
		if($data['password'] == $data['password2']) {
			unset($data['password2']);
			$search = $this->find('all', array('conditions' => array('email' => $data['email'])));
			if(!empty($search)) {

				$this->Lostpassword = ClassRegistry::init('Lostpassword');
				$Lostpassword = $this->Lostpassword->find('all', array('conditions' => array('email' => $data['email'])));
				if(!empty($Lostpassword)) {

					$this->Lostpassword->delete($Lostpassword[0]['Lostpassword']['id']);

					$data_to_save['password'] = $data['password'];

					$this->read(null, $search['0']['User']['id']);
					$this->set($data_to_save);
					$this->save();

					return array('status' => true, 'session' => $search[0]['User']['id']);

				} else {
					return 'INVALID_KEY_FOR_RESET';
				}
			} else {
				return 'INTERNAL_ERROR';
			}
		} else {
			return 'PASSWORD_NOT_SAME';
		}
	}

	private function getDataBySession($session) {
    	if(empty($this->userData)) {
      		$this->userData = $this->find('first', array('conditions' => array('id' => $session)));
    	}
    	return $this->userData;
  	}

	public function isConnected() {
		if(CakeSession::check('user') == false) {
      		return false;
  	  	} else {
        	// Je cherche si il la session est pas vide et si elle est dans la bdd
        	$user = $this->find('all', array(
            	'conditions' => array(
                	'id' => CakeSession::read('user'),
            	)
        	));
       	 	return (isset($user['0']['User']['id']));
    	}
	}

	public function isAdmin() {
		if(CakeSession::check('user') == false) {
          return false;
      	} else {
        	// Je cherche si il la session est pas vide et si elle est dans la bdd
        	$user = $this->getDataBySession(CakeSession::read('user'));
        	return (isset($user['User']['id']) AND $user['User']['rank'] == 3 OR $user['User']['rank'] == 4);
      }
	}

	public function exist($username) {
    	$search_user = $this->find('all', array(
        	'conditions' => array(
            	'pseudo' => $username,
        	)
    	));
    	return (!empty($search_user));
  	}

  	public function getKey($key) {
    	if(CakeSession::check('user')) {
      		$search_user = $this->getDataBySession(CakeSession::read('user'));
      		return ($search_user) ? $search_user['User'][$key] : '';
    	}
  	}

  	public function setKey($key, $value) {
    	if(CakeSession::check('user')) {
      		$search_user = $this->getDataBySession(CakeSession::read('user'));
      		if($search_user) {
        		$this->read(null, $search_user['User']['id']);
        		$this->set(array($key => $value));

						// on reset les données
						$userData = null;

        		return $this->save();
      		}
    	}
  	}

  	public function getUsernameByID($id) {
    	$search_user = $this->getDataBySession(CakeSession::read('user'));
      	return ($search_user) ? $search_user['0']['User']['pseudo'] : '';
    }

  	public function getFromUser($key, $username) {
    	$search_user = $this->User->find('all', array(
      		'conditions' => array(
          		'pseudo' => $username,
        	)
    	));
    	return ($search_user) ? $search_user['User'][$key] : NULL;
  	}

  	public function getAllFromCurrentUser() {
    	if(CakeSession::check('user')) {
      		$search_user = $this->getDataBySession(CakeSession::read('user'));
      		return ($search_user) ? $search_user['User'] : NULL;
    	}
  	}

  	public function setToUser($key, $value, $username) {
    	$search_user = $this->find('all', array(
      		'conditions' => array(
          		'pseudo' => $username,
        	)
    	));
    	if($search_user) {
      		$this->read(null, $search_user['0']['User']['id']);
      		$this->set(array($key => $value));
      		return $this->save();
    	}
  	}

	public function afterSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('afterAddUser', $this));
		} else {
			// modification d'un enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('afterEditUser', $this));
		}
	}

	public function afterDelete($cascade = true) {
		$this->getEventManager()->dispatch(new CakeEvent('afterDeleteUser', $this));
	}

}
