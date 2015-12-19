<?php
App::uses('CakeEventListener', 'Event');

class ShopBuyEventListener implements CakeEventListener {
    public function implementedEvents() {
        return array(
            'afterBuy' => 'afterBuy',
        );
    }

    public function afterBuy($event) {
        $item_name = $event->subject()['item_name'];
        if($item_name == "Cape") { // si c'est une cape
        	$this->User = ClassRegistry::init('User');
        	$search = $this->User->find('first', array('conditions' => array('pseudo' => $event->subject()['buyer'])));
        	if(!empty($search)) {
        		$this->User->read(null, $search['User']['id']);
        		$this->User->set(array('cape' => 1));
        		$this->User->save();
        	}
        }
        if($item_name == "Skin") { // si c'est un skin
        	$this->User = ClassRegistry::init('User');
        	$search = $this->User->find('first', array('conditions' => array('pseudo' => $event->subject()['buyer'])));
        	if(!empty($search)) {
        		$this->User->read(null, $search['User']['id']);
        		$this->User->set(array('skin' => 1));
        		$this->User->save();
        	}
        }
    }
}
