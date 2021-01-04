<?php

class Notification extends AppModel
{

    public $belongsTo = 'User';

    public function getFromUser($user_id, $type)
    {
        $query = $this->find('all', [
            'conditions' => [
                'user_id' => $user_id,
                'type' => $type
            ],
            'order' => 'id DESC'
        ]);

        $data = [];

        $UserModel = ClassRegistry::init('User');
        App::uses('CakeTime', 'Utility');
        CakeTime::$wordFormat = 'd/m/y';

        foreach ($query as $notification) {

            if ($notification['Notification']['from'] == null) {
                $from = null;
            } else {
                $from = $UserModel->getFromUser('pseudo', $notification['Notification']['from']);
            }

            $data[] = [
                'id' => intval($notification['Notification']['id']),
                'from' => $from,
                'content' => $notification['Notification']['content'],
                'time' => CakeTime::timeAgoInWords($notification['Notification']['created']),
                'seen' => ($notification['Notification']['seen'] == 1)
            ];

        }

        return $data;
    }

    public function setToAdmin($content, $from = null)
    {
        $group = $this->generateGroup();
        $this->setToRank($content, 4, $from, 'admin', $group);
        $this->setToRank($content, 3, $from, 'admin', $group);
    }

    /*
      ID: A_I
      USER_ID: INT or NULL (for all)
      FROM: INT or NULL (for all)
      CONTENT: TEXT LIMIT 255
    */

    private function generateGroup()
    {
        return substr(md5(microtime()), rand(0, 26), 10);
    }

    public function setToRank($content, $rank_id, $from = null, $type = 'user', $group = null)
    {
        if (!$group)
            $group = $this->generateGroup();
        $UserModel = ClassRegistry::init('User');
        $usersToNotify = $UserModel->find('all', ['conditions' => ['rank' => $rank_id]]);

        foreach ($usersToNotify as $user) {
            $this->setToUser($content, $user['User']['id'], $from, $type, $group);
        }

    }

    public function setToUser($content, $user_id, $from = null, $type = 'user', $group = null)
    {
        if (empty($content) || strlen($content) > 255 || empty($user_id))
            return false;
        if ($group === null)
            $group = $this->generateGroup();

        $this->create();
        $this->set([
            'group' => $group,
            'content' => $content,
            'user_id' => $user_id,
            'from' => $from,
            'type' => $type
        ]);
        return $this->save();
    }

    public function setToAll($content, $from = null)
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
        return $this->deleteAll(['user_id' => $user_id, 'Notification.id' => $id]);
    }

    public function clearAllFromUser($user_id)
    {
        return $this->deleteAll(['user_id' => $user_id]);
    }

    public function markAsSeenFromUser($id, $user_id)
    {
        return $this->updateAll(['seen' => 1], ['user_id' => $user_id, 'Notification.id' => $id]);
    }

    public function markAllAsSeenFromUser($user_id)
    {
        return $this->updateAll(['seen' => 1], ['user_id' => $user_id]);
    }

    public function clearFromAllUsers($id)
    {
        return $this->deleteAll(['id' => $id]);
    }

    public function markAsSeenFromAllUsers($id)
    {
        return $this->updateAll(['seen' => 1], ['Notification.id' => $id]);
    }

    public function clearAllFromGroup($group)
    {
        return $this->deleteAll(['group' => $group]);
    }

    public function clearAllFromAllUsers()
    {
        return $this->deleteAll(['1' => '1']);
    }

    public function markAllAsSeenFromAllUsers()
    {
        return $this->updateAll(['seen' => 1], ['1' => '1']);
    }
}
