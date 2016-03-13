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
}
