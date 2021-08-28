<?php

class Maintenance extends AppModel
{
    function checkMaintenance($url = "")
    {
        $check = $this->find("first", ["conditions" => ["url" => $url, "active" => 1]]);
        if ($check)
            return $check;
        $is_full = $this->isFullMaintenance();
        if ($is_full)
            return $is_full;
        return false;
    }

    function isFullMaintenance()
    {
        return $this->find("first", ["conditions" => ["url" => "", "active" => 1]]);
    }
}