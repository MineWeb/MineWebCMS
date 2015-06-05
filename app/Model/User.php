<?php
App::uses('CakeEvent', 'Event');

class User extends AppModel {

	public function beforeSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('addUser', $this));
		} else {
			// modification d'un enregistrement
			$search = $this->find('first', array('conditions' => array('pseudo' => $this->data['pseudo'])));
			if($this->data['session'] != $search['User']['session']) { // si on modifie la session -> Connexion
				$this->getEventManager()->dispatch(new CakeEvent('onLogin', $this));
			} else { // sinon -> modification de l'utilisateur
				$this->getEventManager()->dispatch(new CakeEvent('editUser', $this));
			}
		}
	}

	public function beforeDelete() {
		$this->getEventManager()->dispatch(new CakeEvent('deleteUser', $this));
	}

}