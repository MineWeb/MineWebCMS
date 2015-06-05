<?php
App::uses('CakeEvent', 'Event');

class ReplyTicket extends SupportAppModel {

	public function afterSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('replyToTicket', $this));
		}
	}

	public function afterDelete() {
		$this->getEventManager()->dispatch(new CakeEvent('deleteReply', $this));
	}

}