<?php
App::uses('CakeEvent', 'Event');

class History extends AppModel {

	public function beforeSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			
			switch ($this->data['History']['action']) {
				case 'BUY_ITEM':
					$this->getEventManager()->dispatch(new CakeEvent('onBuy', $this));
					break;
				case 'BUY_MONEY':
					$this->getEventManager()->dispatch(new CakeEvent('addMoney', $this));
					break;
				
				default:
					$this->getEventManager()->dispatch(new CakeEvent('addHistory', $this));
					break;
			}
		}
	}

	public function beforeDelete() {
		$this->getEventManager()->dispatch(new CakeEvent('deleteHistory', $this));
	}

}