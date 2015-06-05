<?php
App::uses('CakeEvent', 'Event');

class Like extends AppModel {

	public function beforeSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('addLike', $this));
		}
	}

	public function beforeDelete() {
		$this->getEventManager()->dispatch(new CakeEvent('deleteLike', $this));
	}
}