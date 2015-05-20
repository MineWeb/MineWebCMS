<?php

App::uses('ExceptionRenderer', 'Error');

class AppExceptionHandler {
    public static function handle($error) {
    	if($error->getMessage() == "Database connection \"Mysql\" is missing, or could not be created.") {
    		 
    		echo '<h3><center>'.$Lang->get('PROBLEM_DATABASE_NO_CONNECTION').'</center></h1>';
    		echo '<center><p>'.$Lang->get('PROBLEM_DATABASE_NO_CONNECTION_HOW_DO').'</p></center>';
        } else {
        	echo 'Problem : ' . $error->getMessage();
        }
    }
}