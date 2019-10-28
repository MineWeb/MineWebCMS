<?php
$db = ConnectionManager::getDataSource('default');
// for all update
function add_column($table, $name, $sql) {
  //global $db;
  $db = ConnectionManager::getDataSource('default');
  $verif = $db->query('SHOW COLUMNS FROM '.$table.';');
  $execute = true;
  foreach ($verif as $k => $v) {
    if($v['COLUMNS']['Field'] == $name) {
      $execute = false;
      break;
    }
  }
  if($execute) {
    @$query = $db->query('ALTER TABLE `'.$table.'` ADD `'.$name.'` '.$sql.';');
  }
	
}
function remove_column($table, $name) {
  //global $db;
	$db = ConnectionManager::getDataSource('default');
  $verif = $db->query('SHOW COLUMNS FROM '.$table.';');
  $execute = false;
  foreach ($verif as $k => $v) {
    if($v['COLUMNS']['Field'] == $name) {
      $execute = true;
      break;
    }
  }
  if($execute) {
    @$query = $db->query('ALTER TABLE `'.$table.'` DROP COLUMN `'.$name.'`;');
  }
}
// 1.8.0
add_column('users', 'uuid', 'varchar(255) DEFAULT NULL AFTER `pseudo`');
add_column('configurations', 'uuid', 'int(1) DEFAULT \'0\' AFTER `end_layout_code`');
// end 1.8.0
@clearFolder(ROOT.'/app/tmp/cache/models/');
@clearFolder(ROOT.'/app/tmp/cache/persistent/');
@unlink(ROOT.DS.'lang'.DS.'fr.json');
?>
