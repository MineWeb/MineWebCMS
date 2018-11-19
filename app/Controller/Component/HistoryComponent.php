<?php

/**
* Composant qui gère les différents historiques
**/
App::uses('CakeObject', 'Core');

class HistoryComponent extends CakeObject {

  private $controller;

  function shutdown($controller) {
  }

  function beforeRender($controller) {
  }

  function beforeRedirect() {
  }

  function initialize($controller) {
    $this->controller = $controller;
    $this->controller->set('History', $this);
  }

  function startup($controller) {
  }

  function set($action, $category, $optionnal = null, $user_id = null) { // Ajoute une entrée dans l'historique général
      // j'inclue le fichier lang
    $this->User = ClassRegistry::init('User');

    $user_id = (empty($user_id)) ? $this->User->getKey('id') : $user_id;

    $this->History = ClassRegistry::init('History'); // le model history
    $this->History->read(null, null);
    $this->History->set(array(
      'action' => $action,
      'category' => $category,
      'user_id' => $user_id,
      'other' => $optionnal
      ));
    if($this->History->save()) {
      return true;
    } else {
      return false;
    }
  }

  function get($category = false, $limit = false, $date = false, $action = false) { // récupére tout l'historique ou seulement une catégorie
      // j'inclue le fichier lang
    $this->History = ClassRegistry::init('History'); // le model history

    if($category != false) {
      $array['conditions']['category'] = $category;
    }
    if($limit != false) {
      $array['limit'] = $limit;
    }
    if($date != false) {
      $array['conditions']['created LIKE'] = $date.'%';
    }
    if($action != false) {
      $array['conditions']['action'] = $action;
    }
    $array['order'] = 'id DESC';
    $search_history = $this->History->find('all', $array);

    $i = 0;

    $this->Lang = $this->controller->Lang;

    foreach ($search_history as $key => $value) { // je remplace les actions par leur traduction (ex: BUY_ITEM devient Achat d'un article)
      $search_history[$i]['History']['action'] = str_replace($value['History']['action'], $this->Lang->get($value['History']['action']), $value['History']['action']);
      $i++;
    }
    return $search_history;
  }

  function get_by_author($author) { // récupére tout l'historique d'un utilisateur
      // j'inclue le fichier lang
    $this->Lang = $this->controller->Lang;

    $this->History = ClassRegistry::init('History'); // le model history
    $search_history = $this->History->find('all', array('conditions' => array('author' => $author))); // je cherche l'historique de l'utilisateur
    $i = 0;
    foreach ($search_history as $key => $value) { // je remplace les actions par leur traduction (ex: BUY_ITEM devient Achat d'un article)
      $search_history[$i]['History']['action'] = str_replace($value['History']['action'], $this->Lang->get($value['History']['action']), $value['History']['action']);
      $i++;
    }
    return $search_history;
  }

}
