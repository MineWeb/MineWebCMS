<?php

class PermissionsController extends AppController {

	public $components = array('Session', 'History');

	function admin_index() {
	    if (!$this->Permissions->can('MANAGE_PERMISSIONS'))
	        throw new ForbiddenException();
        $this->set('title_for_layout', $this->Lang->get('PERMISSIONS__LABEL'));
        $this->layout = 'admin';

        $this->loadModel('Rank');
        $custom_ranks = $this->Rank->find('all');
        $this->set(compact('custom_ranks'));


        if ($this->request->is('post')) {
            $permissions = [];
            foreach ($this->request->data as $permission => $checked) {
                list($permission, $rank) = explode('-', $permission);
                $permissions[$rank][] = $permission;
            }

            $this->loadModel('Permission');
            foreach ($permissions as $rank => $permission) {
                if (!empty(($row = $this->Permission->find('first', ['conditions' => ['rank' => $rank]]))))
                    $this->Permission->read(null, $row['Permission']['id']);
                else
                    $this->Permission->create();
                $this->Permission->set([
                    'permissions' => serialize($permission),
                    'rank' => $rank
                ]);
                $this->Permission->save();
            }

            $this->Session->setFlash($this->Lang->get('PERMISSIONS__SUCCESS_SAVE'), 'default.success');
        }

        $this->Permissions->ranks = [];
        $this->Permissions->permModel->cacheQueries = false;
        $this->set('permissions', $this->Permissions->get_all());
	}

	function admin_add_rank() {
		if($this->isConnected && $this->Permissions->can('MANAGE_PERMISSIONS')) {
			$this->autoRender = false;
			$this->response->type('json');
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

					$this->Session->setFlash($this->Lang->get('USER__RANK_ADD_SUCCESS'), 'default.success');
					$this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('USER__RANK_ADD_SUCCESS'))));

				} else {
					$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));
				}

			} else {
				$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST'))));
			}
		} else {
			$this->redirect('/');
		}
	}

	function admin_delete_rank($id = false) {
		if($this->isConnected && $this->Permissions->can('MANAGE_PERMISSIONS')) {
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

				$this->Session->setFlash($this->Lang->get('USER__RANK_DELETE_SUCCESS'), 'default.success');
				$this->redirect(array('controller' => 'permissions', 'action' => 'index', 'admin' => true));

			} else {
				$this->redirect(array('controller' => 'permissions', 'action' => 'index', 'admin' => true));
			}

		} else {
			$this->redirect('/');
		}
	}

}
