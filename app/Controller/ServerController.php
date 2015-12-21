<?php

class ServerController extends AppController {

	public $components = array('Session');


	public function admin_link() {
		if($this->isConnected AND $this->User->isAdmin()) {
			$this->layout = "admin";

			$this->set('title_for_layout',$this->Lang->get('LINK_SERVER'));

			$this->loadModel('Server');
			$servers = $this->Server->find('all');

			$banner_server = unserialize($this->Configuration->get('banner_server'));

			if($banner_server) {
				foreach ($servers as $key => $value) {
					if(in_array($value['Server']['id'], $banner_server)) {
						$servers[$key]['Server']['activeInBanner'] = true;
					} else {
						$servers[$key]['Server']['activeInBanner'] = false;
					}
				}
			}

			$this->set(compact('servers'));

			$this->set('timeout', $this->Configuration->get('server_timeout'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_switchBanner($id = false) {
		$this->autoRender = false;
		if($this->isConnected && $this->User->isAdmin()) {
			if($id) {

				$banner = unserialize($this->Configuration->get('banner_server'));

				if($banner) {

					if(in_array($id, $banner)) {
						unset($banner[array_search($id, $banner)]);
					} else {
						$banner[] = $id;
					}

					$this->Configuration->set('banner_server', serialize($banner));

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
					$this->Session->setFlash($this->Lang->get('SUCCESS_DELETE_SERVER'), 'default.success');
					$this->redirect(array('controller' => 'server', 'action' => 'link', 'admin' => true));
				} else {
					$this->Session->setFlash($this->Lang->get('ERROR_WHEN_AJAX'), 'default.error');
					$this->redirect(array('controller' => 'server', 'action' => 'link', 'admin' => true));
				}
			} else {
				$this->Session->setFlash($this->Lang->get('ERROR_WHEN_AJAX'), 'default.error');
				$this->redirect(array('controller' => 'server', 'action' => 'link', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_config() {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->layout = null;
			if($this->request->is('ajax')) {
				if(!empty($this->request->data['timeout'])) {
					if(filter_var($this->request->data['timeout'], FILTER_VALIDATE_FLOAT)) {
						$this->Configuration->set('server_timeout', $this->request->data['timeout']);

						echo $this->Lang->get('SUCCESS_SAVE_TIMEOUT').'|true';
					} else {
						echo $this->Lang->get('INVALID_TIMEOUT').'|false';
					}
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST' ,$language).'|false';
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_link_ajax() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->layout = null;
			if($this->request->is('ajax')) {

				if(!empty($this->request->data['host']) AND !empty($this->request->data['port']) AND !empty($this->request->data['name'])) {
					$secret_key = $this->Server->get('secret_key');
					if($secret_key !== false) {
						$timeout = $this->Configuration->get('server_timeout');
						if(!empty($timeout)) {
							if($this->Server->check('connection', array('host' => $this->request->data['host'], 'port' => $this->request->data['port'], 'timeout' => $timeout, 'secret_key' => $secret_key))) {
								$this->Configuration->set('server_state', 1);

								if(!empty($this->request->data['id'])) {
									$id = $this->request->data['id'];
								} else {
									$id = null;
								}

								$this->loadModel('Server');
								$this->Server->read(null, $id);
								$this->Server->set(array('name' => $this->request->data['name'], 'ip' => $this->request->data['host'], 'port' => $this->request->data['port']));
								$this->Server->save();

								$this->Configuration->set('server_secretkey', $secret_key);
								echo $this->Lang->get('SUCCESS_CONNECTION_SERVER').'|true';
							} else {
								echo $this->Lang->get('SERVER_CONNECTION_FAILED').'|false';
							}
						} else {
							echo $this->Lang->get('NEED_CONFIG_SERVER_TIMEOUT').'|false';
						}
					} else {
						echo $this->Lang->get('SERVER_CONNECTION_FAILED').'|false';
					}
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST' ,$language).'|false';
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
			$this->set('title_for_layout',$this->Lang->get('BANLIST'));
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
			$this->set('title_for_layout',$this->Lang->get('WHITELIST'));
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
			$this->set('title_for_layout',$this->Lang->get('ONLINE'));
		} else {
			$this->redirect('/');
		}
	}

}
