<?php

class ModuleComponent extends Object {

	protected $controller;
    static public $vars;

	function shutdown(&$controller) {}
	function beforeRender(&$controller) {}
  	function beforeRedirect() {}
	function initialize(&$controller) {
		$this->controller =& $controller;
		$this->controller->set('Module', new ModuleComponent());
	}
    function startup(&$controller) {}

    private function loadPlugins() { // on donne une liste des plugins
    	App::import('Component', 'EyPluginComponent'); // on charge le composant des plugins
    	$this->EyPlugin = new EyPluginComponent;
    	return $this->EyPlugin->getPluginsActive(); // et on retourne la liste
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
        App::import('Component', 'LangComponent'); // on charge le composant de langue
        $Lang = new LangComponent;

        App::uses('HtmlHelper', 'View/Helper');
        $this->Html = new HtmlHelper(new View());

        if(!empty(self::$vars) && is_array(self::$vars)) {
            foreach (self::$vars as $key => $value) {
                if(is_bool($value)) {
                    $value = ($value) ? 'true' : 'false';
                }
                eval('$'.$key.' = '.$value.';');
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