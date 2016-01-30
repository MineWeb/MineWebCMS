<?php
class PluginController extends AppController{

	function admin_index() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->set('title_for_layout',$this->Lang->get('PLUGIN__LIST'));
			$this->layout = 'admin';
		} else {
			$this->redirect('/');
		}
	}

	function admin_delete($id = false) {
		if($this->isConnected AND $this->User->isAdmin()) {
			if($id != false) {

				if($this->EyPlugin->delete($id)) {
					$this->History->set('DELETE_PLUGIN', 'plugin');
					$this->Session->setFlash($this->Lang->get('PLUGIN__DELETE_SUCCESS'), 'default.success');
					$this->redirect(array('controller' => 'plugin', 'action' => 'index', 'admin' => true));
				} else {
					$this->Session->setFlash($this->Lang->get('INTERNAL_ERROR'), 'default.error');
					$this->redirect(array('controller' => 'plugin', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'plugin', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	function admin_enable($id = false) {
		if($this->isConnected AND $this->User->isAdmin()) {
			if($id != false) {

				if($this->EyPlugin->enable($id)) {
					$this->History->set('ENABLE_PLUGIN', 'plugin');
					$this->Session->setFlash($this->Lang->get('PLUGIN__ENABLE_SUCCESS'), 'default.success');
					$this->redirect(array('controller' => 'plugin', 'action' => 'index', 'admin' => true));
				} else {
					$this->Session->setFlash($this->Lang->get('INTERNAL_ERROR'), 'default.error');
					$this->redirect(array('controller' => 'plugin', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'plugin', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	function admin_disable($id = false) {
		if($this->isConnected AND $this->User->isAdmin()) {
			if($id != false) {

				if($this->EyPlugin->disable($id)) {
					$this->History->set('DISABLE_PLUGIN', 'plugin');
					$this->Session->setFlash($this->Lang->get('PLUGIN__DISABLE_SUCCESS'), 'default.success');
					$this->redirect(array('controller' => 'plugin', 'action' => 'index', 'admin' => true));
				} else {
					$this->Session->setFlash($this->Lang->get('INTERNAL_ERROR'), 'default.error');
					$this->redirect(array('controller' => 'plugin', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'plugin', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	function admin_install($apiID = false, $slug = false) {

		$this->autoRender = false;

		if($this->isConnected AND $this->User->isAdmin()) {
			if($apiID != false AND $slug != false) {

				if($this->EyPlugin->download($apiID, $slug, true)) {
					$this->History->set('INSTALL_PLUGIN', 'plugin');

					$this->loadModel('Plugin');
					$search = $this->Plugin->find('first', array('conditions' => array('apiID' => $apiID)));

					echo json_encode(array(
						'statut' => 'success',
						'plugin' => array(
							'name' => $this->EyPlugin->findPluginByApiID($search['Plugin']['apiID'])->name,
							'DBid' => $search['Plugin']['id'],
							'author' => $search['Plugin']['author'],
							'dateformatted' => $this->Lang->date($search['Plugin']['created']),
							'version' => $search['Plugin']['version'],
						)
					));
				} else {
					echo json_encode(array('statut' => 'error', 'msg' => $this->Session->read('Message.flash.message')));
					$this->Session->delete('Message.flash');
				}
			} else {
				echo json_encode(array('statut' => 'error', 'msg' => $this->Session->read('Message.flash.message')));
				$this->Session->delete('Message.flash');
			}
		} else {
			throw new ForbiddenException();
		}
	}

	function admin_update($plugin_id, $plugin_name) {
		if($this->isConnected AND $this->User->isAdmin()) {
			if($plugin_id != false AND $plugin_name != false) {

				if($this->EyPlugin->update($plugin_id, $plugin_name)) {
					$this->History->set('UPDATE_PLUGIN', 'plugin');
					$this->Session->setFlash($this->Lang->get('PLUGIN__UPDATE_SUCCESS'), 'default.success');
					$this->redirect(array('controller' => 'plugin', 'action' => 'index', 'admin' => true));
				} else {
					$this->redirect(array('controller' => 'plugin', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'plugin', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

}
