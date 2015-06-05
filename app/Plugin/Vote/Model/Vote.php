<?php
App::uses('CakeEvent', 'Event');

class VoteModel extends VoteAppModel {

	public function beforeSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('onVote', $this));
		}
	}

}