<?php
class Server extends AppModel {

  public function findSelectableServers() {
    $search_servers = $this->find('all', array('conditions' => array('type' => 0)));
    if (empty($search_servers))
        return array();

    $servers = array();
    foreach ($search_servers as $server)
        $servers[$server['Server']['id']] = $server['Server']['name'];
    return $servers;
  }

}
