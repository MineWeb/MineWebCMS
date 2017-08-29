<?php

use PharIo\Version\Version;
use PharIo\Version\VersionConstraintParser;

class ThemeComponent extends Object
{

    public $themesFolder;
    private $themesAvailable;
    private $themesInstalled;
    private $alreadyCheckValid;

    private $apiVersion = '2';

    private $controller;

    public function __construct()
    {
        $this->themesFolder = ROOT . DS . 'app' . DS . 'View' . DS . 'Themed';
    }

    public function shutdown(&$controller) {}

    public function beforeRender(&$controller) {}

    public function beforeRedirect() {}

    public function startup(&$controller) {}

    // init
    public function initialize(&$controller)
    {
        $this->controller =& $controller;
        $this->controller->set('Theme', $this);

        // plugins
        $this->EyPlugin = $this->controller->EyPlugin;

        // versioning
        App::import('Vendor', 'load', array('file' => 'phar-io/version-master/load.php'));
    }

    // get themes in folder
    public function getThemesInstalled($api = true)
    {
        if (!empty($this->themesInstalled[$api]))
            return $this->themesInstalled[$api];

        // scan folder
        $dir = $this->themesFolder;
        $themes = scandir($dir);
        if ($themes === false) {
            $this->log('Unable to scan theme folder.');
            return $this->themesInstalled[$api] = (object)array();
        }
        // result
        $themesList = (object)array();
        $bypassedFiles = array('.', '..', '.DS_Store', '__MACOSX'); // not a theme
        // set themes on $this->themesAvailable
        if ($api)
            $this->getThemesOnAPI(true);
        // each installed themes
        foreach ($themes as $slug) {
            if (in_array($slug, $bypassedFiles)) // not a valid theme
                continue;
            // get config
            $config = $this->getConfig($slug);
            if (empty($config)) continue; // config not found
            // add config
            $id = strtolower($config->author . '.' . $slug . '.' . $config->apiID);;
            $themesList->$id = $config;
            $checkSupported = $this->checkSupported($slug);
            $themesList->$id->supported = (empty($checkSupported)) ? true : false;
            $themesList->$id->supportedErrors = $checkSupported;
            if ($this->isValid($slug) && $this->checkSecure($slug))
                $themesList->$id->valid = true;
            else
                $themesList->$id->valid = false;
            // set last version
            if (isset($this->themesAvailable['all']) && isset($this->themesAvailable['all'][$id]))
                $themesList->$id->lastVersion = $this->themesAvailable['all'][$id]['version'];
        }
        // cache for this request
        return $this->themesInstalled[$api] = $themesList;
    }

    // get themes on api
    public function getThemesOnAPI($all = true, $deleteInstalledThemes = false)
    {
        $type = ($all) ? 'all' : 'free';
        if (!empty($this->themesAvailable[$type]))
            return $this->themesAvailable[$type];
        $url = ($all) ? 'http://api.mineweb.org/api/v' . $this->apiVersion . '/theme/all' : 'http://api.mineweb.org/api/v' . $this->apiVersion . '/theme/free';

        // get themes
        $getAllThemes = @file_get_contents($url);
        $getAllThemes = ($getAllThemes) ? json_decode($getAllThemes, true) : array();

        // get purchases themes
        if (!$all) {
            $getPurchasedThemes = $this->controller->sendToAPI(array(), 'theme/purchased');
            if ($getPurchasedThemes['code'] === 200 && ($getPurchasedThemes = json_decode($getPurchasedThemes['content'], true)))
                $getAllThemes = array_merge($getAllThemes, $getPurchasedThemes['success']);
        }
        // delete themes already installed
        if ($deleteInstalledThemes) {
            $installed = $this->getThemesInstalled();
            foreach ($getAllThemes as $themeid => $themedata) {
                if (isset($installed->$themeid))
                    unset($getAllThemes[$themeid]);
            }
        }

        $this->themesAvailable[$type] = $getAllThemes;
        return $getAllThemes;
    }

    public function getPath($slug)
    {
        return $this->themesFolder . DS . $slug;
    }

    // get config
    public function getConfig($slug, $array = false)
    {
        $path = $this->getPath($slug) . DS . 'Config' . DS . 'config.json';
        if (strtolower($slug) === 'default' || !file_exists($path))
            return @json_decode(@file_get_contents(ROOT . DS . 'config' . DS . 'theme.default.json'), $array);
        return @json_decode(@file_get_contents($path), $array);
    }

    public function setConfig($slug, $config = array())
    {
        $path = $this->getPath($slug) . DS . $slug . DS . 'Config' . DS . 'config.json';
        if (strtolower($slug) === 'default' || !file_exists($path))
            return @file_put_contents(ROOT . DS . 'config' . DS . 'theme.default.json', json_encode($config));
        return @file_put_contents($path, json_encode($config));
    }

    // get version
    public function getVersion($slug)
    {
        $config = $this->getConfig($slug);
        if (!$config)
            return false;
        return $config->version;
    }

    // check secure
    private function checkSecure($slug)
    {
        $path = $this->themesFolder . DS . $slug;
        $configuration = $this->getConfig($slug, true);

        $cache = @rsa_decrypt(@file_get_contents(ROOT . DS . 'config' . DS . 'last_check'));
        if (!$cache)
            return false;
        $cache = @json_decode($cache, true);
        if (!$cache)
            return false;
        if ($cache['type'] === 'DEV')
            return true;

        // Get file
        if (!file_exists($path  . DS . 'secure'))
            return false;
        $content = @json_decode(@file_get_contents($path  . DS . 'secure'));
        if (!$content)
            return false;
        $infos = @json_decode(@rsa_decrypt($content[0]));
        if (!$infos)
            return false;
        $content = openssl_decrypt(hex2bin($content[1]), 'AES-128-CBC', $infos->pwd, OPENSSL_RAW_DATA, $infos->iv);
        if (!$content)
            return false;
        $content = json_decode($content, true);
        if (!$content)
            return false;

        // Check options key
        if ($content['options'] !== array_keys($configuration['configurations']))
            return false;

        // Check configurations
        unset($configuration['configurations']);
        if ($content['configuration'] !== $configuration)
            return false;

        // Check files
        foreach ($content['files'] as $file => $size) {
            if (!file_exists($path . DS . $file))
                return false;
            if (($fileSize = filesize($path . DS . $file)) === $size)
                continue;
            if ($fileSize > $size && (($size / $fileSize) * 100) < 75)
                return false;
            else if ($size > $fileSize && (($fileSize / $size) * 100) < 75)
                return false;
        }

        // Check if purchased
        if (in_array($configuration['apiID'], $cache['themes'])) // in not purchased used themes list
            return false;

        return true;
    }

    public function getCurrentTheme()
    {
        $configuredTheme = $this->controller->Configuration->getKey('theme');
        foreach ($this->getThemesInstalled(false) as $theme)
            if ($configuredTheme === $theme->slug && $theme->valid)
                return [$theme->slug, (array)$theme->configurations];
        return ['default', $this->getConfig('default', true)];
    }

    // check if valid
    private function isValid($slug)
    {
        $slug = ucfirst($slug);
        $file = $this->themesFolder . DS . $slug; // On met le chemin pour aller le chercher

        // cache
        if (isset($this->alreadyCheckValid[$slug])) {
            return $this->alreadyCheckValid[$slug];
        }

        // check file
        if (!file_exists($file)) {
            $this->log('Themes folder : ' . $file . ' doesn\'t exist! Theme not valid!');
            return false;
        }
        if (!is_dir($file)) {
            $this->log('File : ' . $file . ' is not a folder! Theme not valid! Please remove this file from de theme folder.'); // ce n'est pas un dossier
            return false;
        }

        // required files
        $neededFiles = array('Config/config.json');
        foreach ($neededFiles as $key => $value) {
            if (!file_exists($file . DS . $value)) { // si le fichier existe bien
                $this->log('Theme "' . $slug . '" not valid! The file or folder "' . $file . DS . $value . '" doesn\'t exist! Please verify documentation for more informations.');
                $this->alreadyCheckValid[$slug] = false;
                return false; // on retourne false, le plugin est invalide et on log
            }
        }

        // json configuration
        $needToBeJSON = array('Config/config.json');
        foreach ($needToBeJSON as $key => $value) {
            if (json_decode(file_get_contents($file . DS . $value)) === false || json_decode(file_get_contents($file . DS . $value)) === null) { // si le JSON n'est pas valide
                $this->log('Theme "' . $slug . '" not valid! The file "' . $file . DS . $value . '" is not at JSON format! Please verify documentation for more informations.');
                $this->alreadyCheckValid[$slug] = false;
                return false; // on retourne false, le plugin est invalide et on log
            }
        }

        // check config
        $config = json_decode(file_get_contents($file . DS . 'Config' . DS . 'config.json'), true);
        $needConfigKey = array('name' => 'string', 'slug' => 'string', 'author' => 'string', 'version' => 'string', 'apiID' => 'int', 'configurations' => 'array', 'supported' => 'array');
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

                    if (is_array($key)) { // Si c'est une clé multi-dimensionnel
                        $key = '["' . implode('"]["', $key) . '"]';
                    }

                    $this->log('File : ' . $slug . ' is not a valid theme! The config is not complete! ' . $key . ' is not a good type (' . $value . ' required).'); // la clé n'existe pas
                    $this->alreadyCheckValid[$slug] = false;
                    return false; // c'est pas le type demandé donc on retourne false et on log
                }

            } else {

                if (is_array($key)) { // Si c'est une clé multi-dimensionnel
                    $key = '["' . implode('"]["', $key) . '"]';
                }

                $this->log('File : ' . $slug . ' is not a valid theme! The config is not complete! ' . $key . ' is not defined.'); // la clé n'existe pas
                $this->alreadyCheckValid[$slug] = false;
                return false;
            }

        }

        // Check version
        try {
            new Version($config['version']);
        } catch (Exception $e) {
            $this->log('File : ' . $slug . ' is not a valid theme! The version configured is not at good format !');
            return $this->alreadyCheckValid[$slug] = false;
        }

        return $this->alreadyCheckValid[$slug] = true;
    }

    // check if is outdated
    public function checkSupported($slug)
    {
        $config = $this->getConfig($slug); // on récup la config
        $supported = (is_object($config) && isset($config->supported) && !empty($config->supported)) ? $config->supported : null;
        $errors = array();

        $versionParser = new VersionConstraintParser();
        foreach ($supported as $type => $version) { // on parcours tout les pré-requis
            // Set version to compare
            if ($type == 'CMS')
                $versionToCompare = $this->controller->Configuration->getKey('version');
            else {
                // find plugin
                $search = $this->EyPlugin->findPlugin('id', $type);
                if (empty($search)) { // plugin not installed
                    $errors[$type] = 'missing';
                    continue;
                }
                $versionToCompare = $this->EyPlugin->getPluginConfig($search->slug)->version;
            }

            // Check
            try {
                $neededVersion = $versionParser->parse($version); // ex: ^7.0
            } catch (Exception $e) {
                $errors[$type] = $e->getMessage();
                continue;
            }

            if (!$neededVersion->complies(new Version($versionToCompare)))
                $errors[$type] = $version;
        }

        return $errors;
    }

    // install plugin
    public function install($apiID, $update = false, $slug = null)
    {
        // ask to api
        $return = $this->controller->sendToAPI(array(), '/theme/download/' . $apiID);
        if ($return['code'] !== 200) {
            $this->log('[Install theme] Couldn\'t download files, error code (http) : ' . $return['code']);
            return false;
        }
        $apiResponse = json_decode($return['content'], true);
        if (!$apiResponse) {
            $this->log('[Install theme] Couldn\'t download files, invalid json returned.');
            return false;
        }
        if ($update)
            $oldConfig = $this->getCustomData($slug)[0];
        // unzip files
        if (!unzip($return['content'], $this->themesFolder, 'theme-' . $apiID . '-zip', true)) {
            $this->log('[Install theme] Couldn\'t unzip files.');
            return false;
        }
        // delete mac os files (fuck hidden files)
        App::uses('Folder', 'Utility');
        $folder = new Folder($this->themesFolder . DS . '__MACOSX');
        $folder->delete();
        if ($update) {
            // Config
            $config = $this->getConfig($slug);
            foreach ($config['configurations'] as $key => $value) {
                if (isset($oldConfig[$key]))
                    $config['configurations'][$key] = $oldConfig[$key];
            }
            $this->setConfig($slug, $config);

            // Edit file
            if (file_exists($this->getPath($slug) . DS . 'update.json')) {
                $updateFile = @json_decode(@file_get_contents($this->getPath($slug) . DS . 'update.json'));
                if ($updateFile) {
                    foreach ($updateFile as $type => $value) {
                        if ($type === 'delete') {
                            foreach ($value as $file) {
                                unlink($this->getPath($slug) . DS . $file);
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    // return config + name
    public function getCustomData($slug)
    {
        $config = (object)array();
        if ($slug == "default") {
            $config = json_decode(file_get_contents(ROOT . DS . 'config' . DS . 'theme.default.json'), true);
            $theme_name = "Bootstrap";
        } else {
            $themesInstalled = $this->getThemesInstalled(false);
            foreach ($themesInstalled as $id => $data) {
                if ($data->slug == $slug) {
                    $theme_name = $data->name;
                    $config = $data->configurations;
                    break;
                }
            }
        }
        // to array
        if (isset($config))
            $config = (array)$config;
        // return
        return (isset($theme_name)) ? array($theme_name, $config) : false;
    }

    // Traite et enregistre les données passé pour la configurations perso d'un thème
    public function processCustomData($slug, $request)
    {

        if ($slug == "default") { // thème par défaut
            $data = json_encode($request->data, JSON_PRETTY_PRINT);
            $fp = @fopen(ROOT . DS . 'config' . DS . 'theme.default.json', "w+");
            if (!$fp) {
                $this->log('Unable to save theme config. File not writable.');
                return false;
            }
            fwrite($fp, $data);
            fclose($fp);

            return false;
        }

        // Les components utiles

        $this->Util = $this->controller->Util;
        $this->Session = $this->controller->Session;
        $this->Lang = $this->controller->Lang;

        // on détermine si le thème est installé
        $finded = false;
        $themesInstalled = $this->getThemesInstalled(false);
        foreach ($themesInstalled as $id => $data) {
            if ($data->slug == $slug) {
                $finded = true;
                $config = $data->configurations;
                break;
            }
        }

        if (!$finded) {
            return false;
        } //on a rien trouvé

        // On traite les données

        if (!isset($request->data['img_edit'])) {

            $checkIfImageAlreadyUploaded = (isset($request->data['img-uploaded']));
            if ($checkIfImageAlreadyUploaded) {

                $request->data['logo'] = Router::url('/') . 'img' . DS . 'uploads' . $request->data['img-uploaded'];
                unset($request->data['img-uploaded']);

            } else {

                $isValidImg = $this->Util->isValidImage($request, array('png', 'jpg', 'jpeg'));

                if (!$isValidImg['status'] && $isValidImg['msg'] != $this->Lang->get('FORM__EMPTY_IMG')) {
                    $this->Session->setFlash($isValidImg['msg'], 'default.error');
                    return false;
                } else {
                    if (isset($isValidImg['infos'])) {
                        $infos = $isValidImg['infos'];
                    } else {
                        $infos = false;
                    }
                }

                if ($infos) {
                    $url_img = WWW_ROOT . 'img' . DS . 'uploads' . DS . 'theme_logo.' . $infos['extension'];

                    if (!$this->Util->uploadImage($request, $url_img)) {
                        $this->Session->setFlash($this->Lang->get('FORM__ERROR_WHEN_UPLOAD'), 'default.error');
                        return false;
                    }

                    $request->data['logo'] = Router::url('/') . 'img' . DS . 'uploads' . DS . 'theme_logo.' . $infos['extension'];
                } else {
                    $request->data['logo'] = false;
                }

            }
        } else {
            $request->data['logo'] = $config->logo;
        }

        $json = json_decode(file_get_contents($this->themesFolder . DS . $slug . DS . 'Config' . DS . 'config.json'));

        $json->configurations = $request->data;

        $data = json_encode($json, JSON_PRETTY_PRINT);
        $fp = @fopen($this->themesFolder . DS . $slug . DS . 'Config' . DS . 'config.json', "w+");
        if (!$fp) {
            $this->log('Unable to save theme config. File not writable.');
            return false;
        }
        fwrite($fp, $data);
        fclose($fp);

        return true;
    }

}
