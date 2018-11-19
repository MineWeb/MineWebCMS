<?php
App::uses('CakeObject', 'Core');

class ModuleComponent extends CakeObject
{

    protected $controller;
    static public $vars;

    function shutdown($controller)
    {
    }

    function beforeRender($controller)
    {
    }

    function beforeRedirect()
    {
    }

    function initialize($controller)
    {
        $this->controller = $controller;
        $this->controller->set('Module', $this);
    }

    function startup($controller)
    {
    }

    private function loadPlugins()
    {
        return $this->controller->EyPlugin->getPluginsActive();
    }

    private function listModules()
    {
        $modules = [];
        $plugins = $this->loadPlugins();
        foreach ($plugins as $plugin) {
            $files = @scandir($this->controller->EyPlugin->pluginsFolder . DS . $plugin->slug . DS . 'Modules');
            if (!$files) continue;
            $files = array_delete_value($files, '.');
            $files = array_delete_value($files, '..');
            $files = array_delete_value($files, '.DS_Store');
            foreach ($files as $filename) {
                $modules[explode('.', $filename)[0]][] = $plugin->slug;
            }
        }
        return $modules;
    }

    public function loadModules($name)
    {
        $HTML = '';
        $list = $this->listModules();
        if (!isset($list[$name]))
            return false;

        $Lang = $this->controller->Lang;
        $Configuration = $this->controller->Configuration;
        App::uses('HtmlHelper', 'View/Helper');
        $this->Html = new HtmlHelper(new View());

        if (!empty(self::$vars) && is_array(self::$vars))
            $vars = array_merge(self::$vars, $this->controller->viewVars);
        else
            $vars = $this->controller->viewVars;
        extract($vars);

        foreach ($list[$name] as $pluginSlug) {
            ob_start();
            $file = $this->controller->EyPlugin->pluginsFolder . DS . $pluginSlug . DS . 'Modules' . DS . $name . '.ctp';
            $themeFile = ROOT . DS . 'app' . DS . 'View' . DS . 'Themed' . DS . $this->controller->theme . DS . 'Plugin' . DS . $pluginSlug . DS . 'Modules' . DS . $name . '.ctp';
            if (file_exists($themeFile))
                $file = $themeFile;
            include $file;
            $HTML = $HTML . "\n" . ob_get_clean();
        }
       return $HTML;
    }

}
