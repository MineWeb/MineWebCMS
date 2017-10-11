<?php
App::uses('CakeEvent', 'Event');

class User extends AppModel
{

    private $userData;

    public $hasMany = array(
        'Comment' => array(
            'className' => 'Comment',
            'foreignKey' => 'user_id',
            'order' => 'Comment.created DESC',
            'dependent' => true
        ),
        'Like' => array(
            'className' => 'Like',
            'foreignKey' => 'user_id',
            'dependent' => true
        )
    );

    private $isConnected = null;
    private $isAdmin = null;

    public function validRegister($data, $UtilComponent)
    {
        if (preg_match('`^([a-zA-Z0-9_]{2,16})$`', $data['pseudo'])) {
            $data['password'] = $UtilComponent->password($data['password'], $data['pseudo']);
            $data['password_confirmation'] = $UtilComponent->password($data['password_confirmation'], $data['pseudo']);
            if ($data['password'] == $data['password_confirmation']) {
                if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $search_member_by_pseudo = $this->find('all', array('conditions' => array('pseudo' => $data['pseudo'])));
                    $search_member_by_email = $this->find('all', array('conditions' => array('email' => $data['email'])));
                    if (empty($search_member_by_pseudo)) {
                        if (empty($search_member_by_email)) {
                            return true;
                        } else {
                            return 'USER__ERROR_EMAIL_ALREADY_REGISTERED';
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

        $data_to_save = array();

        $data_to_save['pseudo'] = htmlentities($data['pseudo']);
        $data_to_save['email'] = htmlentities($data['email']);

        $data_to_save['ip'] = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? htmlentities($_SERVER["HTTP_CF_CONNECTING_IP"]) : $_SERVER["REMOTE_ADDR"];
        $data_to_save['rank'] = 0;

        $data_to_save['password'] = $UtilComponent->password($data['password'], $data['pseudo']);

        $this->create();
        $this->set($data_to_save);
        $this->save();
        return $this->getLastInsertId();
    }

    public function login($data, $confirmEmailIsNeeded = false, $controller)
    {
        $UtilComponent = $controller->Util;
        $LoginRetryTable = ClassRegistry::init('LoginRetry');
        $ip = $UtilComponent->getIP();
        $findRetryWithIP = $LoginRetryTable->find('first', ['conditions' => [
            'ip' => $ip,
            'modified >=' => strtotime('-2 hours'),
            'count >=' => 10
        ]]);
        if (!empty($findRetryWithIP))
            return 'LOGIN__BLOCKED';

        $user = $this->find('first', ['conditions' => [
            'pseudo' => $data['pseudo'],
            'password' => $UtilComponent->password($data['password'], $data['pseudo'])
        ]]);
        if (empty($user)) {
            $LoginRetryTable->updateAll(
                ['count' => 'count + 1'],
                ['ip' => $ip]
            );
            return 'USER__ERROR_INVALID_CREDENTIALS';
        }
        $user = $user['User'];
        $LoginRetryTable->deleteAll(['ip' => $ip]);

        if ($confirmEmailIsNeeded && !empty($user['confirmed']) && date('Y-m-d H:i:s', strtotime($user['confirmed'])) != $user['confirmed']) {
            $controller->Session->write('email.confirm.user.id', $user['id']);
            return 'USER__MSG_NOT_CONFIRMED_EMAIL';
        }

        return ['status' => true, 'session' => $user['id']];
    }

    public function resetPass($data, $controller)
    {
        $UtilComponent = $controller->Util;
        if ($data['password'] == $data['password2']) {
            unset($data['password2']);
            $search = $this->find('all', array('conditions' => array('email' => $data['email'])));
            if (!empty($search)) {

                $this->Lostpassword = ClassRegistry::init('Lostpassword');
                $Lostpassword = $this->Lostpassword->find('all', array('conditions' => array('email' => $data['email'], 'key' => $data['key'])));
                if (!empty($Lostpassword) && strtotime('+1 hour', strtotime($Lostpassword[0]['Lostpassword']['created'])) >= time()) {

                    $data_to_save['password'] = $UtilComponent->password($data['password'], $search['0']['User']['pseudo']);

                    $event = new CakeEvent('beforeResetPassword', $this, array('user_id' => $search[0]['User']['id'], 'new_password' => $data_to_save['password']));
                    $controller->getEventManager()->dispatch($event);
                    if ($event->isStopped()) {
                        return $event->result;
                    }

                    $this->Lostpassword->delete($Lostpassword[0]['Lostpassword']['id']);

                    $this->read(null, $search['0']['User']['id']);
                    $this->set($data_to_save);
                    $this->save();

                    return array('status' => true, 'session' => $search[0]['User']['id']);

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

    private function getDataBySession()
    {
        if (empty($this->userData))
            $this->userData = $this->find('first', array('conditions' => array('id' => CakeSession::read('user'))));
        return $this->userData;
    }

    public function isConnected()
    {
        $user = $this->getDataBySession();
        return !empty($user);
    }

    public function isAdmin()
    {
        $user = $this->getDataBySession();
        if (empty($user)) return false;
        return ($user['User']['rank'] == 3 || $user['User']['rank'] == 4);
    }

    public function __makeCondition($search)
    {
        if ((string)(int)$search == $search) {
            return array(
                'id' => intval($search)
            );
        } else {
            return array(
                'pseudo' => $search
            );
        }
    }

    public function exist($search)
    { //username || id
        $search_user = $this->find('first', array('conditions' => $this->__makeCondition($search)));
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
        $search_user = $this->find('first', array('conditions' => array('id' => $id)));
        return (!empty($search_user)) ? $search_user['User']['pseudo'] : '';
    }

    public function getFromUser($key, $search)
    {
        $search_user = $this->find('first', array('conditions' => $this->__makeCondition($search)));
        return (!empty($search_user)) ? $search_user['User'][$key] : NULL;
    }

    public function getAllFromCurrentUser()
    {
        if (CakeSession::check('user')) {
            $search_user = $this->getDataBySession(CakeSession::read('user'));
            return ($search_user) ? $search_user['User'] : NULL;
        }
    }

    public function getAllFromUser($search = null)
    {
        $search_user = $this->find('first', array('conditions' => $this->__makeCondition($search)));
        if (!empty($search_user)) {
            return ($search_user) ? $search_user['User'] : NULL;
        }
        return array();
    }

    public function setToUser($key, $value, $search)
    {
        $search_user = $this->find('first', array('conditions' => $this->__makeCondition($search)));
        if (!empty($search_user)) {
            $this->id = $search_user['User']['id'];
            return $this->saveField($key, $value);
        }
    }

}
