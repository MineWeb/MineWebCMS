<?php
App::uses('CakeEventListener', 'Event');

class ShopPaySafeCardMessagesEventListener implements CakeEventListener {
    public function implementedEvents() {
        return array(
            'onLoadPage' => 'checkPSCMessages',
        );
    }

    public function checkPSCMessages($event) {

      // On chage les models
      $this->User = ClassRegistry::init('User');
      $this->PaysafecardMessage = ClassRegistry::init('Shop.PaysafecardMessage');


      $search_psc_msg = $this->PaysafecardMessage->find('all', array('conditions' => array('to' =>  $this->User->getKey('pseudo'))));
      if(!empty($search_psc_msg)) {
        $this->PaysafecardMessage->deleteAll(array('to' =>  $this->User->getKey('pseudo')));

        ModuleComponent::$vars['search_psc_msg'] = $search_psc_msg;
      }

    }
}
