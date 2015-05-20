<?php 

class PermissionsController extends AppController {

	public $components = array('Session', 'History', 'Connect');

	function admin_index() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout', $this->Lang->get('PERMISSIONS'));
			$this->layout = 'admin';

			if($this->request->is('post')) {
				foreach ($this->request->data as $key => $value) {
					$perm = explode('-', $key);
					$data[$perm['1']][] = $perm['0'];
					unset($perm);
				}
				$this->loadModel('Permissions');
				foreach ($data as $key => $value) {
					$id = $this->Permissions->find('all', array('conditions' => array('rank' => $key)))['0']['Permissions']['id'];
					$this->Permissions->read(null, $id);
					$this->Permissions->set(array('permissions' => serialize($value)));
					$this->Permissions->save();
				}
			}

		} else {
			$this->redirect('/');
		}
	}

}