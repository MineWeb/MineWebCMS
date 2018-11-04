<?php
// Ajout des tables pour la MAJ
$db = ConnectionManager::getDataSource('default');
// 1.6.2
@$db->query('CREATE TABLE IF NOT EXISTS `server_cmds` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT \'\',
  `server_id` int(8) NOT NULL,
  `cmd` varchar(255) NOT NULL DEFAULT \'\',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
// Général
@clearFolder(ROOT.'/app/tmp/cache/models/');
@clearFolder(ROOT.'/app/tmp/cache/persistent/');
@unlink(ROOT.DS.'lang'.DS.'fr.json');
?>
