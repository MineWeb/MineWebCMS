<?php

class ConfigurationController extends AppController {

	public $components = array('Session', 'RequestHandler', 'Util');

	public function admin_index() {
		if($this->isConnected AND $this->User->isAdmin()) {
			$this->layout = "admin";

			if($this->request->is('post')) {
				foreach ($this->request->data as $key => $value) {
					if($key != "version" && $key != "social_btn" && $key != "social_btn_edited" && $key != "social_btn_added") {
						if($key == "banner_server") {
							$value = serialize($value);
						}
						$this->Configuration->set($key, $value);
						if($key == "mineguard") {
							if($value == "true") {
								$this->ServerComponent = $this->Components->load('Server');
								$this->loadModel('Server');
								$findServers = $this->Server->findSelectableServers(true);
								if(!empty($findServers)) {
									foreach ($findServers as $server_id => $server_name) {
										$this->ServerComponent->call(array('setMineguard' => 'true'), true, $server_id);
									}
								}
							} else {
								$this->ServerComponent = $this->Components->load('Server');
								$this->loadModel('Server');
								$findServers = $this->Server->findSelectableServers(true);
								if(!empty($findServers)) {
									foreach ($findServers as $server_id => $server_name) {
										$this->ServerComponent->call(array('setMineguard' => 'false'), true, $server_id);
									}
								}
							}
						}
					} elseif($key == "social_btn") { // si c'est pour les boutons sociaux personnalisés

						$this->loadModel('SocialButton');
						foreach ($value as $k => $v) { // on enregistre le tout
							if(!empty($v['color']) && !empty($v['url']) && (!empty($v['title']) || !empty($v['img']))) {
								$this->SocialButton->create();
								$this->SocialButton->set(array(
									'title' => $v['title'],
									'img' => $v['img'],
									'color' => $v['color'],
									'url' => $v['url']
								));
								$this->SocialButton->save();
							}
						}

					} elseif($key == "social_btn_edited") { // si c'est pour les boutons sociaux personnalisés

						$this->loadModel('SocialButton');
						foreach ($value as $k => $v) { // on enregistre le tout
							if(!empty($v['color']) && !empty($v['url']) && (!empty($v['title']) || !empty($v['img']))) {
								$this->SocialButton->read(null, $v['id']);
								$this->SocialButton->set(array(
									'title' => $v['title'],
									'img' => $v['img'],
									'color' => $v['color'],
									'url' => $v['url']
								));
								$this->SocialButton->save();
							}
						}

					} elseif($key == "social_btn_added") {
						$this->loadModel('SocialButton');
						foreach ($value['deleted'] as $k => $v) { // on enregistre le tout
							$find = $this->SocialButton->findById($v);
							if(!empty($find)) {
								$this->SocialButton->delete($v);
							}
						}
					}
				}

				$this->History->set('EDIT_CONFIGURATION', 'configuration');

				$this->Session->setFlash($this->Lang->get('EDIT_CONFIGURATION_SUCCESS'), 'default.success');
			}

			$this->Lang->lang = $this->Lang->getLang(); // on refresh les messages

			$config = $this->Configuration->get_all(true)['Configuration'];

			$config['lang'] = $this->Lang->getLang('config')['path'];

			foreach ($this->Lang->languages as $key => $value) {
				$config['languages_available'][$key] = $value['name'];
			}

			$this->set('config', $config);

			$this->set('shopIsInstalled', $this->EyPlugin->isInstalled('shop.1.eywek'));

			$this->loadModel('SocialButton');
			$this->set('social_buttons', $this->SocialButton->find('all', array('order' => 'id desc')));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_editLang() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->layout = 'admin';

			if($this->request->is('post')) {

				if(!preg_match('#<a href="http://mineweb.org">mineweb.org</a>#', $this->request->data['COPYRIGHT'])) {
					$this->Session->setFlash($this->Lang->get('CONFIG__ERROR_SAVE_LANG'), 'default.error');
				} else {

					$this->Lang->setAll($this->request->data);

					$this->History->set('EDIT_LANG', 'lang');

					$this->Session->setFlash($this->Lang->get('EDIT_LANG_SUCCESS'), 'default.success');

				}
			}

			$this->Lang->lang = $this->Lang->getLang(); // on refresh les messages

			$this->set('messages', $this->Lang->lang['messages']);
			$this->set('title_for_layout', $this->Lang->get('LANG'));

		} else {
			$this->redirect('/');
		}
	}

}
