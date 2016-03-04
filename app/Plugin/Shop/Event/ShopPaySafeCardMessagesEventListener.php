<?php
App::uses('CakeEventListener', 'Event');

class ShopPaySafeCardMessagesEventListener implements CakeEventListener {

  private $controller;

  public function __construct($request, $response, $controller) {
    $this->controller = $controller;
  }

  public function implementedEvents() {
      return array(
          'onLoadPage' => 'checkPSCMessages',
      );
  }

  public function checkPSCMessages($event) {
    if($this->controller->params['controller'] == "user" && $this->controller->params['action'] == "profile") {
      // On chage les models
      $this->User = ClassRegistry::init('User');
      $this->PaysafecardMessage = ClassRegistry::init('Shop.PaysafecardMessage');


      $search_psc_msg = $this->PaysafecardMessage->find('all', array('conditions' => array('user_id' =>  $this->User->getKey('id'))));
      if(!empty($search_psc_msg)) {
        $this->PaysafecardMessage->deleteAll(array('user_id' =>  $this->User->getKey('id')));

        ModuleComponent::$vars['search_psc_msg'] = $search_psc_msg;
      }
    }
  }
}
