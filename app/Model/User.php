<?php
App::uses('CakeEvent', 'Event');

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

    public function validRegister($data, $UtilComponent)
    {
        if (preg_match('`^([a-zA-Z0-9_]{2,16})$`', $data['pseudo'])) {
            if ($data['password'] == $data['password_confirmation']) {
                $data['password'] = $data['password_confirmation'] = $UtilComponent->password($data['password'], $data['pseudo']);

                if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $search_member_by_pseudo = $this->find('all', ['conditions' => ['pseudo' => $data['pseudo']]]);
                    $search_member_by_uuid = $this->find('all', ['conditions' => ['uuid' => $data['uuid']]]);
                    $search_member_by_email = $this->find('all', ['conditions' => ['email' => $data['email']]]);
                    if (empty($search_member_by_pseudo)) {
                        if (!ClassRegistry::init('Configuration')->getKey('check_uuid') || empty($search_member_by_uuid)) {
                            if (empty($search_member_by_email)) {
                                return true;
                            } else {
                                return 'USER__ERROR_EMAIL_ALREADY_REGISTERED';
                            }
                        } else {
                            return 'USER__ERROR_UUID_ALREADY_REGISTERED';
                        }
                    } else {
                        return 'USER__ERROR_PSEUDO_ALREADY_REGISTERED';
                    }
                } else {
                    return 'USER__ERROR_EMAIL_NOT_VALID';
                }
            } else {
                return 'USER__ERROR_PASSWORDS_NOT_SAME';
            }
        } else {
            return 'USER__ERROR_PSEUDO_INVALID_FORMAT';
        }
    }

    public function register($data, $UtilComponent)
    {

        $data_to_save = [];

        $data_to_save['pseudo'] = htmlentities($data['pseudo']);
        $data_to_save['email'] = htmlentities($data['email']);

        $data_to_save['ip'] = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? htmlentities($_SERVER["HTTP_CF_CONNECTING_IP"]) : $_SERVER["REMOTE_ADDR"];
        $data_to_save['rank'] = 0;

        $data_to_save['uuid'] = htmlentities($data['uuid']);

        $data_to_save['password'] = $UtilComponent->password($data['password'], $data['pseudo']);
        $data_to_save['password_hash'] = $UtilComponent->getPasswordHashType();

        $this->create();
        $this->set($data_to_save);
        $this->save();
        return $this->getLastInsertId();
    }

    public function login($user, $data, $confirmEmailIsNeeded = false, $checkUUID = false, $controller)
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

        $username = $user['pseudo'];
        if ($user['password'] != $UtilComponent->password($data['password'], $username, $user['password'], $user['password_hash']))
            return 'USER__ERROR_INVALID_CREDENTIALS';
        $LoginRetryTable->deleteAll(['ip' => $ip]);
        $conditions = [];

        if ($this->getFromUser('password_hash', $username) != $UtilComponent->getPasswordHashType()) {
            $conditions['password'] = $UtilComponent->password($data['password'], $username);
            $conditions['password_hash'] = $UtilComponent->getPasswordHashType();
        }

        if ($confirmEmailIsNeeded && !empty($user['confirmed']) && date('Y-m-d H:i:s', strtotime($user['confirmed'])) != $user['confirmed']) {
            $controller->Session->write('email.confirm.user.id', $user['id']);
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

        $this->read(null, $user['id']);
        $this->set($conditions);
        $this->save();

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
        $UtilComponent = $controller->Util;
        if ($data['password'] == $data['password2']) {
            unset($data['password2']);
            $search = $this->find('all', ['conditions' => ['email' => $data['email']]]);
            if (!empty($search)) {

                $this->Lostpassword = ClassRegistry::init('Lostpassword');
                $Lostpassword = $this->Lostpassword->find('all', ['conditions' => ['email' => $data['email'], 'key' => $data['key']]]);
                if (!empty($Lostpassword) && strtotime('+1 hour', strtotime($Lostpassword[0]['Lostpassword']['created'])) >= time()) {

                    $data_to_save['password'] = $UtilComponent->password($data['password'], $search['0']['User']['pseudo']);
                    $data_to_save['password_hash'] = $UtilComponent->getPasswordHashType();

                    $event = new CakeEvent('beforeResetPassword', $this, ['user_id' => $search[0]['User']['id'], 'new_password' => $data_to_save['password']]);
                    $controller->getEventManager()->dispatch($event);
                    if ($event->isStopped()) {
                        return $event->result;
                    }

                    $this->Lostpassword->delete($Lostpassword[0]['Lostpassword']['id']);

                    $this->read(null, $search['0']['User']['id']);
                    $this->set($data_to_save);
                    $this->save();

                    return ['status' => true, 'session' => $search[0]['User']['id']];

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
