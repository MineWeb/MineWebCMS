<?php
App::uses('CakeObject', 'Core');

class EySecurityComponent extends CakeObject
{
    private $controller;

    function shutdown($controller)
    {
    }

    function beforeRender($controller)
    {
    }

    function beforeRedirect()
    {
    }

    function startup($controller)
    {
    }

    function initialize($controller)
    {
        $this->controller = $controller;
    }

    public function xssProtection($string)
    {

        require_once ROOT . '/vendors/anti-xss/AntiXSS.php';
        return htmLawed($string, ['safe' => 1]);

    }
}
