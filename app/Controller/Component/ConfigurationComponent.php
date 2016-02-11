<?php

// Component de la configuration
// la config est dans la bdd

class ConfigurationComponent extends Object {

  public $components = array('Session');

  static private $data;

  function __construct() {
    if(!file_exists(ROOT.'/config/install.txt')) {

      App::uses('CakeSchema', 'Model');
      $this->Schema = new CakeSchema(array('name' => 'App', 'path' => ROOT.DS.'app'.DS.'Config'.DS.'Schema', 'file' => 'schema.php', 'connection' => 'default', 'plugin' => null));

      App::uses('SchemaShell', 'Console/Command');
      $SchemaShell = new SchemaShell();

      App::import('Model', 'ConnectionManager');
      $con = new ConnectionManager;
      $cn = $con->getDataSource($this->Schema->connection);
      if(!$cn->isConnected()) {
          exit('Could not connect to database. Please check the settings in app/config/database.php and try again');
      }

      $db = ConnectionManager::getDataSource($this->Schema->connection);

      $options = array(
          'name' => $this->Schema->name,
          'path' => $this->Schema->path,
          'file' => $this->Schema->file,
          'plugin' => null,
          'connection' => $this->Schema->connection,
      );
      $Schema = $this->Schema->load($options);

      $Old = $this->Schema->read(array('models' => false));
      $compare = $this->Schema->compare($Old, $Schema);

      $contents = array();

      foreach ($compare as $table => $changes) {
          if (isset($compare[$table]['create'])) {
              $contents[$table] = $db->createSchema($Schema, $table);
          } else {

              // on vérifie que ce soit pas un plugin (pour ne pas supprimer ses modifications sur la tables lors d'une MISE A JOUR)
              if(isset($compare[$table]['drop'])) { // si ca concerne un drop de colonne

                  foreach ($compare[$table]['drop'] as $column => $structure) {

                      // vérifions que cela ne correspond pas à une colonne de plugin
                      if(count(explode('__', $column)) > 1) {
                          unset($compare[$table]['drop'][$column]);
                      }
                  }

              }

              if(isset($compare[$table]['drop']) && count($compare[$table]['drop']) <= 0) {
                  unset($compare[$table]['drop']); // on supprime l'action si y'a plus rien à faire dessus
              }

              if(count($compare[$table]) > 0) {
                  $contents[$table] = $db->alterSchema(array($table => $compare[$table]), $table);
              }
          }
      }

      $error = array();
      if(!empty($contents)) {
          foreach ($contents as $table => $query) {
              if(!empty($query)) {
                  try {
                      $db->execute($query);
                  } catch (PDOException $e) {
                      $error[] = $table . ': ' . $e->getMessage();
                      $this->log('MYSQL Schema install : '.$e->getMessage());
                  }
              }
          }
      }

      $Schema->after(array(), true);

      if(empty($error)) {
        $data = "CREATED AT ".date('H:i:s d/m/Y')."\n";
        $fp = fopen(ROOT."/config/install.txt","w+");
        fwrite($fp, $data);
        fclose($fp);
      } else {
        $this->log('Unable to install MySQL tables');
        die('Unable to install MYSQL tables');
      }
    }
  }

	function shutdown(&$controller) {}
	function beforeRender(&$controller) {}
  function beforeRedirect() {}


    function initialize(&$controller) {
        // sauvegarde la référence du contrôleur pour une utilisation ultérieure
        $this->controller =& $controller;
        $this->controller->set('Configuration', $this);
    }

    /*function initialize(&$controller) {}*/
    function startup(&$controller) {}

    public function get_all($forced = false) {
      if(empty(self::$data) || $forced) {
        self::$data = ClassRegistry::init('Configuration')->find('first');
      }
      return self::$data;
    }

    public function get_layout() {
      return $this->get_all()['Configuration']['layout'];
    }

    public function get_money_name($plural = true, $singular = false) {
      $money = $this->get_all();
      if($plural) {
        return $money['Configuration']['money_name_plural'];
      } elseif ($singular) {
        return $money['Configuration']['money_name_singular'];
      }
    }

    public function get($key) {
      return (isset($this->get_all()['Configuration'][$key])) ? $this->get_all()['Configuration'][$key] : false;
    }

    public function set($key, $value) {
      $this->Configuration = ClassRegistry::init('Configuration');
      $this->Configuration->read(null, 1);
      $this->Configuration->set(array($key => $value));
      if($this->Configuration->save()) {
        return true;
      } else {
        return false;
      }
    }

    public function get_first_admin() {
      $this->User = ClassRegistry::init('User');
      $search = $this->User->find('all', array('conditions' => array('rank' => '4')));
      return $search['0']['User']['pseudo'];
    }

    public function get_created_date() {
      $this->User = ClassRegistry::init('User');
      $search = $this->User->find('all', array('conditions' => array('rank' => '4')));
      return $search['0']['User']['created'];
    }
}
