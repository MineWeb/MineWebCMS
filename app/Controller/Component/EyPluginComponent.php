<?php

/**
* Class gérant les extensions/plugins MineWeb
*
* @author Eywek
* @version 1.0.0
*/

class EyPluginComponent extends Object {

  public $pluginsInFolder = array();
  public $pluginsInDB = array();

  public $pluginsFolder;
  private $apiVersion = '1';

  public $pluginsLoaded;

  function __construct() {
    $this->pluginsFolder = ROOT.DS.'app'.DS.'Plugin';
  }

  function shutdown(&$controller) {}
  function beforeRender(&$controller) {}
  function beforeRedirect() {}
  function startup(&$controller) {}

  // Initialisation du composant


    function initialize(&$controller) {

    // Initialisation des variables importantes
      $this->pluginsInFolder = $this->getPluginsInFolder();
      $this->pluginsInDB = $this->getPluginsInDB();

      // Installons les plugins non installés & présent dans le dossier plugins
      $this->checkIfNeedToBeInstalled($this->pluginsInFolder, $this->pluginsInDB);

      // Supprimons les plugins installés en base de données mais plus présent dans le dossier
      $this->checkIfNeedToBeDeleted($this->pluginsInFolder, $this->pluginsInDB);

      $this->pluginsLoaded = $this->loadPlugins();

    }

  // Retourne la config JSON d'un plugin

    public function getPluginConfig($slug) {
      $config = @file_get_contents($this->pluginsFolder.DS.$slug.DS.'config.json'); // On récup la config JSON

      if($config !== false) { // Si c'est pas false

        return json_decode($config); // On retourne ou le JSON décodé ou false (pour un pb)

      }

      return false;
    }

  // Chargement des plugins installés et de leurs configuration - Retourne un objet

    public function loadPlugins() {

      // On récupére le modal
      $PluginModel = ClassRegistry::init('Plugin');

      // On cherche tout les plugins installés en db
      $dbPlugins = $PluginModel->find('all');

      $pluginList = (object) array(); // On met ça en object vide pour l'instant

      foreach ($dbPlugins as $key => $value) { // On les parcours tous

        $v = $value['Plugin']; // On met ça comme ça pour plus de simplicité

        $config = $this->getPluginConfig($v['name']); // On charge la config

        $id = strtolower($v['author'].'.'.$v['name'].'.'.$config->apiID); // on fais l'id - tout en minuscule

        $pluginList->$id = (object) array(); // on initialize les données
        $pluginList->$id = $config; // On met l'object config dedans
        $pluginList->$id->DBid = $v['id']; // on met l'id de la base de donnée
        $pluginList->$id->DBinstall = $v['created']; // on met quand on l'a installé sur la bdd
        $pluginList->$id->active = ($v['state']) ? true : false; // On met l'object config dedans
        $pluginList->$id->tables = unserialize($v['tables']);

      }

      return $pluginList;

    }

  // Retourner les plugins chargés/installés & activés

    public function getPluginsActive() {
      $plugins = $this->loadPlugins(); // on prend les plugins installés

      $pluginList = (object) array(); // On met ça en object vide pour l'instant

      foreach ($plugins as $key => $value) {
        if($value->active) { // si il est activé
          $pluginList->$key = $value; // on ajoute dans la liste
        }
      }

      return $pluginList; // on retourne la list
    }

  // Récupération des plugins dans le dossier app/Plugin

    private function getPluginsInFolder() {

      // On set le dossier & on le scan
      $dir = $this->pluginsFolder;
      $plugins = scandir($dir);
      if($plugins !== false) {

        $bypassedFiles = array('.', '..', '.DS_Store', '__MACOSX'); // On met les fichiers que l'on ne considère pas comme un plugin
        $pluginsList = array(); // On dis que de base la liste est vide

        foreach ($plugins as $key => $value) { // On parcours tout ce qu'on à trouvé dans le dossier
          if(!in_array($value, $bypassedFiles) && $this->isValid($value)) { // Si c'est pas un fichier que l'on ne doit pas prendre & qu'il est valide
            $pluginsList[] = $value; // On l'ajoute dans la liste
          }
        }

        return $pluginsList;

      } else {
        $this->log('Unable to scan plugins folder.'); // on log ça
        return array(); // Impossible de scanner le dossier
      }
    }

  // Récupération des plugins installés en base de données

    private function getPluginsInDB() {

      // On récupére le modal
      $PluginModel = ClassRegistry::init('Plugin');
      // On cherche tout
      $search = $PluginModel->find('all');

      // De base la liste est vide
      $pluginsList = array();

      if(!empty($search)) { // si c'est pas vide (pour éviter l'erreur)
        foreach ($search as $key => $value) { // on parcours tout pour récupérer leurs noms
          $pluginsList[] = $value['Plugin']['name'];
        }
      }

      return $pluginsList;
    }

  // Vérifier si le plugin donné (nom/chemin) est bien un dossier contenant tout les pré-requis d'un plugin

    private function isValid($slug) {

      $file = $this->pluginsFolder.DS.$slug; // On met le chemin pour aller le chercher

      if(file_exists($file)) { // on vérifie d'abord que le fichier existe bien

        if(is_dir($file)) { // être sur que c'est un dossier

          // Passons aux pré-requis des plugins.
            // Simple fichier
            $neededFiles = array('Config/routes.php', 'Config/bootstrap.php', 'lang/fr.json', 'lang/en.json', 'Controller', 'Controller/Component', 'Model', 'Model/Behavior', 'View', 'View/Helper', 'View', 'View/Layouts', 'config.json', 'SQL/tables.json', 'SQL/tables.json');
            foreach ($neededFiles as $key => $value) {
              if(!file_exists($file.DS.$value)) { // si le fichier existe bien
                $this->log('Plugin "'.$slug.'" not valid ! The file or folder "'.$file.DS.$value.'" doesn\'t exist ! Please verify documentation for more informations.');
                return false; // on retourne false, le plugin est invalide et on log
              }
            }

            // Configuration valide (JSON)
            $needToBeJSON = array('lang/fr.json', 'lang/en.json', 'config.json', 'SQL/tables.json');
            foreach ($needToBeJSON as $key => $value) {
              if(json_decode(file_get_contents($file.DS.$value)) === false) { // si le JSON n'est pas valide
                $this->log('Plugin "'.$slug.'" not valid ! The file "'.$file.DS.$value.'" is not at JSON format ! Please verify documentation for more informations.');
                return false; // on retourne false, le plugin est invalide et on log
              }
            }

            // Que la configuration soit valide avec tout les key necessaires et leur type de value
            $config = json_decode(file_get_contents($file.DS.'config.json'), true);
            $needConfigKey = array('name' => 'string', 'slug' => 'string', 'nav' => 'bool', 'admin' => 'bool', 'author' => 'string', 'version' => 'string', 'apiID' => 'int', 'useEvents' => 'bool', 'permissions' => 'array', 'permissions-available' => 'array', 'permissions-default' => 'array', 'requirements' => 'array');
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

                  $this->log('File : '.$slug.' is not a valid plugin ! The config is not complete ! '.$key.' is not a good type ('.$value.' required).'); // la clé n'existe pas
                  return false; // c'est pas le type demandé donc on retourne false et on log
                }

              } else {

                if(is_array($key)) { // Si c'est une clé multi-dimensionnel
                  $key = '["'.implode('"]["', $key).'"]';
                }

                $this->log('File : '.$slug.' is not a valid plugin ! The config is not complete ! '.$key.' is not defined.'); // la clé n'existe pas
                return false;
              }

            } // fin du foreach des clés indispensable

            // Valider la version (qu'elle soit correcte pour les prochaines comparaison)
            $testVersion = explode('.', $config['version']);
            if(count($testVersion) < 3 && count($testVersion) > 4) { // On autorise que du type 1.0.0 ou 1.0.0.0
              $this->log('File : '.$slug.' is not a valid plugin ! The version configured is not at good format !'); // la clé n'existe pas
              return false;
            }

            // Vérifier que les tables sont bien préfixé par le slug
            $tablesJSON = json_decode(file_get_contents($file.DS.'SQL'.DS.'tables.json'), true)['list']; // on récupére la liste des tables
            if(!empty($tablesJSON)) {
              foreach ($tablesJSON as $key => $value) { // on les parcours si elles sont pas vides

                $valueExploded = explode('__', $value); // on explode le nom

                if(count($valueExploded) <= 1 || $valueExploded[0] != strtolower($slug)) { // si c'est un array de moins d'une key (donc pas de prefix) OU que la première clé n'est pas le slug
                  $this->log('File : '.$slug.' is not a valid plugin ! SQL tables need to be prefixed by slug.'); // ce n'est pas un dossier
                  return false;
                }

              }
            }

            return true;  // ca s'est bien passé
        } else {
          $this->log('File : '.$file.' is not a folder ! Plugin not valid ! Please remove this file from de plugin folder.'); // ce n'est pas un dossier
          return false;
        }

      } else {
        $this->log('Plugins folder : '.$file.' doesn\'t exist ! Plugin not valid !'); // Le fichier n'existe pas
        return false;
      }

    }

  // Installation des plugins non installés

    private function checkIfNeedToBeInstalled($pluginsInFolder, $pluginsInDB) {

      if(!empty($pluginsInFolder)) { // Si y'a des plugins dans le dossier (prêt à être éventuellement installé)

        $diff = array_diff($pluginsInFolder, $pluginsInDB); // On calcule la différence entre les plugins dans le dossier et les plugins installés

        if(!empty($diff)) { // si y'a une différence

          foreach ($diff as $key => $value) { // on parcours les différences


            // Fonction d'installation
            $this->install($value);

          }

        }

      }

      return false; // on a rien à faire.
    }

  // Suppression des plugins non installés

    private function checkIfNeedToBeDeleted($pluginsInFolder, $pluginsInDB) {

      if(!empty($pluginsInFolder)) { // Si y'a des plugins dans le dossier (prêt à être éventuellement installé)

        $diff = array_diff($pluginsInDB, $pluginsInFolder); // On calcule la différence entre les plugins dans le dossier et les plugins installés

        if(!empty($diff)) { // si y'a une différence

          foreach ($diff as $key => $value) { // on parcours les différences

            // On peux le supprimer du coup

              $this->delete($value, true); // On lance la fonction de suppression

          }

          $this->refreshPermissions(); // On refresh les perms pour ne mettre que celle qu'on à besoin
          Cache::clear(false, '_cake_core_'); // On clear le cache

        }

      }

      return false; // on a rien à faire.
    }

  // Fonction de suppression

    public function delete($slug, $isForced = false) {
      // Si y'a un MainComponent
      if(file_exists($this->pluginsFolder.$slug.DS.'Controller'.DS.'Component'.DS.'MainComponent.php')) { // On fais le onDisable si il existe
        App::uses('MainComponent', 'Plugin'.DS.$slug.DS.'Controller'.DS.'Component');
        $this->Main = new MainComponent();
        $this->Main->onDisable(); // on le lance
      }

      // On récupére le modal
      $PluginModel = ClassRegistry::init('Plugin');

      $search = $PluginModel->find('first', array('conditions' => array('name' => $slug))); // on le cherche pour récupérer les tables
      if(!empty($search)) {

        $tables = unserialize($search['Plugin']['tables']); // pour récupérer les tables installées

        App::import('Model', 'ConnectionManager');
        $con = new ConnectionManager;             // charger la db principal
        $cn = $con->getDataSource('default');


        foreach ($tables as $k => $v) { // on les parcours et on les supprimes
          if(!empty($v)) {
            $cn->query("DROP TABLE ".$v); // on les supprimes
          }
        }

        $PluginModel->delete($search['Plugin']['id']); // On supprime le plugin de la db

        clearDir($this->pluginsFolder.DS.$slug);
        CakePlugin::unload($slug); // On unload sur cake
        Cache::clear(false, '_cake_core_'); // On clear le cache
      }

    }

  // Fonction de download (pré-installation)

    public function download($apiID, $slug, $install = false) {
        // get du zip sur mineweb.org
      $url = 'http://mineweb.org/api/get_plugin/'.$apiID;
      $secure = file_get_contents(ROOT.'/config/secure');
      $secure = json_decode($secure, true);
      $postfields = array(
        'id' => $secure['id'],
          'key' => $secure['key'],
          'domain' => Router::url('/', true)
      );

      $postfields = json_encode($postfields);
      $post[0] = rsa_encrypt($postfields, '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCvFK7LMlAnF8Hzmku9WGbHqYNb
ehNueKDbF/j4yYwf8WqIizB7k+S++SznqPw3KzeHOshiPfeCcifGzp0kI43grWs+
nuScYjSuZw9FEvjDjEZL3La00osWxLJx57zNiEX4Wt+M+9RflMjxtvejqXkQoEr/
WCqkx22behAGZq6rhwIDAQAB
-----END PUBLIC KEY-----');

      $curl = curl_init();

      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_COOKIESESSION, true);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

      $return = curl_exec($curl);
      curl_close($curl);

      if(!preg_match('#Errors#i', $return)) {
        $return_json = json_decode($return, true);
        if(!$return_json) {
          $zip = $return;
        } elseif($return_json['status'] == "error") {

          $LangComponent = new LangComponent();
          SessionComponent::setFlash($LangComponent->get('ERROR__PLUGIN_CANT_BE_DOWNLOADED'), 'default.error');

          return false;
        }
      } else {

        $LangComponent = new LangComponent();
        SessionComponent::setFlash($LangComponent->get('ERROR__PLUGIN_CANT_BE_DOWNLOADED'), 'default.error');

        return false;
      }

      if($install) {
        if(unzip($zip, $this->pluginsFolder, 'install-zip', true)) { // on dé-zip tout
          $this->install($slug, true);
          return;
        }
      } else {
        return $zip;
      }

      $LangComponent = new LangComponent();
      SessionComponent::setFlash($LangComponent->get('ERROR__PLUGIN_CANT_BE_DOWNLOADED'), 'default.error');

      return false;
    }

  // Fonction d'installation

    public function install($slug, $downloaded = false) {
      if($this->isValid($slug)) { // Si le plugin est valide

        if($this->requirements($slug)) { // Si tout les pré-requis sont réunis pour le plugins

          // On peux l'installer du coup
            $this->addTables($slug); // On ajoute les tables

            // On récupére la configuration & les noms des tables ajoutées
            $config = json_decode(file_get_contents($this->pluginsFolder.DS.$slug.DS.'config.json'));
            $tablesName = json_decode(file_get_contents($this->pluginsFolder.DS.$slug.DS.'SQL/tables.json'), true)['list']; // on décode le JSON et on prend l'array dans list

            // On ajoute les permissions
            $this->addPermissions($config->permissions); // On ajoute les permissions

            // On récupére le modal
            $PluginModel = ClassRegistry::init('Plugins');

            // On l'ajoute dans la base de données
            $PluginModel->create();
            $PluginModel->set(array(
              'apiID' => $config->apiID,
              'name' => $slug,
              'author' => $config->author,
              'version' => $config->version,
              'tables' => serialize($tablesName)
            ));
            $PluginModel->save(); // On sauvegarde le tout

            // Si y'a un MainComponent
            if(file_exists($this->pluginsFolder.$slug.DS.'Controller'.DS.'Component'.DS.'MainComponent.php')) { // On fais le onEnable si il existe
              App::uses('MainComponent', 'Plugin'.DS.$slug.DS.'Controller'.DS.'Component');
              $this->Main = new MainComponent();
              $this->Main->onEnable(); // on le lance
            }

            CakePlugin::load(array($slug => array('routes' => true, 'bootstrap' => true))); // On load sur cake

        } else {
          if($downloaded) {
            clearDir($this->pluginsFolder.DS.$slug); // On supprime ce qu'on a dl

            CakePlugin::unload($slug); // On unload sur cake
            Cache::clear(false, '_cake_core_'); // On clear le cache

            $LangComponent = new LangComponent();
            SessionComponent::setFlash($LangComponent->get('ERROR__PLUGIN_REQUIREMENTS'), 'default.error');
          }
        }

      } else {
        if($downloaded) {
          clearDir($this->pluginsFolder.DS.$slug); // On supprime ce qu'on a dl

          CakePlugin::unload($slug); // On unload sur cake
          Cache::clear(false, '_cake_core_'); // On clear le cache

          $LangComponent = new LangComponent();
          SessionComponent::setFlash($LangComponent->get('ERROR__PLUGIN_NOT_VALID'), 'default.error');
        }
      }
    }

  // Fonction d'update

    public function update($apiID, $slug) {

      // on récup la config du plugin qu'on veux installé
      $config = $this->getPluginFromAPI($apiID);

      // les requirements
      if(!empty($config) && $config !== false && $this->requirements($slug, $config)) { // si on a bien les pré-requis

        $download = $this->download($apiID, $slug);
        if($download !== false) { // si on à bien dl

          CakePlugin::unload($slug); // On unload sur cake pour éviter des erreurs

          App::import('Component', 'UpdateComponent');
          $this->Update = new UpdateComponent();         // On importe le composant Update
          if($this->Update->plugin($download, $slug, $apiID)) {

            $pluginConfig = json_decode(file_get_contents($this->pluginsFolder.DS.$slug.DS.'config.json'), true);

            $pluginVersion = $pluginConfig['version']; // récupére la nouvelle version
            $pluginTables = json_decode(file_get_contents($this->pluginsFolder.DS.$slug.DS.'SQL/tables.json'), true)['list']; // on décode le JSON et on prend l'array dans list

            // On récupére le modal
            $PluginModel = ClassRegistry::init('Plugins');

            // On récup l'ID du plugin
            $pluginDBID = $PluginModel->find('first', array('name' => $slug))['Plugin']['id'];

            // On update dans la base de donnée
            $PluginModel->read(null, $pluginDBID);
            $PluginModel->set(array('version' => $pluginVersion, 'tables' => serialize($pluginTables)));
            $PluginModel->save();

            // Si y'a un fichier d'update à faire, on l'éxécute
            if(file_exists(ROOT.DS.'temp'.DS.$slug.'_update.php')) {
              // on l'inclue pour l'exec
              try {
                include(ROOT.DS.'temp'.DS.$slug.'_update.php');
              } catch(Exception $e) {
                // y'a eu une erreur
                $this->log('Error on plugin update ('.$slug.') - '.$e->getMessage());
              }
              unlink(ROOT.DS.'temp'.DS.$slug.'_update.php'); // on le supprime
            }

            $this->refreshPermissions(); // On met les jours les permissions

            Cache::clear(false, '_cake_core_'); // vidons le cache
            CakePlugin::load(array($slug => array('routes' => true, 'bootstrap' => true))); // On load sur cake

          }
        }
        return false;
      } else {
        $LangComponent = new LangComponent();
        SessionComponent::setFlash($LangComponent->get('ERROR__PLUGIN_REQUIREMENTS'), 'default.error');
      }
    }

  // Ajouter les tables des plugins

    private function addTables($name) {
      $tables = file_get_contents($this->pluginsFolder.DS.$name.DS.'SQL/tables.sql'); // On récupérer les tables

      if(!empty($tables)) { // Si le fichier n'est pas vide

        $tables = explode("\n\n", $tables); // On split toutes les requêtes

        App::import('Model', 'ConnectionManager');
        $con = new ConnectionManager;             // On récupére la bdd principale
        $cn = $con->getDataSource('default');

        foreach ($tables as $do) { // On parcours les requêtes à faire
          if(!empty($do)) {
            $cn->query($do); // On les fais
          }
        }

      }
    }

  // Fonctions de recherche parmis les plugins chargés

    public function findPluginsByName($name) {
      $plugins = array();
      $pluginsLoaded = $this->loadPlugins();
      foreach ($pluginsLoaded as $id => $data) {
        if($data->name == $name) {
          $plugins[$id] = $data;
        }
      }
      return $plugins;
    }

    public function findPluginBySlug($slug) {
      $plugin = NULL;
      $pluginsLoaded = $this->loadPlugins();
      foreach ($pluginsLoaded as $id => $data) {
        if($data->slug == $slug) {
          $plugin = $data;
          $plugin->id = $id;
        }
      }
      return $plugin;
    }

    public function findPluginByApiID($apiId) {
      $plugin = null;
      $pluginsLoaded = $this->loadPlugins();
      foreach ($pluginsLoaded as $id => $data) {
        if($data->apiID == $apiId) {
          $plugin = $data;
          $plugin->id = $id;
        }
      }
      return $plugin;
    }

    public function findPluginByID($id) {
      $this->pluginsLoaded = $this->loadPlugins();
      return (isset($this->pluginsLoaded->$id)) ? $this->pluginsLoaded->$id : (object) array();
    }

    public function findPluginsByAuthor($author) {
      $plugins = array();
      $pluginsLoaded = $this->loadPlugins();
      foreach ($pluginsLoaded as $id => $data) {
        if($data->author == $author) {
          $plugins[$id] = $data;
        }
      }
      return $plugins;
    }

  // Vérifier si un plguin est installé

    public function isInstalled($id) { // on le recherche avec son ID (auteur.name.apiid)
      return !empty($this->findPluginByID($id));
    }

  // Récupérer les plugins ou la navbar est activé (pour la nav)

    public function findPluginsWithNavbar() {
      $plugins = array();
      $pluginsLoaded = $this->loadPlugins();
      foreach ($pluginsLoaded as $id => $data) {
        if($data->navbar) {
          $plugins[$id] = $data;
        }
      }
      return $plugins;
    }

  // Changer d'état un plugin

    public function enable($dbID) {

      $PluginsModel = ClassRegistry::init('Plugin'); // On charge le model

      $PluginsModel->read(null, $dbID);
      $PluginsModel->set(array('state' => 1));  // On change l'état dans la bdd
      $PluginsModel->save();

      $plugin = $PluginsModel->find('first', array('id' => $dbID)); // On récupére le nom
      $pluginName = $plugin['Plugin']['name'];

      if(file_exists($this->pluginsFolder.DS.$pluginName.DS.'Controller'.DS.'Component'.DS.'MainComponent.php')) {
        App::uses('MainComponent', $this->pluginsFolder.DS.$pluginName.DS.'Controller'.DS.'Component');
        $this->Main = new MainComponent();        // On lance l'event onEnable
        $this->Main->onEnable();
      }

      CakePlugin::load(array($pluginName => array('routes' => true, 'bootstrap' => true))); // On load sur cake
    }

    public function disable($dbID) {

      $PluginsModel = ClassRegistry::init('Plugin'); // On charge le model

      $PluginsModel->read(null, $dbID);
      $PluginsModel->set(array('state' => 0));  // On change l'état dans la bdd
      $PluginsModel->save();

      $plugin = $PluginsModel->find('first', array('id' => $dbID)); // On récupére le nom
      $pluginName = $plugin['Plugin']['name'];

      if(file_exists($this->pluginsFolder.DS.$pluginName.DS.'Controller'.DS.'Component'.DS.'MainComponent.php')) {
        App::uses('MainComponent', $this->pluginsFolder.DS.$pluginName.DS.'Controller'.DS.'Component');
        $this->Main = new MainComponent();        // On lance l'event onEnable
        $this->Main->onDisable();
      }

      CakePlugin::unload($pluginName); // On unload sur cake

      Cache::clear(false, '_cake_core_'); // vidons le cache

    }

  // Vérifie les pré-requis d'un plugin

    private function requirements($name, $config = false) {

      if(!$config) { // si on a pas la config qui est set (donnée par l'API)
        $config = $this->getPluginConfig($name); // on récup la config
      }

      if(isset($config->requirements) && !empty($config->requirements)) {
        return false;
      }

      if(is_object($config)) {
        $requirements = $config->requirements;
      } else {
        $requirements = $config['requirements'];
      }

      foreach ($requirements as $type => $version) { // on parcours tout les pré-requis

        if($type == "CMS") { // Si c'est sur le cms

          App::import('Component', 'ConfigurationComponent'); // On charge le component d'update
          $this->Configuration = new ConfigurationComponent();

          $versionExploded = explode(' ', $version);
          $operator = $versionExploded[0]; // On récupére l'opérateur et la version qui sont définis
          $versionNeeded = $versionExploded[1];

          if(!version_compare($this->Configuration->get('version'), $versionNeeded, $operator)) { // Si la version du CMS ne correspond pas à ce qui est demandé
            $this->log('Plugin : '.$name.' can\'t be installed, CMS version need to be '.$operator.' '.$versionNeeded.' !');
            return false; // On arrête tout
          }

        } elseif(count(explode('--', $type)) == 2) { // si c'est un pré-requis normal

          $versionExploded = explode(' ', $version);
          $operator = $versionExploded[0]; // On récupére l'opérateur et la version qui sont définis
          $versionNeeded = $versionExploded[1];

          $typeExploded = explode('--', $type);
          $type = $typeExploded[0];             // On veux savoir le type + l'id de ce qui est concerné
          $id = $typeExploded[1];

          if($type == "plugin") {

            // C'est un plugin donc il nous faut sa version
            $search = $this->findPluginByID($id);
            if(!empty($search)) { // si on a trouvé quelque chose

              $pluginVersion = $this->getPluginConfig($search->slug);
              if(!version_compare($pluginVersion->version, $versionNeeded, $operator)) { // Si la version du CMS ne correspond pas à ce qui est demandé
                $this->log('Plugin : '.$name.' can\'t be installed, '.$search->slug.' (plugin) version need to be '.$operator.' '.$versionNeeded.' !');
                return false; // On arrête tout
              }

            } else {
              $this->log('Plugin : '.$name.' can\'t be installed, '.$search[$id]['slug'].' (plugin) is not installed !');
              return false; // On arrête tout
            }

          } elseif($type == "theme") {

            $themeFinded = false;

            // C'est un theme donc il nous faut sa version
            $themeFolder = ROOT.DS.'app'.DS.'View'.DS.'Themed';
            $themeFolderContent = scandir($themeFolder); // on scan le dossier de thèmes
            if($themeFolderContent !== false) {

              $bypassedFiles = array('.', '..', '.DS_Store', '__MACOSX', 'AdminTheme'); // On met les fichiers que l'on ne considère pas comme un plugin
              $themeList = array(); // On dis que de base la liste est vide

              foreach ($themeFolderContent as $key => $value) { // On parcours tout ce qu'on à trouvé dans le dossier
                if(!in_array($value, $bypassedFiles)) { // Si c'est pas un fichier que l'on ne doit pas prendre
                  $themeConfig = json_decode(file_get_contents($themeFolder.$value)); // on récup la config
                  $themeId = $themeConfig['author'].'.'.$value.'.'.$themeConfig['apiID']; // on fais l'id
                  if($themeId == $id) {// si on trouve une correspondance
                    if(version_compare($themeConfig['version'], $versionNeeded, $operator)) { // on compare les versions
                      $themeFinded = true; // on dis que c'est bon pour ça
                      break; // et on arrête cette boucle pour voir les autres pré-requis
                    } else {
                      $this->log('Plugin : '.$name.' can\'t be installed, '.$search[$id]['slug'].' (theme) version need to be '.$operator.' '.$versionNeeded.' !');
                      return false; // On arrête tout
                    }
                  }
                }
              }
            }

            if(!$themeFinded) { // y'a pas eu de theme trouvé
              $this->log('Plugin : '.$name.' can\'t be installed, '.$search[$id]['slug'].' (theme) is not installed !');
              return false; // On arrête tout
            }

          }

        } // sinon on s'en fou

      }

      return true; // De base c'est bon

    }


  // Rafraichi les permssions (ne laisse que celle de base + celles des plugins installés)

    private function refreshPermissions() {

      // Chargons le component
      App::import('Component', 'Permissions');
      $PermissionsComponent = new PermissionsComponent();

      $defaultPermissions = $PermissionsComponent->permissions; // les permissions par défaut
      $pluginsPermissions = array(); // les permissions des plugins installés

      foreach ($this->loadPlugins() as $id => $data) { // On parcours tout les plugins (actualiser) pour récupérer leurs permissions

        // on parcours les permissions par défaut
        foreach ($data->permissions->available as $key => $permission) {
          $pluginsPermissions[] = $permission; // on ajoute la permissions à la liste des permissions par défaut
        }

      }

      // étape 2 : parcourir les permissions dans la base de donnée et la supprimer si elle ne fais pas partie de celle normale

      $PermissionsModel = ClassRegistry::init('Permission'); // On charge le model

      $searchPermissions = $PermissionsModel->find('all'); // on récupére tout

      foreach ($searchPermissions as $k => $value) { // on parcours tout

        $permissions = unserialize($value['Permission']['permissions']);
        $permissionsBeforeCheck = $permissions;
        foreach ($permissions as $k2 => $perm) { // on parcours toutes les permissions
          if(!in_array($perm, $defaultPermissions) && !in_array($perm, $pluginsPermissions)) { // si la perm n'est pas dans celles par défaut ou celles des plugins installés
            unset($permissions[$k2]); // elle n'a rien à faire ici alors on la supprime
          }
        }

        if(count($permissions) != count($permissionsBeforeCheck)) { // si les permissions ont changé entre temps (certaines supprimé), on update

          $PermissionsModel->read(null, $value['Permission']['id']);
          $PermissionsModel->set(array('permissions' => serialize($permissions))); // On re-sérialize les permissions et on set
          $PermissionsModel->save(); // On enregistre le tout

        }

      }

    }


  // Ajoute les permission du plugins avec la config spécifié

    private function addPermissions($config) {

      $this->Permission = ClassRegistry::init('Permission'); // On charge le model

      if(isset($config->default) AND !empty($config->default)) { // On vérifie que ca existe bien & si c'est pas vide

        foreach ($config->default as $key => $value) { // On parcours les permissions par défaut du plugin

          $searchRank = $this->Permission->find('first', array('conditions' => array('rank' => $key))); // je cherche les perms du rank

          if(!empty($searchRank)) { // si on trouve un rank avec cet ID

            $rankPermissions = unserialize($searchRank['Permission']['permissions']); // On récupére ses permissions déjà configurées

            foreach ($rankPermissions as $k2 => $v2) { // on les parcours
              foreach ($value as $kp => $perm) {
                $rankPermissions[] = $perm; // on ajoute les perms
              }
            }

            $this->Permission->read(null, $searchRank['Permission']['id']);
            $this->Permission->set(array('permissions' => serialize($rankPermissions))); // On sauvegarde les nouvelles permissions
            $this->Permission->save();
          } // On trouve pas de rank
        }
      } // pas de permissions par défaut
    }

  // Get sur l'API

    public function getPluginLastVersion($apiID) {
      $pluginVersion = '0.0.0'; // Pour ne pas rien retourner au cas où
      $url = @file_get_contents('http://mineweb.org/api/v'.$this->apiVersion.'/getAllPlugins'); // On get tout les plugins
      if($url !== false) {
        $JSON = json_decode($url, true);
        if($JSON !== false) {

          foreach ($JSON as $key => $value) { // On parcours les plugins
            if($value['apiID'] == $apiID) { // si le plugin est celui qu'on recherche
              $pluginVersion = $value['version']; // on set la version
              break; // on arrête la boucle
            }
          }

        }
      }
      return $pluginVersion;
    }

    public function getFreePlugins() {
      // On gère les plugins gratuits de base
        $pluginList = array(); // Pour ne pas rien retourner au cas où
        $url = @file_get_contents('http://mineweb.org/api/v'.$this->apiVersion.'/getFreePlugins'); // On get tout les plugins
        if($url !== false) {
          $JSON = json_decode($url, true);
          $pluginList = ($JSON !== false) ? $JSON : array();
        }

      // On gère les plugins payés par l'utilisateur
        $infos = json_decode(file_get_contents(ROOT.DS.'config'.DS.'secure'), true); // On récupère les infos personnelles

        $url = @file_get_contents('http://mineweb.org/api/v'.$this->apiVersion.'/getPurchasedPlugins/'.$infos['id']); // On get tout les plugins achetés
        if($url !== false) { // si on a récup quelque chose
          $purchasedPluginsList = json_decode($url, true);
          if($purchasedPluginsList !== false && isset($purchasedPluginsList['status'])) { // si on peux décoder (éviter l'erreur 500) & que y'a un status
            if($purchasedPluginsList['status'] == "success") { // si y'a pas eu d'erreur

              foreach ($purchasedPluginsList['success'] as $key => $value) { // On parcours tout
                if(!in_array($value['name'], $pluginList)) { // si on a pas déjà mis le plugins dans la liste
                  $pluginsList[] = $value;
                }
              }

            }
          }
        }

      // on supprime les plugins qu'on a déjà
        if(!empty($pluginList)) {
          $dbPlugins = $this->getPluginsInDB();
          foreach ($pluginList as $key => $value) {
            if(in_array($value['name'], $dbPlugins)) {
              unset($pluginList[$key]);
            }
          }
        }

      return $pluginList;
    }

    public function getPluginFromAPI($apiID) {
      $pluginInfo = array(); // Pour ne pas rien retourner au cas où
      $url = @file_get_contents('http://mineweb.org/api/v'.$this->apiVersion.'/getAllPlugins'); // On get tout les plugins
      if($url !== false) {
        $JSON = json_decode($url, true);
        $pluginInfo = ($JSON !== false && isset($JSON[$apiID])) ? $JSON[$apiID] : array();
      }
      return $pluginInfo;
    }

}
// @ignore_user_abort(true);
// @set_time_limit(0);

// /**
// * Composant qui gère les plugins
// * Leur affichage sur la navbar et leur configuration
// **/

// class EyPluginComponent extends Object {

//   function shutdown(&$controller) {
//   }

//   function beforeRender(&$controller) {
//   }

//   function beforeRedirect() {
//   }

// /**
// *
// * Fonction lancé au lancement du composant
// * -> Check si il n'y a pas un plugin à installé
// * -> Check si il n'y a pas un plugin à supprimé
// *
// **/

//   function initialize(&$controller) {
//     $this->plugins = ClassRegistry::init('plugins'); // le model plugins

//     $plugins_list = $this->getPluginsInFolder(); // on récupére tout les plugins dans le dossier

//     if($plugins_list != false) {

//       foreach ($plugins_list as $k => $v) { // on les passes touts 1 à 1

//         $search = $this->plugins->find('all', array('conditions' => array('name' => $v))); // je cherche tout les plugins du dossier dans la bdd

//         if(empty($search)) { // si un est pas activé dans la bdd

//           $config = file_get_contents(ROOT.'/app/Plugin/'.$v.'/config.json');
//           $config = json_decode($config, true); // on récupère la config

//           $p_tables = file_get_contents(ROOT.'/app/Plugin/'.$v.'/sql_name.txt');
//           $p_tables = explode(',', $p_tables); // on récupère les tables qui vont être installés

//           $this->plugins->create();
//           $this->plugins->set(array('plugin_id' => $config['plugin_id'], 'name' => $v, 'author' => $config['author'], 'version' => $config['version'], 'tables' => serialize($p_tables)));
//           $this->plugins->save(); // on save le plugin dans la base de données

//           $this->addTables($v); // on ajoute les tables nécessaires

//           if(file_exists(ROOT.'/app/Plugin/'.$v.'/Controller/Component/MainComponent.php')) { // On fais le onEnable si il existe
//             App::uses('MainComponent', 'Plugin/'.$v.'/Controller/Component');
//             $this->Main = new MainComponent();
//             $this->Main->onEnable();
//           }

//           // une fois que on a mis les tables, on s'occupe des permissions
//           $this->addPermissions($config); // on ajoute les perms par défaut

//         }
//       }

//       $search = $this->plugins->find('all'); // on les cherche tous

//       foreach ($search as $key => $value) { // on rend ca plus clair pour le array_diff
//         $array[] = $value['plugins']['name'];
//       }

//       $bdd = $array;
//       $diff = array_diff($bdd, $plugins_list); // on cherche le plugin qui est présent dans la bdd et non pas dans le dossier

//       if(!empty($diff)) { // si il y a une différence entre les deux listes

//         foreach ($diff as $key => $value) { // on les parcours

//           $search = $this->plugins->find('all', array('conditions' => array('name' => $value))); // on cherche le nom

//           $tables_plugins = unserialize($search['0']['plugins']['tables']); // pour récupérer les tables installées
//           App::import('Model', 'ConnectionManager');
//           $con = new ConnectionManager;
//           $cn = $con->getDataSource('default');
//           foreach ($tables_plugins as $k => $v) {
//             if(!empty($v)) {
//               $cn->query("DROP TABLE ".$v); // on les supprimes
//             }
//           }

//           $this->plugins->delete($search['0']['plugins']['id']); // je le supprime de la bdd

//           @clearDir(ROOT.'/app/Plugin/'.$search['0']['plugins']['name']);
//           @clearFolder(ROOT.'/app/tmp/cache/models/');
//           @clearFolder(ROOT.'/app/tmp/cache/persistent/'); // et on clear le cache pour éviter les soucis
//         }

//       }

//     }
//   }

//   function startup(&$controller) {
//     $plugins = $this->getPluginsInFolder();
//     if($plugins && !empty($plugins)) {
//       Configure::write('eyplugins.plugins.list', $plugins);
//     } else {
//       Configure::write('eyplugins.plugins.list', false);
//     }
//   }

//   private function addTables($plugin_name) {
//     $tables_plugins = file_get_contents(ROOT.'/app/Plugin/'.$plugin_name.'/sql.txt');
//     if(!empty($tables_plugins)) {
//       $tables_plugins = explode('|', $tables_plugins);
//       App::import('Model', 'ConnectionManager');
//       $con = new ConnectionManager;
//       $cn = $con->getDataSource('default');
//       foreach ($tables_plugins as $do) {
//         if(!empty($do)) {
//           $cn->query($do);
//         }
//       }
//     }
//   }

//   private function getPluginsInFolder() {
//     $dir = ROOT.'/app/Plugin';
//     $plugins = scandir($dir);
//     $plugins = array_delete_value($plugins, '.');
//     $plugins = array_delete_value($plugins, '..');
//     $plugins = array_delete_value($plugins, '.DS_Store');
//     if(!empty($plugins)) {
//       foreach ($plugins as $key => $value) {
//         $list_plugins[] = $value;
//       }
//     } else {
//       return false;
//     }
//     if(!empty($plugins)) {
//       return $list_plugins;
//     }
//     return false;
//   }

//   private function addPermissions($config) {
//     $this->Permission = ClassRegistry::init('Permission');
//     if(isset($config['permissions']['default']) AND !empty($config['permissions']['default'])) {
//       foreach ($config['permissions']['default'] as $key => $value) {
//         $where = $this->Permission->find('all', array('conditions' => array('rank' => $key))); // je cherche les perms du rank
//         if(!empty($where)) {
//           $addperm = unserialize($where['0']['Permission']['permissions']);
//           foreach ($addperm as $k2 => $v2) {
//             foreach ($value as $kp => $perm) {
//               $addperm[] = $perm; // on ajoute les perms
//             }
//           }
//           $this->Permission->read(null, $where['0']['Permission']['id']);
//           $this->Permission->set(array('permissions' => serialize($addperm)));
//           $this->Permission->save();
//         }
//       }
//     }
//   }

//   function delete($id) {
//     $this->plugins = ClassRegistry::init('plugins');
//     $search = $this->plugins->find('all', array('conditions' => array('id' => $id)));
//     $config = file_get_contents(ROOT.'/app/Plugin/'.$search[0]['plugins']['name'].'/config.json');
//     $config = json_decode($config, true);
//     $tables_plugins = unserialize($search['0']['plugins']['tables']);
//     App::import('Model', 'ConnectionManager');
//     $con = new ConnectionManager;
//     $cn = $con->getDataSource('default');
//     foreach ($tables_plugins as $k => $v) {
//       if(!empty($v)) {
//         $cn->query("DROP TABLE ".$v);
//       }
//     }
//     // une fois que on a mis les tables, on s'occupe des permissions
//     $this->Permission = ClassRegistry::init('Permission');
//     if(isset($config['permissions']['available']) AND !empty($config['permissions']['available'])) {
//       $where = $this->Permission->find('all'); // je cherche les perms
//       if(!empty($where)) {
//         foreach ($where as $key => $value) {
//           $addperm = unserialize($value['Permission']['permissions']);
//           foreach ($addperm as $k2 => $v2) {
//             if(in_array($v2, $config['permissions']['available'])) {
//               $addperm = array_delete_value($addperm, $perm); // on supprime les perms
//             }
//           }
//           $this->Permission->read(null, $value['Permission']['id']);
//           $this->Permission->set(array('permissions' => serialize($addperm)));
//           $this->Permission->save();
//         }
//       }
//     }
//     if($this->plugins->delete($search['0']['plugins']['id'])) {
//       if(file_exists(ROOT.'/app/Plugin/'.$search['0']['plugins']['name'].'/Controller/Component/MainComponent.php')) {
//         App::uses('MainComponent', 'Plugin/'.$search['0']['plugins']['name'].'/Controller/Component');
//         $this->Main = new MainComponent();
//         $this->Main->onDisable();
//       }
//       clearDir(ROOT.'/app/Plugin/'.$search['0']['plugins']['name']);
//       clearFolder(ROOT.'/app/tmp/cache/models/');
//       clearFolder(ROOT.'/app/tmp/cache/persistent/');
//       return true;
//     } else {
//       return false;
//     }
//   }

//   function enable($id) {
//     $this->plugins = ClassRegistry::init('plugins');
//     $this->plugins->read(null, $id);
//     $this->plugins->set(array('state' => 1));
//     if($this->plugins->save()) {
//       $search = $this->plugins->find('all', array('conditions' => array('id' => $id)));
//       if(file_exists(ROOT.'/app/Plugin/'.$search['0']['plugins']['name'].'/Controller/Component/MainComponent.php')) {
//         App::uses('MainComponent', 'Plugin/'.$search['0']['plugins']['name'].'/Controller/Component');
//         $this->Main = new MainComponent();
//         $this->Main->onEnable();
//       }
//       return true;
//     } else {
//       return false;
//     }
//   }

//   function disable($id) {
//     $this->plugins = ClassRegistry::init('plugins');
//     $this->plugins->read(null, $id);
//     $this->plugins->set(array('state' => 0));
//     if($this->plugins->save()) {
//       $search = $this->plugins->find('all', array('conditions' => array('id' => $id)));
//       if(file_exists(ROOT.'/app/Plugin/'.$search['0']['plugins']['name'].'/Controller/Component/MainComponent.php')) {
//         App::uses('MainComponent', 'Plugin/'.$search['0']['plugins']['name'].'/Controller/Component');
//         $this->Main = new MainComponent();
//         $this->Main->onDisable();
//       }
//       @clearFolder(ROOT.'/app/tmp/cache/models/');
//       @clearFolder(ROOT.'/app/tmp/cache/persistent/');
//       Cache::clear(false, '_cake_core_');
//       return true;
//     } else {
//       return false;
//     }
//   }

//   function get_plugins($all = false) {
//     $this->plugins = ClassRegistry::init('plugins');
//     if(!$all) {
//       $plugins = $this->plugins->find('all', array('conditions' => array('state' => '1'))); // pour avoir seulement les plugins activés & installés
//     } else {
//       $plugins = $this->plugins->find('all');
//     }
//     if(!empty($plugins)) {
//       foreach ($plugins as $key => $value) {
//         $plugins_list[] = $value['plugins']['name'];
//       }
//       $plugins = $plugins_list;
//     } else {
//       $plugins = array();
//     }
//     return $plugins;
//   }

//   function get_list($all = true) {
//     $this->plugins = ClassRegistry::init('plugins');
//     if($all) {
//       $search = $this->plugins->find('all');
//     } else {
//       $search = $this->plugins->find('all', array('conditions' => array('state' => 1)));
//     }
//     return $search;
//   }

//   function get($key, $plugin_name) {
//     $config = @file_get_contents(ROOT.'/app/Plugin/'.$plugin_name.'/config.json');
//     if($config) {
//       $config = json_decode($config, true);
//       return $config[$key];
//     } else {
//       return false;
//     }
//   }

//   function is_installed($name) {
//     $plugins = $this->get_plugins();
//     if($plugins != false) {
//       if(in_array($name, $plugins)) {
//         return true;
//       } else {
//         return false;
//       }
//     } else {
//       return false;
//     }
//   }

//   function get_a_plugin_by_slug($search) {
//     $plugins = Configure::read('eyplugins.plugins.list');
//     foreach ($plugins as $k => $v) {
//       $config = file_get_contents(ROOT.'/app/Plugin/'.$v.'/config.json');
//       $config = json_decode($config, true);
//       if($config['slug'] == $search) {
//         $url = $v;
//       }
//     }
//     return $url;
//   }

//   function get_navbar() {
//     $plugins = $this->get_plugins();
//     foreach ($plugins as $k => $v) {
//       $config = file_get_contents(ROOT.'/app/Plugin/'.$v.'/config.json');
//       $config = json_decode($config, true);
//       if($config['nav'] == true) {
//         $plugin_in_nav[] = array($config['name'] => $config['slug']);
//       }
//     }
//     if(!empty($plugin_in_nav)) {
//       return $plugin_in_nav;
//     } else {
//       return false;
//     }
//   }

//   function get_last_version($plugin_id) {
//     $available_plugins = @file_get_contents('http://mineweb.org/api/getAllPlugins');
//     if($available_plugins) {
//       $available_plugins = json_decode($available_plugins, true);
//       foreach ($available_plugins as $key => $value) {
//         if($value['plugin_id'] == $plugin_id) {
//           $version = $value['version'];
//         }
//       }
//     } else {
//       $version = array();
//     }
//     return $version;
//   }

//   function get_free_plugins() {
//     $plugins = $this->get_plugins(true);
//     $available_plugins = @file_get_contents('http://mineweb.org/api/getFreePlugins');
//     if($available_plugins) {
//       $available_plugins = json_decode($available_plugins, true);
//       foreach ($available_plugins as $key => $value) {
//         if(!in_array($value['name'], $plugins)) {
//           $free_plugins[] = array('plugin_id' => $value['plugin_id'], 'name' => $value['name'], 'author' => $value['author'], 'version' => $value['version']);
//         }
//       }
//     } else {
//       $free_plugins = array();
//     }

//     // plugins payés
//     $secure = file_get_contents(ROOT.'/config/secure');
//     $secure = json_decode($secure, true);
//     $purchased_plugins = @file_get_contents('http://mineweb.org/api/getPurchasedPlugins/'.$secure['id']);
//     if($purchased_plugins) {
//       $purchased_plugins = json_decode($purchased_plugins, true);
//       if($purchased_plugins['status'] == "success") {
//         foreach ($purchased_plugins['success'] as $key => $value) {
//           if(!in_array($value['name'], $plugins)) {
//             $free_plugins[] = array('plugin_id' => $value['plugin_id'], 'name' => $value['name'], 'author' => $value['author'], 'version' => $value['version']);
//           }
//         }
//       }
//     }

//     if(!empty($free_plugins)) {
//       return $free_plugins;
//     } else {
//       return false;
//     }
//   }


//   function install($plugin_id = false, $plugin_name = false) {

//     // get du zip sur mineweb.org
//     $url = 'http://mineweb.org/api/get_plugin/'.$plugin_id;
//     $secure = file_get_contents(ROOT.'/config/secure');
//     $secure = json_decode($secure, true);
//     $postfields = array(
//       'id' => $secure['id'],
//         'key' => $secure['key'],
//         'domain' => Router::url('/', true)
//     );

//     $postfields = json_encode($postfields);
//     $post[0] = rsa_encrypt($postfields, '-----BEGIN PUBLIC KEY-----
// MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCvFK7LMlAnF8Hzmku9WGbHqYNb
// ehNueKDbF/j4yYwf8WqIizB7k+S++SznqPw3KzeHOshiPfeCcifGzp0kI43grWs+
// nuScYjSuZw9FEvjDjEZL3La00osWxLJx57zNiEX4Wt+M+9RflMjxtvejqXkQoEr/
// WCqkx22behAGZq6rhwIDAQAB
// -----END PUBLIC KEY-----');

//     $curl = curl_init();

//     curl_setopt($curl, CURLOPT_URL, $url);
//     curl_setopt($curl, CURLOPT_COOKIESESSION, true);
//     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($curl, CURLOPT_POST, true);
//     curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

//     $return = curl_exec($curl);
//     curl_close($curl);

//     if(!preg_match('#Errors#i', $return)) {
//            $return_json = json_decode($return, true);
//           if(!$return_json) {
//             $zip = $return;
//           } elseif($return_json['status'] == "error") {
//             return false;
//           }
//     } else {
//       return false;
//     }

//     if(unzip($zip, ROOT.'/app/Plugin', 'install-zip', true)) {
//       $this->plugins = ClassRegistry::init('plugins');
//       $config = file_get_contents(ROOT.'/app/Plugin/'.$plugin_name.'/config.json');
//       $config = json_decode($config, true);

//       $p_tables = file_get_contents(ROOT.'/app/Plugin/'.$plugin_name.'/sql_name.txt');
//       $p_tables = explode(',', $p_tables);

//       $this->plugins->create();
//       $this->plugins->set(array('plugin_id' => $plugin_id, 'name' => $plugin_name, 'author' => $config['author'], 'version' => $config['version'], 'tables' => serialize($p_tables)));
//       $this->plugins->save();

//       $this->addTables($plugin_name);

//       if(file_exists(ROOT.'/app/Plugin/'.$plugin_name.'/Controller/Component/MainComponent.php')) {
//         App::uses('MainComponent', 'Plugin/'.$plugin_name.'/Controller/Component');
//         $this->Main = new MainComponent();
//         $this->Main->onEnable();
//       }

//       // une fois que on a mis les tables, on s'occupe des permissions
//       $this->addPermissions($config);

//       clearDir(ROOT.'/app/Plugin/__MACOSX');
//       clearFolder(ROOT.'/app/tmp/cache/models/');
//       clearFolder(ROOT.'/app/tmp/cache/persistent/');
//       return true;
//     } else {
//       return false;
//     }
//   }

//   function update($plugin_id = false, $plugin_name = false) {

//     // get du zip sur mineweb.org
//     $url = 'http://mineweb.org/api/get_plugin/'.$plugin_id;
//     $secure = file_get_contents(ROOT.'/config/secure');
//     $secure = json_decode($secure, true);
//     $postfields = array(
//       'id' => $secure['id'],
//       'key' => $secure['key'],
//       'domain' => Router::url('/', true)
//     );

//     $postfields = json_encode($postfields);
//     $post[0] = rsa_encrypt($postfields, '-----BEGIN PUBLIC KEY-----
// MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCvFK7LMlAnF8Hzmku9WGbHqYNb
// ehNueKDbF/j4yYwf8WqIizB7k+S++SznqPw3KzeHOshiPfeCcifGzp0kI43grWs+
// nuScYjSuZw9FEvjDjEZL3La00osWxLJx57zNiEX4Wt+M+9RflMjxtvejqXkQoEr/
// WCqkx22behAGZq6rhwIDAQAB
// -----END PUBLIC KEY-----');

//     $curl = curl_init();

//     curl_setopt($curl, CURLOPT_URL, $url);
//     curl_setopt($curl, CURLOPT_COOKIESESSION, true);
//     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($curl, CURLOPT_POST, true);
//     curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

//     $return = curl_exec($curl);
//     curl_close($curl);

//     if(!preg_match('#Errors#i', $return)) {
//           $return_json = json_decode($return, true);
//           if(!$return_json) {
//             $zip = $return;
//           } elseif($return_json['status'] == "error") {
//             return false;
//           }
//     } else {
//       return false;
//     }

//     App::import('Component', 'UpdateComponent');
//     $this->Update = new UpdateComponent();
//     if($this->Update->plugin($zip, $plugin_name, $plugin_id)) { // on update
//     // si ca réussi
//       // on récupère la nouvelle config
//       $config = file_get_contents(ROOT.'/app/Plugin/'.$plugin_name.'/config.json');
//       $config = json_decode($config, true);
//       $version = $config['version'];
//       $p_tables = file_get_contents(ROOT.'/app/Plugin/'.$plugin_name.'/sql_name.txt');
//       $p_tables = explode(',', $p_tables);

//       // on change la version/les tables dans la base de donnée
//       $id = $this->plugins->find('all', array('conditions' => array('plugin_id' => $plugin_id)));
//       $id = $id[0]['plugins']['id'];
//       $this->plugins->read(null, $id);
//       $this->plugins->set(array('version' => $version, 'tables' => serialize($p_tables)));
//       $this->plugins->save();

//       // on execute le fichier de modification si il existe (suppresion de fichier, modification sql)
//       if(file_exists(ROOT.'/temp/'.$plugin_name.'_update.php')) {
//         include(ROOT.'/temp/'.$plugin_name.'_update.php'); // on l'inclue
//         unlink(ROOT.'/temp/'.$plugin_name.'_update.php'); // et on le supprime
//       }

//       // on supprime les fichiers inutiles
//       clearDir(ROOT.'/app/Plugin/__MACOSX');
//       clearDir(ROOT.'/temp/');
//       clearFolder(ROOT.'/app/tmp/cache/models/');
//       clearFolder(ROOT.'/app/tmp/cache/persistent/');
//       return true; // et on dis qu'on a réussi
//     } else {
//       return false;
//     }
//   }

// }
