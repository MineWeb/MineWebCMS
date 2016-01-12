<?php
App::uses('CakeEvent', 'Event');

class News extends AppModel {

	public function find($conditions = null, $fields = array(), $order = null, $recursive = null) {
		$result = Cache::read('news', 'data');
    if($result === false || !isset($result[md5(serialize(func_get_args()))])) {
        $result[md5(serialize(func_get_args()))] = parent::find($conditions, $fields, $order, $recursive);
        Cache::write('news', $result, 'data');
    }
    return $result[md5(serialize(func_get_args()))];
	}

	public function afterSave($created, $options = array()) {
		if($created) {
			// nouvel enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('afterAddNews', $this));
		} else {
			// modification d'un enregistrement
			$this->getEventManager()->dispatch(new CakeEvent('afterEditNews', $this));

			// on supprime le cache
			Cache::delete('news', 'data');
		}
	}

	public function afterDelete($cascade = true) {
		$this->getEventManager()->dispatch(new CakeEvent('afterDeleteNews', $this));

		// on supprime le cache
		Cache::delete('news', 'data');
	}

}
