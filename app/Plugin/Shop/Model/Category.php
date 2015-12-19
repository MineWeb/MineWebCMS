<?php
App::uses('CakeEvent', 'Event');

class Category extends ShopAppModel {

	public function afterSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('afterAddCategory', $this));
		} else {
			// modification d'un enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('afterEditCategory', $this));
		}
	}

	public function afterDelete($cascade = true) {
		$this->getEventManager()->dispatch(new CakeEvent('afterDeleteCategory', $this));
	}

}
