<?php
App::uses('CakeEvent', 'Event');

class Comment extends AppModel {

	public $belongsTo = array(
		'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id'
		),
    'News' => array(
        'className' => 'News',
        'foreignKey' => 'news_id'
    )
  );

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
