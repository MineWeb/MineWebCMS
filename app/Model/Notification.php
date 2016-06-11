<?php
class Notification extends AppModel {

  public function getFromUser($user_id) {
    $query = $this->find('all', array(
      'conditions' => array(
        'user_id' => array(
          $user_id,
          NULL
        )
      )
    ));

    $data = array();

    $UserModel = ClassRegistry::init('User');
    App::uses('CakeTime', 'Utility');
    CakeTime::$wordFormat = 'd/m/y';

    foreach ($query as $notification) {

      if($notification['Notification']['from'] == null) {
        $from = null;
      } else {
        $from = $UserModel->getFromUser('pseudo', $notification['Notification']['from']);
      }

      $data[] = array(
        'id' => intval($notification['Notification']['id']),
        'from' => $from,
        'content' => $notification['Notification']['content'],
        'time' => CakeTime::timeAgoInWords($notification['Notification']['created'])
      );

    }

    return $data;
  }

  /*
    ID: A_I
    USER_ID: INT or NULL (for all)
    FROM: INT or NULL (for all)
    CONTENT: TEXT LIMIT 255
  */
  public function setToUser($content, $user_id = NULL, $from = NULL) {
    if(empty($content) || strlen($content) > 255) {
      return false;
    }

    return $this->save(array(
      'content' => $content,
      'user_id' => $user_id,
      'from' => $from
    ));
  }

  public function clearFromUser($id, $user_id) {
    return $this->deleteAll(array('user_id' => $user_id, 'id' => $id));
  }
  public function clearAllFromUser($user_id) {
    return $this->deleteAll(array('user_id' => $user_id));
  }

  public function clearFromAllUsers($id) {
    return $this->deleteAll(array('id' => $id));
  }

  public function clearAllFromAllUsers() {
    return $this->deleteAll(array('1' => '1'));
  }

}
