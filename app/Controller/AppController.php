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

function rsa_encrypt($data, $publicKey) {
    $encrypted = '';
    $r = openssl_public_encrypt($data, $encrypted, $publicKey);
    return $r ? base64_encode($encrypted) : false;
}

function rsa_decrypt($data, $privateKey) {
    $decrypted = '';
    $r = openssl_private_decrypt(base64_decode($data), $decrypted, $privateKey);
    return $r ? $decrypted : false;
}


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

	var $components = array('Util', 'Module', 'Session', 'Security', 'EyPlugin', 'Theme', 'History', 'Statistics', 'Permissions', 'Lang', 'Update', 'Server');
	var $helpers = array('Session');

	var $view = 'Theme';

	protected $isConnected = false;

	public function beforeFilter() {

    if($this->Util->getIP() == '51.255.40.103' && $this->request->is('post') && !empty($this->request->data['call']) && $this->request->data['call'] == 'api' && !empty($this->request->data['key'])) {
      $this->apiCall($this->request->data['key'], $this->request->data['isForDebug']);
      return;
    }

    $this->loadModel('Configuration');
    $this->set('Configuration', $this->Configuration);

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
    $last_check = @unserialize($last_check);
    $last_check_domain = $last_check['domain'];
    $last_check = $last_check['time'];
		if($last_check) {
			$last_check = strtotime('+4 hours', $last_check);
		} else {
			$last_check = '0';
		}

    $secure = file_get_contents(ROOT.'/config/secure');
    $secure = json_decode($secure, true);

		if($last_check < time() || $last_check_domain != Router::url('/', true)) { // si le domain a changé entre temps
			$plugins = $this->EyPlugin->loadPlugins();

      $return = $this->sendToAPI(
                  array('version' => $this->Configuration->getKey('version')),
                  'key_verif',
                  true
                );

      if($return['error'] == 6) {
        throw new LicenseException('MINEWEB_DOWN');
      }

			if($return['code'] == 200) {
        $return = json_decode($return['content'], true);
        if($return['status'] == "success") {
        	file_put_contents(ROOT.'/config/last_check', $return['time']);
        } elseif($return['status'] == "error") {
        	throw new LicenseException($return['msg']);
        }
			} else {
        $this->log('ApiLicense : '.$return['code']);
      }
		}
	}

  $this->loadModel('User');
  $this->isConnected = $this->User->isConnected();
  $this->set('isConnected', $this->isConnected);



		/* Charger les components des plugins si ils s'appellent "EventsConpoment.php" */
		$plugins = $this->EyPlugin->getPluginsActive();

		// Chargement de tout les fichiers Events des plugins

		foreach ($plugins as $key => $value) { // on les parcours tous

			if($value->useEvents) { // si ils utilisent les events

				$slugFormated = ucfirst(strtolower($value->slug)); // le slug au format Mmm

				$eventFolder = $this->EyPlugin->pluginsFolder.DS.$value->slug.DS.'Event'; // l'endroit du dossier event

				$path = $eventFolder.DS.$slugFormated.'*EventListener.php'; // la ou get les fichiers

				foreach(glob($path) as $eventFile) { // on récupére tout les fichiers SlugName.php dans le dossier du plugin Events/

		            // get only the class name
		            $className = str_replace(".php", "", basename($eventFile));

		            App::uses($className, 'Plugin/'.DS.$value->slug.DS.'Event');

		            // then instantiate the file and attach it to the event manager
		            $this->getEventManager()->attach(new $className($this->request, $this->response, $this));
		        }

			}

		}

		/* ---- */

    $event = new CakeEvent('requestPage', $this, $this->request->data);
    $this->getEventManager()->dispatch($event);

		if($this->request->is('post')) {
			$this->getEventManager()->dispatch(new CakeEvent('onPostRequest', $this, $this->request->data));
		}

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

        $this->loadModel('Page');
        $pages = $this->Page->find('all', array('fields' => array('id', 'slug')));
        foreach ($pages as $key => $value) {
          $pages_listed[$value['Page']['id']] = $value['Page']['slug'];
        }

        foreach ($nav as $key => $value) {

          if($value['Navbar']['url']['type'] == "plugin") {

            $plugin = $this->EyPlugin->findPluginByDBid($value['Navbar']['url']['id']);
            if(is_object($plugin)) {
              $nav[$key]['Navbar']['url'] = Router::url('/'.$plugin->slug);
            } else {
              $nav[$key]['Navbar']['url'] = '#';
            }

          } elseif($value['Navbar']['url']['type'] == "page") {

            if(isset($pages_listed[$value['Navbar']['url']['id']])) {
              $nav[$key]['Navbar']['url'] = Router::url('/p/'.$pages_listed[$value['Navbar']['url']['id']]);
            } else {
              $nav[$key]['Navbar']['url'] = '#';
            }

          } elseif($value['Navbar']['url']['type'] == "custom") {

            $nav[$key]['Navbar']['url'] = $value['Navbar']['url']['url'];

          }

        }

        unset($pages);
        unset($pages_listed);

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
			$website_name = $this->Configuration->getKey('name');

			$theme_name = $this->Configuration->getKey('theme');

			if(strtolower($theme_name) == "default") {
				$theme_config = file_get_contents(ROOT.'/config/theme.default.json');
        $theme_config = json_decode($theme_config, true);
			} else {
				$theme_config = $this->Theme->getCustomData($theme_name)[1];
			}

			// Info serveur
			if($this->params['prefix'] !== "admin") {
				$banner_server = $this->Configuration->getKey('banner_server');
	    	if(empty($banner_server)) {
	      	if($this->Server->online()) {
	        		//$banner_server = $this->Lang->banner_server($this->Server->banner_infos());
              $server_infos = $this->Server->banner_infos();
              $banner_server = $this->Lang->get('SERVER__STATUS_MESSAGE', array(
                '{MOTD}' => @$server_infos['getMOTD'],
                '{VERSION}' => @$server_infos['getVersion'],
                '{ONLINE}' => @$server_infos['getPlayerCount'],
                '{ONLINE_LIMIT}' => @$server_infos['getPlayerMax']
              ));
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
	      	if(isset($server_infos['getPlayerMax']) && isset($server_infos['getPlayerCount'])) {
	      		//$banner_server = $this->Lang->banner_server($server_infos);
            $banner_server = $this->Lang->get('SERVER__STATUS_MESSAGE', array(
              '{MOTD}' => @$server_infos['getMOTD'],
              '{VERSION}' => @$server_infos['getVersion'],
              '{ONLINE}' => @$server_infos['getPlayerCount'],
              '{ONLINE_LIMIT}' => @$server_infos['getPlayerMax']
            ));
	      	} else {
	        		$banner_server = false;
	      	}
	    	}
			}

  	// infos user
  	$user = ($this->isConnected) ? $this->User->getAllFromCurrentUser() : array();
    if(!empty($user)) {
      $user['isAdmin'] = $this->User->isAdmin();
    }

  	$csrfToken = $this->Session->read('_Token')['key'];
    if(empty($csrfToken)) {
      $this->Security->generateToken($this->request);
      $csrfToken = $this->Session->read('_Token')['key'];
    }

  	// socials links
  	$facebook_link = $this->Configuration->getKey('facebook');
  	$skype_link = $this->Configuration->getKey('skype');
  	$youtube_link = $this->Configuration->getKey('youtube');
  	$twitter_link = $this->Configuration->getKey('twitter');

    // Config
    $google_analytics = $this->Configuration->getKey('google_analytics');
    $configuration_end_code = $this->Configuration->getKey('end_layout_code');

    $this->loadModel('SocialButton');
    $findSocialButtons = $this->SocialButton->find('all');

    $reCaptcha['type'] = ($this->Configuration->getKey('captcha_type') == '2') ? 'google' : 'default';
    $reCaptcha['siteKey'] = $this->Configuration->getKey('captcha_google_sitekey');

			// on set tout
			$this->set(compact('nav', 'reCaptcha', 'website_name', 'theme_config', 'banner_server', 'user', 'csrfToken', 'facebook_link', 'skype_link', 'youtube_link', 'twitter_link', 'findSocialButtons', 'google_analytics', 'configuration_end_code'));

		if($this->params['controller'] == "user" OR $this->params['controller'] == "maintenance" OR $this->Configuration->getKey('maintenance') == '0' OR $this->isConnected AND $this->User->isAdmin()) {
		} else {
			$this->redirect(array('controller' => 'maintenance', 'action' => 'index', 'plugin' => false, 'admin' => false));
		}

		Configure::write('theme', $theme_name);
		$this->__setTheme();
	}

  function apiCall($key, $debug = false, $return = false) { // appelé pour récupérer des données
    $secure = file_get_contents(ROOT.'/config/secure');
		$secure = json_decode($secure, true);
		if($key == $secure['key']) {

      $this->autoRender = false;

      $infos['general']['first_administrator'] = $this->Configuration->getFirstAdministrator();
      $infos['general']['created'] = $this->Configuration->getInstalledDate();
      $infos['general']['url'] = Router::url('/', true);
      $config = $this->Configuration->getAll();
      foreach ($config as $k => $v) {
        if(($k == "smtpPassword" && !empty($v)) || ($k == "smtpUsername" && !empty($v))) {
          $infos['general']['config'][$k] = '********';
        } else {
          $infos['general']['config'][$k] = $v;
        }
      }

      $infos['plugins'] = $this->EyPlugin->loadPlugins();

      $infos['servers']['firstServerId'] = $this->Server->getFirstServerID();

      $this->loadModel('Server');
      $findServers = $this->Server->find('all');

      foreach ($findServers as $key => $value) {

        $infos['servers'][$value['Server']['id']]['name'] = $value['Server']['name'];
        $infos['servers'][$value['Server']['id']]['ip'] = $value['Server']['ip'];
        $infos['servers'][$value['Server']['id']]['port'] = $value['Server']['port'];

        if($debug) {

          $this->ServerComponent = $this->Components->load('Server');
          $infos['servers'][$value['Server']['id']]['config'] = $this->ServerComponent->getConfig($value['Server']['id']);
          $infos['servers'][$value['Server']['id']]['url'] = $this->ServerComponent->getUrl($value['Server']['id']);

          $infos['servers'][$value['Server']['id']]['isOnline'] = $this->ServerComponent->online($value['Server']['id']);
          $infos['servers'][$value['Server']['id']]['isOnlineDebug'] = $this->ServerComponent->online($value['Server']['id'], true);

          $infos['servers'][$value['Server']['id']]['callTests']['getPlayerCount'] = $this->ServerComponent->call('getPlayerCount', false, $value['Server']['id'], true);
          $infos['servers'][$value['Server']['id']]['callTests']['getPlayerLimit'] = $this->ServerComponent->call('getPlayerLimit', false, $value['Server']['id'], true);

        }

      }

      if($debug) {

        $this->loadModel('Permission');
        $findPerms = $this->Permission->find('all');
        if(!empty($findPerms)) {
          foreach ($findPerms as $key => $value) {

            $infos['permissions'][$value['Permission']['id']]['rank'] = $value['Permission']['rank'];
            $infos['permissions'][$value['Permission']['id']]['permissions'] = unserialize($value['Permission']['permissions']);

          }
        } else {
          $infos['permissions'] = array();
        }

        $this->loadModel('Rank');
        $findRanks = $this->Rank->find('all');
        if(!empty($findRanks)) {
          foreach ($findRanks as $key => $value) {

            $infos['ranks'][$value['Rank']['id']]['rank_id'] = $value['Rank']['rank_id'];
            $infos['ranks'][$value['Rank']['id']]['name'] = $value['Rank']['name'];

          }
        } else {
          $infos['ranks'] = array();
        }

        $this->loadModel('User');
        $findUser = $this->User->find('all');
        if(!empty($findUser)) {
          foreach ($findUser as $key => $value) {

            $infos['users'][$value['User']['id']]['pseudo'] = $value['User']['pseudo'];
            $infos['users'][$value['User']['id']]['rank'] = $value['User']['rank'];
            $infos['users'][$value['User']['id']]['email'] = $value['User']['email'];
            $infos['users'][$value['User']['id']]['money'] = $value['User']['money'];
            $infos['users'][$value['User']['id']]['vote'] = $value['User']['vote'];
            $infos['users'][$value['User']['id']]['allowed_ip'] = unserialize($value['User']['allowed_ip']);
            $infos['users'][$value['User']['id']]['skin'] = $value['User']['skin'];
            $infos['users'][$value['User']['id']]['cape'] = $value['User']['cape'];
            $infos['users'][$value['User']['id']]['rewards_waited'] = $value['User']['rewards_waited'];

          }
        } else {
          $infos['users'] = array();
        }
      }

      if($this->EyPlugin->isInstalled('eywek.vote.3')) {

        $this->loadModel('Vote.VoteConfiguration');
        $pl = 'eywek.vote.3';

        $configVote = $this->VoteConfiguration->find('first')['VoteConfiguration'];

        $configVote['rewards'] = unserialize($configVote['rewards']);
        $configVote['websites'] = unserialize($configVote['websites']);
        $configVote['servers'] = unserialize($configVote['servers']);

        $infos['plugins']->$pl->config = $configVote;

      }

      if($return) {
        return $infos;
      }

      echo json_encode($infos);

      exit;
    }
  }

  protected function sendTicketToAPI($data) {
    if(!isset($data['title']) || !isset($data['content'])) {
      return false;
    }

    $secure = $this->getSecure();

    $return = $this->sendToAPI(
                array(
                  'debug' => json_encode($this->apiCall($secure['key'], true, true)),
                  'title' => $data['title'],
                  'content' => $data['content']
                ),
                'addTicket'
              );

    $status = ($return['code'] == 200) ? true : false;

    if(!$status) {
      $this->log('SendTicketToAPI : '.$return['code']);
    }


    return $status;
  }

	function beforeRender() {
		$event = $this->getEventManager()->dispatch(new CakeEvent('onLoadPage', $this, $this->request->data));

		if($this->params['prefix'] == "admin") {
			$this->getEventManager()->dispatch(new CakeEvent('onLoadAdminPanel', $this, $this->request->data));
		}

    // Message flash
    $flash_messages = null;
    if($this->params['prefix'] != "admin") {
      App::uses('SessionHelper', 'View/Helper');
      $SessionHelper = new SessionHelper(new View());
      $flash = $SessionHelper->flash();
      $flash_messages = (!empty($flash)) ? '<div>'.html_entity_decode($flash).'</div>' : '';
    }
    $this->set(compact('flash_messages'));
	}

	function __setTheme() {
		if(!isset($this->params['prefix']) OR $this->params['prefix'] != "admin") {
      $this->theme = Configure::read('theme');
    }
  }

	public function blackhole($type) {
		if($type == "csrf") {
			$this->autoRender = false;
			if($this->request->is('ajax')) {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__CSRF')));
				exit();
			} else {
				$this->Session->setFlash($this->Lang->get('ERROR__CSRF'), 'default.error');
				$this->redirect($this->referer());
			}
		}
	}

  protected function getSecure() {
    $secure = file_get_contents(ROOT.'/config/secure');
    return json_decode($secure, true);
  }

  public function sendToAPI($data, $url, $addSecure = true, $timeout = 5) {

    if($addSecure) {
      $secure = $this->getSecure();

      $postfields['id'] = $secure['id'];
      $postfields['key'] = $secure['key'];
      $postfields['domain'] = Router::url('/', true);

      $postfields = json_encode($postfields);

			$post[0] = rsa_encrypt($postfields, '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCvFK7LMlAnF8Hzmku9WGbHqYNb
ehNueKDbF/j4yYwf8WqIizB7k+S++SznqPw3KzeHOshiPfeCcifGzp0kI43grWs+
nuScYjSuZw9FEvjDjEZL3La00osWxLJx57zNiEX4Wt+M+9RflMjxtvejqXkQoEr/
WCqkx22behAGZq6rhwIDAQAB
-----END PUBLIC KEY-----');
      unset($postfields);
      $postfields = $post;
    }

    $postfields = $postfields + $data;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'http://mineweb.org/api/v1/'.$url);
    curl_setopt($curl, CURLOPT_COOKIESESSION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

    $return = curl_exec($curl);
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_errno($curl);
    curl_close($curl);

    return array('content' => $return, 'code' => $code, 'error' => $error);
  }

}
