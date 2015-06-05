<?php
App::uses('CakeEvent', 'Event');

class History extends AppModel {

	public function afterSave($created, $options = array()) {
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

	public function afterDelete() {
		$this->getEventManager()->dispatch(new CakeEvent('deleteHistory', $this));
	}

}