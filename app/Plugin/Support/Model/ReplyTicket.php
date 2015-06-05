<?php
App::uses('CakeEvent', 'Event');

class ReplyTicket extends SupportAppModel {

	public function beforeSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('replyToTicket', $this));
		}
	}

	public function beforeDelete() {
		$this->getEventManager()->dispatch(new CakeEvent('deleteReply', $this));
	}

}