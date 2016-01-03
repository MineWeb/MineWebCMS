<?php

class NavbarController extends AppController {

	public $components = array('Session');

	public function admin_index() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_NAV')) {

			$this->set('title_for_layout',$this->Lang->get('NAVBAR'));
			$this->layout = 'admin';
			$this->loadModel('Navbar');
			$navbars = $this->Navbar->find('all', array('order' => 'order'));
			$this->set(compact('navbars'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_save_ajax() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_NAV')) {
			$this->layout = null;

			if($this->request->is('post')) {
				if(!empty($this->request->data)) {
					$data = $this->request->data['nav'];
					$data = explode('&', $data);
					$i = 1;
					foreach ($data as $key => $value) {
						$data2[] = explode('=', $value);
						$data3 = substr($data2[0][0], 0, -2);
						$data1[$data3] = $i;
						unset($data3);
						unset($data2);
						$i++;
					}
					$data = $data1;
					$this->loadModel('Navbar');
					foreach ($data as $key => $value) {
						$id = $this->Navbar->find('all', array('conditions' => array('name' => $key)));
						if(!empty($id)) {
							$id = $id[0]['Navbar']['id'];
							$this->Navbar->read(null, $id);
							$this->Navbar->set(array(
								'order' => $value
							));
							$this->Navbar->save();
						} else {
							$error = 1;
						}
					}
					if(empty($error)) {
						$this->History->set('EDIT_NAVBAR', 'navbar');
						echo $this->Lang->get('SUCCESS_SAVE_NAVBAR').'|true';
					} else {
						echo $this->Lang->get('INTERNAL_ERROR').'|false';
					}
				} else {
					echo $this->Lang->get('CANT_BE_EMPTY').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST' ,$language).'|false';
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_delete($id = false) {
		if($this->isConnected AND $this->Permissions->can('MANAGE_NAV')) {
			if($id != false) {

				$this->loadModel('Navbar');
				if($this->Navbar->delete($id)) {
					$this->History->set('DELETE_NAV', 'navbar');
					$this->Session->setFlash($this->Lang->get('NAV_DELETE_SUCCESS'), 'default.success');
					$this->redirect(array('controller' => 'navbar', 'action' => 'index', 'admin' => true));
				} else {
					$this->redirect(array('controller' => 'navbar', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'navbar', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_NAV')) {
			$this->layout = 'admin';

			$this->set('title_for_layout', $this->Lang->get('ADD_NAV'));
			$url_plugins = $this->EyPlugin->getPluginsActive();
			foreach ($url_plugins as $key => $value) {
				$slug = $value->slug;
				$url_plugins2[$slug] = $value->name;
			}
			if(!empty($url_plugins2)) {
				$url_plugins = $url_plugins2;
			} else {
				$url_plugins = array();
			}
			$this->loadModel('Page');
			$url_pages = $this->Page->find('all');
			foreach ($url_pages as $key => $value) {
				$url_pages2[$value['Page']['slug']] = $value['Page']['title'];
			}
			$url_pages = @$url_pages2;
			$this->set(compact('url_plugins'));
			$this->set(compact('url_pages'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add_ajax() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_NAV')) {
			$this->layout = null;

			if($this->request->is('post')) {
				if(!empty($this->request->data['name']) AND !empty($this->request->data['type'])) {
					$this->loadModel('Navbar');
					if($this->request->data['type'] == "normal") {
						if(!empty($this->request->data['url']) AND $this->request->data['url'] != "undefined") {
							$order = $this->Navbar->find('first', array('order' => array('order' => 'DESC')));
							$order = $order['Navbar']['order'];
							$order = intval($order) + 1;
							$open_new_tab = ($this->request->data['open_new_tab']) ? 1 : 0;
							$this->Navbar->read(null, null);
							$this->Navbar->set(array(
								'order' => $order,
								'name' => $this->request->data['name'],
								'type' => 1,
								'url' => $this->request->data['url'],
								'open_new_tab' => $open_new_tab
							));
							$this->Navbar->save();
							$this->History->set('ADD_NAV', 'navbar');
							echo $this->Lang->get('SUCCESS_NAV_ADD').'|true';
							$this->Session->setFlash($this->Lang->get('SUCCESS_NAV_ADD'), 'default.success');
						} else {
							echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
						}
					} else {
						if(!empty($this->request->data['url']) AND $this->request->data['url'] != "undefined") {
							$order = $this->Navbar->find('first', array('order' => array('order' => 'DESC')));
							$order = $order['Navbar']['order'];
							$order = intval($order) + 1;
							$open_new_tab = ($this->request->data['open_new_tab']) ? 1 : 0;
							$this->Navbar->read(null, null);
							$this->Navbar->set(array(
								'order' => $order,
								'name' => $this->request->data['name'],
								'type' => 2,
								'url' => '#',
								'submenu' => json_encode($this->request->data['url']),
								'open_new_tab' => $open_new_tab
							));
							$this->Navbar->save();
							$this->History->set('ADD_NAV', 'navbar');
							echo $this->Lang->get('SUCCESS_NAV_ADD').'|true';
							$this->Session->setFlash($this->Lang->get('SUCCESS_NAV_ADD'), 'default.success');
						} else {
							echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
						}
					}
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST' ,$language).'|false';
			}
		} else {
			$this->redirect('/');
		}
	}
}
