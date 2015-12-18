<?php // http://www.phpencode.org
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
define('TIMESTAMP_DEBUT', microtime(true));
App::uses('Controller', 'Controller');
require ROOT.'/config/function.php';

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	var $components = array('Module', 'Session', 'Security', /*'Connect', */'Configuration', 'EyPlugin', 'History', 'Statistics', 'Permissions', 'Lang', 'Update', 'Server');
	var $helpers = array('Session');

	var $view = 'Theme';

	protected $isConnected = false;

	public function beforeFilter() {

		$this->Security->blackHoleCallback = 'blackhole';
		$this->Security->validatePost = false;
		$this->Security->csrfUseOnce = false;

	/* VERIFICATION API-CMS */
	if($this->params['controller'] != "install") {
		$last_check = @file_get_contents(ROOT.'/config/last_check');
		$last_check = @rsa_decrypt($last_check, '-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDGKSGFj8368AmYYiJ9fp1bsu3mzIiUfU7T2uhWXULe9YFqSvs9
AA/PqTiOgGj8hid2KDamUvzI9UH5RWI83mwAMsj5mxk+ujuoR6WuZykO+A1XN6n4
I3MWhBe1ZYWRwwgMgoDDe7DDbT2Y6xMxh6sbgdqxeKmkd4RtVB7+UwyuSwIDAQAB
AoGAbuXz6bBqIUaWyB4bmUnzvK7tbx4GTbu3Et9O6Y517xtMWvUtl5ziPGBC05VP
rAtUKE8nDnwhFkITsvI+oTwFCjZOEC4t7B39xtRgzICi3KkR1ICB/k+I6gsadGdU
GY3Xf7slY5MEYwpvq6wiczxeMYuxkDzeOkPy1U1FgGBcTukCQQD18+M3Sfoko/Kw
TiVFNk8rDvre/0iOiU1o/Yvi8AU/NXJbPOlm8hVfdXBNH35L+WYmt74uBI7Mxrmb
YrUUvc7XAkEAzkFyPjcnaL9wnX5oRLgk8j3cAzAiiUFbk/KnFEHTjmdcF00hSyrB
aQKyqnWAeFFzLIDdXzC3M07fzHR3RP1xrQJAH4sAx/V33D0egdfz1bWKX7ZTHEhX
MNiREfb6esdXlOyw1tyv/mDrtstj9LAmTW4V2L9V56bz/XU7Fp+JI7jYDwJARbQQ
a74v71JjOJZznmWs9sC5DcrCoSgZTtJ+bHYijMmZcbZ7Pe/hFR/4SWsUU5UTG0Mh
jP3lq81IDMx/Ui1ksQJBAO4hTKBstrDNlUPkUr0i/2Pb/edVSgZnJ9t3V94OAD+Z
wJKpVWIREC/PMQD8uTHOtdxftEyPoXMLCySqMBjY58w=
-----END RSA PRIVATE KEY-----');
		if($last_check) {
			$last_check = strtotime('+8 hours', $last_check);
		} else {
			$last_check = '0';
		}
		if($last_check < time()) {
			$plugins = array();
			/*$plugins_enabled = $this->EyPlugin->get_list();
			$plugins_disabled = $this->EyPlugin->get_list();
			foreach ($plugins_enabled as $key => $value) {
				$plugins[$value]['statut'] = true;
				$plugins[$value]['id'] = $this->EyPlugin->get('id', $value['plugins']['name']);
				$plugins[$value]['version'] = $this->EyPlugin->get('version', $value['plugins']['name']);
			}
			foreach ($plugins_enabled as $key => $value) {
				$plugins[$value]['statut'] = false;
				$plugins[$value]['id'] = $this->EyPlugin->get('id', $value['plugins']['name']);
				$plugins[$value]['version'] = $this->EyPlugin->get('version', $value['plugins']['name']);
			}*/

			$url = 'http://mineweb.org/api/v1/key_verif/';
			$secure = file_get_contents(ROOT.'/config/secure');
			$secure = json_decode($secure, true);
			$postfields = array(
				'id' => $secure['id'],
			    'key' => $secure['key'],
			    'domain' => Router::url('/', true),
			    'version' => $this->Configuration->get('version'),
			    'plugins' => serialize($plugins)
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
		        $return = json_decode($return, true);
		        if($return['status'] == "success") {
		        	file_put_contents(ROOT.'/config/last_check', $return['time']);
		        } elseif($return['status'] == "error") {
		        	throw new LicenseException($return['msg']);
		        }
			}
		}
	}


		/* Charger les components des plugins si ils s'appellent "EventsConpoment.php" */
		$plugins = $this->EyPlugin->getPluginsActive();
		/*foreach ($plugins as $key => $value) {
			if($value->useEvents) {
				$component = $this->Components->load($value->slug.'.Events');
				$component->startup($this);
				$this->getEventManager()->attach($component);
			}
		}

		$event = $this->getEventManager()->dispatch(new CakeEvent('requestPage', $this, $this->request->data));*/

		// Chargement de tout les fichiers Events des plugins

		foreach ($plugins as $key => $value) { // on les parcours tous

			if($value->useEvents) { // si ils utilisent les events

				$slugFormated = ucfirst(strtolower($value->slug)); // le slug au format Mmm

				$eventFolder = $this->EyPlugin->pluginsFolder.DS.$value->slug.DS.'Event'; // l'endroit du dossier event

				$path = $eventFolder.DS.$slugFormated.'*Listener.php'; // la ou get les fichiers

				foreach(glob($path) as $eventFile) { // on récupére tout les fichiers SlugName.php dans le dossier du plugin Events/

		            // get only the class name
		            $className = str_replace(".php", "", basename($eventFile));

		            App::uses($className, 'Plugin/'.DS.$value->slug.DS.'Event');

		            // then instantiate the file and attach it to the event manager
		            $this->getEventManager()->attach(new $className($request, $response));
		        }

			}

		}

		/* ---- */

		$event = $this->getEventManager()->dispatch(new CakeEvent('requestPage', $this, $this->request->data));

		$this->loadModel('User');
		$this->isConnected = $this->User->isConnected();
		$this->set('isConnected', $this->isConnected);

		if($this->isConnected) {
			if($this->User->getKey('rank') == 5 AND $this->params['controller'] != "maintenance") {
				$this->redirect(array('controller' => 'maintenance', 'action' => 'index/banned', 'plugin' => false, 'admin' => false));
			}
		}

		if($this->params['prefix'] == "admin") {
			$plugins_need_admin = $this->EyPlugin->getPluginsActive();
			foreach ($plugins_need_admin as $key => $value) {
				if($value->admin) {
					$plugins_admin[] = array('name' => $value->name, 'slug' => $value->slug);
				}
			}
			if(!empty($plugins_admin)) {
				$plugins_need_admin = $plugins_admin;
			} else {
				$plugins_need_admin = null;
			}
			$this->set(compact('plugins_need_admin'));
		}

		/* === Variables === */

			// Navbar
			$this->loadModel('Navbar');
			$nav = $this->Navbar->find('all', array('order' => 'order'));
			if(!empty($nav)) {
				$nav = $nav;
			} else {
				$nav = false;
			}

			/*$navbar = '';
            if(!empty($nav)) {
              	$i = 0;
              	foreach ($nav as $key => $value) {
                	if(empty($value['Navbar']['submenu'])) {
                  		$navbar += '<li class="li-nav';
                  		$navbar += ($this->params['controller'] == $value['Navbar']['name']) ? ' actived' : '';
                  		$navbar += '">';
                  		$navbar += '<a href="'.$value['Navbar']['url'].'">'.$value['Navbar']['name'].'</a>';
                  		$navbar += '</li>';
                	} else {
                		$navbar += '<li class="dropdown">';
						$navbar += '<li class="dropdown">'
                    	$navbar += '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">'.$value['Navbar']['name'].' <span class="caret"></span></a>';
                    	$navbar += '<ul class="dropdown-menu" role="menu">';

                    	$submenu = json_decode($value['Navbar']['submenu']);
                    	foreach ($submenu as $k => $v) {
                      		$navbar += '<li><a href="'.rawurldecode($v).'">'.rawurldecode(str_replace('+', ' ', $k)).'</a></li>';
                    	}
                    	$navbar += '</ul>';
                  		$navbar += '</li>';
                	}
              	$i++;
            	}
          	}*/

			// Configuration Thème/Générale
			$website_name = $this->Configuration->get('name');

			$theme_name = $this->Configuration->get('theme');

			if($theme_name == "default") {
				$theme_config = file_get_contents(ROOT.'/config/theme.default.json');
			} elseif(file_exists(ROOT.'/app/View/Themed/'.$theme_name.'/config/config.json')) {
				$theme_config = file_get_contents(ROOT.'/app/View/Themed/'.$theme_name.'/config/config.json');
			}
			$theme_config = json_decode($theme_config, true);

			// Info serveur
			$banner_server = $this->Configuration->get('banner_server');
    	if(empty($banner_server)) {
      	if($this->Server->online()) {
        		$banner_server = $this->Lang->banner_server($this->Server->banner_infos());
      	} else {
        		$banner_server = false;
      	}
    	} else {
      	$banner_server = unserialize($banner_server);
      	if(count($banner_server) == 1) {
        		$server_infos = $this->Server->banner_infos($banner_server[0]);
      	} else {
        	$server_infos = $this->Server->banner_infos($banner_server);
      	}
      	if(!empty($server_infos['getPlayerMax']) && !empty($server_infos['getPlayerCount'])) {
      		$banner_server = $this->Lang->banner_server($this->Server->banner_infos($server_infos));
      	} else {
        		$banner_server = false;
      	}
    	}

    	// Message flash
    if($this->params['prefix'] != "admin") {
    	App::uses('SessionHelper', 'View/Helper');
			$SessionHelper = new SessionHelper(new View());
    	$flash = $SessionHelper->flash();
  		$flash_messages = (!empty($flash)) ? '<div class="container">'.html_entity_decode($flash).'</div>' : '';
    }

  	// infos user
  	$user = ($this->isConnected) ? $this->User->getAllFromCurrentUser() : array();

  	$csrfToken = $this->Session->read('_Token')['key'];

  	// socials links
  	$facebook_link = $this->Configuration->get('facebook');
  	$skype_link = $this->Configuration->get('skype');
  	$youtube_link = $this->Configuration->get('youtube');
  	$twitter_link = $this->Configuration->get('twitter');

			// on set tout
			$this->set(compact('nav', 'website_name', 'theme_config', 'banner_server', 'flash_messages', 'user', 'csrfToken', 'facebook_link', 'skype_link', 'youtube_link', 'twitter_link'));

		if($this->params['controller'] == "user" OR $this->params['controller'] == "maintenance" OR $this->Configuration->get('maintenance') == '0' OR $this->isConnected AND $this->Connect->if_admin()) {
		} else {
			$this->redirect(array('controller' => 'maintenance', 'action' => 'index', 'plugin' => false, 'admin' => false));
		}
		Configure::write('theme', $theme_name);
		$this->__setTheme();
	}

	function beforeRender() {
		/*
		$return['status'] = 'error';
        $return['msg'] = 'Un problème d\'évent est survenu !';
        return $return;
		*/
		$event = $this->getEventManager()->dispatch(new CakeEvent('onLoadPage', $this, $this->request->data));
		//if(!empty($event['result']) && $event['result']['status'] == "error") {
		//	return throw new InternalException($event['result']['msg']);
		//}


		if($this->request->is('post')) {
			$this->getEventManager()->dispatch(new CakeEvent('onRequest', $this, $this->request->data));
		}
		if($this->params['prefix'] == "admin") {
			$this->getEventManager()->dispatch(new CakeEvent('onLoadAdminPanel', $this, $this->request->data));
		}
	}

	function __setTheme() {
		if(!isset($this->params['prefix']) OR $this->params['prefix'] != "admin") {
        	$this->theme = Configure::read('theme');
        } else {
        	if(isset($_COOKIE['admin_layout'])) {
        		$this->theme = $_COOKIE['admin_layout'];
        	} else {
        		$this->theme = 'default';
        	}
        }
    }

	public function blackhole($type) {
		if($type == "csrf") {
			$this->autoRender = false;
			if($this->request->is('ajax')) {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR_CSRF')));
				exit();
			} else {
				$this->Session->setFlash($this->Lang->get('ERROR_CSRF'), 'default.error');
				$this->redirect($this->referer());
			}
		}
	}

}
