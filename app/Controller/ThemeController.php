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
		      $list_themes[] = strtolower($value);
		    }
		    if(!empty($list_themes)) {
		    	unset($themes);
			    foreach ($list_themes as $key => $value) {
			    	$config = file_get_contents(ROOT.'/app/View/Themed/'.$value.'/config/config.json');
    				$config = json_decode($config, true);
			    	$themes[$value]['version'] = $config['version'];
			    }

			    $free_themes_available = file_get_contents('http://dev.eywek.fr/org/api/getFreeThemes');
			    $free_themes_available = json_decode($free_themes_available, true);
			    foreach ($free_themes_available as $key => $value) {
			      if(!in_array(strtolower($value['name']), $list_themes)) {
			        $free_themes[] = array('theme_id' => $value['theme_id'], 'name' => $value['name'], 'author' => $value['author'], 'version' => $value['version']);
			      }
			    }

			    $getAllThemes = file_get_contents('http://dev.eywek.fr/org/api/getAllThemes');
			    $getAllThemes = json_decode($getAllThemes, true);

			    foreach ($getAllThemes as $key => $value) {
			    	if(in_array(strtolower($value['name']), $list_themes)) {
			    		$themes[$value['name']]['last_version'] = $value['version'];
			    		$themes[$value['name']]['theme_id'] = $value['theme_id'];
			    	}
			    }

			    $this->set(compact('themes'));
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
		$this->autoRender = false;
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			if($theme_id != false AND $theme_name != false) {

				// get du zip sur mineweb.org
			    $url = 'http://mineweb.org/api/get_theme/'.$theme_id;
			    $secure = file_get_contents(ROOT.'/config/secure');
			    $secure = json_decode($secure, true);
			    $postfields = array(
			      'id' => $secure['id'],
			      'key' => $secure['key'],
			      'domain' => Router::url('/', true)
			    );

			    $postfields = json_encode($postfields);
			    $post[0] = rsa_encrypt($postfields, '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCvFK7LMlAnF8Hzmku9WGbHqYNb
ehNueKDbF/j4yYwf8WqIizB7k+S++SznqPw3KzeHOshiPfeCcifGzp0kI43grWs+
nuScYjSuZw9FEvjDjEZL3La00osWxLJx57zNiEX4Wt+M+9RflMjxtvejqXkQoEr/
WCqkx22behAGZq6rhwIDAQAB
-----END PUBLIC KEY-----');

			    $curl = curl_init();

			    curl_setopt($curl, CURLOPT_URL, $url);
			    curl_setopt($curl, CURLOPT_COOKIESESSION, true);
			    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($curl, CURLOPT_POST, true);
			    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

			    $return = curl_exec($curl);
			    curl_close($curl);

			    if(!preg_match('#Errors#i', $return)) {
			          $return_json = json_decode($return, true);
			          if(!$return_json) {
			            $zip = $return;
			          } elseif($return_json['status'] == "error") {
			            return false;
			          }
			    } else {
			      return false;
			    }
				 
				if(unzip($zip, '../View/Themed', 'install-zip', true)) {
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

	function admin_update($theme_id = false, $theme_name = false) {
		$this->autoRender = false;
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			if($theme_id != false AND $theme_name != false) {

				// get du zip sur mineweb.org
			    $url = 'http://mineweb.org/api/get_theme/'.$theme_id;
			    $secure = file_get_contents(ROOT.'/config/secure');
			    $secure = json_decode($secure, true);
			    $postfields = array(
			      'id' => $secure['id'],
			      'key' => $secure['key'],
			      'domain' => Router::url('/', true)
			    );

			    $postfields = json_encode($postfields);
			    $post[0] = rsa_encrypt($postfields, '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCvFK7LMlAnF8Hzmku9WGbHqYNb
ehNueKDbF/j4yYwf8WqIizB7k+S++SznqPw3KzeHOshiPfeCcifGzp0kI43grWs+
nuScYjSuZw9FEvjDjEZL3La00osWxLJx57zNiEX4Wt+M+9RflMjxtvejqXkQoEr/
WCqkx22behAGZq6rhwIDAQAB
-----END PUBLIC KEY-----');

			    $curl = curl_init();

			    curl_setopt($curl, CURLOPT_URL, $url);
			    curl_setopt($curl, CURLOPT_COOKIESESSION, true);
			    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($curl, CURLOPT_POST, true);
			    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

			    $return = curl_exec($curl);
			    curl_close($curl);

			    if(!preg_match('#Errors#i', $return)) {
			          $return_json = json_decode($return, true);
			          if(!$return_json) {
			            $zip = $return;
			          } elseif($return_json['status'] == "error") {
			            return false;
			          }
			    } else {
			      return false;
			    }

			    clearDir(ROOT.'/app/View/Themed/'.$theme_name);
				 
				if(unzip($zip, '../View/Themed', 'install-zip', true)) {
					clearDir(ROOT.'/app/View/Themed/__MACOSX');
					$this->History->set('UPDATE_THEME', 'theme');
					$this->Session->setFlash($this->Lang->get('THEME_UPDATE_SUCCESS'), 'default.success');
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