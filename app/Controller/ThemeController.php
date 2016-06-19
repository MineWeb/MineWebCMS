<?php

class ThemeController extends AppController{

	function admin_index() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_THEMES')) {

			$this->set('title_for_layout',$this->Lang->get('THEME__LIST'));
			$this->layout = 'admin';

			$this->set('themesAvailable', $this->Theme->getThemesOnAPI(true, true));
			$this->set('themesInstalled', $this->Theme->getThemesInstalled());

		} else {
			$this->redirect('/');
		}
	}

	function admin_enable($slug = false) {
		if($this->isConnected AND $this->Permissions->can('MANAGE_THEMES')) {
			if($slug != false) {

				$this->layout = null;
				$this->Configuration->setKey('theme', $slug);
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
		if($this->isConnected AND $this->Permissions->can('MANAGE_THEMES')) {
			if($slug != false) {

				$this->layout = null;
				if($this->Configuration->getKey('theme') != $slug) {
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
		if($this->isConnected AND $this->Permissions->can('MANAGE_THEMES')) {
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
		if($this->isConnected AND $this->Permissions->can('MANAGE_THEMES')) {
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
		if($this->isConnected AND $this->Permissions->can('MANAGE_THEMES')) {
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
					$this->render(DS.'Themed'.DS.$slug.DS.'Config'.DS.'view');
				}
			} else {
				$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_custom_files($slug) {
		if($this->isConnected AND $this->Permissions->can('MANAGE_THEMES')) {
			$this->layout = 'admin';


			App::uses('Folder', 'Utility');
			App::uses('File', 'Utility');

			if($slug == "default") {
				$CSSfolder = ROOT.DS.'app'.DS.'webroot'.DS.'css';
			} else {
				$CSSfolder = ROOT.DS.'app'.DS.'View'.DS.'Themed'.DS.$slug.DS.'webroot'.DS.'css';
			}

			$dir = new Folder($CSSfolder);

			$files = $dir->findRecursive('.*\.css');

			foreach ($files as $path) {

				$file = new File($path);
				$basename = substr($path, strlen($CSSfolder));

				$css_files[] = array(
					'basename' => $basename,
					'name' => $file->name
				);
			}

			$this->set(compact('slug', 'css_files'));
		} else {
			throw new ForbiddenException();
		}
	}

	public function admin_get_custom_file($slug) {
		if($this->isConnected AND $this->Permissions->can('MANAGE_THEMES')) {

			$this->autoRender = false;

			$file = func_get_args();
			unset($file[0]);

			$file = implode(DS, $file);
			$ext = pathinfo($file, PATHINFO_EXTENSION);

			if($slug == "default") {
				$CSSfolder = ROOT.DS.'app'.DS.'webroot'.DS.'css';
			} else {
				$CSSfolder = ROOT.DS.'app'.DS.'View'.DS.'Themed'.DS.$slug.DS.'webroot'.DS.'css';
			}

			if(!file_exists($CSSfolder.DS.$file) || $ext != 'css') {
				throw new NotFoundException();
			}

			$get = @file_get_contents($CSSfolder.DS.$file);

			$this->response->body($get);

		} else {
			throw new ForbiddenException();
		}
	}

	public function admin_save_custom_file($slug) {
		if($this->isConnected AND $this->Permissions->can('MANAGE_THEMES')) {

			$this->autoRender = false;

			$file = $this->request->data['file'];
			$ext = pathinfo($file, PATHINFO_EXTENSION);
			$content = $this->request->data['content'];

			if($slug == "default") {
				$CSSfolder = ROOT.DS.'app'.DS.'webroot'.DS.'css';
			} else {
				$CSSfolder = ROOT.DS.'app'.DS.'View'.DS.'Themed'.DS.$slug.DS.'webroot'.DS.'css';
			}

			if(!file_exists($CSSfolder.DS.$file) || $ext != 'css') {
				throw new NotFoundException();
			}

			@file_put_contents($CSSfolder.DS.$file, $content);

			$this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('THEME__CUSTOM_FILES_FILE_CONTENT_SAVE_SUCCESS'))));

		} else {
			throw new ForbiddenException();
		}
	}

}
