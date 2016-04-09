<?php
class PermissionsComponent extends Object {

  public $permissions = array('COMMENT_NEWS', 'LIKE_NEWS', 'DELETE_HIS_COMMENT', 'DELETE_COMMENT', 'EDIT_HIS_EMAIL', 'ACCESS_DASHBOARD', 'MANAGE_NEWS', 'MANAGE_SLIDER', 'MANAGE_PAGE', 'MANAGE_NAV');

  public $components = array('Session');

  private $userModel;

  private $controller;

	function shutdown(&$controller) {}
	function beforeRender(&$controller) {}
  function beforeRedirect() {}
  function initialize(&$controller) {
    $this->controller =& $controller;
    $this->userModel = ClassRegistry::init('User');
  }
  function startup(&$controller) {
    $controller->set('Permissions', $this);
  }
  function __construct() {}

  public function can($perm) {
    if($this->userModel->isConnected()) {
      if($this->userModel->isAdmin()) {
        return true;
      } else {
        $this->Perm = ClassRegistry::init('Permission');
        $search_perm = $this->Perm->find('first', array('conditions' => array('rank' => $this->userModel->getKey('rank'))));
        $search_perm = is_array(unserialize($search_perm['Permission']['permissions'])) ? unserialize($search_perm['Permission']['permissions']) : array();
        return in_array($perm, $search_perm);
      }
    }
    return false;
  }

  public function have($rank, $perm) {
    if($rank == 3 OR $rank == 4) {
      return 'true';
    } else {
      $this->Perm = ClassRegistry::init('Permission');
      $search_perm = $this->Perm->find('first', array('conditions' => array('rank' => $rank)));
      if(!empty($search_perm)) {
        $search_perm = (is_array(unserialize($search_perm['Permission']['permissions']))) ? unserialize($search_perm['Permission']['permissions']) : array();
        return (in_array($perm, $search_perm)) ? 'true' : 'false';
      }
    }
    return 'false';
  }

  public function get_all() {
    $return = $this->permissions;
    // on récupére les perms des plugins
    $this->EyPlugin = $this->controller->EyPlugin;

    foreach ($this->EyPlugin->getPluginsActive() as $key => $value) {
      $plugins_perm = $value->permissions->available;
      if(isset($plugins_perm)) {
        foreach ($plugins_perm as $k => $v) {
          array_push($return, $v);
        }
      }
    }
    $this->Rank = ClassRegistry::init('Rank');
    $custom_ranks = $this->Rank->find('all');
    foreach ($return as $key => $value) {
      $return[$value]['0'] = $this->have(0, $value);
      $return[$value]['2'] = $this->have(2, $value);
      foreach ($custom_ranks as $k => $v) {
         $return[$value][$v['Rank']['rank_id']] = $this->have($v['Rank']['rank_id'], $value);
      }
      unset($return[$key]);
    }
    return $return;
  }

}
