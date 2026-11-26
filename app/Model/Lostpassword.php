<?php
App::uses('CakeEvent', 'Event');

class Lostpassword extends AppModel
{

    public $belongsTo = 'User';

    public function generateKey()
    {
        $byteLength = 16;

        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes($byteLength));
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($byteLength, $strong);
            if ($bytes !== false && $strong) {
                return bin2hex($bytes);
            }
        }

        throw new RuntimeException('Unable to generate a secure password reset key.');
    }

}
