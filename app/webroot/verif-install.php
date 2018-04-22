<?php

$needDisplayDatabase = (strpos(file_get_contents(ROOT.DS.'app'.DS.'Config'.DS.'database.php'), 'LOGIN1')) ? true : false;

if(!file_exists(ROOT.DS.'config'.DS.'install.txt')) {
  if($_POST) {

    if($_GET['action'] == "db" && $needDisplayDatabase) {

      if(!empty($_POST['host']) && !empty($_POST['database']) && !empty($_POST['login'])) {

        $sql_host = $_POST['host'];
        $sql_name = $_POST['database'];
        $sql_user = $_POST['login'];
        $sql_pass = $_POST['password'];

        try {
            $pdo = new PDO("mysql:host=$sql_host;dbname=$sql_name;", $sql_user, $sql_pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $sql_error = false;
        }
        catch(PDOException $mysqlException) {
            $sql_error = true;
        }
        if(!$sql_error) {
          $dbFile = fopen(ROOT.DS.'app'.DS.'Config'.DS.'database.php', 'w');

          if(!$dbFile) {
            echo json_encode(array('status' => false, 'msg' => 'Le fichier /app/Config/database.php ne peut pas être écris !'));
            exit;
          }

          $databaseStructure = '<?php
          class DATABASE_CONFIG {

          	public $default = array(
          		\'datasource\' => \'Database/Mysql\',
          		\'persistent\' => false,
          		\'host\' => \''.$sql_host.'\',
          		\'login\' => \''.$sql_user.'\',
          		\'password\' => \''.$sql_pass.'\',
          		\'database\' => \''.$sql_name.'\',
          		\'encoding\' => \'utf8\',
          	);
          }
          ';
          $write = fwrite($dbFile, $databaseStructure);
          fclose($dbFile);

          if(!$write) {
            echo json_encode(array('status' => false, 'msg' => 'Le fichier /app/Config/database.php ne peut pas être écris !'));
            exit;
          }

          echo json_encode(array('status' => true));
          exit;

        } else {
          echo json_encode(array('status' => false, 'msg' => 'Erreur lors de la connexion ! (<em>' . $mysqlException->getMessage() . '</em>)'));
          exit;
        }

      } else {
        echo json_encode(array('status' => false, 'msg' => 'Veuillez remplir tout les champs !'));
        exit;
      }

    }

  }
}

function affichImg($bool) {
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

$compatible = array();

$compatible['chmod'] = (is_writable(ROOT . DS . 'app' . DS . 'Config') && is_writable(ROOT . DS . 'app' . DS . 'Plugin') && is_writable(ROOT . DS . 'app' . DS . 'View' . DS . 'Themed') && is_writable(ROOT . DS . 'config') && is_writable(ROOT . DS . 'app' . DS . 'tmp')) ? true : false;

$compatible['phpVersion'] = false;
$compatible['pdo'] = false;
$compatible['curl'] = false;
$compatible['rewriteUrl'] = false;
$compatible['gd2'] = false;
$compatible['openZip'] = false;
$compatible['openSSL'] = false;

$compatible['curl'] = extension_loaded('cURL');

$compatible['phpVersion'] = version_compare(PHP_VERSION, '5.6', '>=') && version_compare(PHP_VERSION, '7.2', '<');

$compatible['pdo'] = in_array('pdo_mysql', get_loaded_extensions());

//if(function_exists('apache_get_modules')) {
  //$compatible['rewriteUrl'] = in_array('mod_rewrite', @apache_get_modules());
//} else {
  $compatible['rewriteUrl'] = (!isset($InstallRewrite)) ? true : false;
//}

$compatible['gd2'] = function_exists('imagettftext');

$compatible['openZip'] = function_exists('zip_open');

$compatible['openSSL'] = function_exists('openssl_pkey_new');

//allow_url_fopen
if(function_exists('ini_get') && ini_get('allow_url_fopen') == "1") {
  $compatible['allowGetURL'] = true;
} elseif(file_exists(ROOT.DS.'config'.DS.'installed.txt') || @file_get_contents('https://google.fr')) {
  $compatible['allowGetURL'] = true;
}

$needAffichCompatibility = (in_array(false, $compatible)) ? true : false;
if(file_exists(ROOT.DS.'config'.DS.'bypass_compatibility')) {
  $needAffichCompatibility = false;
}
?>
