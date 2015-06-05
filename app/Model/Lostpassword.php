<?php
App::uses('CakeEvent', 'Event');

class Lostpassword extends AppModel {

	public function beforeSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('lostPassword', $this));
		}
	}

	public function beforeDelete() {
		$this->getEventManager()->dispatch(new CakeEvent('resetPassword', $this));
	}

}