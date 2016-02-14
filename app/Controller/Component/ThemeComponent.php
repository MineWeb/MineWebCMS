<?php

/**
* Class gérant les thèmes MineWeb
*
* @author Eywek
* @version 1.0.0
*/

class ThemeComponent extends Object {

  public $themesFolder;

  private $themesAvailable;
  private $themesInstalled;
  private $alreadyCheckValid;

  private $apiVersion = '1';

  private $controller;

  function __construct() {
    $this->themesFolder = ROOT.DS.'app'.DS.'View'.DS.'Themed';
  }

  function shutdown(&$controller) {}
  function beforeRender(&$controller) {}
  function beforeRedirect() {}
  function startup(&$controller) {}

  // Initialisation du composant

    function initialize(&$controller) {

      $this->controller =& $controller;
      $this->controller->set('Theme', $this);

    }

  // Récupérer les thèmes

    public function getThemesInstalled($api = true) {

      if(!empty($this->themesInstalled)) {
        return $this->themesInstalled;
      }

      // On set le dossier & on le scan
      $dir = $this->themesFolder;
      $themes = scandir($dir);
      if($themes !== false) {

        $themesList = (object) array();

        $bypassedFiles = array('.', '..', '.DS_Store', '__MACOSX'); // On met les fichiers que l'on ne considère pas comme un thème

        if($api) {
          $this->getThemesOnAPI(true);
        }

        foreach ($themes as $key => $slug) { // On parcours tout ce qu'on à trouvé dans le dossier
          if(!in_array($slug, $bypassedFiles) && $this->isValid($slug)) { // Si c'est pas un fichier que l'on ne doit pas prendre

            $config = $this->getConfig($slug);

            if(empty($config)) {
              continue;
            }

            $id = strtolower($config->author.'.'.$slug.'.'.$config->apiID);;

            $themesList->$id = $config;

            $checkSupported = $this->checkSupported($slug);
            $themesList->$id->supported = (empty($checkSupported)) ? true : false;
            $themesList->$id->supportedErrors = $checkSupported;

            if(isset($this->themesAvailable[$id])) {
              $themesList->$id->lastVersion = $this->themesAvailable[$id]['version'];
            }

          }
        }

        $this->themesInstalled = $themesList;
        return $themesList;

      } else {
        $this->log('Unable to scan theme folder.'); // on log ça
        return array(); // Impossible de scanner le dossier
      }
    }

  // Récupérer les thèmes sur MineWeb.org

    public function getThemesOnAPI($all = true) {

      $type = ($all) ? 'all' : 'free';

      if(!empty($this->themesAvailable[$type])) {
        return $this->themesAvailable[$type];
      }

      $url = ($all) ? 'http://mineweb.org/api/v'.$this->apiVersion.'/getAllThemes' : 'http://mineweb.org/api/v'.$this->apiVersion.'/getFreeThemes';

      // Get sur l'API de tous
        $getAllThemes = @file_get_contents($url);
        $getAllThemes = ($getAllThemes) ? json_decode($getAllThemes, true) : array();

      // Get sur l'API de ceux achetés
      if(!$all) {

        $secure = $this->getSecure();

        $getPurchasedThemes = @file_get_contents('http://mineweb.org/api/v'.$this->apiVersion.'/getPurchasedThemes/'.$secure['id']);
        $getPurchasedThemes = ($getPurchasedThemes) ? json_decode($getPurchasedThemes, true) : array();

        if(isset($getPurchasedThemes['status']) && $getPurchasedThemes['status'] == "success") {
          $getAllThemes += $getPurchasedThemes['success'];
        }

      }

      $this->themesAvailable[$type] = $getAllThemes;

      return $getAllThemes;

    }

  // Récupérer la configuration du thème dans son config.json

    public function getConfig($slug) {

      return json_decode(file_get_contents($this->themesFolder.DS.$slug.DS.'Config'.DS.'config.json'));

    }

  // Vérifier si le thème est valide (config)

    public function isValid($slug) {
      $slug = ucfirst($slug);

      $file = $this->themesFolder.DS.$slug; // On met le chemin pour aller le chercher

      if(file_exists($file)) { // on vérifie d'abord que le fichier existe bien

        if(is_dir($file)) { // être sur que c'est un dossier

          if(isset($this->alreadyCheckValid[$slug])) {
            return $this->alreadyCheckValid[$slug];
          }

          // Passons aux pré-requis des plugins.
            // Simple fichier
            $neededFiles = array('Config/config.json');
            foreach ($neededFiles as $key => $value) {
              if(!file_exists($file.DS.$value)) { // si le fichier existe bien
                $this->log('Theme "'.$slug.'" not valid! The file or folder "'.$file.DS.$value.'" doesn\'t exist! Please verify documentation for more informations.');
                $this->alreadyCheckValid[$slug] = false;
                return false; // on retourne false, le plugin est invalide et on log
              }
            }

            // Configuration valide (JSON)
            $needToBeJSON = array('Config/config.json');
            foreach ($needToBeJSON as $key => $value) {
              if(json_decode(file_get_contents($file.DS.$value)) === false || json_decode(file_get_contents($file.DS.$value)) === null) { // si le JSON n'est pas valide
                $this->log('Theme "'.$slug.'" not valid! The file "'.$file.DS.$value.'" is not at JSON format! Please verify documentation for more informations.');
                $this->alreadyCheckValid[$slug] = false;
                return false; // on retourne false, le plugin est invalide et on log
              }
            }

            // Que la configuration soit valide avec tout les key necessaires et leur type de value
            $config = json_decode(file_get_contents($file.DS.'Config'.DS.'config.json'), true);
            $needConfigKey = array('name' => 'string', 'slug' => 'string', 'author' => 'string', 'version' => 'string', 'apiID' => 'int', 'configurations' => 'array', 'supported' => 'array');
            foreach ($needConfigKey as $key => $value) {

              $key = (is_array(explode('-', $key))) ? explode('-', $key) : $key; // si c'est une key multi-dimensionnel
              if(is_array($key) && count($key) > 1) { // si la clé est multi-dimensionnel
                $configKey = $config;
                $multi = true; // De base c'est ok pour le multi-dimensionnel
                foreach ($key as $k => $v) { // on parcours les "sous-clés"
                  if(array_key_exists($v, $configKey)) {
                    $configKey = $configKey[$v]; // au fur et à mesure on avance dans la config
                  } else {
                    $multi = false; // C'est mort, il manque une clé on arrête tout.
                    break;
                  }
                }
              } else {
                $configKey = @$config[$key[0]]; // c'est pas multi-dimensionnel donc on met juste la clé
                $key = $key[0];
              }

              if((isset($multi) && $multi === true) || (!is_null($config) && !isset($multi) && array_key_exists($key, $config))) { // si le multi-dimensionnel est validé OU que c'est pas le multi-dimensionnel ET que la clé existe

                // on check le type de la clé
                $function = 'is_'.$value;
                if(!$function($configKey)) {

                  if(is_array($key)) { // Si c'est une clé multi-dimensionnel
                    $key = '["'.implode('"]["', $key).'"]';
                  }

                  $this->log('File : '.$slug.' is not a valid theme! The config is not complete! '.$key.' is not a good type ('.$value.' required).'); // la clé n'existe pas
                  $this->alreadyCheckValid[$slug] = false;
                  return false; // c'est pas le type demandé donc on retourne false et on log
                }

              } else {

                if(is_array($key)) { // Si c'est une clé multi-dimensionnel
                  $key = '["'.implode('"]["', $key).'"]';
                }

                $this->log('File : '.$slug.' is not a valid theme! The config is not complete! '.$key.' is not defined.'); // la clé n'existe pas
                $this->alreadyCheckValid[$slug] = false;
                return false;
              }

            } // fin du foreach des clés indispensable

            // Valider la version (qu'elle soit correcte pour les prochaines comparaison)
            $testVersion = explode('.', $config['version']);
            if(count($testVersion) < 3 && count($testVersion) > 4) { // On autorise que du type 1.0.0 ou 1.0.0.0
              $this->log('File : '.$slug.' is not a valid theme! The version configured is not at good format !'); // la clé n'existe pas
              $this->alreadyCheckValid[$slug] = false;
              return false;
            }

            $this->alreadyCheckValid[$slug] = true;
            return true;

        } else {
          $this->log('File : '.$file.' is not a folder! Theme not valid! Please remove this file from de theme folder.'); // ce n'est pas un dossier
          return false;
        }

      } else {
        $this->log('Themes folder : '.$file.' doesn\'t exist! Theme not valid!'); // Le fichier n'existe pas
        return false;
      }
    }

  // Vérifier si ce que supporte le thème n'est pas outdated (au dessus de la version)

    public function checkSupported($slug) {

      $config = $this->getConfig($slug); // on récup la config

      $supported = (is_object($config) && isset($config->supported) && !empty($config->supported)) ? $config->supported : null;

      if(empty($supported)) {
        return array();
      }

      $return = array();

      foreach ($supported as $type => $version) { // on parcours tout les pré-requis

        if($type == "CMS") { // Si c'est sur le cms

          $this->Configuration = $this->controller->Configuration;

          $versionExploded = explode(' ', $version);
          $operator = (count($versionExploded) == 2) ? $versionExploded[0] : '='; // On récupére l'opérateur et la version qui sont définis
          $versionNeeded = (count($versionExploded) == 2) ? $versionExploded[1] : $version;

          if(!version_compare($this->Configuration->get('version'), $versionNeeded, $operator)) { // Si la version du CMS ne correspond pas à ce qui est demandé
            $return['CMS'] = $operator.' '.$versionNeeded;
          }

        } elseif(count(explode('--', $type)) == 2) { // si c'est un pré-requis normal

          $versionExploded = explode(' ', $version);
          $operator = (count($versionExploded) == 2) ? $versionExploded[0] : '='; // On récupére l'opérateur et la version qui sont définis
          $versionNeeded = (count($versionExploded) == 2) ? $versionExploded[1] : $version;

          $typeExploded = explode('--', $type);
          $type = $typeExploded[0];             // On veux savoir le type + l'id de ce qui est concerné
          $id = $typeExploded[1];

          if($type == "plugin") {

            // C'est un plugin donc il nous faut sa version
            $search = $this->controller->EyPlugin->findPluginByID($id);
            if(!empty($search)) { // si on a trouvé quelque chose

              $pluginVersion = $this->controller->EyPlugin->getPluginConfig($search->slug);
              if(!version_compare($pluginVersion->version, $versionNeeded, $operator)) { // Si la version du CMS ne correspond pas à ce qui est demandé
                $return[$search->slug] = $operator.' '.$versionNeeded;
              }

            } else {
              $return[$search[$id]['slug']] = $operator.' '.$versionNeeded;
            }

          }

        } // sinon on s'en fou

      }

      return $return; // De base c'est bon

    }

  // Installation d'un thème depuis l'API

    public function install($apiID) {

      $return = $this->sendToAPI(
                  array(),
                  'get_theme/'.$apiID,
                  true
                );

      if($return['code'] == 200) {
        $return_json = json_decode($return['content'], true);
        if(!$return_json) {
          return false;
        }
      } else {
        return false;
      }

      if(unzip($return_json, $this->themesFolder, 'install-theme-'.$apiID.'-zip', true)) {
        clearDir($this->themesFolder.DS.'__MACOSX');
        return true;
      } else {
        return false;
      }

    }

  // Mise à jour d'un thème depuis l'API

    public function update($apiID) {

      $return = $this->sendToAPI(
                  array(),
                  'get_theme/'.$apiID,
                  true
                );

      if($return['code'] == 200) {
        $return_json = json_decode($return['content'], true);
        if(!$return_json) {
          return false;
        }
      } else {
        return false;
      }

      if(unzip($return_json, $this->themesFolder, 'update-theme-'.$apiID.'-zip', true)) {
        return true;
        clearDir($this->themesFolder.DS.'__MACOSX');
      } else {
        return false;
      }

    }

  // Retourne la config personnalisé + le nom du thème selon son slug

    public function getCustomData($slug) {

      if($slug == "default") {

        $config = file_get_contents(ROOT.DS.'config'.DS.'theme.default.json');
        $config = json_decode($config, true);
        $theme_name = "Bootstrap";

      } else {

        $themesInstalled = $this->getThemesInstalled(false);
        foreach ($themesInstalled as $id => $data) {
          if($data->slug == $slug) {
            $theme_name = $data->name;
            $config = $data->configurations;
            break;
          }
        }

      }
      if(isset($config)) {
        $config = (array) $config;
      }

      return (isset($theme_name)) ? array($theme_name, $config) : false;

    }

  // Traite et enregistre les données passé pour la configurations perso d'un thème

    public function processCustomData($slug, $request) {

      if($slug == "default") { // thème par défaut
        $data = json_encode($request->data, JSON_PRETTY_PRINT);
        $fp = @fopen(ROOT.DS.'config'.DS.'theme.default.json',"w+");
        if(!$fp) {
          $this->log('Unable to save theme config. File not writable.');
          return false;
        }
        fwrite($fp, $data);
        fclose($fp);

        return;
      }

      // Les components utiles

        $this->Util = $this->controller->Util;
        $this->Session = $this->controller->Session;
        $this->Lang = $this->controller->Lang;

      // on détermine si le thème est installé
        $finded = false;
        $themesInstalled = $this->getThemesInstalled(false);
        foreach ($themesInstalled as $id => $data) {
          if($data->slug == $slug) {
            $finded = true;
            $config = $data->configurations;
            break;
          }
        }

        if(!$finded) { return; } //on a rien trouvé

      // On traite les données

        if(!isset($request->data['img_edit'])) {
          $isValidImg = $this->Util->isValidImage($request, array('png', 'jpg', 'jpeg'));

          if(!$isValidImg['status'] && $isValidImg['msg'] != $this->Lang->get('FORM__EMPTY_IMG')) {
            $this->Session->setFlash($isValidImg['msg'], 'default.error');
            return false;
          } else {
            if(isset($isValidImg['infos'])) {
              $infos = $isValidImg['infos'];
            } else {
              $infos = false;
            }
          }

          if($infos) {
            $url_img = WWW_ROOT.'img'.DS.'uploads'.DS.'theme_logo.'.$infos['extension'];

            if(!$this->Util->uploadImage($request, $url_img)) {
              $this->Session->setFlash($this->Lang->get('FORM__ERROR_WHEN_UPLOAD'), 'default.error');
              return false;
            }

            $request->data['logo'] = Router::url('/').'img'.DS.'uploads'.DS.'theme_logo.'.$infos['extension'];
          } else {
            $request->data['logo'] = false;
          }
        } else {
          $request->data['logo'] = $config['logo'];
        }

        $json = json_decode(file_get_contents($this->themesFolder.DS.$slug.DS.'Config'.DS.'config.json'));

        $json->configurations = $request->data;

        $data = json_encode($json, JSON_PRETTY_PRINT);
        $fp = @fopen($this->themesFolder.DS.$slug.DS.'Config'.DS.'config.json',"w+");
        if(!$fp) {
          $this->log('Unable to save theme config. File not writable.');
          return false;
        }
        fwrite($fp, $data);
        fclose($fp);

        return true;


    }

}
