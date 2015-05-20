<?php

class LangComponent extends Object {
  	
  	public $components = array('Cookie');

	function shutdown(&$controller) {}
	function beforeRender(&$controller) {}
  	function beforeRedirect() {}
	function initialize(&$controller) {
		$this->controller =& $controller;
		$this->controller->set('Lang', new LangComponent());
	}
    function startup(&$controller) {}

    function get_lang() {
    	/*if(isset($_COOKIE['language'])) {
	    	$language = $_COOKIE['language'];
		} elseif(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
			$language = $language{0}.$language{1};
			if($language == "en") {
				$language = "en";
			} elseif($language == "fr") {
				$language = "fr";
			} else {
				$language = "en";
			}
		} else {
			setcookie('language', 'fr');
			$language = 'fr';
		}
		return $language;*/
		App::import('Component', 'Configuration');
		$this->Configuration = new ConfigurationComponent();
		$lang = $this->Configuration->get('lang');
		if(!empty($lang)) {
			return $this->Configuration->get('lang');
		} else {
			return 'fr';
		}
    }

    function getall() {
    	$language = $this->get_lang();

		if(file_get_contents(ROOT.'/lang/'.$language.'.json')) {
			$language_file = file_get_contents(ROOT.'/lang/'.$language.'.json');
			$language_file = json_decode($language_file, true);
		} else {
			$language_file = file_get_contents(ROOT.'/lang/fr.json');
			$language_file = json_decode($language_file, true);
		}

		
		return $language_file;
    }

    function setall($data) {
    	$language = $this->get_lang();

		if(file_get_contents(ROOT.'/lang/'.$language.'.json')) {
			$data = json_encode($data, JSON_PRETTY_PRINT);
			$fp = fopen(ROOT.'/lang/'.$language.'.json',"w+");
			fwrite($fp, $data);
			fclose($fp);
		} else {
			$data = json_encode($data, JSON_PRETTY_PRINT);
			$fp = fopen(ROOT.'/lang/fr.json',"w+");
			fwrite($fp, $data);
			fclose($fp);
		}
    }

    function get($msg) {

    	$language = $this->get_lang();

		if(file_get_contents(ROOT.'/lang/'.$language.'.json')) {
			$language_file = file_get_contents(ROOT.'/lang/'.$language.'.json');
			$language_file = json_decode($language_file, true);
		} else {
			$language_file = file_get_contents(ROOT.'/lang/fr.json');
			$language_file = json_decode($language_file, true);
		}

		if(isset($language_file[$msg])) { // et si le msg existe
			return $language_file[$msg]; // je retourne le msg config
		} else { // sinon
			return $msg; // le msg tel quel
		}
	}

	function banner_server($jsonapi) {
		$language = $this->get_lang();

		if(file_get_contents(ROOT.'/lang/'.$language.'.json')) {
			$language_file = file_get_contents(ROOT.'/lang/'.$language.'.json');
			$language_file = json_decode($language_file, true);
		} else {
			$language_file = file_get_contents(ROOT.'/lang/fr.json');
			$language_file = json_decode($language_file, true);
		}

		if(isset($language_file['BANNER_SERVER'])) {
			$return = str_replace('{MOTD}', $jsonapi['getMOTD'], $language_file['BANNER_SERVER']);
			$return = str_replace('{VERSION}', $jsonapi['getVersion'], $return);
			$return = str_replace('{ONLINE}', $jsonapi['getPlayerCount'], $return);
			$return = str_replace('{ONLINE_LIMIT}', $jsonapi['getPlayerMax'], $return);
			return $return;
		} else {
			return 'BANNER_SERVER';
		}
	}

	function date($date) {

		$language = $this->get_lang();

		if(file_get_contents(ROOT.'/lang/'.$language.'.json')) {
			$language_file = file_get_contents(ROOT.'/lang/'.$language.'.json');
			$language_file = json_decode($language_file, true);
		} else {
			$language_file = file_get_contents(ROOT.'/lang/fr.json');
			$language_file = json_decode($language_file, true);
		}

		if(isset($language_file['FORMAT_DATE'])) { // si le format de la date est configuré je fais les actions suivantes 
			$date = explode(' ', $date); // j'explode les espaces pour séparé date & heure
			$time = explode(':', $date['1']); // ensuite je sépare tout les chiffre de la date
			$date = explode('-', $date['0']); // puis tout ceux de l'heure
			$return = str_replace('{%day}', $date['2'], $language_file['FORMAT_DATE']); // puis je remplace les variable de la config lang.php par les chiffres | Le jour 
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

 }

  ?>