<?php
App::uses('SecureRandom', 'Lib');

class RememberToken extends AppModel
{

    public $primaryKey = 'token_hash';
    public $cacheQueries = false;
    public $belongsTo = ['User' => ['type' => 'INNER']];

    public function issue($userId)
    {
        if (!filter_var($userId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
            return null;
        }

        $this->getDataSource()->flushQueryCache();
        $password = $this->User->field('password', ['User.id' => $userId]);
        if (!is_string($password)) {
            return null;
        }

        $token = SecureRandom::hex(32);
        $now = time();
        $this->deleteAll(['expires <=' => $now], false);
        $this->create();
        $saved = $this->save([
            'token_hash' => hash('sha256', $token),
            'user_id' => $userId,
            'password_fingerprint' => hash_hmac('sha256', $password, $token),
            'expires' => $now + WEEK
        ]);
        return $saved ? $token : null;
    }

    public function userIdFor($token)
    {
        if (!$this->isValidToken($token)) {
            return null;
        }

        $record = $this->find('first', [
            'conditions' => ['RememberToken.token_hash' => hash('sha256', $token), 'RememberToken.expires >' => time()],
            'fields' => ['RememberToken.password_fingerprint', 'User.id', 'User.password'],
            'recursive' => 0
        ]);
        if (empty($record)) {
            return null;
        }

        $fingerprint = hash_hmac('sha256', $record['User']['password'], $token);
        return hash_equals($record['RememberToken']['password_fingerprint'], $fingerprint) ? $record['User']['id'] : null;
    }

    public function revoke($token)
    {
        if (!$this->isValidToken($token)) {
            return false;
        }
        return $this->deleteAll(['token_hash' => hash('sha256', $token)], false);
    }

    private function isValidToken($token)
    {
        return is_string($token) && strlen($token) === 64 && ctype_xdigit($token);
    }

}
