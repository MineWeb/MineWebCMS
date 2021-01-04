<?php
App::uses('CakeEvent', 'Event');

class Comment extends AppModel
{

    public $belongsTo = [
        'User' => [
            'className' => 'User',
            'foreignKey' => 'user_id'
        ],
        'News' => [
            'className' => 'News',
            'foreignKey' => 'news_id'
        ]
    ];

    public function afterSave($created, $options = [])
    {
        Cache::delete('news', 'data');
    }

    public function afterDelete($cascade = true)
    {
        Cache::delete('news', 'data');
    }

}
