<?php
class MainComponent extends Object {

     public function onEnable() {

    	/* Ajout de la colone "rewards_waited" qui est NULL si confirmé ou le code de confirmation */ 

    	App::import('Model', 'ConnectionManager');
	    $con = new ConnectionManager;
	    $db = $con->getDataSource('default');
	    $verif = $db->query('SHOW COLUMNS FROM users;');
		$execute = true;
		foreach ($verif as $k => $v) {
		  	if($v['COLUMNS']['Field'] == 'rewards_waited') {
				$execute = false;
				break;
			}
		}
		if($execute) {
	    	$db->query('ALTER TABLE `users` ADD  `rewards_waited` int NULL DEFAULT NULL ;');
	    }
    }

    public function onDisable() {

    	/* Suppression de la colonne */ 
    	App::import('Model', 'ConnectionManager');
	    $con = new ConnectionManager;
	    $db = $con->getDataSource('default');

    	$verif = $db->query('SHOW COLUMNS FROM users;');
		$execute = false;
		foreach ($verif as $k => $v) {
		  	if($v['COLUMNS']['Field'] == 'rewards_waited') {
				$execute = true;
				break;
			}
		}
		if($execute) {
	   		$db->query('ALTER TABLE `users` DROP `rewards_waited`;');
	   	}

	   	clearFolder(ROOT.'/app/tmp/cache/models/');
      	clearFolder(ROOT.'/app/tmp/cache/persistent/');

    }

}