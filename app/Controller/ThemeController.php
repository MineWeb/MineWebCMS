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
					$this->Session->setFlash($this->Lang->get('INTERNAL_ERROR'), 'default.error');
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
					$this->Session->setFlash($this->Lang->get('INTERNAL_ERROR'), 'default.error');
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
/*
				if($theme_name == "default") {
					$config = file_get_contents(ROOT.'/config/theme.default.json');
					$config = json_decode($config, true);
					$this->set(compact('config'));
					$this->set(compact('theme_name'));

					if($this->request->is('post')) {
						$data = json_encode($this->request->data, JSON_PRETTY_PRINT);
						$fp = fopen(ROOT.'/config/theme.default.json',"w+");
						fwrite($fp, $data);
						fclose($fp);
						$this->Session->setFlash($this->Lang->get('THEME__CUSTOMIZATION_SUCCESS'), 'default.success');
						$this->redirect(array('controller' => 'theme', 'action' => 'custom/default', 'admin' => true));
					}
				} else {
					$this->set(compact('theme_name'));
					$config = file_get_contents(ROOT.'/app/View/Themed/'.$theme_name.'/config/config.json');
					$config = json_decode($config, true);
					$this->set(compact('config'));

					if($this->request->is('post')) {
						$config = json_decode(file_get_contents(ROOT.'/app/View/Themed/'.$theme_name.'/config/config.json'), true);
						$this->request->data['version'] = $config['version'];

						if(!isset($this->request->data['img_edit'])) {
							$isValidImg = $this->Util->isValidImage($this->request, array('png', 'jpg', 'jpeg'));

							if(!$isValidImg['status'] && $isValidImg['msg'] != $this->Lang->get('FORM__EMPTY_IMG')) {
								$this->Session->setFlash($isValidImg['msg'], 'default.error');
								exit;
							} else {
								if(isset($isValidImg['infos'])) {
									$infos = $isValidImg['infos'];
								} else {
									$infos = false;
								}
							}

							if($infos) {
								$url_img = WWW_ROOT.'img'.DS.'uploads'.DS.'theme_logo.'.$infos['extension'];

								if(!$this->Util->uploadImage($this->request, $url_img)) {
									$this->Session->setFlash($this->Lang->get('FORM__ERROR_WHEN_UPLOAD'), 'default.error');
									exit;
								}

								$this->request->data['logo'] = Router::url('/').'img'.DS.'uploads'.DS.'theme_logo.'.$infos['extension'];
							} else {
								$this->request->data['logo'] = false;
							}
						} else {
							$this->request->data['logo'] = $config['logo'];
						}

						$data = json_encode($this->request->data, JSON_PRETTY_PRINT);
						$fp = fopen(ROOT.'/app/View/Themed/'.$theme_name.'/config/config.json',"w+");
						fwrite($fp, $data);
						fclose($fp);
						$this->Session->setFlash($this->Lang->get('THEME__CUSTOMIZATION_SUCCESS'), 'default.success');
						$this->redirect(array('controller' => 'theme', 'action' => 'custom/'.$theme_name, 'admin' => true));
					}

					$this->render('/Themed/'.$theme_name.'/config/view');
				}*/
			} else {
				$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

}
