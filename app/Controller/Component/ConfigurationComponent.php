<?php

// Component de la configuration
// la config est dans la bdd

class ConfigurationComponent extends Object {
  	
  public $components = array('Session');

  private $data;

  function __construct() {
    if(!file_exists(ROOT.'/config/install.txt')) {
      $tables = file_get_contents(ROOT.'/config/install/sql.txt');
          $tables = explode('|', $tables);
          App::import('Model', 'ConnectionManager');
          $con = new ConnectionManager;
          $cn = $con->getDataSource('default');
          if(!$cn->isConnected()) {
              exit('Could not connect to database. Please check the settings in app/config/database.php and try again');
          }
          $data = "CREATED AT ".date('H:i:s d/m/Y')."\n";
          foreach ($tables as $do) {
            $cn->query($do);
          }
      $fp = fopen(ROOT."/config/install.txt","w+");
      fwrite($fp, $data);
      fclose($fp);
    }
  }
  
	function shutdown(&$controller) {}
	function beforeRender(&$controller) {}
  function beforeRedirect() {}


    function initialize(&$controller) {
        // sauvegarde la référence du contrôleur pour une utilisation ultérieure
        $this->controller =& $controller;
        $this->controller->set('Configuration', new ConfigurationComponent());
    }

    /*function initialize(&$controller) {}*/
    function startup(&$controller) {}

    public function get_all() {
      if(empty($this->data)) {
        $this->data = ClassRegistry::init('Configuration')->find('first');
      }
      return $this->data;
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
      return $this->get_all()['Configuration'][$key];
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