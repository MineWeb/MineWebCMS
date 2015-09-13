<?php 

class PermissionsController extends AppController {

	public $components = array('Session', 'History', 'Connect');

	function admin_index() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout', $this->Lang->get('PERMISSIONS'));
			$this->layout = 'admin';

			$this->loadModel('Rank');
			$custom_ranks = $this->Rank->find('all');
			$this->set(compact('custom_ranks'));

			if($this->request->is('post')) {
				foreach ($this->request->data as $key => $value) {
					$perm = explode('-', $key);
					$data[$perm['1']][] = $perm['0'];
					unset($perm);
				}
				$this->loadModel('Permissions');
				foreach ($data as $key => $value) {
					$id = $this->Permissions->find('all', array('conditions' => array('rank' => $key)));
					if(!empty($id)) {
						$id = $id['0']['Permissions']['id'];
						$sql_data = array('permissions' => serialize($value));
						$this->Permissions->read(null, $id);
					} else {
						$sql_data = array('rank' => $key, 'permissions' => serialize($value));
						$this->Permissions->create();
					}
					$this->Permissions->set($sql_data);
					$this->Permissions->save();
				}

			}

		} else {
			$this->redirect('/');
		}
	}

	function admin_add_rank() {
		if($this->Connect->connect() && $this->Connect->if_admin()) {
			$this->autoRender = false;
			if($this->request->is('ajax')) {

				if(!empty($this->request->data['name'])) {

					$this->loadModel('Rank');

					// Le rank_id | L'id du rank utilisé dans le composant des permissions & dans la colonne rank des utilisateurs
					// Le rank_id de base pour les rangs personnalisés commence à partir de 10
					$rank_id = $this->Rank->find('first', array('limit' => '1', 'order' => 'rank_id desc'));
					if(!empty($rank_id)) {
						$rank_id = $rank_id['Rank']['rank_id'] + 1;
					} else {
						$rank_id = 10;
					}

					// on save
					$this->Rank->create();
					$this->Rank->set(array('name' => $this->request->data['name'], 'rank_id' => $rank_id));
					$this->Rank->save();

					$this->History->set('ADD_RANK', 'permissions');

					$this->Session->setFlash($this->Lang->get('SUCCESS_ADD_RANK'), 'default.success');
					echo $this->Lang->get('SUCCESS_ADD_RANK').'|true';

				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}

			} else {
				echo $this->Lang->get('NOT_POST').'|false';
			}
		} else {
			$this->redirect('/');
		}
	}

	function admin_delete_rank($id = false) {
		if($this->Connect->connect() && $this->Connect->if_admin()) {
			$this->autoRender = false;

			$this->loadModel('Rank');
			$search = $this->Rank->find('first', array('conditions' => array('rank_id' => $id)));
			if(!empty($search)) {

				$this->Rank->delete($search['Rank']['id']);

				$this->loadModel('Permission');
				$search_perm = $this->Permission->find('first', array('conditions' => array('id' => $id)));
				if(!empty($search_perm)) {
					$this->Permission->delete($search_perm['Permission']['id']);
				}

				$this->Session->setFlash($this->Lang->get('SUCCESS_DELETE_RANK'), 'default.success');
				$this->redirect(array('controller' => 'permissions', 'action' => 'index', 'admin' => true));

			} else {
				$this->redirect(array('controller' => 'permissions', 'action' => 'index', 'admin' => true));
			}

		} else {
			$this->redirect('/');
		}
	}

}