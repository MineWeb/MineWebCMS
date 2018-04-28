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

function cut($data, $how) {
	$return = substr($data, 0, $how);
	return (strlen($data) > $how) ? $return.'...' : $return;
}

// Fonction qui génére une classe parmis toute celle disponible pour les news. Permet une couleur aléatoire.
function rand_color_news() {
	$colors = array('border-top-color-dark-blue', 'border-top-color-dark-blue-2', 'border-top-color-yellow', 'border-top-color-dark-yellow', 'border-top-color-blue', 'border-top-color-magenta', 'border-top-color-green'); // toute les class disponible
	$color = rand(0, count($colors)-1); // génére un chiffre aléatoire entre 0 et le nombre de class
	return $colors[$color]; // et je retourne la class selon le chiffre aléatoire
}

// Fonction qui sert a retourner un texte sécurisé avant de l'afficher (exemple: contenu d'une news).
function before_display($content) {
	return htmlentities($content);
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
	  unlink($path.'/zip/'.$name.'.zip');
	  rmdir($path.'/zip');
	  return false;
	}
}

function clearDir($dossier) {
	if(file_exists($dossier) && is_dir($dossier)) {
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
	                unlink($folder.$fichier);
	        }
	}
	//Fermer le dossier vide
	closedir($dossier);
}

?>
