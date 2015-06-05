<?php
App::uses('CakeEvent', 'Event');

class User extends AppModel {

	public function afterSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('addUser', $this));
		} else {
			// modification d'un enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('editUser', $this));
		}
	}

	public function afterDelete() {
		$this->getEventManager()->dispatch(new CakeEvent('deleteUser', $this));
	}

}