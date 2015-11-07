<?php

/**
* Composant des statistiques
* @author Eywek
* Avec l'aide de : http://openclassrooms.com/courses/des-statistiques-pour-votre-site
**/

/**
* BDD
*
* -- Table visits --
*
* ip
* created
* referer
* location
*
* -- Table rush_hours --
*
* created 
* visits
*
* -- connected --
*
* ip
* created
* location
*
**/
App::uses('AppHelper', 'View/Helper');
App::uses('CakeSession', 'Model/Datasource');
class StatisticsComponent extends Object {
  
  function shutdown(&$controller) {
  }

  function beforeRender(&$controller) {
  }
  
  function beforeRedirect() { 
  }

  function initialize(&$controller) {
  }

  function startup(&$controller) {
    $cookie = CakeSession::read('visit_check');
    if(!isset($cookie) OR empty($cookie)) {
      $this->Visit = ClassRegistry::init('Visit');
      $visits = $this->Visit->find('all', array('conditions' => array('ip' => $_SERVER["REMOTE_ADDR"], 'created LIKE' => date('Y-m-d').'%')));
      if(empty($visits)) {
        if(!empty($_SERVER['HTTP_REFERER'])) {
          $referer = $_SERVER['HTTP_REFERER'];
        } else {
          $referer = 'null';
        }
        $language = (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'null';
        $language = $language{0}.$language{1};
        $this->Visit->read(null, null);
        $this->Visit->set(array('ip' => $_SERVER["REMOTE_ADDR"], 'referer' => $referer, 'lang' => $language, 'navigator' => $_SERVER['HTTP_USER_AGENT'], 'page' => "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
        $this->Visit->save();
      }
      CakeSession::write('visit_check', true, true, '1 day');
    }
  }

  function get_all_visits() {
    $this->Visit = ClassRegistry::init('Visit');
    return $this->Visit->find('count');
  }

  function get_visits_by_day($day) { // $day au format : date('Y-m-d')
    $this->Visit = ClassRegistry::init('Visit');
    return $this->Visit->find('count', array('conditions' => array('created LIKE' => $day.'%')));
  }

  function get_all_referer() {
    $this->Visit = ClassRegistry::init('Visit');
    $referers = $this->Visit->find('all');
    foreach ($referers as $key => $value) {
      $referer[] = $value['Visit']['referer'];
    }
    $referers = $referer;
    return $referers;
  }

  function get_referers() {
    $this->Visit = ClassRegistry::init('Visit');
    $referers = $this->Visit->find('all', array('group' => 'referer'));
    foreach ($referers as $key => $value) {
      $nbr = $this->Visit->find('count', array('conditions' => array('referer' => $value['Visit']['referer'])));
      $result[$value['Visit']['referer']] = $nbr;
    }
    arsort($result);
    return $result;
  }

  function get_pages() {
    $this->Visit = ClassRegistry::init('Visit');
    $pages = $this->Visit->find('all', array('group' => 'page'));
    foreach ($pages as $key => $value) {
      $nbr = $this->Visit->find('count', array('conditions' => array('page' => $value['Visit']['page'])));
      $result[$value['Visit']['page']] = $nbr;
    }
    arsort($result);
    return $result;
  }

  function get_language() {
    $this->Visit = ClassRegistry::init('Visit');
    $pages = $this->Visit->find('all', array('group' => 'lang'));
    foreach ($pages as $key => $value) {
      $nbr = $this->Visit->find('count', array('conditions' => array('lang' => $value['Visit']['lang'])));
      $result[$value['Visit']['lang']] = $nbr;
    }
    arsort($result);
    return $result;
  }

  function get_all_navigators() {
    $this->Visit = ClassRegistry::init('Visit');
    $navigators = $this->Visit->find('all');
    foreach ($navigators as $key => $value) {
      $navigator[] = $value['Visit']['navigator'];
    }
    $navigators = $navigator;
    return $navigators;
  }
}