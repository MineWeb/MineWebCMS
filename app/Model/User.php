<?php
App::uses('CakeEvent', 'Event');

class User extends AppModel {

	public function validRegister($data) {
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