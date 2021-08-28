<?php

class Maintenance extends AppModel
{
    function checkMaintenance($url = "")
    {
        $start_url = "/" . explode("/", $url)[1];
        $check = $this->find("first", ["conditions" => ["url LIKE" => $start_url . "%", "active" => 1]])["Maintenance"];

        if ($check && (($check["url"] == $url) || ($check["sub_url"] && $url != "/")))
            return $check;

        $is_full = $this->isFullMaintenance();
        if ($is_full)
            return $is_full;
        return false;
    }

    function isFullMaintenance()
    {
        return $this->find("first", ["conditions" => ["url" => "", "active" => 1]])["Maintenance"];
    }
}