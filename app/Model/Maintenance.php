<?php

class Maintenance extends AppModel
{
    function checkMaintenance($url = "")
    {
        $check = $this->find("first", ["conditions" => ["url LIKE" => $url . "%", "active" => 1]])["Maintenance"];
        if ($check) {
            if (!$check["sub_url"] && $check["url"] != $url)
                return false;
            return true;
        }
        $is_full = $this->isFullMaintenance();
        if ($is_full)
            return true;
        return false;
    }

    function isFullMaintenance()
    {
        return $this->find("first", ["conditions" => ["url" => "", "active" => 1]]);
    }
}