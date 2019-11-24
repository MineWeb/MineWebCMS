<?php

$needDisplayDatabase = (strpos(file_get_contents(ROOT . DS . 'app' . DS . 'Config' . DS . 'database.php'), 'LOGIN1')) ? true : false;

if (!file_exists(ROOT . DS . 'config' . DS . 'install.txt')) {

    if ($_POST) {
        if ($_GET['action'] == "db" && $needDisplayDatabase) {

            if (!empty($_POST['host']) && !empty($_POST['database']) && !empty($_POST['login'])) {

                $sql_host = $_POST['host'];
                $sql_name = $_POST['database'];
                $sql_user = $_POST['login'];
                $sql_pass = $_POST['password'];

                try {
                    $pdo = new PDO("mysql:host=$sql_host;dbname=$sql_name;", $sql_user, $sql_pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                    $sql_error = false;
                } catch (PDOException $mysqlException) {
                    $sql_error = true;
                }
                if (!$sql_error) {
                    $dbFile = fopen(ROOT . DS . 'app' . DS . 'Config' . DS . 'database.php', 'w');

                    $databaseStructure = "<?php
class DATABASE_CONFIG {

	public \$default = [
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => '{$sql_host}',
		'login' => '{$sql_user}',
		'password' => '{$sql_pass}',
		'database' => '{$sql_name}',
		'encoding' => 'utf8',
	];
}
";
                    if (!$dbFile || !fwrite($dbFile, $databaseStructure)) {
                        echo json_encode(array('status' => false, 'msg' => 'Le fichier /app/Config/database.php ne peut pas être écris !'));
                        exit;
                    }

                    fclose($dbFile);

                    if (!file_exists(ROOT . DS . 'config' . DS . 'secure.txt') && is_writable(ROOT . DS . 'config')) {
                        $date = date('H:i:s d/m/Y');
                        $ip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? htmlentities($_SERVER["HTTP_CF_CONNECTING_IP"]) : $_SERVER["REMOTE_ADDR"];
                        $data = '{ "created": "' . $date . '", "ip": "' . $ip . '" }';
                        $fp = fopen(ROOT . DS . 'config' . DS . 'secure.txt', 'w+');
                        fwrite($fp, $data);
                        fclose($fp);
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

function affichImg($bool)
{
    if ($bool) {
        return '<img src="data:image/x-icon;base64,' .
            'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2V' .
            'SZWFkeXHJZTwAAAGrSURBVDjLvZPZLkNhFIV75zjvYm7VGFNCqoZUJ+roKUUpjRuqp61Wq0NKDMelGGqOxBSUIBKXWtWGZxAvobr8lW' .
            'jChRgSF//dv9be+9trCwAI/vIE/26gXmviW5bqnb8yUK028qZjPfoPWEj4Ku5HBspgAz941IXZeze8N1bottSo8BTZviVWrEh546EO0' .
            '3EXpuJOdG63otJbjBKHkEp/Ml6yNYYzpuezWL4s5VMtT8acCMQcb5XL3eJE8VgBlR7BeMGW9Z4yT9y1CeyucuhdTGDxfftaBO7G4L+z' .
            'g91UocxVmCiy51NpiP3n2treUPujL8xhOjYOzZYsQWANyRYlU4Y9Br6oHd5bDh0bCpSOixJiWx71YY09J5pM/WEbzFcDmHvwwBu2wni' .
            'kg+lEj4mwBe5bC5h1OUqcwpdC60dxegRmR06TyjCF9G9z+qM2uCJmuMJmaNZaUrCSIi6X+jJIBBYtW5Cge7cd7sgoHDfDaAvKQGAlRZ' .
            'Yc6ltJlMxX03UzlaRlBdQrzSCwksLRbOpHUSb7pcsnxCCwngvM2Rm/ugUCi84fycr4l2t8Bb6iqTxSCgNIAAAAAElFTkSuQmCC' .
            '" alt="Oui"/>';
    } elseif (!$bool) {
        return '<img src="data:image/x-icon;base64,' .
            'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2V' .
            'SZWFkeXHJZTwAAAIhSURBVDjLlZPrThNRFIWJicmJz6BWiYbIkYDEG0JbBiitDQgm0PuFXqSAtKXtpE2hNuoPTXwSnwtExd6w0pl2Ot' .
            'PlrphKLSXhx07OZM769qy19wwAGLhM1ddC184+d18QMzoq3lfsD3LZ7Y3XbE5DL6Atzuyilc5Ciyd7IHVfgNcDYTQ2tvDr5crn6uLSv' .
            'X+Av2Lk36FFpSVENDe3OxDZu8apO5rROJDLo30+Nlvj5RnTlVNAKs1aCVFr7b4BPn6Cls21AWgEQlz2+Dl1h7IdA+i97A/geP65Whbm' .
            'rnZZ0GIJpr6OqZqYAd5/gJpKox4Mg7pD2YoC2b0/54rJQuJZdm6Izcgma4TW1WZ0h+y8BfbyJMwBmSxkjw+VObNanp5h/adwGhaTXF4' .
            'NWbLj9gEONyCmUZmd10pGgf1/vwcgOT3tUQE0DdicwIod2EmSbwsKE1P8QoDkcHPJ5YESjgBJkYQpIEZ2KEB51Y6y3ojvY+P8XEDN7u' .
            'KS0w0ltA7QGCWHCxSWWpwyaCeLy0BkA7UXyyg8fIzDoWHeBaDN4tQdSvAVdU1Aok+nsNTipIEVnkywo/FHatVkBoIhnFisOBoZxcGtQ' .
            'd4B0GYJNZsDSiAEadUBCkstPtN3Avs2Msa+Dt9XfxoFSNYF/Bh9gP0bOqHLAm2WUF1YQskwrVFYPWkf3h1iXwbvqGfFPSGW9Eah8HSS' .
            '9fuZDnS32f71m8KFY7xs/QZyu6TH2+2+FAAAAABJRU5ErkJggg==' .
            '" alt="Non"/>';
    } else {
        return 'Inconnu';
    }
}

/* OS Detection */

$os = '';
$php = substr(PHP_VERSION, 0, 3);

function phpinfo2array()
{
    $entitiesToUtf8 = function ($input) {
        // http://php.net/manual/en/function.html-entity-decode.php#104617
        return preg_replace_callback("/(&#[0-9]+;)/",
            function ($m) {
                $char = current($m);
                $utf = iconv('UTF-8', 'UCS-4', $char);
                return sprintf("&#x%s;", ltrim(strtoupper(bin2hex($utf)), "0"));
            }, $input);
    };
    $plainText = function ($input) use ($entitiesToUtf8) {
        return trim(html_entity_decode($entitiesToUtf8(strip_tags($input))));
    };
    $titlePlainText = function ($input) use ($plainText) {
        return '# ' . $plainText($input);
    };

    ob_start();
    phpinfo(-1);

    $phpinfo = array('phpinfo' => array());

    // Strip everything after the <h1>Configuration</h1> tag (other h1's)
    if (!preg_match('#(.*<h1[^>]*>\s*Configuration.*)<h1#s', ob_get_clean(), $matches)) {
        return array();
    }

    $input = $matches[1];
    $matches = array();

    if (preg_match_all(
        '#(?:<h2.*?>(?:<a.*?>)?(.*?)(?:<\/a>)?<\/h2>)|' .
        '(?:<tr.*?><t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>(?:<t[hd].*?>(.*?)\s*</t[hd]>)?)?</tr>)#s',
        $input,
        $matches,
        PREG_SET_ORDER
    )) {
        foreach ($matches as $match) {
            $fn = strpos($match[0], '<th') === false ? $plainText : $titlePlainText;
            if (strlen($match[1])) {
                $phpinfo[$match[1]] = array();
            } elseif (isset($match[3])) {
                $keys1 = array_keys($phpinfo);
                $phpinfo[end($keys1)][$fn($match[2])] = isset($match[4]) ? array($fn($match[3]), $fn($match[4])) : $fn($match[3]);
            } else {
                $keys1 = array_keys($phpinfo);
                $phpinfo[end($keys1)][] = $fn($match[2]);
            }

        }
    }

    return $phpinfo;
}

$infos = phpinfo2array()["phpinfo"];

switch (true) {
    case strpos($infos['System'], 'deb8') || strpos($infos['System'], 'debian-8') > 0:
        $os = 'debian 8';
        break;
    case strpos($infos['System'], 'deb9') || strpos($infos['System'], 'debian-9') > 0:
        $os = 'debian 9';
        break;
    case strpos(strtolower($infos['System']), 'centos') > 0:
        $os = 'centos';
        break;
    case strpos(strtolower($infos[1]), 'ubuntu') > 0:
        $offset = stripos($infos[1], 'ubuntu');
        $os = 'ubuntu ' . substr($infos[1], $offset + 6, 5);
        break;
    case PHP_OS === "WINNT":
        $os = 'windows';
        break;
    default:
        $os = 'linux';
        break;
}

$compatible = [];

/*
 * @var array  $compatible         Dependencies for MineWeb
 * @var bool   $compatible['key']  State of dependencies
 * @var string $help               Text to help user to solve his installation problems
 */

$compatible['chmod'] = (is_writable(ROOT . DS . 'app' . DS . 'Config') && is_writable(ROOT . DS . 'app' . DS . 'Plugin') && is_writable(ROOT . DS . 'app' . DS . 'View' . DS . 'Themed') && is_writable(ROOT . DS . 'config') && is_writable(ROOT . DS . 'app' . DS . 'tmp')) ? true : false;

if (!$compatible['chmod']) {
    $help['chmod'] = "";

    if (!is_writable(ROOT . DS . 'app' . DS . 'Config')) {
        $help['chmod'] = "Le dossier /app/Config ne peut être écrit. <br /><br />";
    }

    if (!is_writable(ROOT . DS . 'app' . DS . 'Plugin')) {
        $help['chmod'] = "Le dossier /app/Plugin ne peut être écrit. <br /><br />";
    }

    if (!is_writable(ROOT . DS . 'app' . DS . 'View' . DS . 'Themed')) {
        $help['chmod'] = "Le dossier /app/View/Themed ne peut être écrit. <br /><br />";
    }

    if (!is_writable(ROOT . DS . 'config')) {
        $help['chmod'] = "Le dossier /config ne peut être écrit. <br /><br />";
    }

    if (!file_exists(ROOT . DS . 'app' . DS . 'tmp')) {
        $help['chmod'] = "Le dossier app/tmp n'existe pas. <br /><br />";
    }

    if (!is_writable(ROOT . DS . 'app' . DS . 'tmp')) {
        $help['chmod'] = "Le dossier app/tmp ne peut être écrit. <br /><br />";
    }
}

$compatible['phpVersion'] = false;
$compatible['pdo'] = false;
$compatible['curl'] = false;
$compatible['rewriteUrl'] = false;
$compatible['gd2'] = false;
$compatible['openZip'] = false;
$compatible['openSSL'] = false;

$compatible['curl'] = extension_loaded('cURL');

if (!$compatible['curl']) {
    $help['curl'] = "<a target='_blank' href='https://www.google.fr/search?query=Install+curl+on+$os'>Aide à propos de l'installation de curl sur ma machine</a>";
}

$compatible['phpVersion'] = version_compare(PHP_VERSION, '5.6', '>=') && version_compare(PHP_VERSION, '7.5', '<');

if (!$compatible['phpVersion']) {
    $help['phpVersion'] = "<a target='_blank' href='https://www.google.fr/search?query=Install+PHP+7.1+on+$os'>Aide à propos de l'installation de php 7.1 sur ma machine</a>";
}

$compatible['pdo'] = in_array('pdo_mysql', get_loaded_extensions());

if (!$compatible['pdo']) {
    $help['pdo'] = "<a target='_blank' href='https://www.google.fr/search?query=Install+pdo_mysql+on+$os'>Aide à propos de l'installation de pdo_mysql sur ma machine</a>";
}

$compatible['rewriteUrl'] = (!isset($InstallRewrite)) ? true : false;

if (!$compatible['rewriteUrl']) {
    $help['rewriteUrl'] = "";

    if (!file_exists(ROOT . DS . ".htaccess")) {
        $help['rewriteUrl'] .= "Le fichier .htaccess semble manquant à la racine du site, activez les fichiers cachés et transférez le sur votre site depuis l'archive de MineWeb. <br /><br />";
    }

    if (!file_exists(ROOT . DS . "app" . DS . ".htaccess")) {
        $help['rewriteUrl'] .= "Le fichier .htaccess semble manquant dans le dossier app/, activez les fichiers cachés et transférez le sur votre site depuis l'archive de MineWeb. <br /><br />";
    }

    if ($os != "windows") {
        $help['rewriteUrl'] .= "Essayez de taper : <b>sudo a2enmod rewrite</b> dans une invite de commande SSH. <br /><br />";
        $help['rewriteUrl'] .= "Ajoutez ces lignes : <br /> <br />";
        $help['rewriteUrl'] .= '<b>' . htmlspecialchars('<Directory "/var/www/html">') . '<br />';
        $help['rewriteUrl'] .= "AllowOverride All <br />";
        $help['rewriteUrl'] .= htmlspecialchars('</Directory>') . "</b><br /> <br /> entre les balises <b />" . htmlspecialchars('<VirtualHost>') . "</b>, dans le fichier de configuration d'apache2 située ici <b>/etc/apache2/sites-available/000-default.conf</b> puis veuillez redémarrer apache2 avec <b>service apache2 restart</b>";
    }

}

$compatible['gd2'] = function_exists('imagettftext');

if (!$compatible['gd2']) {
    $help['gd2'] = "<a target='_blank' href='https://www.google.fr/search?query=Install+php" . $php . "-gd+on+$os'>Aide à propos de l'installation de GD2 sur ma machine</a><br /><br />";
    $help['gd2'] .= "<i class='fa fa-info'></i> Essayer cette commande : <b>sudo apt-get install php" . $php . "-gd</b>";
}

$compatible['openZip'] = function_exists('zip_open');

if (!$compatible['openZip']) {
    $help['openZip'] = "<a target='_blank' href='https://www.google.fr/search?query=Install+php" . $php . "-zip+on+$os'>Aide à propos de l'installation de php-zip sur ma machine</a><br /><br />";
    $help['openZip'] .= "<i class='fa fa-info'></i> Essayer cette commande : <b>sudo apt-get install php" . $php . "-zip</b>";
}

$compatible['openSSL'] = function_exists('openssl_pkey_new');

//allow_url_fopen
if (function_exists('ini_get') && ini_get('allow_url_fopen') == "1") {
    $compatible['allowGetURL'] = true;
} elseif (file_exists(ROOT . DS . 'config' . DS . 'installed.txt') || @file_get_contents('https://google.fr')) {
    $compatible['allowGetURL'] = true;
}

$needAffichCompatibility = (in_array(false, $compatible)) ? true : false;
if (file_exists(ROOT . DS . 'config' . DS . 'bypass_compatibility')) {
    $needAffichCompatibility = false;
}

?>
