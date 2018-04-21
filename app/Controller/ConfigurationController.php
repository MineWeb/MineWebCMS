<?php

class ConfigurationController extends AppController {

	public $components = array('Session', 'RequestHandler', 'Util');

	public function admin_index() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_CONFIGURATION')) {
			$this->layout = "admin";

			$data = array();

			if($this->request->is('post')) {
				foreach ($this->request->data as $key => $value) {
					if($key != "version" && $key != "social_btn" && $key != "social_btn_edited" && $key != "social_btn_added") {
						if($key == "banner_server") {
							$value = serialize($value);
						}
						$data[$key] = $value;
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

				$this->Configuration->read(null, 1);
				$this->Configuration->set($data);
				$this->Configuration->save();

				$this->History->set('EDIT_CONFIGURATION', 'configuration');

				$this->Configuration->cacheQueries = false; //On désactive le cache
				$this->Configuration->dataConfig = null;
				$this->Lang->lang = $this->Lang->getLang(); // on refresh les messages

				$this->Session->setFlash($this->Lang->get('CONFIG__EDIT_SUCCESS'), 'default.success');
			}

			$config = $this->Configuration->getAll();

			$this->Configuration->cacheQueries = true; //On le réactive

			$config['lang'] = $this->Lang->getLang('config')['path'];

			foreach ($this->Lang->languages as $key => $value) {
				$config['languages_available'][$key] = $value['name'];
			}

			$this->set('config', $config);

			$this->set('shopIsInstalled', $this->EyPlugin->isInstalled('eywek.shop'));

			$this->loadModel('SocialButton');
			$this->set('social_buttons', $this->SocialButton->find('all', array('order' => 'id desc')));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_editLang() {
		if($this->isConnected AND $this->Permissions->can('MANAGE_CONFIGURATION')) {

			$this->layout = 'admin';

			if($this->request->is('post')) {

				if(stripos($this->request->data['GLOBAL__FOOTER'], '<a href="http://mineweb.org">mineweb.org</a>') === FALSE) {
					$this->Session->setFlash($this->Lang->get('CONFIG__ERROR_SAVE_LANG'), 'default.error');
				} else {

					$this->Lang->setAll($this->request->data);

					$this->History->set('EDIT_LANG', 'lang');

					$this->Session->setFlash($this->Lang->get('CONFIG__EDIT_LANG_SUCCESS'), 'default.success');

				}
			}

			$this->Lang->lang = $this->Lang->getLang(); // on refresh les messages

			$this->set('messages', $this->Lang->lang['messages']);
			$this->set('title_for_layout', $this->Lang->get('CONFIG__LANG_LABEL'));

		} else {
			$this->redirect('/');
		}
	}

}
