<?php
class ConnectComponent extends Object {

  private $data;
  	
  public $components = array('Session');
  
	function shutdown(&$controller) {}
	function beforeRender(&$controller) {}
  function beforeRedirect() {}
  function initialize(&$controller) {
    $this->controller =& $controller;
  }
  function startup(&$controller) {}

  private function getDataBySession($session) {
    if(empty($this->data)) {
      $this->data = ClassRegistry::init('User')->find('first', array('conditions' => array('session' => $session)));
    }
    return $this->data;
  }
  
  /* Je regarde si la session est pas vide, si elle existe alors je cherche dans la bdd
  la session, pour voir si elle correspond a un compte et si c'est la cas alors je return
  true. */
	public function connect() {
      if(CakeSession::check('user') == false) {
      	return false;
  	  } else {
        // Je charge le model
          $this->User = ClassRegistry::init('User');
        // Je cherche si il la session est pas vide et si elle est dans la bdd
        	$user = $this->User->find('all', array(
            	'conditions' => array(
                	'session' => CakeSession::read('user'),
            )
        	));
        	if(isset($user['0']['User']['session'])) {
            return true;
        } else {
            return false;
        }
    	}
    }

  public function if_admin() {
      if(CakeSession::check('user') == false) {
          return false;
      } else {
        // Je cherche si il la session est pas vide et si elle est dans la bdd
        $user = $this->getDataBySession(CakeSession::read('user'));
        if(isset($user['User']['session']) AND $user['User']['rank'] == 3 OR $user['User']['rank'] == 4) {
          return true;
        } else {
            return false;
        }
      }
  }

  public function user_exist($username) {
    $this->User = ClassRegistry::init('User');
    $search_user = $this->User->find('all', array(
        'conditions' => array(
            'pseudo' => $username,
        )
    ));
    if(!empty($search_user)) {
      return true;
    } else {
      return false;
    }
  }

  public function get_id() {
    if(CakeSession::check('user')) {
      $search_user = $this->getDataBySession(CakeSession::read('user'));
      if($search_user) {
        return $search_user['User']['id'];
      }
    }
  }

  public function get_pseudo() {
    if(CakeSession::check('user')) {
      $search_user = $this->getDataBySession(CakeSession::read('user'));
      if($search_user) {
        return $search_user['User']['pseudo'];
      }
    }
  }

  public function get($key) {
    if(CakeSession::check('user')) {
      $search_user = $this->getDataBySession(CakeSession::read('user'));
      if($search_user) {
        return $search_user['User'][$key];
      }
    }
  }

  public function set($key, $value) {
    $this->User = ClassRegistry::init('User');
    if(CakeSession::check('user')) {
      $search_user = $this->getDataBySession(CakeSession::read('user'));
      if($search_user) {
        $this->User->read(null, $search_user['0']['User']['id']);
        $this->User->set(array($key => $value));
        if($this->User->save()) {
          return true;
        } else {
          return false;
        }
      }
    }
  }

  public function get_username($id) {
    $search_user = $this->getDataBySession(CakeSession::read('user'));
    if($search_user) {
      return $search_user['0']['User']['pseudo'];
    }
  }

  public function get_to_user($key, $username) {
    $this->User = ClassRegistry::init('User');
    $search_user = $this->User->find('all', array(
      'conditions' => array(
          'pseudo' => $username,
        )
    ));
    if($search_user) {
      return $search_user['0']['User'][$key];
    }
  }

  public function set_to_user($key, $value, $username) {
    $this->User = ClassRegistry::init('User');
    $search_user = $this->User->find('all', array(
      'conditions' => array(
          'pseudo' => $username,
        )
    ));
    if($search_user) {
      $this->User->read(null, $search_user['0']['User']['id']);
      $this->User->set(array($key => $value));
      if($this->User->save()) {
        return true;
      } else {
        return false;
      }
    }
  }
}