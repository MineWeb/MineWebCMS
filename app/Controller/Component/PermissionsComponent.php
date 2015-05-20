<?php
class PermissionsComponent extends Object {

  private $permissions = array('COMMENT_NEWS', 'LIKE_NEWS', 'DELETE_HIS_COMMENT', 'DELETE_COMMENT');
  	
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

  public function have($rank, $perm) {
    $search_perm = $this->Perm->find('all', array('conditions' => array('rank' => $rank)));
    $search_perm = unserialize($search_perm[0]['Permission']['permissions']);
    if(in_array($perm, $search_perm)) {
      return 'true';
    } else {
      return 'false';
    }
  }

  public function get_all() {
    $return = $this->permissions;
    // on récupére les perms des plugins
    App::import('Component', 'EyPlugin');
    $this->EyPlugin = new EyPluginComponent();

    foreach ($this->EyPlugin->get_list() as $key => $value) {
      $plugins_perm = $this->EyPlugin->get('permissions', $value['plugins']['name']);
      $plugins_perm = $plugins_perm['available'];
      foreach ($plugins_perm as $k => $v) {
        array_push($return, $v);
      }
    }
    foreach ($return as $key => $value) {
      $return[$value]['0'] = $this->have(0, $value);
      $return[$value]['2'] = $this->have(2, $value);
      unset($return[$key]);
    }
    return $return;
  }

}