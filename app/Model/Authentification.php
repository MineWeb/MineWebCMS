<?php

class Authentification extends AppModel
{
    public $useTable = 'users__twofactorauth';
    public $cacheQueries = false;

    public function requiresCode($userId)
    {
        return (bool)$this->field('id', ['user_id' => $userId, 'enabled' => true]);
    }

    public function verifyCode($userId, $code)
    {
        if (!is_string($code) || !preg_match('/^[0-9]{6}\z/', $code)) {
            return false;
        }
        $factor = $this->find('first', ['conditions' => ['user_id' => $userId, 'enabled' => true]]);
        if (empty($factor)) {
            return false;
        }

        $factor = $factor[$this->alias];
        $conditions = ['id' => $factor['id'], 'enabled' => true, 'secret' => $factor['secret']];
        $previous = ['attempts' => $factor['attempts'], 'attempts_since' => $factor['attempts_since']];
        $next = $previous;
        if ($next['attempts_since'] <= time() - 600) {
            $next = ['attempts' => 0, 'attempts_since' => time()];
        }
        if ($next['attempts'] >= 5) {
            return false;
        }
        ++$next['attempts'];
        if (!$this->updateAll($next, $conditions + $previous) || $this->getAffectedRows() !== 1) {
            return false;
        }

        require_once ROOT . DS . 'vendors' . DS . 'auth' . DS . 'GoogleAuthentificator.php';
        $authenticator = new PHPGangsta_GoogleAuthenticator();
        if (!$authenticator->verifyCode($factor['secret'], $code, 2)) {
            return false;
        }
        return $this->updateAll(['attempts' => 0, 'attempts_since' => 0], $conditions) && $this->getAffectedRows() === 1;
    }
}