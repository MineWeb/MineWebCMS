<?php
App::uses('CakeEvent', 'Event');
App::uses('SecureRandom', 'Lib');

class User extends AppModel
{

    public $hasMany = [
        'Comment' => [
            'className' => 'Comment',
            'foreignKey' => 'user_id',
            'order' => 'Comment.created DESC',
            'dependent' => true
        ],
        'Like' => [
            'className' => 'Like',
            'foreignKey' => 'user_id',
            'dependent' => true
        ]
    ];
    private $userData;
    private $isConnected = null;
    private $isAdmin = null;
    private $isBanned = null;

    public function validRegister($data, $UtilComponent)
    {
        if (!isset($data['pseudo']) || !is_string($data['pseudo']) || !preg_match('/^[a-zA-Z0-9_]{2,16}\z/', $data['pseudo'])) {
            return 'USER__ERROR_PSEUDO_INVALID_FORMAT';
        }
        if (!isset($data['password'], $data['password_confirmation']) || !is_string($data['password']) || $data['password'] !== $data['password_confirmation']) {
            return 'USER__ERROR_PASSWORDS_NOT_SAME';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'USER__ERROR_EMAIL_NOT_VALID';
        }
        if ($this->hasAny(['pseudo' => $data['pseudo']])) {
            return 'USER__ERROR_PSEUDO_ALREADY_REGISTERED';
        }
        if (ClassRegistry::init('Configuration')->getKey('check_uuid')) {
            if (empty($data['uuid']) || !is_string($data['uuid'])) {
                return 'USER__ERROR_UUID';
            }
            if ($this->hasAny(['uuid' => $data['uuid']])) {
                return 'USER__ERROR_UUID_ALREADY_REGISTERED';
            }
        }
        if ($this->hasAny(['email' => $data['email']])) {
            return 'USER__ERROR_EMAIL_ALREADY_REGISTERED';
        }
        return true;
    }

    public function register($data, $UtilComponent, $confirmEmail = false, $microsoftUserId = null)
    {

        $data_to_save = [];

        $data_to_save['pseudo'] = htmlentities($data['pseudo']);
        $data_to_save['email'] = htmlentities($data['email']);

        $data_to_save['ip'] = $UtilComponent->getIP();
        $data_to_save['rank'] = 0;

        $data_to_save['uuid'] = htmlentities($data['uuid']);

        $data_to_save['password'] = $UtilComponent->password($data['password'], $data['pseudo']);
        $data_to_save['password_hash'] = $UtilComponent->getPasswordHashType();

        if ($microsoftUserId !== null) {
            $data_to_save['microsoft_user_id'] = $microsoftUserId;
            $data_to_save['registered_by_microsoft'] = true;
        }
        if ($confirmEmail) {
            $data_to_save['confirmed'] = SecureRandom::hex(12);
        }

        $this->create();
        if (!$this->save($data_to_save)) {
            throw new InternalErrorException();
        }
        $userId = $this->getLastInsertId();
        if ($confirmEmail) {
            $UtilComponent->sendConfirmationEmail($data['email'], $data['pseudo'], $data_to_save['confirmed']);
        }
        return $userId;
    }

    public function confirmEmail($userId, $code)
    {
        $date = $this->getDataSource()->value(date('Y-m-d H:i:s'), 'string');
        return $this->updateAll(['confirmed' => $date], ['id' => $userId, 'confirmed' => $code])
            && $this->getAffectedRows() === 1;
    }

    public function login($user, $data, $confirmEmailIsNeeded = false, $checkUUID = false, $controller = null, $storePendingEmail = true)
    {
        $UtilComponent = $controller->Util;
        $LoginRetryTable = ClassRegistry::init('LoginRetry');
        $ip = $UtilComponent->getIP();
        App::uses('CakeTime', 'Utility');
        $findRetryWithIP = $LoginRetryTable->find('first', ['conditions' => [
            'ip' => $ip,
            'modified >= ' => CakeTime::format('-10 minutes', '%Y-%m-%d %H:%M:%S')
        ], 'order' => 'created DESC']);
        $date = date('Y-m-d H:i:s');
        if (empty($findRetryWithIP)) {
            $LoginRetryTable->create();
            $LoginRetryTable->set([
                'ip' => $ip,
                'count' => 1
            ]);
            $LoginRetryTable->save();
        } else {
            $LoginRetryTable->updateAll(
                ['count' => 'count + 1', 'modified' => "'$date'"],
                ['ip' => $ip]
            );
        }
        if (!empty($findRetryWithIP) && $findRetryWithIP['LoginRetry']['count'] >= 5)
            return 'LOGIN__BLOCKED';

        if (empty($user)) {
            return 'USER__ERROR_INVALID_CREDENTIALS';
        }

        $username = $user['pseudo'];
        $conditions = [];
        if (empty($user['microsoft_connection'])) {
            if (!isset($data['password']) || !is_string($data['password']) || $data['password'] === '' || empty($user['password'])) {
                return 'USER__ERROR_INVALID_CREDENTIALS';
            }
            $storedHash = preg_match('/^\$2[axy]\$/', $user['password']) ? 'blowfish' : $user['password_hash'];
            $candidate = $UtilComponent->password($data['password'], $username, $user['password'], $storedHash);
            if (!is_string($candidate) || !hash_equals($user['password'], $candidate)) {
                return 'USER__ERROR_INVALID_CREDENTIALS';
            }

            $hash = $UtilComponent->getPasswordHashType();
            // bcrypt truncates after 72 bytes. Preserve a longer legacy password in full.
            if ($hash && $storedHash && $storedHash !== $hash && ($hash !== 'blowfish' || strlen($data['password']) <= 72)) {
                $db = $this->getDataSource();
                $password = $UtilComponent->password($data['password'], $username);
                $changed = $this->updateAll([
                    'password' => $db->value($password, 'string'),
                    'password_hash' => $db->value($hash, 'string')
                ], ['id' => $user['id'], 'password' => $user['password']]);
                if (!$changed || $this->getAffectedRows() !== 1) {
                    return 'USER__ERROR_INVALID_CREDENTIALS';
                }
            }
        }
        $LoginRetryTable->deleteAll(['ip' => $ip]);

        if ($confirmEmailIsNeeded && !empty($user['confirmed']) && date('Y-m-d H:i:s', strtotime($user['confirmed'])) != $user['confirmed']) {
            if ($storePendingEmail) {
                $controller->Session->write('email.confirm.user.id', $user['id']);
            }
            return 'USER__MSG_NOT_CONFIRMED_EMAIL';
        }
        if ($checkUUID) {
            if (!isset($user['uuid'])) {
                $pseudoToUUID = file_get_contents("https://api.mojang.com/users/profiles/minecraft/" . $user['pseudo']);
                $conditions['uuid'] = json_decode($pseudoToUUID, true)['id'];

            } else {
                $uuidToPseudo = file_get_contents("https://api.mojang.com/user/profiles/" . $user['uuid'] . "/names");
                if (!empty($uuidToPseudo))
                    $conditions['pseudo'] = end(json_decode($uuidToPseudo, true))['name'];
            }
        }
        $conditions['ip'] = $ip;

        $this->id = $user['id'];
        if (!$this->save($conditions, true, array_keys($conditions))) {
            return 'ERROR__INTERNAL_ERROR';
        }

        return ['status' => true, 'session' => $user['id']];
    }

    public function getFromUser($key, $search)
    {
        $search_user = $this->find('first', ['conditions' => $this->__makeCondition($search)]);
        return (!empty($search_user)) ? $search_user['User'][$key] : null;
    }

    public function __makeCondition($search)
    {
        if ((string)(int)$search == $search) {
            return [
                'id' => intval($search)
            ];
        } else {
            return [
                'pseudo' => $search
            ];
        }
    }

    public function resetPass($data, $controller)
    {
        foreach (['email', 'key', 'password', 'password2'] as $field) {
            if (!isset($data[$field]) || !is_string($data[$field]) || $data[$field] === '') {
                return 'ERROR__FILL_ALL_FIELDS';
            }
        }

        $UtilComponent = $controller->Util;
        if ($data['password'] === $data['password2']) {
            unset($data['password2']);
            $matchingUsers = $this->find('all', ['conditions' => ['email' => $data['email']], 'limit' => 2]);
            if (!empty($matchingUsers)) {
                if (count($matchingUsers) > 1) {
                    return 'USER__PASSWORD_RESET_INVALID_KEY';
                }
                $user = $matchingUsers[0]['User'];

                $this->Lostpassword = ClassRegistry::init('Lostpassword');
                $Lostpassword = $this->Lostpassword->find('all', ['conditions' => ['email' => $data['email'], 'key' => $data['key']]]);
                if (!empty($Lostpassword) && strtotime('+1 hour', strtotime($Lostpassword[0]['Lostpassword']['created'])) >= time()) {

                    $data_to_save['password'] = $UtilComponent->password($data['password'], $user['pseudo']);
                    $data_to_save['password_hash'] = $UtilComponent->getPasswordHashType();

                    $event = new CakeEvent('beforeResetPassword', $this, ['user_id' => $user['id'], 'new_password' => $data_to_save['password']]);
                    $controller->getEventManager()->dispatch($event);
                    if ($event->isStopped()) {
                        return $event->result;
                    }

                    if (!$this->Lostpassword->delete($Lostpassword[0]['Lostpassword']['id'])) {
                        return 'USER__PASSWORD_RESET_INVALID_KEY';
                    }
                    if (!$this->read(null, $user['id']) || !$this->save($data_to_save)) {
                        return 'ERROR__INTERNAL_ERROR';
                    }

                    return ['status' => true, 'session' => $user['id']];

                } else {
                    return 'USER__PASSWORD_RESET_INVALID_KEY';
                }
            } else {
                return 'ERROR__INTERNAL_ERROR';
            }
        } else {
            return 'USER__ERROR_PASSWORDS_NOT_SAME';
        }
    }

    public function isConnected()
    {
        $user = $this->getDataBySession();
        return !empty($user);
    }

    public function isBanned()
    {
        $check = ClassRegistry::init("Ban")->find('first', ["conditions" => ['user_id' => $this->getKey("id")]]);
        $this->isBanned = $check ? $check["Ban"]["reason"] : false;

        return $this->isBanned;

    }

    private function getDataBySession()
    {
        if (empty($this->userData))
            $this->userData = $this->find('first', ['conditions' => ['id' => CakeSession::read('user')]]);
        return $this->userData;
    }

    public function isAdmin()
    {
        $user = $this->getDataBySession();
        if (empty($user)) return false;
        return ($user['User']['rank'] == 3 || $user['User']['rank'] == 4);
    }

    public function exist($search)
    { //username || id
        $search_user = $this->find('first', ['conditions' => $this->__makeCondition($search)]);
        return (!empty($search_user));
    }

    public function getKey($key)
    {
        if (CakeSession::check('user')) {
            $search_user = $this->getDataBySession(CakeSession::read('user'));
            return ($search_user && isset($search_user['User'][$key])) ? $search_user['User'][$key] : '';
        }
    }

    public function setKey($key, $value)
    {
        if (CakeSession::check('user')) {
            $search_user = $this->getDataBySession(CakeSession::read('user'));
            if ($search_user) {
                $this->id = $search_user['User']['id'];
                $save = $this->saveField($key, $value);

                // on reset les données
                $this->userData = null;

                return $save;
            }
        }
    }

    public function getUsernameByID($id)
    {
        $search_user = $this->find('first', ['conditions' => ['id' => $id]]);
        return (!empty($search_user)) ? $search_user['User']['pseudo'] : '';
    }

    public function getAllFromCurrentUser()
    {
        if (CakeSession::check('user')) {
            $search_user = $this->getDataBySession(CakeSession::read('user'));
            return ($search_user) ? $search_user['User'] : null;
        }
    }

    public function getAllFromUser($search = null)
    {
        $search_user = $this->find('first', ['conditions' => $this->__makeCondition($search)]);
        if (!empty($search_user)) {
            return ($search_user) ? $search_user['User'] : null;
        }
        return [];
    }

    public function setToUser($key, $value, $search)
    {
        $search_user = $this->find('first', ['conditions' => $this->__makeCondition($search)]);
        if (!empty($search_user)) {
            $this->id = $search_user['User']['id'];
            return $this->saveField($key, $value);
        }
    }

}
