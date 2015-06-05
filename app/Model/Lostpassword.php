<?php
App::uses('CakeEvent', 'Event');

class Lostpassword extends AppModel {

	public function afterSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('lostPassword', $this));
		}
	}

	public function afterDelete($cascade = true) {
		$this->getEventManager()->dispatch(new CakeEvent('resetPassword', $this));
	}

}