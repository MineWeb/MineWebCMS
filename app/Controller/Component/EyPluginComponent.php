<?php
use PharIo\Version\Version;
use PharIo\Version\VersionConstraintParser;

class EyPluginComponent extends Object {

  public $pluginsInFolder = array();
  public $pluginsInDB = array();
  private $alreadyCheckValid = array();

  public $pluginsFolder;
  private $apiVersion = '2';

  public $pluginsLoaded;

  private $controller;

  private $CmsSqlTables = array();

  function __construct() {
    $this->pluginsFolder = ROOT.DS.'app'.DS.'Plugin';
  }

  function shutdown(&$controller) {}
  function beforeRender(&$controller) {}
  function beforeRedirect() {}
  function startup(&$controller) {}

  public function clearCakeCache() {
    Cache::clearGroup(false, '_cake_core_');
    Cache::clearGroup(false, '_cake_model_');
  }

  // init
  function initialize(&$controller) {
    $this->controller =& $controller;
    $this->controller->set('EyPlugin', $this);
    // models
    if(!class_exists('ClassRegistry')) // cakephp lol
      App::uses('ClassRegistry', 'Utility');
    $this->models = (object)array(
      'Plugin' => ClassRegistry::init('Plugin')
    );
    // plugins list
    $this->pluginsInFolder = $this->getPluginsInFolder();
    $this->pluginsInDB = $this->getPluginsInDB();
    // install plugins in folder but not in database
    $this->checkIfNeedToBeInstalled($this->pluginsInFolder['onlyValid'], $this->pluginsInDB);
    // delete plugins on db but on in folder
    $this->checkIfNeedToBeDeleted($this->pluginsInFolder['all'], $this->pluginsInDB);
    // load plugins (or unload)
    $this->pluginsLoaded = $this->loadPlugins();
  }

  // get cms sql tables from schema.php
  private function getCmsSqlTables() {
    if (empty($this->CmsSqlTables)) { // cache for this request
      require_once ROOT.DS.'app'.DS.'Config'.DS.'Schema'.DS.'schema.php';
      if (!class_exists('AppSchema')) return array(); // error
      // init class and get vars
      $class = new AppSchema();
      $tables = get_class_vars(get_class($class));
      // remove useless vars
      $ignoredVars = array('name', 'path', 'file', 'connection', 'plugin', 'tables');
      foreach ($tables as $key => $value) {
        if(!in_array($key, $ignoredVars))
          $this->CmsSqlTables[] = $key;
      }
    }
    return $this->CmsSqlTables; // return result
  }

  // get plugin json config from his folder
  public function getPluginConfig($slug) {
    $config = @json_decode(@file_get_contents($this->pluginsFolder.DS.$slug.DS.'config.json'));
    if (!$config) return false; // error
    return $config;
  }

  // unload plugins (disabled or invalid) and list plugins
  public function loadPlugins() {
    $dbPlugins = $this->models->Plugin->find('all');
    // result
    $pluginList = (object) array();
    // get cakephp loaded plugins
    $loadedCakePlugins = CakePlugin::loaded();
    // each db plugins
    foreach ($dbPlugins as $plugin) { // On les parcours tous
      $plugin = $plugin['Plugin'];
      // get config
      $config = $this->getPluginConfig($plugin['name']);
      if (!is_object($config)) // invalid plugin
        CakePlugin::unload($plugin['name']); // ask to cake to unload it (lol)
      // set config
      $id = strtolower($plugin['author'].'.'.$plugin['name'].'.'.$config->apiID); // on fais l'id - tout en minuscule
      $pluginList->$id = (object) array(); // init
      $pluginList->$id = $config; // add file config
      $pluginList->$id->id = $id;
      $pluginList->$id->slug = $plugin['name'];
      $pluginList->$id->DBid = $plugin['id'];
      $pluginList->$id->DBinstall = $plugin['created'];
      $pluginList->$id->active = ($plugin['state']) ? true : false;
      $pluginList->$id->tables = unserialize($plugin['tables']);
      $pluginList->$id->isValid = $this->isValid($pluginList->$id->slug); // plugin valid
      $pluginList->$id->loaded = false;
      // check if loaded
      if(in_array($plugin['name'], $loadedCakePlugins)) // cakephp have load it ? (or not because fucking cache)
        $pluginList->$id->loaded = true;
      // unload if invalid
      if(!$pluginList->$id->isValid || !$pluginList->$id->active) {
        $pluginList->$id->loaded = false;
        CakePlugin::unload($pluginList->$id->slug);
      }
    }
    // return list
    return $pluginList;
  }

  // get loaded plugins
  public function getPluginsActive() {
    $plugins = $this->pluginsLoaded;
    $pluginList = (object) array(); // result

    foreach ($plugins as $key => $value) {
      if($value->loaded) // loaded (by cake) + active + valid
        $pluginList->$key = $value; // on ajoute dans la liste
    }
    // result
    return $pluginList;
  }

  // get plugins in folder
  private function getPluginsInFolder() {
    // config
    $dir = $this->pluginsFolder;
    $plugins = scandir($dir);
    if($plugins === false) { // can't scan folder
      $this->log('Unable to scan plugins folder.');
      return array();
    }
    $bypassedFiles = array('.', '..', '.DS_Store', '__MACOSX'); // invalid plugins
    $pluginsList = array('all' => array(), 'onlyValid' => array()); // result var
    // each files
    foreach ($plugins as $key => $value) { // On parcours tout ce qu'on à trouvé dans le dossier
      if (in_array($value, $bypassedFiles)) continue; // invalid plugin
      $pluginsList['all'][] = $value; // add to list
      if ($this->isValid($value)) // if valid, add to valid plugins list
        $pluginsList['onlyValid'][] = $value;
    }
    return $pluginsList;
  }

  // get db plugins
  private function getPluginsInDB() {
    // get from database
    $search = $this->models->Plugin->find('all');
    if (empty($search)) return array(); // not plugins
    // result var
    $pluginsList = array();
    // each row, formatting
    foreach ($search as $key => $value) {
      $pluginsList[] = $value['Plugin']['name'];
    }
    return $pluginsList;
  }

  // Vérifier si le plugin donné (nom/chemin) est bien un dossier contenant tout les pré-requis d'un plugin
  private function isValid($slug) {
    $slug = ucfirst($slug);
    $file = $this->pluginsFolder.DS.$slug; // On met le chemin pour aller le chercher

    if(isset($this->alreadyCheckValid[$slug]))
      return $this->alreadyCheckValid[$slug];

    if (!file_exists($file)) {
      $this->log('Plugins folder : '.$file.' doesn\'t exist! Plugin not valid!'); // Le fichier n'existe pas
      return $this->alreadyCheckValid[$slug] = false;
    }
    if (!is_dir($file)) {
      $this->log('File : '.$file.' is not a folder! Plugin not valid! Please remove this file from de plugin folder.'); // ce n'est pas un dossier
      return $this->alreadyCheckValid[$slug] = false;
    }

    // REQUIRED FILES
    $neededFiles = array('Config/routes.php', 'Config/bootstrap.php', 'lang/fr_FR.json', 'lang/en_US.json', 'Controller', /*'Controller/Component',*/ 'Model', /*'Model/Behavior',*/ 'View', /*'View/Helper',*/ 'View', /*'View/Layouts',*/ 'config.json', 'SQL/schema.php');
    foreach ($neededFiles as $key => $value) {
      if (!file_exists($file.DS.$value)) {
        $this->log('Plugin "'.$slug.'" not valid! The file or folder "'.$file.DS.$value.'" doesn\'t exist! Please verify documentation for more informations.');
        return $this->alreadyCheckValid[$slug] = false;
      }
    }

    // Check JSON files
    $needToBeJSON = array('lang/fr_FR.json', 'lang/en_US.json', 'config.json');
    foreach ($needToBeJSON as $key => $value) {
      if (json_decode(file_get_contents($file.DS.$value)) === false || json_decode(file_get_contents($file.DS.$value)) === null) { // si le JSON n'est pas valide
        $this->log('Plugin "'.$slug.'" not valid! The file "'.$file.DS.$value.'" is not at JSON format! Please verify documentation for more informations.');
        return $this->alreadyCheckValid[$slug] = false;
      }
    }

    // Check config
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
          if(is_array($key)) // Si c'est une clé multi-dimensionnel
            $key = '["'.implode('"]["', $key).'"]';

          $this->log('File : '.$slug.' is not a valid plugin! The config is not complete! '.$key.' is not a good type ('.$value.' required).'); // la clé n'existe pas
          return $this->alreadyCheckValid[$slug] = false;
        }

      } else {
        if(is_array($key)) // Si c'est une clé multi-dimensionnel
          $key = '["'.implode('"]["', $key).'"]';

        $this->log('File : '.$slug.' is not a valid plugin! The config is not complete! '.$key.' is not defined.'); // la clé n'existe pas
        return $this->alreadyCheckValid[$slug] = false;
      }

    }

    // Check version
    $testVersion = explode('.', $config['version']);
    if(count($testVersion) < 3 && count($testVersion) > 4) { // On autorise que du type 1.0.0 ou 1.0.0.0
      $this->log('File : '.$slug.' is not a valid plugin! The version configured is not at good format !'); // la clé n'existe pas
      return $this->alreadyCheckValid[$slug] = false;
    }

    // Check tables
    $filenameTables = $file.DS.'SQL'.DS.'schema.php'; // on récupére la liste des tables
    if (!file_exists($filenameTables)) {
      $this->log('File : '.$slug.' is not a valid plugin! SQL Schema is not created!');
      return $this->alreadyCheckValid[$slug] = false;
    }
    App::import('Model', 'CakeSchema');
    $nameClass = ucfirst(strtolower($slug)).'AppSchema';
    if (!class_exists($nameClass))
      require_once $filenameTables;
    if (!class_exists($nameClass)) {
      $this->log('File : '.$slug.' is not a valid plugin! SQL Schema is not created!'); // ce n'est pas un dossier
      return $this->alreadyCheckValid[$slug] = false;
    }
    $class = new $nameClass();

    if (!method_exists($class, 'before') || !method_exists($class, 'after')) {
      $this->log('File : '.$slug.' is not a valid plugin! SQL Schema class is not valid!'); // ce n'est pas un dossier
      return $this->alreadyCheckValid[$slug] = false;
    }

    $tables = get_class_vars(get_class($class));
    $ignoredVars = array('name', 'path', 'file', 'connection', 'plugin', 'tables');
    foreach ($tables as $key => $value) { // on les parcours si elles sont pas vides
      if(!in_array($key, $ignoredVars)) {
        // On vérifie que le nom de la table ne soit pas parmis ceux du CMS de base
        $CmsSqlTables = $this->getCmsSqlTables();
        if (!in_array($key, $CmsSqlTables)) {
          $valueExploded = explode('__', $key); // on explode le nom

          if(count($valueExploded) <= 1 || $valueExploded[0] != strtolower($slug)) { // si c'est un array de moins d'une key (donc pas de prefix) OU que la première clé n'est pas le slug
            $this->log('File : '.$slug.' is not a valid plugin! SQL tables need to be prefixed by slug.'); // ce n'est pas un dossier
            $this->alreadyCheckValid[$slug] = false;
            return false;
          }
        }
      }
    }

    return $this->alreadyCheckValid[$slug] = true;
  }

  // Installation des plugins non installés
  private function checkIfNeedToBeInstalled($pluginsInFolder, $pluginsInDB) {
    if (empty($pluginsInFolder)) return false; // no plugins

    $diff = array_diff($pluginsInFolder, $pluginsInDB);
    if (empty($diff)) return false; // no plugins

    // each plugins
    foreach ($diff as $key => $value) {
      $this->install($value);
    }
  }

  // Suppression des plugins non installés
  private function checkIfNeedToBeDeleted($pluginsInFolder, $pluginsInDB) {
    if (empty($pluginsInFolder)) return false; // no plugins

    $diff = array_diff($pluginsInDB, $pluginsInFolder);
    if (empty($diff)) return false; // no plugins

    // each plugins
    foreach ($diff as $key => $value) {
      $this->delete($value, true);
    }

    $this->refreshPermissions();
    $this->clearCakeCache();
  }

  // Fonction de suppression
  public function delete($slug, $isForced = false) {
    if (empty($slug)) return false;

    // onDisable event
    if(file_exists($this->pluginsFolder.$slug.DS.'Controller'.DS.'Component'.DS.'MainComponent.php')) {
      App::uses('MainComponent', 'Plugin'.DS.$slug.DS.'Controller'.DS.'Component');
      $this->Main = new MainComponent();
      $this->Main->onDisable(); // on le lance
    }

    $this->editDatabaseWithSchema($slug, 'DROP'); // delete custom columns

    $plugin = $this->models->Plugin->find('first', array('conditions' => array('slug' => $slug)));
    if (!empty($plugin))
      $this->models->Plugin->delete($plugin['Plugin']['id']); // On supprime le plugin de la db

    clearDir($this->pluginsFolder.DS.$slug);
    CakePlugin::unload($slug); // On unload sur cake
    $this->clearCakeCache();
  }

  private function editDatabaseWithSchema($slug, $type, $update = false) {
    if (!$slug || !in_array($type, array('CREATE', 'DROP'))) return false; // invalid

    // Init & compare
    App::uses('CakeSchema', 'Model');
    $this->Schema = new CakeSchema(array('name' => ucfirst(strtolower($slug)).'App', 'path' => ROOT.DS.'app'.DS.'Plugin'.DS.$slug.DS.'SQL', 'file' => 'schema.php', 'connection' => 'default', 'plugin' => null, 'models' => false));
    App::uses('SchemaShell', 'Console/Command');
    $schemaShell = new SchemaShell();

    $db = ConnectionManager::getDataSource($this->Schema->connection);
    $db->cacheSources = false;

    $options = array(
        'name' => $this->Schema->name,
        'path' => $this->Schema->path,
        'file' => $this->Schema->file,
        'plugin' => null,
        'connection' => $this->Schema->connection,
        'models' => false
    );
    $schema = $this->Schema->load($options);

    $old = $this->Schema->read($options);
    $compare = $this->Schema->compare($old, $schema);

    // Check edits
    $databaseQueries = array();

    foreach ($compare as $table => $changes) {
      if (isset($compare[$table]['create'])) continue; // not handle create here

      // DELETE PLUGIN
      if ($type === 'DROP') {
        if (!isset($compare[$table]['drop'])) continue; // no drop
        // each drop
        foreach ($compare[$table]['drop'] as $column => $structure) {
          if (explode('-', $column)[0] != $slug) // other plugin
            unset($compare[$table]['drop'][$column]);
        }
        // remove empty actions
        if(count($compare[$table]['drop']) <= 0) unset($compare[$table]['drop']);
        if(isset($compare[$table]['add'])) unset($compare[$table]['add']);
        // set sql schema
        if(count($compare[$table]) > 0)
          $contents[$table] = $db->alterSchema(array($table => $compare[$table]), $table);
      }
      // INSTALL PLUGIN
      else if ($type === 'CREATE') {
        if (!isset($compare[$table]['add'])) continue; // no add

        if (explode('__', $table)[0] != strtolower($slug)) { // other plugin
          foreach ($compare[$table]['add'] as $column => $structure) {
            if(explode('-', $column)[0] != strtolower($slug)) // other plugin
              unset($compare[$table]['add'][$column]);
          }
        }
        // remove empty actions
        if(count($compare[$table]['drop']) <= 0) unset($compare[$table]['drop']);
        if(isset($compare[$table]['add'])) unset($compare[$table]['add']);
        // set sql schema
        if(count($compare[$table]) > 0)
          $contents[$table] = $db->alterSchema(array($table => $compare[$table]), $table);
      }
    }

    // INSTALL PLUGIN
    if ($type === 'CREATE') {
      // add tables
      $pluginTables = array();
      foreach ($compare as $table => $changes) {
        if (isset($compare[$table]['create'])) { // is create
          $contents[$table] = $db->createSchema($schema, $table);
          $pluginTables[] = $table; // save for delete
        }
      }
    }
    // DELETE PLUGIN
    else if ($type === 'DROP') {
      $search = $this->models->Plugin->find('first', array('conditions' => array('name' => $slug)));
      if (empty($search)) return false;

      $tables = unserialize($search['Plugin']['tables']);
      App::import('Model', 'ConnectionManager');
      $con = new ConnectionManager;
      $cn = $con->getDataSource('default');

      if (is_array($tables)) {
        foreach ($tables as $table) { // on les parcours et on les supprimes
          try {
            $cn->query("DROP TABLE IF EXISTS $table"); // on les supprimes
          } catch (Exception $e) {
            $this->log('Error when delete plugin '.$slug.' : '.$e->getMessage());
          }
        }
      }
    }

    // Execute queries
    $error = array();
    if (!empty($contents)) {
      foreach ($contents as $table => $query) {
        if (empty($query)) continue;
        try {
          $db->execute($query);
        } catch (PDOException $e) {
          $error[] = $table . ': ' . $e->getMessage();
          $this->log('MYSQL Schema update for "'.$slug.'" plugin ('.$type.') : '.$e->getMessage());
        }
      }
    }

    // Others actions on install
    if ($type === 'CREATE') {
      $updateEntries = array();
      // custom
      if (file_exists($this->pluginsFolder.DS.$slug.DS.'Schema'.DS.'update-entries.php'))
        include $this->pluginsFolder.DS.$slug.DS.'Schema'.DS.'update-entries.php';
      // callback
      $this->Schema->after(array(), !$update, $updateEntries);

      if (!empty($error)) {
        foreach ($error as $key => $value) {
          if(strpos($value, 'Base table or view already exists'))
            unset($error[$key]);
        }
      }
    }

    // Return
    if (empty($error) && $type === 'CREATE')
      return array('status' => true, 'tables' => $pluginTables);
    else if (empty($error) && $type === 'DROP')
      return array('status' => true);
    else
      return array('status' => false, 'error' => $error);
  }

  // Fonction de download (pré-installation)
  public function download($apiID, $slug, $install = false) {
    // Check requirements
    $config = $this->getPluginFromAPI($apiID);
    if (!$this->requirements($slug, $config))
      return 'ERROR__PLUGIN_REQUIREMENTS';

    // get files
    $apiQuery = $this->controller->sendToAPI(array(), '/plugin/download/'.$apiID, true);
    if ($apiQuery['code'] !== 200) {
      $this->log('Error when downloading plugin, HTTP CODE: '.$apiQuery['code']);
      return 'ERROR__PLUGIN_CANT_BE_DOWNLOADED';
    }
    // Check if JSON
    if (@json_decode($apiQuery['content'])) {
      $this->log('Error when downloading plugin, JSON: '.$apiQuery['content']);
      return 'ERROR__PLUGIN_CANT_BE_DOWNLOADED';
    }

    // Temporary file
    $filename = ROOT.DS.'app'.DS.'tmp'.DS.'plugin-'.$slug.'-'.$apiID.'.zip';
    $file = fopen($filename, 'w+');
    if(!fwrite($file, $zip)) {
      $this->log('Error when downloading plugin, save files failed.');
      return false;
    }
    fclose($file);

    // Set into plugin folder
    $zip = new ZipArchive;
    $res = $zip->open($filename);
    if ($res !== TRUE) {
      $this->log('Error when downloading plugin, unable to open zip.');
      return false;
    }
    $zip->extractTo($this->pluginsFolder.DS);
    $zip->close();

    // Delete temporary file
    unlink($filename);

    // Delete MacOS hidden files
    App::uses('Folder', 'Utility');
    $folder = new Folder($this->pluginsFolder.DS.'__MACOSX');
    $folder->delete();

    // Return (& install if needed)
    return ($install) ? $this->install($slug, true) : true;
  }

  // Fonction d'installation
  public function install($slug, $downloaded = false) {
    if (!$this->isValid($slug)) { // invalid plugin
      if ($downloaded)
        clearDir($this->pluginsFolder.DS.$slug); // delete
      CakePlugin::unload($slug); // unload
      return 'ERROR__PLUGIN_NOT_VALID';
    }

    // Add tables
    $addTables = $this->editDatabaseWithSchema($slug, 'CREATE'); // On ajoute les tables
    if ($addTables['status'])
      $tablesName = $addTables['tables'];
    else
      return 'ERROR__PLUGIN_SQL_INSTALLATION';

    // Get config
    $config = json_decode(file_get_contents($this->pluginsFolder.DS.$slug.DS.'config.json'));

    // Add permissions
    $this->addPermissions($config->permissions);

    // Add into database
    $this->models->Plugin->create();
    $this->models->Plugin->set(array(
      'apiID' => $config->apiID,
      'name' => $slug,
      'author' => $config->author,
      'version' => $config->version,
      'tables' => serialize($tablesName)
    ));
    $this->models->Plugin->save(); // On sauvegarde le tout

    // onEnable callback
    if (file_exists($this->pluginsFolder.$slug.DS.'Controller'.DS.'Component'.DS.'MainComponent.php')) { // On fais le onEnable si il existe
      App::uses('MainComponent', 'Plugin'.DS.$slug.DS.'Controller'.DS.'Component');
      $this->Main = new MainComponent();
      $this->Main->onEnable(); // on le lance
    }

    // Load it
    CakePlugin::load(array($slug => array('routes' => true, 'bootstrap' => true))); // On load sur cake
    return true;
  }

  // Fonction d'update
  public function update($apiID) {
    // get config from api
    $config = $this->getPluginFromAPI($apiID);
    $slug = $config['slug'];
    // check requirements
    if (!$config || empty($config))
      return 'ERROR__PLUGIN_REQUIREMENTS';

    // download plugin
    if (!$this->download($apiID, $slug))
      return 'ERROR__PLUGIN_PERMISSIONS';

    // Unload plugin
    CakePlugin::unload($slug);
    // new config
    $pluginConfig = json_decode(file_get_contents($this->pluginsFolder.DS.$slug.DS.'config.json'), true);
    $pluginVersion = $pluginConfig['version']; // récupére la nouvelle version

    // Get db id
    $searchPlugin = $this->models->Plugin->find('first', array('conditions' => array('apiID' => $apiID)))['Plugin'];
    $pluginDBID = $searchPlugin['id'];

    // Custom actions
    if(file_exists($this->pluginsFolder.DS.$slug.DS.'Update'.DS.'beforeSchema.php')) {
      try {
        include($this->pluginsFolder.DS.$slug.DS.'Update'.DS.'beforeSchema.php');
      } catch(Exception $e) {
        $this->log('Error on plugin update ('.$slug.') - '.$e->getMessage());
      }
      unlink($this->pluginsFolder.DS.$slug.DS.'Update'.DS.'beforeSchema.php'); // on le supprime
    }

    // Add tables
    $addTables = $this->editDatabaseWithSchema($slug, 'CREATE', true);
    if($addTables['status'])
      $pluginTables = $addTables['tables'];
    else
      return 'ERROR__PLUGIN_SQL_INSTALLATION';

    // Custom actions
    if(file_exists($this->pluginsFolder.DS.$slug.DS.'Update'.DS.'afterSchema.php')) {
      try {
        include($this->pluginsFolder.DS.$slug.DS.'Update'.DS.'afterSchema.php');
      } catch(Exception $e) {
        $this->log('Error on plugin update ('.$slug.') - '.$e->getMessage());
      }
      unlink($this->pluginsFolder.DS.$slug.DS.'Update'.DS.'afterSchema.php'); // on le supprime
    }

    // Edit version and tables
    $this->models->Plugin->read(null, $pluginDBID);
    $this->models->Plugin->set(array('version' => $pluginVersion, 'tables' => serialize($pluginTables)));
    $this->models->Plugin->save();
    // Permissions
    $this->refreshPermissions(); // On met les jours les permissions

    // Cakephp
    $this->clearCakeCache();
    CakePlugin::load(array($slug => array('routes' => true, 'bootstrap' => true)));

    return true;
  }

  // Fonctions de recherche parmis les plugins chargés
  public function findPlugin($key, $value) {
    foreach ($this->pluginsLoaded as $id => $data) {
      if($data->$key == $value)
        return $data;
    }
  }

  // Vérifier si un plguin est installé
  public function isInstalled($id) { // on le recherche avec son ID (auteur.name.apiid)
    $find = $this->findPlugin('id', $id);
    return (!empty($find) && $find->loaded);
  }

  // Récupérer les plugins ou la navbar est activé (pour la nav)
  public function findPluginsWithNavbar() {
    $plugins = array();
    foreach ($this->pluginsLoaded as $id => $data) {
      if ($data->navbar)
        $plugins[$id] = $data;
    }
    return $plugins;
  }

  // Changer d'état un plugin
  public function enable($dbID) {
    $this->models->Plugin->read(null, $dbID);
    $this->models->Plugin->set(array('state' => 1));  // On change l'état dans la bdd
    $this->models->Plugin->save();

    $plugin = $this->models->Plugin->find('first', array('id' => $dbID)); // On récupére le nom
    $pluginName = $plugin['Plugin']['name'];

    // Custom callback
    if (file_exists($this->pluginsFolder.DS.$pluginName.DS.'Controller'.DS.'Component'.DS.'MainComponent.php')) {
      App::uses('MainComponent', $this->pluginsFolder.DS.$pluginName.DS.'Controller'.DS.'Component');
      if (class_exists('MainComponent')) {
        $this->Main = new MainComponent();        // On lance l'event onEnable
        $this->Main->onEnable();
      }
    }

    // load
    CakePlugin::load(array($pluginName => array('routes' => true, 'bootstrap' => true)));

    return true;
  }

  public function disable($dbID) {
    $this->models->Plugin->read(null, $dbID);
    $this->models->Plugin->set(array('state' => 0));  // On change l'état dans la bdd
    $this->models->Plugin->save();

    $plugin = $this->models->Plugin->find('first', array('id' => $dbID)); // On récupére le nom
    $pluginName = $plugin['Plugin']['name'];

    // Custom callback
    if (file_exists($this->pluginsFolder.DS.$pluginName.DS.'Controller'.DS.'Component'.DS.'MainComponent.php')) {
      App::uses('MainComponent', $this->pluginsFolder.DS.$pluginName.DS.'Controller'.DS.'Component');
      if (class_exists('MainComponent')) {
        $this->Main = new MainComponent();        // On lance l'event onDisable
        $this->Main->onDisable();
      }
    }

    CakePlugin::unload($pluginName);
    $this->clearCakeCache();

    return true;
  }

  // find theme version
  private function __findThemeVersion($id) {
    // define paths
    $themeFolder = ROOT.DS.'app'.DS.'View'.DS.'Themed';
    $themeFolderContent = scandir($themeFolder); // scan theme folder
    if ($themeFolderContent === false) return false; // unable to scan

    // not a valid theme
    $bypassedFiles = array('.', '..', '.DS_Store', '__MACOSX');
    // each folder
    foreach ($themeFolderContent as $key => $value) {
      if (in_array($value, $bypassedFiles)) continue; // not a theme
      // get config & theme id
      $themeConfig = json_decode(file_get_contents($themeFolder.$value)); // on récup la config
      $themeId = $themeConfig['author'].'.'.$value.'.'.$themeConfig['apiID']; // on fais l'id

      if ($themeId == $id)
        return $themeConfig['version'];
    }
  }

  // Vérifie les pré-requis d'un plugin
  private function requirements($name, $config = false) {
    if (!$config) // Get config if not configured
      $config = $this->getPluginConfig($name);

    if (is_object($config))
      $requirements = (isset($config->requirements) && !empty($config->requirements)) ? $config->requirements : null;
    else
      $requirements = (isset($config['requirements']) && !empty($config['requirements'])) ? $config['requirements'] : null;

    if (empty($requirements)) return true; // no requirements

    // Semantic versioning
    App::import('Vendor', 'load', array('file' => 'phar-io/version-master/load.php'));
    $versionParser = new VersionConstraintParser();

    foreach ($requirements as $type => $version) { // each requirements

      // Get actual version to compare
      if ($type == "CMS") {
        $versionToCompare = $this->controller->Configuration->getKey('version'); // ex: 7.0.0
      } else if (count(explode('--', $type)) == 2) { // ex: plugin-- or theme--
        $typeExploded = explode('--', $type);
        $type = $typeExploded[0]; // plugin or theme
        $id = $typeExploded[1]; // id for extension

        if ($type == 'plugin') {
          // find plugin
          $search = $this->findPluginByID($id);
          if (empty($search)) { // plugin not installed
            $this->log('Plugin : '.$name.' can\'t be installed, plugin '.$id.' is missing !');
            return false;
          }
          $versionToCompare = $this->getPluginConfig($search->slug);
        } else if ($type == 'theme') {
          $findThemeVersion = $this->__findThemeVersion($id);
          if (!$findThemeVersion) { // plugin not installed
            $this->log('Plugin : '.$name.' can\'t be installed, theme '.$id.' is missing !');
            return false;
          }
          $versionToCompare = $findThemeVersion;
        } else {
          continue; // invalid type
        }
      } else {
        continue; // invalid type
      }

      // Version required by plugin
      $neededVersion = $parser->parse($version); // ex: ^7.0

      if (!$neededVersion->complies(new Version($versionToCompare))) { // invalid version
        $this->log('Plugin : '.$name.' can\'t be installed, requirements not fulfilled ('.$type.' '.$version.') !');
        return false;
      }

    }

    return true; // it's okay
  }


  // Rafraichi les permssions (ne laisse que celle de base + celles des plugins installés)

    private function refreshPermissions() {

      // Chargons le component
      $PermissionsComponent = $this->controller->Permissions;

      $defaultPermissions = $PermissionsComponent->permissions; // les permissions par défaut
      $pluginsPermissions = array(); // les permissions des plugins installés

      foreach ($this->loadPlugins() as $id => $data) { // On parcours tout les plugins (actualiser) pour récupérer leurs permissions

        // on parcours les permissions par défaut
        if(isset($data->permissions->available)) {
          foreach ($data->permissions->available as $key => $permission) {
            $pluginsPermissions[] = $permission; // on ajoute la permissions à la liste des permissions par défaut
          }
        }

      }

      // étape 2 : parcourir les permissions dans la base de donnée et la supprimer si elle ne fais pas partie de celle normale

      $PermissionsModel = ClassRegistry::init('Permission'); // On charge le model

      $searchPermissions = $PermissionsModel->find('all'); // on récupére tout

      foreach ($searchPermissions as $k => $value) { // on parcours tout

        $permissions = unserialize($value['Permission']['permissions']);
        $permissionsBeforeCheck = $permissions;

        $permissionsChecked = array();
        foreach ($permissions as $k2 => $perm) { // on parcours toutes les permissions
          if((!in_array($perm, $defaultPermissions) && !in_array($perm, $pluginsPermissions)) || (in_array($perm, $permissionsChecked))) { // si la perm n'est pas dans celles par défaut ou celles des plugins installés OU qu'elle est en double
            unset($permissions[$k2]); // elle n'a rien à faire ici alors on la supprime
          }
          $permissionsChecked[] = $perm;
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

            if(!is_array($rankPermissions)) {
              $rankPermissions = array();
            }

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

    public function getFreePlugins($all = false) { // si $all == false -> on récupère pas les plugins déjà installés
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
        if(!empty($pluginList) && !$all) {
          $dbPlugins = $this->getPluginsInDB();
          foreach ($pluginList as $key => $value) {
            if(in_array($value['slug'], $dbPlugins)) {
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
        if($JSON !== false) {
          foreach ($JSON as $key => $value) {
            if($value['apiID'] == $apiID) {
              $pluginInfo = $value;
              break;
            }
          }
        }
      }
      return $pluginInfo;
    }

}
