<?php
App::uses('CakeEvent', 'Event');

class Item extends AppModel {

	public function afterSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('addItem', $this));
		} else {
			// modification d'un enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('editItem', $this));
		}
	}

	public function afterDelete() {
		$this->getEventManager()->dispatch(new CakeEvent('deleteItem', $this));
	}

}