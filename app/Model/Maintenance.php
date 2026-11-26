<?php

class Maintenance extends AppModel
{
    function checkMaintenance($url, $utilComponent)
    {
        $result = $this->find('first', [
            'conditions' => [
                'active' => 1,
                'OR' => [
                    'url' => ['', $url],
                    ['sub_url' => 1, 'INSTR(LOWER(?), LOWER(Maintenance.url)) = 1' => $url]
                ]
            ],
            'order' => ['LENGTH(Maintenance.url)' => 'DESC', 'Maintenance.id' => 'ASC']
        ]);
        return $result ? $result['Maintenance'] : false;
    }

    function isFullMaintenance()
    {
        $result = $this->find("first", ["conditions" => ["url" => "", "active" => 1]]);
        if (isset($result["Maintenance"]))
            $result = $result["Maintenance"];
        return $result;
    }
}