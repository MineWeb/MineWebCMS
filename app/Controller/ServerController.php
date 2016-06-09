<?php

class ServerController extends AppController {

	public $components = array('Session');


	public function admin_link() {
		if($this->isConnected AND $this->User->isAdmin()) {
			$this->layout = "admin";

			$this->set('title_for_layout',$this->Lang->get('SERVER__LINK'));

			$this->loadModel('Server');
			$servers = $this->Server->find('all');

			$banner_server = unserialize($this->Configuration->getKey('banner_server'));

			if($banner_server) {
				foreach ($servers as $key => $value) {
					if(in_array($value['Server']['id'], $banner_server)) {
						$servers[$key]['Server']['activeInBanner'] = true;
					} else {
						$servers[$key]['Server']['activeInBanner'] = false;
					}
				}
			}

			$bannerMsg = $this->Lang->get('SERVER__STATUS_MESSAGE');

			$this->set(compact('servers', 'bannerMsg'));

			$this->set('isEnabled', $this->Configuration->getKey('server_state'));
			$this->set('isCacheEnabled', $this->Configuration->getKey('server_cache'));

			$this->set('timeout', $this->Configuration->getKey('server_timeout'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_editBannerMsg() {
		$this->autoRender = false;
		$this->response->type('json');

		if($this->isConnected AND $this->User->isAdmin()) {

			if($this->request->is('ajax')) {

				$this->Lang->set('SERVER__STATUS_MESSAGE', $this->request->data['msg']);

				$this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('SERVER__EDIT_BANNER_MSG_SUCCESS'))));

			} else {
				throw new NotFoundException();
			}

		}
	}

	public function admin_switchState() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->autoRender = false;

			$value = ($this->Configuration->getKey('server_state')) ? 0 : 1;

			$this->Configuration->setKey('server_state', $value);

			$this->Session->setFlash($this->Lang->get('SERVER__SUCCESS_SWITCH'), 'default.success');
			$this->redirect(array('action' => 'link'));

		} else {
			$this->redirect('/');
		}
	}

	public function admin_switchCacheState() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->autoRender = false;

			$value = ($this->Configuration->getKey('server_cache')) ? 0 : 1;

			$this->Configuration->setKey('server_cache', $value);

			$this->Session->setFlash($this->Lang->get('SERVER__SUCCESS_CACHE_SWITCH'), 'default.success');
			$this->redirect(array('action' => 'link'));

		} else {
			$this->redirect('/');
		}
	}

	public function admin_switchBanner($id = false) {
		$this->autoRender = false;
		if($this->isConnected && $this->User->isAdmin()) {
			if($id) {

				$banner = unserialize($this->Configuration->getKey('banner_server'));

				if($banner) {

					if(in_array($id, $banner)) {
						unset($banner[array_search($id, $banner)]);
					} else {
						$banner[] = $id;
					}

					$banner = array_values($banner);

					$this->Configuration->setKey('banner_server', serialize($banner));

				} else {
					$this->Configuration->setKey('banner_server', serialize(array($id)));
				}
			}

		} else {
			throw new ForbiddenException();
		}
	}


	public function admin_delete($id = false) {
		$this->autoRender = false;
		if($this->isConnected && $this->User->isAdmin()) {
			if($id) {
				$this->loadModel('Server');
				if($this->Server->delete($id)) {
					$this->Session->setFlash($this->Lang->get('SERVER__DELETE_SERVER_SUCCESS'), 'default.success');
					$this->redirect(array('controller' => 'server', 'action' => 'link', 'admin' => true));
				} else {
					$this->Session->setFlash($this->Lang->get('ERROR__INTERNAL_ERROR'), 'default.error');
					$this->redirect(array('controller' => 'server', 'action' => 'link', 'admin' => true));
				}
			} else {
				$this->Session->setFlash($this->Lang->get('ERROR__INTERNAL_ERROR'), 'default.error');
				$this->redirect(array('controller' => 'server', 'action' => 'link', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_config() {
		$this->autoRender = false;
		$this->response->type('json');
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->layout = null;
			if($this->request->is('ajax')) {
				if(!empty($this->request->data['timeout'])) {
					if(filter_var($this->request->data['timeout'], FILTER_VALIDATE_FLOAT)) {
						$this->Configuration->setKey('server_timeout', $this->request->data['timeout']);

						$this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('SERVER__TIMEOUT_SAVE_SUCCESS'))));
					} else {
						$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('SERVER__INVALID_TIMEOUT'))));
					}
				} else {
					$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));
				}
			} else {
				throw new NotFoundException();
			}
		} else {
			throw new ForbiddenException();
		}
	}

	public function admin_link_ajax() {
		$this->autoRender = false;
		$this->response->type('json');
		if($this->isConnected AND $this->User->isAdmin()) {
			if($this->request->is('ajax')) {

				if(!empty($this->request->data['host']) AND !empty($this->request->data['port']) AND !empty($this->request->data['name']) AND isset($this->request->data['type']) && ($this->request->data['type'] == 0 || $this->request->data['type'] == 1 || $this->request->data['type'] == 2)) {

					if($this->request->data['type'] == 0 || $this->request->data['type'] == 1) {
						$secret_key = $this->Server->get('secret_key');
						if($secret_key !== false) {
							$timeout = $this->Configuration->getKey('server_timeout');
							if(!empty($timeout)) {
								if(!$this->Server->check('connection', array('host' => $this->request->data['host'], 'port' => $this->request->data['port'], 'timeout' => $timeout, 'secret_key' => $secret_key))) {
									$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('SERVER__LINK_FAILED'))));
									exit;
								}
							} else {
								$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('SERVER__TIMEOUT_UNDEFINED'))));
								exit;
							}
						} else {
							$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('SERVER__LINK_FAILED'))));
							exit;
						}
					} elseif($this->request->data['type'] == 2) {
						$ping = $this->Server->ping(array('ip' => $this->request->data['host'], 'port' => $this->request->data['port']));
						if(!$ping) {
							$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('SERVER__LINK_FAILED'))));
							exit;
						}
					}

					$this->Configuration->setKey('server_state', 1);

					if(!empty($this->request->data['id'])) {
						$id = $this->request->data['id'];
					} else {
						$id = null;
					}

					$this->loadModel('Server');
					$this->Server->read(null, $id);
					$this->Server->set(array('name' => $this->request->data['name'], 'ip' => $this->request->data['host'], 'port' => $this->request->data['port'], 'type' => $this->request->data['type']));
					$this->Server->save();

					if($this->request->data['type'] != '2' && isset($secret_key)) {
						$this->Configuration->setKey('server_secretkey', $secret_key);
					}
					$this->response->body(json_encode(array('statut' => true, 'msg' => $this->Lang->get('SERVER__LINK_SUCCESS'))));

				} else {
					$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS'))));
				}
			} else {
				$this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST'))));
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_banlist($server_id = false) {
		if($this->isConnected AND $this->User->isAdmin()) {

			if(!$server_id) {
				$server_id = $this->Server->getFirstServerID();
			}

			$this->loadModel('Server');
			$servers = $this->Server->find('all');
			$this->set(compact('servers'));

			$this->layout = "admin";
			$this->ServerComponent = $this->Components->load('Server');
			$list = $this->ServerComponent->call('getPlayersBanned', false, $server_id);
			if($list != 'NEED_SERVER_ON') {
				if(!empty($list['getPlayersBanned'])) {
					$list = explode(',', $list['getPlayersBanned']);
				} else {
					$list = array();
				}
			}
			if(isset($list[0]) AND $list[0] == "none") { $list = array(); }
			$this->set(compact('list'));
			$this->set('title_for_layout',$this->Lang->get('SERVER__BANLIST'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_whitelist($server_id = false) {
		if($this->isConnected AND $this->User->isAdmin()) {

			if(!$server_id) {
				$server_id = $this->Server->getFirstServerID();
			}

			$this->loadModel('Server');
			$servers = $this->Server->find('all');
			$this->set(compact('servers'));

			$this->layout = "admin";
			$this->ServerComponent = $this->Components->load('Server');
			$list = $this->ServerComponent->call('getPlayersWhitelisted', false, $server_id);
			if($list != 'NEED_SERVER_ON') {
				if(!empty($list['getPlayersWhitelisted'])) {
					$list = explode(',', $list['getPlayersWhitelisted']);
				} else {
					$list = array();
				}
			}
			if(isset($list[0]) AND $list[0] == "none") { $list = array(); }
			$this->set(compact('list'));
			$this->set('title_for_layout',$this->Lang->get('SERVER__WHITELIST'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_online($server_id = false) {
		if($this->isConnected AND $this->User->isAdmin()) {

			if(!$server_id) {
				$server_id = $this->Server->getFirstServerID();
			}

			$this->loadModel('Server');
			$servers = $this->Server->find('all');
			$this->set(compact('servers'));

			$this->layout = "admin";
			$this->ServerComponent = $this->Components->load('Server');
			$list = $this->ServerComponent->call('getPlayerList', false, $server_id);
			if($list != 'NEED_SERVER_ON') {
				if(!empty($list['getPlayerList'])) {
					$list = explode(',', $list['getPlayerList']);
				} else {
					$list = array();
				}
			}
			if(isset($list[0]) AND $list[0] == "none") { $list = array(); }
			$this->set(compact('list'));
			$this->set('title_for_layout',$this->Lang->get('SERVER__STATUS_ONLINE'));
		} else {
			$this->redirect('/');
		}
	}

}
