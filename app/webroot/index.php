<?php
/**
 * Index
 *
 * The Front Controller for handling every request
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.webroot
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Use the DS to separate the directories in other defines
 */
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

/**
 * These defines should only be edited if you have cake installed in
 * a directory layout other than the way it is distributed.
 * When using custom settings be sure to use the DS and do not add a trailing DS.
 */

/**
 * The full path to the directory which holds "app", WITHOUT a trailing DS.
 *
 */
if (!defined('ROOT')) {
	define('ROOT', dirname(dirname(dirname(__FILE__))));
}

/**
 * The actual directory name for the "app".
 *
 */
if (!defined('APP_DIR')) {
	define('APP_DIR', basename(dirname(dirname(__FILE__))));
}

/**
 * The absolute path to the "cake" directory, WITHOUT a trailing DS.
 *
 * Un-comment this line to specify a fixed path to CakePHP.
 * This should point at the directory containing `Cake`.
 *
 * For ease of development CakePHP uses PHP's include_path. If you
 * cannot modify your include_path set this value.
 *
 * Leaving this constant undefined will result in it being defined in Cake/bootstrap.php
 *
 * The following line differs from its sibling
 * /lib/Cake/Console/Templates/skel/webroot/index.php
 */
//define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'lib');

/**
 * Editing below this line should NOT be necessary.
 * Change at your own risk.
 *
 */
if (!defined('WEBROOT_DIR')) {
	define('WEBROOT_DIR', basename(dirname(__FILE__)));
}
if (!defined('WWW_ROOT')) {
	define('WWW_ROOT', dirname(__FILE__) . DS);
}

// MineWeb Compatiblite 

if(!file_exists(ROOT.DS.'config'.DS.'install.txt')) {
	$cURL = extension_loaded('cURL');
	$phpversion = version_compare(PHP_VERSION, '5.3', '>=');
	$pdo = in_array('pdo_mysql', get_loaded_extensions());

	if(function_exists('apache_get_modules')) {
	    $rewrite_url = in_array('mod_rewrite', @apache_get_modules());
	} else {

		$rewrite_url = 'undefined';

	}

	$GD2 = function_exists('imagettftext');
	$zip = function_exists('zip_open');
	$openssl = function_exists('openssl_pkey_new');
	$allow_url_fopen = @file_get_contents('http://mineweb.org/api/getFreeThemes');
	if($allow_url_fopen) {
	    $allow_url_fopen = true;
	}
	$ionCube = extension_loaded('ionCube Loader');

	$needed = array($cURL, $phpversion, $pdo, $rewrite_url, $GD2, $zip, $allow_url_fopen, $openssl, $ionCube);

	if(in_array(false, $needed)) {
		function affich_img($bool) {
		    if($bool) {
		        return '<img src="data:image/x-icon;base64,'.
		     'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2V'.
		     'SZWFkeXHJZTwAAAGrSURBVDjLvZPZLkNhFIV75zjvYm7VGFNCqoZUJ+roKUUpjRuqp61Wq0NKDMelGGqOxBSUIBKXWtWGZxAvobr8lW'.
		     'jChRgSF//dv9be+9trCwAI/vIE/26gXmviW5bqnb8yUK028qZjPfoPWEj4Ku5HBspgAz941IXZeze8N1bottSo8BTZviVWrEh546EO0'.
		     '3EXpuJOdG63otJbjBKHkEp/Ml6yNYYzpuezWL4s5VMtT8acCMQcb5XL3eJE8VgBlR7BeMGW9Z4yT9y1CeyucuhdTGDxfftaBO7G4L+z'.
		     'g91UocxVmCiy51NpiP3n2treUPujL8xhOjYOzZYsQWANyRYlU4Y9Br6oHd5bDh0bCpSOixJiWx71YY09J5pM/WEbzFcDmHvwwBu2wni'.
		     'kg+lEj4mwBe5bC5h1OUqcwpdC60dxegRmR06TyjCF9G9z+qM2uCJmuMJmaNZaUrCSIi6X+jJIBBYtW5Cge7cd7sgoHDfDaAvKQGAlRZ'.
		     'Yc6ltJlMxX03UzlaRlBdQrzSCwksLRbOpHUSb7pcsnxCCwngvM2Rm/ugUCi84fycr4l2t8Bb6iqTxSCgNIAAAAAElFTkSuQmCC'.
		     '" alt="Oui"/>';
		    } elseif(!$bool) {
		        return '<img src="data:image/x-icon;base64,'.
		     'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2V'.
		     'SZWFkeXHJZTwAAAIhSURBVDjLlZPrThNRFIWJicmJz6BWiYbIkYDEG0JbBiitDQgm0PuFXqSAtKXtpE2hNuoPTXwSnwtExd6w0pl2Ot'.
		     'PlrphKLSXhx07OZM769qy19wwAGLhM1ddC184+d18QMzoq3lfsD3LZ7Y3XbE5DL6Atzuyilc5Ciyd7IHVfgNcDYTQ2tvDr5crn6uLSv'.
		     'X+Av2Lk36FFpSVENDe3OxDZu8apO5rROJDLo30+Nlvj5RnTlVNAKs1aCVFr7b4BPn6Cls21AWgEQlz2+Dl1h7IdA+i97A/geP65Whbm'.
		     'rnZZ0GIJpr6OqZqYAd5/gJpKox4Mg7pD2YoC2b0/54rJQuJZdm6Izcgma4TW1WZ0h+y8BfbyJMwBmSxkjw+VObNanp5h/adwGhaTXF4'.
		     'NWbLj9gEONyCmUZmd10pGgf1/vwcgOT3tUQE0DdicwIod2EmSbwsKE1P8QoDkcHPJ5YESjgBJkYQpIEZ2KEB51Y6y3ojvY+P8XEDN7u'.
		     'KS0w0ltA7QGCWHCxSWWpwyaCeLy0BkA7UXyyg8fIzDoWHeBaDN4tQdSvAVdU1Aok+nsNTipIEVnkywo/FHatVkBoIhnFisOBoZxcGtQ'.
		     'd4B0GYJNZsDSiAEadUBCkstPtN3Avs2Msa+Dt9XfxoFSNYF/Bh9gP0bOqHLAm2WUF1YQskwrVFYPWkf3h1iXwbvqGfFPSGW9Eah8HSS'.
		     '9fuZDnS32f71m8KFY7xs/QZyu6TH2+2+FAAAAABJRU5ErkJggg=='.
		     '" alt="Non"/>';
		    } else {
		    	return 'Inconnu';
		    }
		}

		die("
	<!DOCTYPE html> 
	<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\" lang=\"fr\"> 
	    <head> 
	        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/> 
	        <title>MineWeb - Compatibilité</title> 
	        <!--[if lt IE 9]>
	          <script src=\"http://html5shim.googlecode.com/svn/trunk/html5.js\"></script>
	        <![endif]-->
	        <meta name=\"author\" content=\"Eywek\">
	        <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,900' rel='stylesheet' type='text/css'>
			<link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css' rel='stylesheet' type='text/css'>
	        <style type=\"text/css\">
	            body {
	                margin: 0;
	                background-color: #f1f1f1;
	            }

	            .bloc {
	                border-top: 5px solid #fbcb43;
	                -webkit-box-sizing: border-box;
	                box-sizing: border-box;
	                background-color: #fff;
	                box-shadow: inset 1px 1px 0 rgba(0,0,0,.1),inset 0 -1px 0 rgba(0,0,0,.07);
	                display: inline-block;
	                padding: 15px;
	                margin-top: 7px;
	                margin-bottom: 7px;
	                width: 700px;

	            }
	            .bloc h2 {
	                color: #262626;
	                display: inline-block;
	                font-size: 20px;
	                font-family: 'Roboto', sans-serif;
	                font-weight: 300;
	            }
	            .bloc p {
	                font-family: 'Roboto', sans-serif;
	                font-weight: 400;
	            }
	            .center-top {
	                margin-top: 15%;
	            }
	        </style>
	    </head> 
	    <body>
	        <div class=\"container\" style=\"width: 700px;\">
	            <div class=\"bloc center-top\">
	                <h2>MineWeb - Compatibilité de votre hébergeur</h2>
	                <br><br>
	                <p>
	                    <table class=\"table table-bordered\">
	                      <thead>
	                        <tr>
	                          <th>Fonctionnalités</th>
	                          <th>État</th>
	                        </tr>
	                      </thead>
	                      <tbody>
	                        <tr>
	                          <td>Version de PHP >= 5.3 </td>
	                          <td>".affich_img($phpversion)."</td>
	                        </tr>
	                        <tr>
	                          <td>ionCube Loader</td>
	                          <td>".affich_img($ionCube)."</td>
	                        </tr>
	                        <tr>
	                          <td>PDO</td>
	                          <td>".affich_img($pdo)."</td>
	                        </tr>
	                        <tr>
	                          <td>cURL</td>
	                          <td>".affich_img($cURL)."</td>
	                        </tr>
	                        <tr>
	                          <td>Réécriture d'URL</td>
	                          <td>".affich_img($rewrite_url)."</td>
	                        </tr>
	                        <tr>
	                          <td>Librairie GD2 (captcha et image des utilisateurs)</td>
	                          <td>".affich_img($GD2)."</td>
	                        </tr>
	                        <tr>
	                          <td>Ouverture d'un zip (mise à jour)</td>
	                          <td>".affich_img($zip)."</td>
	                        </tr>
	                        <tr>
	                          <td>OpenSSL (Connexion à l'API MineWeb)</td>
	                          <td>".affich_img($openssl)."</td>
	                        </tr>
	                        <tr>
	                          <td>Ouverture d'un site à distance (mise à jour)</td>
	                          <td>".affich_img($allow_url_fopen)."</td>
	                        </tr>
	                      </tbody>
	                    </table>
	                </p>
	                <p>
	                   <div class=\"alert alert-danger\">Votre hébergeur est incompatible avec le CMS !</div>
	                </p>
	            </div>  
	        </div>
	    </body> 
	</html>");
	}
}
//


// for built-in server
if (php_sapi_name() === 'cli-server') {
	if ($_SERVER['REQUEST_URI'] !== '/' && file_exists(WWW_ROOT . $_SERVER['PHP_SELF'])) {
		return false;
	}
	$_SERVER['PHP_SELF'] = '/' . basename(__FILE__);
}

if (!defined('CAKE_CORE_INCLUDE_PATH')) {
	if (function_exists('ini_set')) {
		ini_set('include_path', ROOT . DS . 'lib' . PATH_SEPARATOR . ini_get('include_path'));
	}
	if (!include 'Cake' . DS . 'bootstrap.php') {
		$failed = true;
	}
} else {
	if (!include CAKE_CORE_INCLUDE_PATH . DS . 'Cake' . DS . 'bootstrap.php') {
		$failed = true;
	}
}
if (!empty($failed)) {
	trigger_error("CakePHP core could not be found. Check the value of CAKE_CORE_INCLUDE_PATH in APP/webroot/index.php. It should point to the directory containing your " . DS . "cake core directory and your " . DS . "vendors root directory.", E_USER_ERROR);
}

App::uses('Dispatcher', 'Routing');

$Dispatcher = new Dispatcher();
$Dispatcher->dispatch(
	new CakeRequest(),
	new CakeResponse()
);
