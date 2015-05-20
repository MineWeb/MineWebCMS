<?php
class NavbarComponent extends Object {
  	
  	public $components = array('Session');
  
	function shutdown(&$controller) {
	}

	function beforeRender(&$controller) {
	}

	function beforeRedirect() { 
	}

	function initialize(&$controller) {
	    $this->controller =& $controller;
	}

	function startup(&$controller) {
	}

	function get() {
		$this->Navbar = ClassRegistry::init('Navbar');
		$nav = $this->Navbar->find('all', array('order' => 'order'));
		if(!empty($nav)) {
			return $nav;
		} else {
			return false;
		}
	}

	/*
	Type : 1 pour lien classique, 2 pour dropdown
	Submenu : Null de base, serialize des menus et Url du dropdown
	*/
}