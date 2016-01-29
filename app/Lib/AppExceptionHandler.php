<?php

App::uses('ExceptionRenderer', 'Error');

class AppExceptionHandler {
    public static function handle($error) {
    	if($error->getMessage() == "Database connection \"Mysql\" is missing, or could not be created.") {

    		echo '<h3><center>'.$Lang->get('ERROR__DATABASE_TITLE').'</center></h1>';
    		echo '<center><p>'.$Lang->get('ERROR__DATABASE_CONTENT').'</p></center>';
        } else {
        	echo 'Problem : ' . $error->getMessage();
        }
    }
}
