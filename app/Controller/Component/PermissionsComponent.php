<?php
class PermissionsComponent extends Object {
  	
  public $components = array('Session', 'Connect');
  
	function shutdown(&$controller) {}
	function beforeRender(&$controller) {}
  function beforeRedirect() {}
  function initialize(&$controller) {$this->controller =& $controller;}
  function startup(&$controller) {
    $controller->set('Permissions', new PermissionsComponent());
  }
  function __construct() {
    App::import('Component', 'Connect');
    $this->Connect = new ConnectComponent();
    $this->Perm = ClassRegistry::init('Permission');
  }
  
  public function can($perm) {
    if($this->Connect->connect()) {
      if($this->Connect->if_admin()) {
        return true;
      } else {
        $search_perm = $this->Perm->find('all', array('conditions' => array('rank' => $this->Connect->get('rank'))));
        $search_perm = unserialize($search_perm[0]['Permission']['permissions']);
        if(in_array($perm, $search_perm)) {
          return true;
        } else {
          return false;
        }
      }
    } else {
      return false;
    }
  }
}