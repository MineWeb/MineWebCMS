<?php
App::uses('CakeEvent', 'Event');

class Like extends AppModel {

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
		Cache::delete('news', 'data');
	}

	public function afterDelete($cascade = true) {
		Cache::delete('news', 'data');
	}
	
}
