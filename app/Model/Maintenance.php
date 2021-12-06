<?php

class Maintenance extends AppModel
{
    function checkMaintenance($url, $utilComponent)
    {
        $use_sqlite = $utilComponent->useSqlite();

        $condition = ["'" . $url . "' LIKE CONCAT(Maintenance.url, '%')", "active" => 1];

        if ($use_sqlite)
            $condition = ["'" . $url . "' LIKE 'Maintenance.url' || '%')", "active" => 1];

        $check = $this->find("first", ["conditions" => $condition]);
        if (isset($check["Maintenance"]))
            $check = $check["Maintenance"];
        if ($check && (($check["url"] == $url) || ($check["sub_url"] && $url != "/")))
            return $check;
        $is_full = $this->isFullMaintenance();
        if ($is_full)
            return $is_full;
        return false;
    }

    function isFullMaintenance()
    {
        $result = $this->find("first", ["conditions" => ["url" => "", "active" => 1]]);
        if (isset($result["Maintenance"]))
            $result = $result["Maintenance"];
        return $result;
    }
}