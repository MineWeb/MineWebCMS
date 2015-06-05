<?php
App::uses('CakeEvent', 'Event');

class News extends AppModel {

	public function afterSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('addNews', $this));
		} else {
			// modification d'un enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('editNews', $this));
		}
	}

	public function afterDelete() {
		$this->getEventManager()->dispatch(new CakeEvent('deleteNews', $this));
	}

}