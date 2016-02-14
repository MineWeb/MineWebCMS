<?php

class ThemeController extends AppController{

	function admin_index() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->set('title_for_layout',$this->Lang->get('THEME__LIST'));
			$this->layout = 'admin';

			$this->set('themesAvailable', $this->Theme->getThemesOnAPI());
			$this->set('themesInstalled', $this->Theme->getThemesInstalled());

		} else {
			$this->redirect('/');
		}
	}

	function admin_enable($slug = false) {
		if($this->isConnected AND $this->User->isAdmin()) {
			if($slug != false) {

				$this->layout = null;
				$this->Configuration->set('theme', $slug);
				$this->History->set('SET_THEME', 'theme');
				$this->Session->setFlash($this->Lang->get('THEME__ENABLED_SUCCESS'), 'default.success');
				$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
			} else {
				$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	function admin_delete($slug = false) {
		if($this->isConnected AND $this->User->isAdmin()) {
			if($slug != false) {

				$this->layout = null;
				if($this->Configuration->get('theme') != $slug) {
					clearDir(ROOT.'/app/View/Themed/'.$slug);
					$this->History->set('DELETE_THEME', 'theme');
					$this->Session->setFlash($this->Lang->get('THEME__DELETE_SUCCESS'), 'default.success');
					$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
				} else {
					$this->Session->setFlash($this->Lang->get('THEME__CANT_DELETE_IF_ACTIVE'), 'default.error');
					$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	function admin_install($apiID = false) {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {
			if($apiID != false) {

				$install = $this->Theme->install($apiID);

				if(!$install) {
					$this->Session->setFlash($this->Lang->get('ERROR__INTERNAL_ERROR'), 'default.error');
          $this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
				}

				$this->History->set('INSTALL_THEME', 'theme');
				$this->Session->setFlash($this->Lang->get('THEME__INSTALL_SUCCESS'), 'default.success');
				$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
			} else {
				$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	function admin_update($apiID = false) {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {
			if($apiID != false) {

				$update = $this->Theme->update($apiID);
				if($update) {
					$this->History->set('UPDATE_THEME', 'theme');
					$this->Session->setFlash($this->Lang->get('THEME__UPDATE_SUCCESS'), 'default.success');
					$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
				} else {
					$this->Session->setFlash($this->Lang->get('ERROR__INTERNAL_ERROR'), 'default.error');
					$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	function admin_custom($slug = false) {
		if($this->isConnected AND $this->User->isAdmin()) {
			if($slug != false) {
				$this->set('title_for_layout',$this->Lang->get('THEME__CUSTOMIZATION'));
				$this->layout = 'admin';

				list($theme_name, $config) = $this->Theme->getCustomData($slug);
				$this->set(compact('config', 'theme_name'));

				if($this->request->is('post')) {
					if($this->Theme->processCustomData($slug, $this->request)) {
						$this->Session->setFlash($this->Lang->get('THEME__CUSTOMIZATION_SUCCESS'), 'default.success');
					}
					$this->redirect(array('controller' => 'theme', 'action' => 'custom', 'admin' => true, $slug));
				}

				if($slug != "default") {
					$this->render(DS.'Themed'.DS.$slug.DS.'config'.DS.'view');
				}
			} else {
				$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

}
