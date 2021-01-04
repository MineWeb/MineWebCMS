<?php

class Server extends AppModel
{

    public function findSelectableServers($rcon = true)
    {
        $types = [0];
        if ($rcon)
            $types = [0, 2];
        $search_servers = $this->find('all', ['conditions' => ['type' => $types]]);
        if (empty($search_servers))
            return [];

        $servers = [];
        foreach ($search_servers as $server)
            $servers[$server['Server']['id']] = $server['Server']['name'];
        return $servers;
    }

}
