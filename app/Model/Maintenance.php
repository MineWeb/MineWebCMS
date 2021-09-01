<?php

class Maintenance extends AppModel
{
    function checkMaintenance($url = "")
    {
        $check = $this->find("first", ["conditions" => ["'" . $url . "' LIKE CONCAT(Maintenance.url, '%')", "active" => 1]]);
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