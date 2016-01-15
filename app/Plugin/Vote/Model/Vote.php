<?php
App::uses('CakeEvent', 'Event');

class Vote extends VoteAppModel {

	public function afterSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('afterVote', $this));
		}
	}

}
