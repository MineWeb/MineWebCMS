<?php
class MainComponent extends Object {

     public function onEnable() {

    	/* Ajout de la colone "rewards_waited" qui est NULL si confirmé ou le code de confirmation */ 

    	App::import('Model', 'ConnectionManager');
	    $con = new ConnectionManager;
	    $cn = $con->getDataSource('default');
	    $cn->query('ALTER TABLE `users` ADD  `rewards_waited` int NULL DEFAULT NULL ;');
    }

    public function onDisable() {

    	/* Suppression de la colonne */ 
    	
    	App::import('Model', 'ConnectionManager');
	    $con = new ConnectionManager;
	    $cn = $con->getDataSource('default');
	    $cn->query('ALTER TABLE `users` DROP `rewards_waited`;');

    }

}