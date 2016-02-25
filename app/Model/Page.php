<?php
class Page extends AppModel {

  public function find($conditions = null, $fields = array(), $order = null, $recursive = null) {
    $result = parent::find($conditions, $fields, $order, $recursive);

    if($conditions == "all") {

      if(isset($result[0]['Page']['user_id'])) {

        $users = ClassRegistry::init('User')->find('all', array('fields' => array('id', 'pseudo')));
        foreach ($users as $key => $value) {
          $users[$value['User']['id']] = $value['User']['pseudo'];
        }

        foreach ($result as $key => $value) {
          $result[$key]['Page']['author'] = $users[$result[$key]['Page']['user_id']];
        }

      }

    }

    if($conditions == "first" && isset($result['Page']['user_id'])) {

      $users = ClassRegistry::init('User')->find('all', array('fields' => array('id', 'pseudo')));
      foreach ($users as $key => $value) {
        $users[$value['User']['id']] = $value['User']['pseudo'];
      }

      $result['Page']['author'] = $users[$result['Page']['user_id']];

    }

    return $result;
  }

}
