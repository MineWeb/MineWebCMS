<?php
class Server extends AppModel {

  public function findSelectableServers() {

    $search_servers = $this->find('all', array('conditions' => array('type !=' => '2')));
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
