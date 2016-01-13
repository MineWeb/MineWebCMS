<?php
App::uses('CakeEvent', 'Event');

class Comment extends AppModel {

	public $belongsTo = 'News';

	public function afterSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('afterAddComment', $this));
		}
	}

	public function afterDelete() {
		$this->getEventManager()->dispatch(new CakeEvent('afterDeleteComment', $this));
	}
}
