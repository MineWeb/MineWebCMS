<?php
App::uses('CakeEvent', 'Event');

class User extends AppModel {

	private $userData;

	public function validRegister($data) {
		if(preg_match('`^([a-zA-Z0-9-_]{2,36})$`', $data['pseudo'])) {
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
		$data['pseudo'] = before_display($data['pseudo']);
		$data['email'] = before_display($data['email']);

		$data['ip'] = $_SERVER["REMOTE_ADDR"];
		$data['rank'] = 0;

		$data['password'] = password($data['password']);

		$this->create();
		$this->set($data);
		return $this->save();
	}

	public function login($data, $session) {
		$search_user = $this->find('first', array('conditions' => array('pseudo' => $data['pseudo'], 'password' => password($data['password']))));
		if(!empty($search_user)) {

			$this->getEventManager()->dispatch(new CakeEvent('onLogin', $data));

			$this->read(null, $search_user['User']['id']);
			$this->set(array('session' => $session));
			$this->save();
			
			return true;
		} else {
			return 'BAD_PSEUDO_OR_PASSWORD';
		}
	}

	public function resetPass($data, $session) {
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

					$data['session'] = $session; // on connecte l'utilisateur

					$this->read(null, $search['0']['User']['id']);
					$this->set($data);
					$this->save();

					return true;

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
      		$this->userData = $this->find('first', array('conditions' => array('session' => $session)));
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
                	'session' => CakeSession::read('user'),
            	)
        	));
       	 	return (isset($user['0']['User']['session']));
    	}
	}

	public function isAdmin() {
		if(CakeSession::check('user') == false) {
          return false;
      	} else {
        	// Je cherche si il la session est pas vide et si elle est dans la bdd
        	$user = $this->getDataBySession(CakeSession::read('user'));
        	return (isset($user['User']['session']) AND $user['User']['rank'] == 3 OR $user['User']['rank'] == 4);
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
    	return ($search_user) ? $search_user['User'][$key] : '';
  	}

  	public function getAllFromCurrentUser() {
    	if(CakeSession::check('user')) {
      		$search_user = $this->getDataBySession(CakeSession::read('user'));
      		return ($search_user) ? $search_user['User'] : '';
    	}
  	}

  	public function setFromUser($key, $value, $username) {
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
			$search = $this->find('first', array('conditions' => array('pseudo' => $this->data['User']['pseudo'])));
			if($this->data['User']['session'] != $search['User']['session']) { // si on modifie la session -> Connexion
				$this->getEventManager()->dispatch(new CakeEvent('afterLogin', $this));
			} else { // sinon -> modification de l'utilisateur
				$this->getEventManager()->dispatch(new CakeEvent('afterEditUser', $this));
			}
		}
	}

	public function afterDelete($cascade = true) {
		$this->getEventManager()->dispatch(new CakeEvent('afterDeleteUser', $this));
	}

}