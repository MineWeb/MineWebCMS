<?php
/**
* Fichier contenant toutes les fonctions
* Nécessaire au fonctionnement du site
* 
* @author Eywek
**/

require 'lang.php';

// Fonction qui supprime des entrées dans un tableau (utilisé pour les plugins)s
function array_delete_value($array,$search) {
	$temp = array();
	foreach($array as $key => $value) {
		if($value!=$search) $temp[$key] = $value;
	}
	return $temp;
}

// Fonction qui génére une classe parmis toute celle disponible pour les news. Permet une couleur aléatoire.
function rand_color_news() {
	$colors = array('border-top-color-dark-blue', 'border-top-color-dark-blue-2', 'border-top-color-yellow', 'border-top-color-dark-yellow', 'border-top-color-blue', 'border-top-color-magenta', 'border-top-color-green'); // toute les class disponible
	$color = rand(0, count($colors)); // génére un chiffre aléatoire entre 0 et le nombre de class
	if($color > 0) { // si le chiffre généré est supérier à 0 
		$color = $color - 1; // je lui enlève -1 pour bien sélectionner dans l'array après
	}
	return $colors[$color]; // et je retourne la class selon le chiffre aléatoire
}

// Fonction importante et nécessaire au fonctionnement du site. Remplace tout les messages du site dans la langue séléctionner sur le fichier lang.php
function lang($msg, $language, $timeline = false) {
	if($timeline) { // si ce sont des messages pour la timeline de l'index
		if(isset($language['timeline'][$msg])) { // et que le message existe
			return $language['timeline'][$msg]; // alors je retourne le message configuré
		} else { // sinon 
			return $msg; // je retourne le message tel quel
		}
	} else { // sinon, si ce n'est pas un message de timeline
		if(isset($language[$msg])) { // et si le msg existe
			return $language[$msg]; // je retourne le msg config
		} else { // sinon
			return $msg; // le msg tel quel
		}
	}
}

// Fonction qui sert a retourner un texte sécurisé avant de l'afficher (exemple: contenu d'une news).
function before_display($content) {
	$content = htmlentities($content);
	$content = stripcslashes($content);
	return $content;
}

// Met la date dans un format plus lisible qu'en bdd. Cela s'affiche en fonction du FORMAT_DATE du fichier lang.php.
function formate_date($date, $language) {
	if(isset($language['FORMAT_DATE'])) { // si le format de la date est configuré je fais les actions suivantes 
		$date = explode(' ', $date); // j'explode les espaces pour séparé date & heure
		$time = explode(':', $date['1']); // ensuite je sépare tout les chiffre de la date
		$date = explode('-', $date['0']); // puis tout ceux de l'heure
		$return = str_replace('{%day}', $date['2'], $language['FORMAT_DATE']); // puis je remplace les variable de la config lang.php par les chiffres | Le jour 
		$return = str_replace('{%month}', $date['1'], $return); // puis je remplace les variable de la config lang.php par les chiffres | Le mois
		$return = str_replace('{%year}', $date['0'], $return); // puis je remplace les variable de la config lang.php par les chiffres | L'année
		$return = str_replace('{%minutes}', $time['1'], $return); // puis je remplace les variable de la config lang.php par les chiffres | Les minutes 
		$if = explode('|', $return); // ensuite j'explode pour savoir a quelle format je retourne l'heure
		$if = explode('}', $if['1']); // 24h ou 12h
		if($if['0'] == 12) { // donc si c'est 12h
			if($time['0'] > 12) { // et que c'est plus de 12h donc l'après midi
				if($time['0'] == 13) { // je remplace tout les chiffre par leur équivalent en 12h
					$hour = '01';
					$pm_or_am = 'PM'; // et je dis bien que c'est l'après-midi
				} elseif($time['0'] == 13) {
					$hour = '01';
					$pm_or_am = 'PM';
				} elseif($time['0'] == 14) {
					$hour = '02';
					$pm_or_am = 'PM';
				} elseif($time['0'] == 15) {
					$hour = '03';
					$pm_or_am = 'PM';
				} elseif($time['0'] == 16) {
					$hour = '04';
					$pm_or_am = 'PM';
				} elseif($time['0'] == 17) {
					$hour = '05';
					$pm_or_am = 'PM';
				} elseif($time['0'] == 18) {
					$hour = '06';
					$pm_or_am = 'PM';
				} elseif($time['0'] == 19) {
					$hour = '07';
					$pm_or_am = 'PM';
				} elseif($time['0'] == 20) {
					$hour = '08';
					$pm_or_am = 'PM';
				} elseif($time['0'] == 21) {
					$hour = '09';
					$pm_or_am = 'PM';
				} elseif($time['0'] == 22) {
					$hour = '10';
					$pm_or_am = 'PM';
				} elseif($time['0'] == 23) {
					$hour = '11';
					$pm_or_am = 'PM';
				}
			} else { // sinon c'est que c'est le matin
				$hour = $time['0']; // donc je laisse tel quel
				$pm_or_am = 'AM';
			}
			$return = str_replace('{%hour|12}', $hour, $return); // je change donc les variables
			$return = str_replace('{%PM_OR_AM}', $pm_or_am, $return);
		} elseif($if['0'] == 24) { // et si c'est du 24h
			$hour = $time['0']; // je laisse comme c'est en bdd
			$return = str_replace('{%hour|24}', $hour, $return); // et je remplace
		} else {
			return 'ERROR'; // si le format n'est pas reconnu
		} 
	} else { // sinon, si le message FORMAT_DATE n'est pas configuré
		$return = $date; // je laisse la date tel quel
	}
	return $return; // puis je retourne la date & l'heure
}

function password($password) {
	return sha1(md5($password));
}

function banner_server($language, $jsonapi) {
	if(isset($language['BANNER_SERVER'])) {
		$return = str_replace('{MOTD}', $jsonapi['getMOTD'], $language['BANNER_SERVER']);
		$return = str_replace('{VERSION}', $jsonapi['getVersion'], $return);
		$return = str_replace('{ONLINE}', $jsonapi['getPlayerList'], $return);
		$return = str_replace('{ONLINE_LIMIT}', $jsonapi['getPlayerMax'], $return);
		echo $return;
	} else {
		echo 'BANNER_SERVER';
	}
}

function unzip($file, $path, $name = 'install-zip') {
	if ( !is_file($file)) {
		$newUpdate = file_get_contents($file);
		if ( !is_dir($path.'/zip') ) {
			mkdir ($path.'/zip', 0777, true);
		}
		$dlHandler = fopen($path.'/zip/'.$name.'.zip', 'w+');
		if ( !fwrite($dlHandler, $newUpdate) ) { 
			return false; 
		}
		fclose($dlHandler);
	}

	$zip = new ZipArchive;
	$res = $zip->open($path.'/zip/'.$name.'.zip');
	if ($res === TRUE) {
	  $zip->extractTo($path);
	  $zip->close();
	  unlink($path.'/zip/'.$name.'.zip');
	  rmdir($path.'/zip');
	  clearDir($path.'/__MACOSX');
	  return true;
	} else {
	  return false;
	}
}

function clearDir($dossier) {
	$ouverture=opendir($dossier);
	if (!$ouverture) return false;
	while($fichier=readdir($ouverture)) {
		if ($fichier == '.' || $fichier == '..') continue;
			if (is_dir($dossier."/".$fichier)) {
				$r=clearDir($dossier."/".$fichier);
				if (!$r) return false;
			}
			else {
				$r=unlink($dossier."/".$fichier);
				if (!$r) return false;
			}
	}
closedir($ouverture);
$r=rmdir($dossier);
return true;
}

function clearFolder($folder)
{
	// 1 ouvrir le dossier
	$dossier=opendir($folder);
	//2)Tant que le dossier est aps vide
	while ($fichier = readdir($dossier))
	{
	        //3) Sans compter . et ..
	        if ($fichier != "." && $fichier != "..")
	        {
	                //On selectionne le fichier et on le supprime
	                $Vidage= $folder.$fichier;
	                unlink($Vidage);
	        }
	}
	//Fermer le dossier vide
	closedir($dossier);
}

?>