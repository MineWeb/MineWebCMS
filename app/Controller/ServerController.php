<?php

class ServerController extends AppController {

	public $components = array('Session');


	public function admin_link() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			$this->layout = "admin";
			 
			$this->set('title_for_layout',$this->Lang->get('LINK_SERVER'));
			$this->set('server_host', $this->Configuration->get('server_host'));
			$this->set('port', $this->Configuration->get('server_port'));
			$this->set('timeout', $this->Configuration->get('server_timeout'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_test_call($key, $value) {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			$this->autoRender = false;
			 
			echo '<pre>';
			var_dump($this->Server->call(array($key => $value)));
			echo '</pre>';
		} else {
			$this->redirect('/');
		}
	}

	public function admin_link_ajax() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->layout = null;
			if($this->request->is('ajax')) {
				 
				if(!empty($this->request->data['host']) AND !empty($this->request->data['port']) AND !empty($this->request->data['timeout'])) {
					$secret_key = $this->Server->get('secret_key');
					if($this->Server->check('connection', array('host' => $this->request->data['host'], 'port' => $this->request->data['port'], 'timeout' => $this->request->data['timeout'], 'secret_key' => $secret_key))) {
						$this->Configuration->set('server_state', 1);
						$this->Configuration->set('server_host', $this->request->data['host']);
						$this->Configuration->set('server_port', $this->request->data['port']);
						$this->Configuration->set('server_secretkey', $secret_key);
						$this->Configuration->set('server_timeout', $this->request->data['timeout']);
						echo $this->Lang->get('SUCCESS_CONNECTION_SERVER').'|true';
					} else {
						$this->Configuration->set('server_state', 0);
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

	public function admin_banlist() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			$this->layout = "admin";
			$list = $this->Server->call('getPlayersBanned');
			if($list != 'NEED_SERVER_ON') {
				$list = explode(',', $list['getPlayersBanned']);
			}
			if(isset($list[0]) AND $list[0] == "none") { $list = array(); }
			$this->set(compact('list'));
			$this->set('title_for_layout',$this->Lang->get('BANLIST'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_whitelist() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			$this->layout = "admin";
			$list = $this->Server->call('getPlayersWhitelisted');
			if($list != 'NEED_SERVER_ON') {
				$list = explode(',', $list['getPlayersWhitelisted']);
			}
			if(isset($list[0]) AND $list[0] == "none") { $list = array(); }
			$this->set(compact('list'));
			$this->set('title_for_layout',$this->Lang->get('WHITELIST'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_online() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			$this->layout = "admin";
			$list = $this->Server->call('getPlayerList');
			if($list != 'NEED_SERVER_ON') {
				$list = explode(',', $list['getPlayerList']);
			}
			if(isset($list[0]) AND $list[0] == "none") { $list = array(); }
			$this->set(compact('list'));
			$this->set('title_for_layout',$this->Lang->get('ONLINE'));
		} else {
			$this->redirect('/');
		}
	}

}