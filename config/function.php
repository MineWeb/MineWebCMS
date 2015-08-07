<?php
/**
* Fichier contenant toutes les fonctions
* Nécessaire au fonctionnement du site
* 
* @author Eywek
**/

// Fonction qui supprime des entrées dans un tableau (utilisé pour les plugins)s
function array_delete_value($array,$search) {
	$temp = array();
	foreach($array as $key => $value) {
		if($value!=$search) $temp[$key] = $value;
	}
	return $temp;
}

function password($password) {
	return hash('sha256', $password);
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

// Fonction qui sert a retourner un texte sécurisé avant de l'afficher (exemple: contenu d'une news).
function before_display($content) {
	$content = htmlentities($content);
	$content = stripcslashes($content);
	return $content;
}

function unzip($file, $path, $name = 'install-zip', $No_file_get_contents = false) {
	if ($No_file_get_contents === true OR !is_file($file)) {
		if(!$No_file_get_contents) {
			$newUpdate = file_get_contents($file);
		} else {
			$newUpdate = $file;
		}
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
	@$dossier=opendir($folder);
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

function rsa_encrypt($data, $publicKey) {
    $encrypted = '';
    $r = openssl_public_encrypt($data, $encrypted, $publicKey);
    return $r ? base64_encode($encrypted) : false;
}
 
function rsa_decrypt($data, $privateKey) {
    $decrypted = '';
    $r = openssl_private_decrypt(base64_decode($data), $decrypted, $privateKey);
    return $r ? $decrypted : false;
}

?>