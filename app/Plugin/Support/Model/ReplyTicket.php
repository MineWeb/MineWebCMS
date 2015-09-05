<?php
App::uses('CakeEvent', 'Event');

class ReplyTicket extends SupportAppModel {

	public function afterSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('afterReplyToTicket', $this));
		}
	}

	public function afterDelete($cascade = true) {
		$this->getEventManager()->dispatch(new CakeEvent('afterDeleteReply', $this));
	}

}