<?php
class ThemeController extends AppController{
	
	function admin_index() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout',$this->Lang->get('THEME_LIST'));
			$this->layout = 'admin';
			$dir = ROOT.'/app/View/Themed';
			$themes = scandir($dir);
    		$themes = array_delete_value($themes, '.');
    		$themes = array_delete_value($themes, '..');
    		$themes = array_delete_value($themes, '.DS_Store');
			foreach ($themes as $key => $value) {
		      $list_themes[] = $value;
		    }
		    if(!empty($list_themes)) {
			    $themes = $list_themes;
			    $this->set(compact('themes'));
			    $free_themes_available = file_get_contents('http://mineweb.org/api/getFreeThemes');
			    $free_themes_available = json_decode($free_themes_available, true);
			    foreach ($free_themes_available as $key => $value) {
			      if(!in_array($value['name'], $themes)) {
			        $free_themes[] = array('theme_id' => $value['theme_id'], 'name' => $value['name'], 'author' => $value['author'], 'version' => $value['version']);
			      }
			    }
			} else {
				$themes = null;
			    $this->set(compact('themes'));
			    $free_themes_available = file_get_contents('http://mineweb.org/api/getFreeThemes');
			    $free_themes_available = json_decode($free_themes_available, true);
			    foreach ($free_themes_available as $key => $value) {
			       $free_themes[] = array('theme_id' => $value['theme_id'], 'name' => $value['name'], 'author' => $value['author'], 'version' => $value['version']);
			    }
			}
		    if(!empty($free_themes)) {
		    	$free_themes_available = $free_themes;
		    } else {
		    	$free_themes_available = null;
		    }
		    $this->set(compact('free_themes_available'));

		} else {
			$this->redirect('/');
		}
	}

	function admin_enable($name = false) {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			if($name != false) {
				 
				$this->layout = null;
				$this->Configuration->set('theme', $name);
				$this->History->set('SET_THEME', 'theme');
				$this->Session->setFlash($this->Lang->get('SUCCESS_ENABLED_THEME'), 'default.success');
				$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
			} else {
				$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	function admin_delete($name = false) {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			if($name != false) {
				 
				$this->layout = null;
				if($this->Configuration->get('theme') != $name) {
					clearDir(ROOT.'/app/View/Themed/'.$name);
					$this->History->set('DELETE_THEME', 'theme');
					$this->Session->setFlash($this->Lang->get('SUCCESS_DELETE_THEME'), 'default.success');
					$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
				} else {
					$this->Session->setFlash($this->Lang->get('CANT_DELETE_THEME_IF_ACTIVE'), 'default.error');
					$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	function admin_install($theme_id = false, $theme_name = false) {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			if($theme_id != false AND $theme_name != false) {
				 
				if(unzip('http://mineweb.org/api/mineweb_themes/themes/'.$theme_id.'-'.$theme_name.'.zip', '../View/Themed')) {
					clearDir(ROOT.'/app/View/Themed/__MACOSX');
					$this->History->set('INSTALL_THEME', 'theme');
					$this->Session->setFlash($this->Lang->get('THEME_INSTALL_SUCCESS'), 'default.success');
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

	function admin_custom($theme_name = false) {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			if($theme_name != false) {
				$this->set('title_for_layout',$this->Lang->get('CUSTOMIZATION'));
				$this->layout = 'admin';
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
						$this->Session->setFlash($this->Lang->get('THEME_CUSTOMIZATION_SUCCESS'), 'default.success');
						$this->redirect(array('controller' => 'theme', 'action' => 'custom/default', 'admin' => true));
					}
				} else {
					$this->set(compact('theme_name'));
					$config = file_get_contents(ROOT.'/app/View/Themed/'.$theme_name.'/config/config.json');
					$config = json_decode($config, true);
					$this->set(compact('config'));

					if($this->request->is('post')) {
						$data = json_encode($this->request->data, JSON_PRETTY_PRINT);
						$fp = fopen(ROOT.'/app/View/Themed/'.$theme_name.'/config/config.json',"w+");
						fwrite($fp, $data);
						fclose($fp);
						$this->Session->setFlash($this->Lang->get('THEME_CUSTOMIZATION_SUCCESS'), 'default.success');
						$this->redirect(array('controller' => 'theme', 'action' => 'custom/'.$theme_name, 'admin' => true));
					}

					$this->render('/Themed/'.$theme_name.'/config/view');
				}
			} else {
				$this->redirect(array('controller' => 'theme', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

}