<?php
App::uses('SecureRandom', 'Lib');

class Lostpassword extends AppModel
{

    public $belongsTo = 'User';

    public function generateKey()
    {
        return SecureRandom::hex(16);
    }

}
