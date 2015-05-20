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
    $this->Visit = ClassRegistry::init('Visit');
    $visits = $this->Visit->find('all', array('conditions' => array('ip' => $_SERVER["REMOTE_ADDR"], 'created LIKE' => date('Y-m-d').'%')));
    if(empty($visits)) {
      if(!empty($_SERVER['HTTP_REFERER'])) {
        $referer = $_SERVER['HTTP_REFERER'];
      } else {
        $referer = 'null';
      }
      $this->Visit->read(null, null);
      $this->Visit->set(array('ip' => $_SERVER["REMOTE_ADDR"], 'referer' => $referer, 'navigator' => $_SERVER['HTTP_USER_AGENT'], 'page' => "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
      $this->Visit->save();
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

  function get_all_navigators() {
    $this->Visit = ClassRegistry::init('Visit');
    $navigators = $this->Visit->find('all');
    foreach ($navigators as $key => $value) {
      $navigator[] = $value['Visit']['navigator'];
    }
    $navigators = $navigator;
    return $navigators;
  }

  function get_rush_hours() {
    $this->Visit = ClassRegistry::init('Visit');
    $rush_hours = $this->Visit->find('Visit.created');

  }
}