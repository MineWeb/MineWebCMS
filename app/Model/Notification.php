<?php

class Notification extends AppModel
{

    public $belongsTo = 'User';

    public function getFromUser($user_id, $type)
    {
        $query = $this->find('all', array(
            'conditions' => array(
                'user_id' => $user_id,
                'type' => $type
            ),
            'order' => 'id DESC'
        ));

        $data = array();

        $UserModel = ClassRegistry::init('User');
        App::uses('CakeTime', 'Utility');
        CakeTime::$wordFormat = 'd/m/y';

        foreach ($query as $notification) {

            if ($notification['Notification']['from'] == null) {
                $from = null;
            } else {
                $from = $UserModel->getFromUser('pseudo', $notification['Notification']['from']);
            }

            $data[] = array(
                'id' => intval($notification['Notification']['id']),
                'from' => $from,
                'content' => $notification['Notification']['content'],
                'time' => CakeTime::timeAgoInWords($notification['Notification']['created']),
                'seen' => ($notification['Notification']['seen'] == 1)
            );

        }

        return $data;
    }

    private function generateGroup()
    {
        return substr(md5(microtime()),rand(0,26),10);
    }

    /*
      ID: A_I
      USER_ID: INT or NULL (for all)
      FROM: INT or NULL (for all)
      CONTENT: TEXT LIMIT 255
    */
    public function setToUser($content, $user_id, $from = NULL, $type = 'user', $group = NULL)
    {
        if (empty($content) || strlen($content) > 255 || empty($user_id))
            return false;
        if ($group === NULL)
            $group = $this->generateGroup();

        $this->create();
        $this->set(array(
            'group' => $group,
            'content' => $content,
            'user_id' => $user_id,
            'from' => $from,
            'type' => $type
        ));
        return $this->save();
    }

    public function setToRank($content, $rank_id, $from = NULL, $type = 'user', $group = NULL)
    {
        if (!$group)
            $group = $this->generateGroup();
        $UserModel = ClassRegistry::init('User');
        $usersToNotify = $UserModel->find('all', array('conditions' => array('rank' => $rank_id)));

        foreach ($usersToNotify as $user) {
            $this->setToUser($content, $user['User']['id'], $from, $type, $group);
        }

    }

    public function setToAdmin($content, $from = NULL)
    {
        $group = $this->generateGroup();
        $this->setToRank($content, 4, $from, 'admin', $group);
        $this->setToRank($content, 3, $from, 'admin', $group);
    }

    public function setToAll($content, $from = NULL)
    {
        if (empty($from)) {
            $from = 'NULL';
        }
        $group = $this->generateGroup();
        $content = addslashes($content);
        $this->query("INSERT INTO notifications (`group`, `user_id`, `from`, `content`, `type`, `created`) SELECT '$group', id, $from, '$content', 'user', '" . date('Y-m-d H:i:s') . "' FROM users");
    }

    public function clearFromUser($id, $user_id)
    {
        return $this->deleteAll(array('user_id' => $user_id, 'Notification.id' => $id));
    }

    public function clearAllFromUser($user_id)
    {
        return $this->deleteAll(array('user_id' => $user_id));
    }

    public function markAsSeenFromUser($id, $user_id)
    {
        return $this->updateAll(array('seen' => 1), array('user_id' => $user_id, 'Notification.id' => $id));
    }

    public function markAllAsSeenFromUser($user_id)
    {
        return $this->updateAll(array('seen' => 1), array('user_id' => $user_id));
    }

    public function clearFromAllUsers($id)
    {
        return $this->deleteAll(array('id' => $id));
    }

    public function markAsSeenFromAllUsers($id)
    {
        return $this->updateAll(array('seen' => 1), array('Notification.id' => $id));
    }

    public function clearAllFromGroup($group)
    {
        return $this->deleteAll(array('group' => $group));
    }

    public function clearAllFromAllUsers()
    {
        return $this->deleteAll(array('1' => '1'));
    }

    public function markAllAsSeenFromAllUsers()
    {
        return $this->updateAll(array('seen' => 1), array('1' => '1'));
    }
}
