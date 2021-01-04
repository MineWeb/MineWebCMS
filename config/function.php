<?php
/*
 * File containing all functions
 * necessary for the operation of the site
 *
 * @author Eywek
 */


// Function that deletes entries in an array (used for plugins)
function array_delete_value($array, $search)
{
    $temp = [];
    foreach ($array as $key => $value) {
        if ($value != $search) $temp[$key] = $value;
    }
    return $temp;
}

// Function that reduces the size of a text
function cut($data, $how)
{
    $return = substr($data, 0, $how);
    return (strlen($data) > $how) ? $return . '...' : $return;
}

// Function which generates a class among all that available for news. Allows random color.
function rand_color_news()
{
    $colors = ['border-top-color-dark-blue', 'border-top-color-dark-blue-2', 'border-top-color-yellow', 'border-top-color-dark-yellow', 'border-top-color-blue', 'border-top-color-magenta', 'border-top-color-green'];
    $color = rand(0, count($colors) - 1);
    return $colors[$color];
}

// Function which is used to return a secure text before displaying it (example: content of a news).
function before_display($content)
{
    return htmlentities($content);
}

function clearDir($folder)
{
    if (file_exists($folder) && is_dir($folder)) {
        $open = opendir($folder);
        if (!$open) return false;
        while ($file = readdir($open)) {
            if ($file == '.' || $file == '..') continue;
            if (is_dir($folder . "/" . $file)) {
                $r = clearDir($folder . "/" . $file);
                if (!$r) return false;
            } else {
                $r = unlink($folder . "/" . $file);
                if (!$r) return false;
            }
        }
        closedir($open);
        rmdir($folder);
        return true;
    }
    return false;
}
