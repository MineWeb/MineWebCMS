<?php

class ModuleComponent extends Object {

	protected $controller;
  static public $vars;

	function shutdown(&$controller) {}
	function beforeRender(&$controller) {}
  function beforeRedirect() {}
	function initialize(&$controller) {
		$this->controller =& $controller;
		$this->controller->set('Module', $this);
	}
  function startup(&$controller) {}

  private function loadPlugins() { // on donne une liste des plugins
  	return $this->controller->EyPlugin->getPluginsActive(); // et on retourne la liste
  }

  private function listModules() { // on fais une liste des modules disponibles parmis les plugins
  	$plugins = $this->loadPlugins();
  	foreach ($plugins as $key => $value) {
  		$folder = @scandir(ROOT.'/app/Plugin/'.$value->slug.'/Modules');
  		if($folder) {
		    $folder = array_delete_value($folder, '.');
		    $folder = array_delete_value($folder, '..');
		    $folder = array_delete_value($folder, '.DS_Store');
		    foreach ($folder as $k => $v) {
		    	$modules[explode('.', $v)[0]][] = $value->slug;
		    }
		}
  	}
  	if(!empty($modules)) {
  		return $modules;
  	} else {
  		return array();
  	}
  }

  public function loadModules($name) { // affiche le model demandé
  	$HTML = '';
  	$list = $this->listModules();

    $Lang = $this->controller->Lang;
		$Configuration = $this->controller->Configuration;

    App::uses('HtmlHelper', 'View/Helper');
    $this->Html = new HtmlHelper(new View());

		if(!empty(self::$vars) && is_array(self::$vars)) {
			$vars = array_merge(self::$vars, $this->controller->viewVars);
		} else {
			$vars = $this->controller->viewVars;
		}

		if(!empty($vars) && is_array($vars)) {
        foreach ($vars as $key => $value) {
            if(is_bool($value)) {
                $value = ($value) ? 'true' : 'false';
								eval('$'.$key.' = '.$value.';');
            } elseif(is_array($value)) {
                $value = serialize($value);
                eval('$'.$key.' = \''.addslashes($value).'\';');
                eval('$'.$key.' = unserialize(stripslashes($'.$key.'));');
            } elseif(is_string($value) && !empty($value)) {
                eval('$'.$key.' = stripslashes("'.addslashes($value).'");');
            }
        }
    }

  	if(isset($list[$name])) {
  		foreach ($list[$name] as $key => $value) {
			ob_start();
			include ROOT.'/app/Plugin/'.$value.'/Modules/'.$name.'.ctp';
			$HTML = $HTML."\n".ob_get_clean();
  		}
  		return $HTML;
  	} else {
  		return false;
  	}
  }

}
