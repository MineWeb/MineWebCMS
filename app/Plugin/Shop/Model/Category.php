<?php
App::uses('CakeEvent', 'Event');

class Category extends AppModel {

	public function afterSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('addCategory', $this));
		} else {
			// modification d'un enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('editCategory', $this));
		}
	}

	public function afterDelete() {
		$this->getEventManager()->dispatch(new CakeEvent('deleteCategory', $this));
	}

}