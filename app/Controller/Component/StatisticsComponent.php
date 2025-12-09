<?php

App::uses('AppHelper', 'View/Helper');
App::uses('CakeSession', 'Model/Datasource');
App::uses('CakeObject', 'Core');

class StatisticsComponent extends CakeObject
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

    function initialize($controller)
    {
        $this->controller = $controller;
    }

    function startup($controller)
    {
        $cookie = CakeSession::read('visit_check');
        if (!isset($cookie) || empty($cookie)) {
            $this->Visit = ClassRegistry::init('Visit');
            $this->Util = $this->controller->Util;

            $ip = $this->Util->getIP();
            $visits = $this->Visit->find('all', ['conditions' => ['ip' => $ip, 'created LIKE' => date('Y-m-d') . '%']]);

            if (empty($visits)) {
                if (!empty($_SERVER['HTTP_REFERER'])) {
                    $referer = htmlentities($_SERVER['HTTP_REFERER']);
                } else {
                    $referer = 'null';
                }

                $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? htmlentities($_SERVER['HTTP_USER_AGENT']) : 'null';
                $language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? htmlentities($_SERVER['HTTP_ACCEPT_LANGUAGE']) : 'null';

                if ($language !== 'null') {
                    $language = substr($language, 0, 2);
                }

                $page = 'http://' . htmlentities($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

                $this->Visit->create();
                $this->Visit->set([
                    'ip'        => $ip,
                    'referer'   => $referer,
                    'lang'      => $language,
                    'navigator' => $user_agent,
                    'page'      => $page,
                ]);
                $this->Visit->save();
            }

            CakeSession::write('visit_check', true);
        }
    }
}
