<?php

$db = ConnectionManager::getDataSource('default');

// 1.8.0
@$db->query('ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `uuid` VARCHAR(255) NOT NULL;');
// end 1.8.0

// for all update
@clearFolder(ROOT.'/app/tmp/cache/models/');
@clearFolder(ROOT.'/app/tmp/cache/persistent/');
@unlink(ROOT.DS.'lang'.DS.'fr.json');
?>
