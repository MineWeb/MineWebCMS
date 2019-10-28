<?php

use PharIo\Version\Version;
use PharIo\Version\VersionConstraintParser;
App::uses('CakeObject', 'Core');

class EyPluginComponent extends CakeObject
{

    public $pluginsInFolder = array();
    public $pluginsInDB = array();
    private $alreadyCheckValid = array();
    private $reference = 'https://raw.githubusercontent.com/MineWeb/mineweb.org/gh-pages/market/plugins.json';

    public $pluginsFolder;

    public $pluginsLoaded;

    private $controller;

    private $CmsSqlTables = array();

    function __construct()
    {
        $this->pluginsFolder = ROOT . DS . 'app' . DS . 'Plugin';
    }

    function shutdown($controller)
    {
    }

    function beforeRender($controller)
    {
    }

    function beforeRedirect()
    {
    }

    function startup($controller)
    {
    }

    public function clearCakeCache()
    {
        Cache::clearGroup(false, '_cake_core_');
        Cache::clearGroup(false, '_cake_model_');
    }

    // init
    function initialize($controller)
    {
        $this->controller = $controller;
        $this->controller->set('EyPlugin', $this);
        // models
        if (!class_exists('ClassRegistry')) // cakephp lol
            App::uses('ClassRegistry', 'Utility');
        $this->models = (object)array(
            'Plugin' => ClassRegistry::init('Plugin'),
            'Permission' => ClassRegistry::init('Permission')
        );
        // versioning
        App::import('Vendor', 'load', array('file' => 'phar-io/version-master/load.php'));
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
    
    public function displayAvailableUpdate()
    {
        $pluginList = $this->pluginsLoaded;
        if(!empty($pluginList)) {
            $versions = $this->getPluginsLastVersion(array_map(function ($plugin) {
              return $plugin->slug;
            }, (array)$pluginList));
            foreach ($pluginList as $key => $value) {
                $lastVersion = (isset($versions[$value->slug])) ? $versions[$value->slug] : false;
                if($lastVersion && $value->version != $lastVersion) {
                   $this->Lang = $this->controller->Lang;
                    return '<div class="alert alert-warning">' . $this->Lang->get('UPDATE__AVAILABLE') . ' ' . $this->Lang->get('UPDATE__PLUGIN') . ' <a href="' . Router::url(array('controller' => 'plugin', 'action' => 'index', 'admin' => true)) . '" style="margin-top: -6px;" class="btn btn-warning pull-right">' . $this->Lang->get('GLOBAL__UPDATE_LOOK') . '</a></div>';
                }
            }
        }
    }

    // events
    public function initEventsListeners($controller)
    {
        foreach ($this->pluginsLoaded as $plugin) {
            if (!$plugin->useEvents || !$plugin->loaded)
                continue;
            $slugFormated = ucfirst(strtolower($plugin->slug));
            $eventFolder = $this->pluginsFolder . DS . $plugin->slug . DS . 'Event';
            $path = $eventFolder . DS . $slugFormated . '*EventListener.php';

            foreach (glob($path) as $eventFile) {
                $className = str_replace(".php", "", basename($eventFile));

                App::uses($className, 'Plugin' . DS . $plugin->slug . DS . 'Event');
                $controller->getEventManager()->attach(new $className($controller->request, $controller->response, $controller));
            }
        }
    }

    // get cms sql tables from schema.php
    private function getCmsSqlTables()
    {
        if (empty($this->CmsSqlTables)) { // cache for this request
            require_once ROOT . DS . 'app' . DS . 'Config' . DS . 'Schema' . DS . 'schema.php';
            if (!class_exists('AppSchema')) return array(); // error
            // init class and get vars
            $class = new AppSchema();
            $tables = get_class_vars(get_class($class));
            // remove useless vars
            $ignoredVars = array('name', 'path', 'file', 'connection', 'plugin', 'tables');
            foreach ($tables as $key => $value) {
                if (!in_array($key, $ignoredVars))
                    $this->CmsSqlTables[] = $key;
            }
        }
        return $this->CmsSqlTables; // return result
    }

    // get plugin json config from his folder
    public function getPluginConfig($slug, $array = false)
    {
        $config = @json_decode(@file_get_contents($this->pluginsFolder . DS . $slug . DS . 'config.json'), $array);
        if (!$config) return false; // error
        return $config;
    }

    // unload plugins (disabled or invalid) and list plugins
    public function loadPlugins()
    {
        $dbPlugins = $this->models->Plugin->find('all');
        // result
        $pluginList = (object)array();
        // get cakephp loaded plugins
        $loadedCakePlugins = CakePlugin::loaded();
        // each db plugins
        $count = 0;
        foreach ($dbPlugins as $plugin) { // On les parcours tous
            $plugin = $plugin['Plugin'];
            // get config
            $config = $this->getPluginConfig($plugin['name']);
            if (!is_object($config)) { // invalid plugin
                CakePlugin::unload($plugin['name']); // ask to cake to unload it (lol)
                continue;
            }
            // set config
            $id = strtolower($plugin['author'] . '.' . $plugin['name']); // on fais l'id - tout en minuscule
            $pluginList->$id = (object)array(); // init
            $pluginList->$id = $config; // add file config
            $pluginList->$id->id = $id;
            $pluginList->$id->slug = $plugin['name'];
            $pluginList->$id->slugLower = strtolower($plugin['name']);
            $pluginList->$id->DBid = $plugin['id'];
            $pluginList->$id->DBinstall = $plugin['created'];
            $pluginList->$id->active = ($plugin['state']) ? true : false;
            if ($pluginList->$id->active)
                $count++;
            $pluginList->$id->isValid = $this->isValid($pluginList->$id->slug); // plugin valid
            $pluginList->$id->loaded = false;
            // check if loaded
            if (in_array($plugin['name'], $loadedCakePlugins)) // cakephp have load it ? (or not because fucking cache)
                $pluginList->$id->loaded = true;
            // unload if invalid
            if (!$pluginList->$id->isValid || !$pluginList->$id->active) {
                $pluginList->$id->loaded = false;
                CakePlugin::unload($pluginList->$id->slug);
            }
        }
        // return list
        return $pluginList;
    }

    // get loaded plugins
    public function getPluginsActive()
    {
        $plugins = $this->pluginsLoaded;
        $pluginList = (object)array(); // result

        foreach ($plugins as $key => $value) {
            if ($value->loaded) // loaded (by cake) + active + valid
                $pluginList->$key = $value; // on ajoute dans la liste
        }
        // result
        return $pluginList;
    }

    // get plugins in folder
    private function getPluginsInFolder()
    {
        // config
        $dir = $this->pluginsFolder;
        $plugins = scandir($dir);
        if ($plugins === false) { // can't scan folder
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
    private function getPluginsInDB()
    {
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
    private function isValid($slug)
    {
        $slug = ucfirst($slug);
        $file = $this->pluginsFolder . DS . $slug; // On met le chemin pour aller le chercher

        if (isset($this->alreadyCheckValid[$slug]))
            return $this->alreadyCheckValid[$slug];

        if (!file_exists($file)) {
            $this->log('Plugins folder : ' . $file . ' doesn\'t exist! Plugin not valid!'); // Le fichier n'existe pas
            return $this->alreadyCheckValid[$slug] = false;
        }
        if (!is_dir($file)) {
            if (strstr($file, '.gitkeep') === false)
              $this->log('File : ' . $file . ' is not a folder! Plugin not valid! Please remove this file from the plugin folder.'); // ce n'est pas un dossier
            return $this->alreadyCheckValid[$slug] = false;
        }

        // REQUIRED FILES
        $neededFiles = array('Config/routes.php', 'Config/bootstrap.php', 'lang/fr_FR.json', 'lang/en_US.json', 'Controller', /*'Controller/Component',*/
            'Model', /*'Model/Behavior',*/
            'View', /*'View/Helper',*/
            'View', /*'View/Layouts',*/
            'config.json', 'SQL/schema.php');
        foreach ($neededFiles as $key => $value) {
            if (!file_exists($file . DS . $value)) {
                $this->log('Plugin "' . $slug . '" not valid! The file or folder "' . $file . DS . $value . '" doesn\'t exist! Please verify documentation for more informations.');
                return $this->alreadyCheckValid[$slug] = false;
            }
        }

        // Check JSON files
        $needToBeJSON = array('lang/fr_FR.json', 'lang/en_US.json', 'config.json');
        foreach ($needToBeJSON as $key => $value) {
            if (json_decode(file_get_contents($file . DS . $value)) === false || json_decode(file_get_contents($file . DS . $value)) === null) { // si le JSON n'est pas valide
                $this->log('Plugin "' . $slug . '" not valid! The file "' . $file . DS . $value . '" is not at JSON format! Please verify documentation for more informations.');
                return $this->alreadyCheckValid[$slug] = false;
            }
        }

        // Check config
        $config = json_decode(file_get_contents($file . DS . 'config.json'), true);
        $needConfigKey = array('name' => 'string', 'author' => 'string', 'version' => 'string', 'useEvents' => 'bool', 'permissions' => 'array', 'permissions-available' => 'array', 'permissions-default' => 'array', 'requirements' => 'array');
        foreach ($needConfigKey as $key => $value) {
            $key = (is_array(explode('-', $key))) ? explode('-', $key) : $key; // si c'est une key multi-dimensionnel
            if (is_array($key) && count($key) > 1) { // si la clé est multi-dimensionnel
                $configKey = $config;
                $multi = true; // De base c'est ok pour le multi-dimensionnel
                foreach ($key as $k => $v) { // on parcours les "sous-clés"
                    if (array_key_exists($v, $configKey)) {
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

            if ((isset($multi) && $multi === true) || (!is_null($config) && !isset($multi) && array_key_exists($key, $config))) { // si le multi-dimensionnel est validé OU que c'est pas le multi-dimensionnel ET que la clé existe
                // on check le type de la clé
                $function = 'is_' . $value;
                if (!$function($configKey)) {
                    if (is_array($key)) // Si c'est une clé multi-dimensionnel
                        $key = '["' . implode('"]["', $key) . '"]';

                    $this->log('File : ' . $slug . ' is not a valid plugin! The config is not complete! ' . $key . ' is not a good type (' . $value . ' required).'); // la clé n'existe pas
                    return $this->alreadyCheckValid[$slug] = false;
                }

            } else {
                if (is_array($key)) // Si c'est une clé multi-dimensionnel
                    $key = '["' . implode('"]["', $key) . '"]';

                $this->log('File : ' . $slug . ' is not a valid plugin! The config is not complete! ' . $key . ' is not defined.'); // la clé n'existe pas
                return $this->alreadyCheckValid[$slug] = false;
            }

        }

        // Check version
        try {
            new Version($config['version']);
        } catch (Exception $e) {
            $this->log('File : ' . $slug . ' is not a valid plugin! The version configured is not at good format !'); // la clé n'existe pas
            return $this->alreadyCheckValid[$slug] = false;
        }

        // Check tables
        $filenameTables = $file . DS . 'SQL' . DS . 'schema.php'; // on récupére la liste des tables
        if (!file_exists($filenameTables)) {
            $this->log('File : ' . $slug . ' is not a valid plugin! SQL Schema is not created!');
            return $this->alreadyCheckValid[$slug] = false;
        }
        App::import('Model', 'CakeSchema');
        $nameClass = ucfirst(strtolower($slug)) . 'AppSchema';
        if (!class_exists($nameClass))
            require_once $filenameTables;
        if (!class_exists($nameClass)) {
            $this->log('File : ' . $slug . ' is not a valid plugin! SQL Schema is not created!'); // ce n'est pas un dossier
            return $this->alreadyCheckValid[$slug] = false;
        }
        $class = new $nameClass();

        if (!method_exists($class, 'before') || !method_exists($class, 'after')) {
            $this->log('File : ' . $slug . ' is not a valid plugin! SQL Schema class is not valid!'); // ce n'est pas un dossier
            return $this->alreadyCheckValid[$slug] = false;
        }

        $tables = get_class_vars(get_class($class));
        $ignoredVars = array('name', 'path', 'file', 'connection', 'plugin', 'tables');
        foreach ($tables as $key => $value) { // on les parcours si elles sont pas vides
            if (!in_array($key, $ignoredVars)) {
                // On vérifie que le nom de la table ne soit pas parmis ceux du CMS de base
                $CmsSqlTables = $this->getCmsSqlTables();
                if (!in_array($key, $CmsSqlTables)) {
                    $valueExploded = explode('__', $key); // on explode le nom

                    if (count($valueExploded) <= 1 || $valueExploded[0] != strtolower($slug)) { // si c'est un array de moins d'une key (donc pas de prefix) OU que la première clé n'est pas le slug
                        $this->log('File : ' . $slug . ' is not a valid plugin! SQL tables need to be prefixed by slug.'); // ce n'est pas un dossier
                        $this->alreadyCheckValid[$slug] = false;
                        return false;
                    }
                }
            }
        }

        return $this->alreadyCheckValid[$slug] = true;
    }

    // Installation des plugins non installés
    private function checkIfNeedToBeInstalled($pluginsInFolder, $pluginsInDB)
    {
        if (empty($pluginsInFolder)) return false; // no plugins

        $diff = array_diff($pluginsInFolder, $pluginsInDB);
        if (empty($diff)) return false; // no plugins

        // each plugins
        foreach ($diff as $key => $value) {
            $this->install($value);
        }
    }

    // Suppression des plugins non installés
    private function checkIfNeedToBeDeleted($pluginsInFolder, $pluginsInDB)
    {
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
    public function delete($slug)
    {
        if (empty($slug)) return false;

        // onDisable event
        if (file_exists($this->pluginsFolder . $slug . DS . 'Controller' . DS . 'Component' . DS . 'MainComponent.php')) {
            App::uses('MainComponent', 'Plugin' . DS . $slug . DS . 'Controller' . DS . 'Component');
            $this->Main = new MainComponent();
            $this->Main->onDisable(); // on le lance
        }

        $this->editDatabaseWithSchema($slug, 'DROP'); // delete custom columns

        $plugin = $this->models->Plugin->find('first', array('conditions' => array('name' => $slug)));
        if (!empty($plugin))
            $this->models->Plugin->delete($plugin['Plugin']['id']); // On supprime le plugin de la db

        clearDir($this->pluginsFolder . DS . $slug);
        CakePlugin::unload($slug); // On unload sur cake
        $this->clearCakeCache();
    }

    private function editDatabaseWithSchema($slug, $type, $update = false)
    {
        if (!$slug || !in_array($type, array('CREATE', 'DROP'))) return false; // invalid

        // Init & compare
        App::uses('CakeSchema', 'Model');
        $this->Schema = new CakeSchema(array('name' => ucfirst(strtolower($slug)) . 'App', 'path' => ROOT . DS . 'app' . DS . 'Plugin' . DS . $slug . DS . 'SQL', 'file' => 'schema.php', 'connection' => 'default', 'plugin' => null, 'models' => false));
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
        $currentSchema = $this->Schema->read($options);

        if ($type === 'CREATE') {
            $pluginSchema = $this->Schema->load($options);
            $compare = $this->Schema->compare($currentSchema, $pluginSchema);

            // Check edits
            $contents = [];
            foreach ($compare as $table => $changes) {
                if (isset($compare[$table]['create'])) continue; // not handle create here

                if (!isset($compare[$table]['add'])) continue; // no add

                if (explode('__', $table)[0] != strtolower($slug)) { // other plugin
                    foreach ($compare[$table]['add'] as $column => $structure) {
                        if (explode('-', $column)[0] != strtolower($slug)) // other plugin
                            unset($compare[$table]['add'][$column]);
                    }
                }
                // each drop
                foreach ($compare[$table]['drop'] as $column => $structure) {
                    // column not from this plugin on table not from this plugin
                    if (explode('-', $column)[0] != strtolower($slug) && explode('__', $table)[0] != strtolower($slug)) // other plugin
                        unset($compare[$table]['drop'][$column]);
                }
                // remove empty actions
                if (count($compare[$table]['drop']) <= 0) unset($compare[$table]['drop']);
                if (count($compare[$table]['add']) <= 0) unset($compare[$table]['add']);
                // set sql schema
                if (count($compare[$table]) > 0)
                    $contents[$table] = $db->alterSchema(array($table => $compare[$table]), $table);
            }
            // add tables
            $pluginTables = array();
            foreach ($compare as $table => $changes) {
                if (isset($compare[$table]['create'])) { // is create
                    $contents[$table] = $db->createSchema($pluginSchema, $table);
                    $pluginTables[] = $table; // save for delete
                }
            }
        } // DELETE PLUGIN
        else if ($type === 'DROP') {
            foreach ($currentSchema['tables'] as $table => $columns) {
                if (explode('__', $table)[0] === strtolower($slug)) {
                    try {
                        $db->query("DROP TABLE IF EXISTS $table");
                    } catch (Exception $e) {
                        $this->log('Error when delete plugin ' . $slug . ' : ' . $e->getMessage());
                    }
                } else {
                    foreach ($columns as $name => $structure) {
                        if (explode('-', $name)[0] === strtolower($slug)) {
                            try {
                                $db->query("ALTER TABLE `$table` DROP COLUMN `$name`;");
                            } catch (Exception $e) {
                                $this->log('Error when delete plugin ' . $slug . ' : ' . $e->getMessage());
                            }
                        }
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
                    $this->log('MYSQL Schema update for "' . $slug . '" plugin (' . $type . ') : ' . $e->getMessage());
                }
            }
        }

        // Others actions on install
        if ($type === 'CREATE') {
            $updateEntries = array();
            // custom
            if (file_exists($this->pluginsFolder . DS . $slug . DS . 'Schema' . DS . 'update-entries.php'))
                include $this->pluginsFolder . DS . $slug . DS . 'Schema' . DS . 'update-entries.php';
            // callback
            $this->Schema->after(array(), !$update, $updateEntries);

            if (!empty($error)) {
                foreach ($error as $key => $value) {
                    if (strpos($value, 'Base table or view already exists'))
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
    public function download($slug, $install = false)
    {
        // Check requirements
        $config = $this->getPluginFromAPI($slug);
        if (!$this->requirements($slug, $config))
            return 'ERROR__PLUGIN_REQUIREMENTS';

        // get files
        $zip = $this->controller->sendGetRequest('https://github.com/MineWeb/Plugin-' . $slug . '/archive/master.zip');
        if (!$zip)
            return 'ERROR__PLUGIN_CANT_BE_DOWNLOADED';

        // Temporary file
        $zipFile = ROOT . DS . 'app' . DS . 'tmp' . DS . 'plugin-' . $slug . '.zip';
        $file = fopen($zipFile, 'w+');
        if (!fwrite($file, $zip)) {
            $this->log('Error when downloading plugin, save files failed.');
            return 'ERROR__PLUGIN_PERMISSIONS';
        }
        fclose($file);

        // Set into plugin folder
        $zip = new ZipArchive;
        $res = $zip->open($zipFile);
        if ($res !== TRUE) {
            $this->log('Error when downloading plugin, unable to open zip. (CODE: ' . $res . ')');
            return 'ERROR__PLUGIN_PERMISSIONS';
        }
        if (!file_exists(ROOT . DS . 'app' . DS . 'Plugin' . DS . $slug) && !mkdir(ROOT . DS . 'app' . DS . 'Plugin' . DS . $slug))
          return 'ERROR__PLUGIN_PERMISSIONS';
        for($i = 0; $i < $zip->numFiles; $i++) {
          $filename = $zip->getNameIndex($i);
          $fileinfo = pathinfo($filename);
          $stat = $zip->statIndex($i);
          if ($fileinfo['basename'] === 'Plugin-' . $slug . '-master') continue;

          $target = "zip://".$zipFile."#".$filename;
          $dest = ROOT . DS . 'app' . DS . 'Plugin' . DS . $slug . substr($filename, strlen('Plugin-' . $slug . '-master'));
          if ($stat['size'] === 0 && strpos($filename, '.') === false) {
            if (!file_exists($dest)) mkdir($dest);
            continue;
          }
          if (!copy($target, $dest)) return 'ERROR__PLUGIN_PERMISSIONS';
        }
        $zip->close();

        // Delete temporary file
        unlink($zipFile);

        // Delete MacOS hidden files
        App::uses('Folder', 'Utility');
        $folder = new Folder($this->pluginsFolder . DS . '__MACOSX');
        $folder->delete();

        // Return (& install if needed)
        return ($install) ? $this->install($slug, true) : true;
    }

    // Fonction d'installation
    public function install($slug, $downloaded = false)
    {
        if (!$this->isValid($slug)) { // invalid plugin
            if ($downloaded)
                clearDir($this->pluginsFolder . DS . $slug); // delete
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
        $config = json_decode(file_get_contents($this->pluginsFolder . DS . $slug . DS . 'config.json'));

        // Add permissions
        $this->addPermissions($config->permissions);

        // Add into database
        $id = null;
        if (($findPlugin = $this->models->Plugin->find('first', ['conditions' => ['name' => $config->name]])))
            $id = $findPlugin['Plugin']['id'];
        $this->models->Plugin->read(null, $id);
        $this->models->Plugin->set(array(
            'name' => $slug,
            'author' => $config->author,
            'version' => $config->version
        ));
        $this->models->Plugin->save(); // On sauvegarde le tout

        // onEnable callback
        if (file_exists($this->pluginsFolder . $slug . DS . 'Controller' . DS . 'Component' . DS . 'MainComponent.php')) { // On fais le onEnable si il existe
            App::uses('MainComponent', 'Plugin' . DS . $slug . DS . 'Controller' . DS . 'Component');
            $this->Main = new MainComponent();
            $this->Main->onEnable(); // on le lance
        }

        // Load it
        CakePlugin::load(array($slug => array('routes' => true, 'bootstrap' => true))); // On load sur cake
        return true;
    }

    // Fonction d'update
    public function update($slug)
    {
        // get config from api
        $config = $this->getPluginFromAPI($slug);
        // check requirements
        if (!$config || empty($config))
            return 'ERROR__PLUGIN_REQUIREMENTS';

        // download plugin
        $dl = $this->download($slug);
        if ($dl !== true)
            return $dl;

        // Unload plugin
        CakePlugin::unload($slug);
        // new config
        $pluginConfig = json_decode(file_get_contents($this->pluginsFolder . DS . $slug . DS . 'config.json'), true);
        $pluginVersion = $pluginConfig['version']; // récupére la nouvelle version

        // Get db id
        $searchPlugin = $this->models->Plugin->find('first', array('conditions' => array('name' => $slug)))['Plugin'];
        $pluginDBID = $searchPlugin['id'];

        // Custom actions
        if (file_exists($this->pluginsFolder . DS . $slug . DS . 'Update' . DS . 'beforeSchema.php')) {
            try {
                include($this->pluginsFolder . DS . $slug . DS . 'Update' . DS . 'beforeSchema.php');
            } catch (Exception $e) {
                $this->log('Error on plugin update (' . $slug . ') - ' . $e->getMessage());
            }
            unlink($this->pluginsFolder . DS . $slug . DS . 'Update' . DS . 'beforeSchema.php'); // on le supprime
        }

        // Add tables
        $addTables = $this->editDatabaseWithSchema($slug, 'CREATE', true);
        if ($addTables['status'])
            $pluginTables = $addTables['tables'];
        else
            return 'ERROR__PLUGIN_SQL_INSTALLATION';

        // Custom actions
        if (file_exists($this->pluginsFolder . DS . $slug . DS . 'Update' . DS . 'afterSchema.php')) {
            try {
                include($this->pluginsFolder . DS . $slug . DS . 'Update' . DS . 'afterSchema.php');
            } catch (Exception $e) {
                $this->log('Error on plugin update (' . $slug . ') - ' . $e->getMessage());
            }
            unlink($this->pluginsFolder . DS . $slug . DS . 'Update' . DS . 'afterSchema.php'); // on le supprime
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
    public function findPlugin($key, $value)
    {
        foreach ($this->pluginsLoaded as $id => $data) {
            if ($data->$key == $value)
                return $data;
        }
    }

    // Vérifier si un plugin est installé
    public function isInstalled($id)
    { // on le recherche avec son ID (auteur.name)
        $find = $this->findPlugin('id', $id);
        return (!empty($find) && $find->loaded);
    }

    // Récupérer les plugins ou la navbar est activé (pour la nav)
    public function findPluginsLinks()
    {
        $plugins = array();
        foreach ($this->pluginsLoaded as $id => $data) {
            if (isset($data->navbar_routes))
                $plugins[$data->slug] = (object)['name' => $data->name, 'routes' => $data->navbar_routes];
        }
        return $plugins;
    }

    // Changer d'état un plugin
    public function enable($dbID)
    {
        $this->models->Plugin->read(null, $dbID);
        $this->models->Plugin->set(array('state' => 1));  // On change l'état dans la bdd
        $this->models->Plugin->save();

        $plugin = $this->models->Plugin->find('first', array('id' => $dbID)); // On récupére le nom
        $pluginName = $plugin['Plugin']['name'];

        // Custom callback
        if (file_exists($this->pluginsFolder . DS . $pluginName . DS . 'Controller' . DS . 'Component' . DS . 'MainComponent.php')) {
            App::uses('MainComponent', $this->pluginsFolder . DS . $pluginName . DS . 'Controller' . DS . 'Component');
            if (class_exists('MainComponent')) {
                $this->Main = new MainComponent();        // On lance l'event onEnable
                $this->Main->onEnable();
            }
        }

        // load
        CakePlugin::load(array($pluginName => array('routes' => true, 'bootstrap' => true)));

        return true;
    }

    public function disable($dbID)
    {
        $this->models->Plugin->read(null, $dbID);
        $this->models->Plugin->set(array('state' => 0));  // On change l'état dans la bdd
        $this->models->Plugin->save();

        $plugin = $this->models->Plugin->find('first', array('id' => $dbID)); // On récupére le nom
        $pluginName = $plugin['Plugin']['name'];

        // Custom callback
        if (file_exists($this->pluginsFolder . DS . $pluginName . DS . 'Controller' . DS . 'Component' . DS . 'MainComponent.php')) {
            App::uses('MainComponent', $this->pluginsFolder . DS . $pluginName . DS . 'Controller' . DS . 'Component');
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
    private function __findThemeVersion($id)
    {
        // define paths
        $themeFolder = ROOT . DS . 'app' . DS . 'View' . DS . 'Themed';
        $themeFolderContent = scandir($themeFolder); // scan theme folder
        if ($themeFolderContent === false) return false; // unable to scan

        // not a valid theme
        $bypassedFiles = array('.', '..', '.DS_Store', '__MACOSX');
        // each folder
        foreach ($themeFolderContent as $key => $value) {
            if (in_array($value, $bypassedFiles)) continue; // not a theme
            // get config & theme id
            $themeConfig = json_decode(file_get_contents($themeFolder . $value)); // on récup la config
            $themeId = $themeConfig['author'] . '.' . $value; // on fais l'id

            if ($themeId == $id)
                return $themeConfig['version'];
        }
    }

    // Vérifie les pré-requis d'un plugin
    private function requirements($name, $config = false)
    {
        if (!$config) // Get config if not configured
            $config = $this->getPluginConfig($name);

        if (is_object($config))
            $requirements = (isset($config->requirements) && !empty($config->requirements)) ? $config->requirements : null;
        else
            $requirements = (isset($config['requirements']) && !empty($config['requirements'])) ? $config['requirements'] : null;

        if (empty($requirements)) return true; // no requirements

        // Semantic versioning
        $versionParser = new VersionConstraintParser();

        foreach ($requirements as $type => $version) { // each requirements

            // Get actual version to compare
            if ($type == "CMS") {
                $versionToCompare = trim(@file_get_contents(ROOT . DS . 'VERSION'));
            } else if (count(explode('--', $type)) == 2) { // ex: plugin-- or theme--
                $typeExploded = explode('--', $type);
                $type = $typeExploded[0]; // plugin or theme
                $id = $typeExploded[1]; // id for extension

                if ($type == 'plugin') {
                    // find plugin
                    $search = $this->findPlugin('id', $id);
                    if (empty($search)) { // plugin not installed
                        $this->log('Plugin : ' . $name . ' can\'t be installed, plugin ' . $id . ' is missing !');
                        return false;
                    }
                    $versionToCompare = $this->getPluginConfig($search->slug)->version;
                } else if ($type == 'theme') {
                    $findThemeVersion = $this->__findThemeVersion($id);
                    if (!$findThemeVersion) { // plugin not installed
                        $this->log('Plugin : ' . $name . ' can\'t be installed, theme ' . $id . ' is missing !');
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
            try {
                $neededVersion = $versionParser->parse($version); // ex: ^7.0
            } catch (Exception $e) {
                $this->log('Plugin: Version exception: ' . $e->getMessage());
                return false;
            }

            if (!$neededVersion->complies(new Version($versionToCompare))) { // invalid version
                $this->log('Plugin : ' . $name . ' can\'t be installed, requirements not fulfilled (' . $type . ' ' . $version . ') !');
                return false;
            }

        }

        return true; // it's okay
    }

    // Rafraichi les permssions (ne laisse que celle de base + celles des plugins installés)
    private function refreshPermissions()
    {
        // define permissions
        $defaultPermissions = $this->controller->Permissions->permissions;
        $pluginsPermissions = array();

        foreach ($this->loadPlugins() as $id => $data) {
            if (!isset($data->permissions->available)) continue; // no permissions on this plugin
            foreach ($data->permissions->available as $key => $permission) {
                $pluginsPermissions[] = $permission; // add permission to plugins permissions
            }
        }

        // get permissions on database
        $searchPermissions = $this->models->Permission->find('all');

        foreach ($searchPermissions as $rank) {
            $permissions = unserialize($rank['Permission']['permissions']);
            $permissionsBeforeCheck = $permissions;

            // check each permission for each rank
            $permissionsChecked = array();
            foreach ($permissions as $key => $perm) {
                // remove double or permission of a deleted plugin
                if ((!in_array($perm, $defaultPermissions) && !in_array($perm, $pluginsPermissions)) || (in_array($perm, $permissionsChecked)))
                    unset($permissions[$key]); // remove this permission
                $permissionsChecked[] = $perm;
            }
            // save (optionnal) update
            if (count($permissions) != count($permissionsBeforeCheck)) {
                $this->models->Permission->read(null, $rank['Permission']['id']);
                $this->models->Permission->set(array('permissions' => serialize($permissions)));
                $this->models->Permission->save();
            }
        }
    }

    // Ajoute les permission du plugins avec la config spécifié
    private function addPermissions($permissionConfig)
    {
        if (!isset($permissionConfig->default) || empty($permissionConfig->default))
            return; // no permissions

        // each rank
        foreach ($permissionConfig->default as $rank => $permissions) {
            // get rank permission
            $searchRank = $this->models->Permission->find('first', array('conditions' => array('rank' => $rank)));
            if (empty($searchRank)) continue; // no rank found
            $rankPermissions = unserialize($searchRank['Permission']['permissions']);
            if (!is_array($rankPermissions)) // is not an array (wtf?)
                $rankPermissions = array();
            // each permissions for each rank
            foreach ($rankPermissions as $permission) {
                foreach ($permissions as $perm) {
                    $rankPermissions[] = $perm; // add permission to this rank
                }
            }

            // save
            $this->models->Permission->read(null, $searchRank['Permission']['id']);
            $this->models->Permission->set(array('permissions' => serialize($rankPermissions)));
            $this->models->Permission->save();
        }
    }

    public function getFreePlugins($all = false, $removeInstalledPlugins = false)
    {
        $pluginsList = @json_decode($this->controller->sendGetRequest($this->reference), true);
        Cache::set(array('duration' => '+24 hours'));
        $plugins = Cache::read('plugins');
        if (!$plugins) {
            $plugins = [];
            if ($pluginsList) {
                foreach ($pluginsList as $plugin) {
                    if ($plugin['free']) {
                        if (($pl = $this->getPluginFromRepoName($plugin['repo']))) {
                            $pl['free'] = true;
                            $pl['slug'] = $plugin['slug'];
                            $plugins[] = $pl;
                        }
                    } else if ($all) {
                        $plugins[] = $plugin;
                    }
                }
            }
            Cache::write('plugins', $plugins);
        }

        // remove installed plugins
        if ($removeInstalledPlugins) {
            $installedPlugins = array();
            foreach ($this->pluginsLoaded as $id => $config) {
                $installedPlugins[] = $config->slug;
            }
            foreach ($plugins as $key => $plugin) {
                if (in_array($plugin['slug'], $installedPlugins)) // if already installed
                    unset($plugins[$key]); // remove
            }
        }

        return $plugins;
    }

    private function getPluginFromRepoName($repo, $assoc = true)
    {
        $configUrl = "https://raw.githubusercontent.com/$repo/master/config.json";
        if (!($config = @json_decode($this->controller->sendGetRequest($configUrl), $assoc)))
            return false;
        $config['repo'] = $repo;
        return $config;
    }

    public function getPluginsFromAPI(array $slugs)
    {
        $plugins = $this->getFreePlugins(true);
        // each plugin
        $pluginsToFind = array();
        foreach ($plugins as $plugin) {
            $plugin = json_decode(json_encode($plugin)); // to object
            foreach ($slugs as $slug) {
                if ($plugin->slug == $slug)
                    $pluginsToFind[$plugin->slug] = $plugin;
            }
        }
        return $pluginsToFind;
    }

    public function getPluginsLastVersion(array $slug)
    {
        $plugins = $this->getPluginsFromAPI($slug);
        if ($plugins === false) return false;
        $versions = array();
        foreach ($plugins as $plugin) {
            $versions[$plugin->slug] = $plugin->version;
        }
        return $versions;
    }

    public function getPluginFromAPI($slug)
    {
        $plugins = $this->getPluginsFromAPI(array($slug));
        if (!$plugins || !isset($plugins[$slug])) return false;
        return $plugins[$slug];
    }

    public function getPluginLastVersion($slug)
    {
        $plugin = $this->getPluginFromAPI($slug);
        if (!$plugin) return false;
        return $plugin->version;
    }

}
