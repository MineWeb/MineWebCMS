<?php

class UpdateComponent extends Object
{

    public $components = array('Session', 'Configuration', 'Lang');

    public $cmsVersion;
    public $lastVersion;

    private $updateLogFile;
    private $updateLogFileName;
    private $bypassFiles = array('.DS_Store', 'empty', 'app/Config/database.php', 'config/secure', '__MACOSX', 'config.json', 'theme.default.json');

    private $controller;

    function shutdown($controller)
    {}

    function beforeRender($controller)
    {}

    function beforeRedirect()
    {}

    function startup($controller)
    {}


    function initialize($controller)
    {
        $this->controller = $controller;
        $controller->set('Update', $this);

        $this->updateLogFile = ROOT . DS . 'app' . DS . 'tmp' . DS . 'logs' . DS . 'update' . DS;

        // On check si il y a une nouvelle mise à jour
        $this->check();
    }

    /*
      Affiche le message sur le panel admin pour indiquer si une mise à jour est disponible
    */

    public function available()
    {
        if (version_compare($this->cmsVersion, $this->lastVersion, '<')) {
            $this->Lang = $this->controller->Lang;
            return '<div class="alert alert-info">' . $this->Lang->get('UPDATE__AVAILABLE') . ' ' . $this->Lang->get('UPDATE__CMS_VERSION') . ' : ' . $this->cmsVersion . ', ' . $this->Lang->get('UPDATE__LAST_VERSION') . ' : ' . $this->lastVersion . ' <a href="' . Router::url(array('controller' => 'update', 'action' => 'index', 'admin' => true)) . '" style="margin-top: -6px;" class="btn btn-info pull-right">' . $this->Lang->get('GLOBAL__UPDATE') . '</a></div>';
        }
    }

    /*
        Appelé par le component pour check la mise à jour (renouvellement ou non du cache)
    */

    private function check()
    {
        $cmsVersion = file_get_contents(ROOT . DS . 'VERSION');
        $this->cmsVersion = $cmsVersion;

        if (!file_exists(ROOT . DS . 'config' . DS . 'update') || strtotime('+5 hours', filemtime(ROOT . DS . 'config' . DS . 'update')) < time())
            file_put_contents(ROOT . DS . 'config' . DS . 'update', $this->controller->sendGetRequest('https://raw.githubusercontent.com/MineWeb/MineWebCMS/master/VERSION'));
        $this->lastVersion = file_get_contents(ROOT . DS . 'config' . DS . 'update');
        if (empty($this->lastVersion))
            $this->lastVersion = $this->cmsVersion;
    }

    /*
      Téléchargement de la mise à jour et stockage dans le dossier tmp
    */

    private function downloadUpdate()
    {
        if (!($filesContent = $this->controller->sendGetRequest('https://github.com/MineWeb/MineWebCMS/archive/master.zip')))
            return false;
        $write = fopen(ROOT . DS . 'app' . DS . 'tmp' . DS . $this->lastVersion . '.zip', 'w+');
        if (!fwrite($write, $filesContent)) {
            $this->log('[Update] Save files failed.');
            return false;
        }
        fclose($write);
        return true;
    }

    /*
      Mise à jour du CMS
    */

    public function updateCMS($componentUpdated = false)
    {
        //
        $error = false;

        // On récupère les fichiers
        if ($this->downloadUpdate()) {

            // On update le component
            if (!$componentUpdated) {
                $zip = new ZipArchive;

                // On ouvre le zip
                if ($zip->open(ROOT . DS . 'app' . DS . 'tmp' . DS . $this->lastVersion . '.zip') === TRUE) {

                    $updateComponentContent = $zip->getFromName('MineWebCMS-master/app/Controller/Component/UpdateComponent.php');
                    file_put_contents(ROOT . DS . 'app' . DS . 'Controller' . DS . 'Component' . DS . 'UpdateComponent.php', $updateComponentContent);

                    $zip->close();

                    return true;

                } else {
                    return false;
                }
            }

            // On initialise les logs
            $this->updateLog();

            // On ouvre le zip
            $updateFiles = zip_open(ROOT . DS . 'app' . DS . 'tmp' . DS . $this->lastVersion . '.zip');

            // On parcours les fichiers
            while ($fileRessource = zip_read($updateFiles)) {

                // Si on a le fichier "stop"
                if (file_exists(ROOT . '/stop_update')) {
                    unlink(ROOT . '/stop_update');
                    @unlink(ROOT . DS . 'app' . DS . 'tmp' . DS . $this->lastVersion . '.zip');
                    @unlink(ROOT . '/config/update');
                    break;
                }

                // On set quelque variables
                $filename = zip_entry_name($fileRessource);
                if ($filename === 'MineWebCMS-master/') continue;
                $filename = substr($filename, strlen('MineWebCMS-master/')); // remove
                $folder = dirname($filename);

                // On vérifie que c'est un fichier
                if (substr($filename, -1, 1) == '/') continue;

                // On vérifie que le dossier est bien présent
                if (!is_dir(ROOT . DS . $folder)) {
                    // On vérifie que c'est pas un fichier useless
                    if (strstr($folder, '__MACOSX') === false) {

                        // On créé le dossier récursivement
                        if (mkdir(ROOT . DS . $folder, 0755, true)) {
                            // On log
                            $this->updateLog('CREATE_FOLDER', 'success', $folder);
                        } else {
                            // On log
                            $this->updateLog('CREATE_FOLDER', 'error', $folder);
                            $error = true;
                        }
                    }
                }

                // On vérifie que le fichiers ne soit pas un dossier existant
                if (!is_dir(ROOT . DS . $filename)) {

                    // On set quelques variables
                    $fileContent = zip_entry_read($fileRessource, zip_entry_filesize($fileRessource));
                    $fileInfos = pathinfo($filename);
                    $fileExtension = (isset($fileInfos['extension'])) ? $fileInfos['extension'] : '';

                    // Si c'est une image ou une police on a des modifs sur le contenu à ne pas faire
                    if (!in_array($fileExtension, array('png', 'jpg', 'jpeg', 'gif', 'eot', 'svg', 'ttf', 'otf', 'woff'))) {
                        $fileContent = str_replace("\r\n", "\n", $fileContent);
                    }

                    // Si c'est un fichier qui n'est pas à bypass ou un fichier de langue
                    if (!in_array($filename, $this->bypassFiles) && !in_array($fileInfos['filename'], $this->bypassFiles) && $folder != "lang" && strstr($folder, '__MACOSX') === false) {

                        // Si le fichier existe
                        if (file_exists($filename)) {

                            $fileRessourceMD5 = md5($fileContent);
                            $fileMD5 = md5_file(ROOT . DS . $filename);

                            // On compare les md5
                            if ($fileRessourceMD5 != $fileMD5) {

                                if (file_put_contents(ROOT . DS . $filename, $fileContent) !== FALSE) {
                                    $this->updateLog('UPDATE_FILE', 'success', $filename);
                                } else {
                                    $this->updateLog('UPDATE_FILE', 'error', $filename);
                                    $error = true;
                                }

                            }

                        } else {
                            // Le fichier n'existe pas, on le créé sans problèmes
                            if (file_put_contents(ROOT . DS . $filename, $fileContent) !== FALSE) {
                                $this->updateLog('CREATE_FILE', 'success', $filename);
                            } else {
                                $this->updateLog('CREATE_FILE', 'error', $filename);
                                $error = true;
                            }
                        }

                    } elseif ($folder == "lang" && json_decode($fileContent) !== false) {

                        // Si c'est un fichier de langue

                        $this->Lang = $this->controller->Lang;

                        $this->Lang->update($fileContent, $filename);
                    }

                }

            }

            // On finis la MAJ
            if (!$error) {
                @unlink(ROOT . DS . 'app' . DS . 'tmp' . DS . $this->lastVersion . '.zip');
                @unlink(ROOT . '/config/update');

                Cache::clearGroup(false, '_cake_core_');
                Cache::clearGroup(false, '_cake_model_');

                // On met à jour la base de données
                $this->updateDb();

                // au cas ou des modifications non pas pu être faites

                if (file_exists(ROOT . DS . 'modify.php')) {
                    // on l'inclue pour l'exec
                    try {
                        include(ROOT . DS . 'modify.php');
                    } catch (Exception $e) {
                        // y'a eu une erreur
                        $this->log('Error on update (execute modify.php) - ' . $e->getMessage());
                    }
                    unlink(ROOT . DS . 'modify.php'); // on le supprime
                }

                return true;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }


    /*
        Met à jour la base de données
    */

    public function updateDb()
    {
        App::uses('CakeSchema', 'Model');
        $this->Schema = new CakeSchema(array('name' => 'App', 'path' => ROOT . DS . 'app' . DS . 'Config' . DS . 'Schema', 'file' => 'schema.php', 'connection' => 'default', 'plugin' => null, 'models' => false));

        App::uses('SchemaShell', 'Console/Command');
        $SchemaShell = new SchemaShell();

        $db = ConnectionManager::getDataSource($this->Schema->connection);

        $options = array(
            'name' => $this->Schema->name,
            'path' => $this->Schema->path,
            'file' => $this->Schema->file,
            'plugin' => null,
            'connection' => $this->Schema->connection,
            'models' => false
        );
        $Schema = $this->Schema->load($options);

        $Old = $this->Schema->read($options);
        $compare = $this->Schema->compare($Old, $Schema);

        $contents = array();

        foreach ($compare as $table => $changes) {
            if (isset($compare[$table]['create'])) {
                $contents[$table] = $db->createSchema($Schema, $table);
            } else {

                // on vérifie que ce soit pas un plugin (pour ne pas supprimer ses modifications sur la tables lors d'une MISE A JOUR)
                if (isset($compare[$table]['drop'])) { // si ca concerne un drop de colonne

                    foreach ($compare[$table]['drop'] as $column => $structure) {

                        // vérifions que cela ne correspond pas à une colonne de plugin
                        if (count(explode('-', $column)) > 1) {
                            unset($compare[$table]['drop'][$column]);
                        }
                    }

                }

                if (isset($compare[$table]['drop']) && count($compare[$table]['drop']) <= 0) {
                    unset($compare[$table]['drop']); // on supprime l'action si y'a plus rien à faire dessus
                }

                if (count($compare[$table]) > 0) {
                    $contents[$table] = $db->alterSchema(array($table => $compare[$table]), $table);
                }
            }
        }

        $error = array();
        if (!empty($contents)) {
            foreach ($contents as $table => $query) {
                if (!empty($query)) {
                    try {
                        $db->execute($query);
                    } catch (PDOException $e) {
                        $error[] = $table . ': ' . $e->getMessage();
                        $this->log('MYSQL Schema Update : ' . $e->getMessage());
                    }
                }
            }
        }

        App::uses('Folder', 'Utility');
        $folder = new Folder(ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache');
        if (!empty($folder->path)) {
            $folder->delete();
        }

        $updateEntries = array();
        include ROOT . DS . 'app' . DS . 'Config' . DS . 'Schema' . DS . 'update-entries.php';

        App::uses('Folder', 'Utility');
        $folder = new Folder(ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache');
        if (!empty($folder->path)) {
            $folder->delete();
        }

        $this->Schema->after(array(), false, $updateEntries);

        return (empty($error)) ? true : false;
    }


    /*
      Ecris dans le fichier de log
    */

    private function updateLog($action = null, $status = true, $args = null)
    {

        // On créé le dossier en mode récursif
        if (!is_dir($this->updateLogFile)) {
            mkdir($this->updateLogFile, 0755, true);
        }

        // On set le name
        if (empty($this->updateLogFileName)) {
            $this->updateLogFileName = time() . '_' . $this->lastVersion . '.log';

            // On init le fichier
            $write = fopen($this->updateLogFile . $this->updateLogFileName, "x+");
            $header = json_encode(array('head' => array('date' => date('d/m/Y H:i:s'), 'version' => $this->lastVersion)), JSON_PRETTY_PRINT);
            fwrite($write, $header);
            fclose($write);

            return true;
        }

        // On log

        $oldContent = file_get_contents($this->updateLogFile . $this->updateLogFileName);
        $logContent = json_decode($oldContent, true);

        $logContent['update'][] = array(
            $action => array(
                'statut' => $status,
                'arg' => $args
            )
        );

        $logContent = json_encode($logContent, JSON_PRETTY_PRINT);

        return file_put_contents($this->updateLogFile . $this->updateLogFileName, $logContent);
    }

}
