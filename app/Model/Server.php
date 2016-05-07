<?php
class Server extends AppModel {

  public function findSelectableServers($bungee = false) {

    $conditions = ($bungee) ? array('type !=' => 2) : array('type !=' => array(2, 1));

    $search_servers = $this->find('all', array('conditions' => $conditions));
    if(!empty($search_servers)) {
      foreach ($search_servers as $v) {
        $servers[$v['Server']['id']] = $v['Server']['name'];
      }
    } else {
      $servers = array();
    }

    return $servers;

  }

}
