<?php
class ServerComponent extends Object {
  	
  	public $components = array('Session', 'Configuration');

  	private $host;
  	private $port;
  	private $secretkey;
  	private $timeout;

  	function __construct() {
  		$this->Configuration = new ConfigurationComponent;
	    $this->host = $this->Configuration->get('server_host');
	    $this->port = $this->Configuration->get('server_port');
	    $this->secretkey = $this->Configuration->get('server_secretkey');
	    $this->timeout = $this->Configuration->get('server_timeout');
	    $this->url = 'http://'.$this->host.':'.$this->port.'?';
	    //$this->url_key = 'key='.sha1($this->secretkey);
	    $this->url_key = 'key='.$this->secretkey;
	}

	function initialize(&$controller) {
	    $this->controller =& $controller;
		$this->controller->set('Server', new ServerComponent());
	}

	function startup(&$controller) {
		// server online ? server function not disable ?
		if($this->Configuration->get('server_state') == 1) {
			$url = $this->url.'getPlayerLimit=server';
			$opts = array('http' => array('timeout' => $this->timeout));
			@$get = file_get_contents($url, false, stream_context_create($opts));
			if($get != false) {
				Configure::write('server.online', true); // response
			} else {
				Configure::write('server.online', false); // timeout 
			}
		} else {
			Configure::write('server.online', false); // server_state disable
		}
	}
 
	function beforeRender(&$controller) {
	}

	function shutdown(&$controller) {
	}

	function beforeRedirect() { 
	}

	function call($method = false, $needsecretkey = false) {
		if(Configure::read('server.online')) {
			// method example : $method = array('getPlayerLimit' => 'server', 'getPlayer' => 'Eywek');
			// or $method = 'getPlayerLimit';
			if($method != false) {
				if(is_array($method)) {
					foreach ($method as $key => $value) {
						if(is_array($value)) {
							$value = implode('|', $value);
						}
						$list_method[$key] = $value;
					}
					$list_method = implode('&', array_map(
						function ($v, $k) { 
							return sprintf("%s=%s", $k, rawurlencode($v)); 
						}, 
						$list_method, array_keys($list_method)
					));
				} else {
					$list_method = $method.'=server';
				}
				$method = $list_method;
				if(!$needsecretkey) {
					$url = $this->url.$method;
				} else {
					$url = $this->url.$this->url_key.'&'.$method;
				}
				$opts = array('http' => array('timeout' => $this->timeout));
				$get = file_get_contents($url, false, stream_context_create($opts));
				$result = json_decode($get, true);
				return $result;
			} else {
				return 'NEED_SERVER_METHOD';
			}
		} else {
			return 'NEED_SERVER_ON';
		}
	}

	function get($type) {
		if($type == "secret_key") {
			return '42';
		} else {
			return false;
		}
	}

	function check($type, $value) {
		if($type == 'connection') {
			if(!empty($value) AND is_array($value)) {
				$url = 'http://'.$value['host'].':'.$value['port'].'?key='.sha1($value['secret_key']).'&domain='.Router::url('/', true).'&performCommand='.rawurlencode('say La liaison site-serveur a bien été effectuée !');
				$opts = array('http' => array('timeout' => $value['timeout']));
				@$get = file_get_contents($url, false, stream_context_create($opts));
				if($get != false) {
					$response = json_decode($get, true);
					if($response['performCommand'] != 'NEED_KEY') {
						return true; // response
					} else {
						return false;
					}
				} else {
					return false; // timeout 
				}
			}
		} else {
			return false;
		}
	}

	function banner_infos() {
		if(Configure::read('server.online')) {
			$search = $this->call(array('getMOTD' => 'server', 'getVersion' => 'server', 'getPlayerMax' => 'server', 'getPlayerCount' => 'server'));
			if($search['getPlayerList'] == "none") {
				$search['getPlayerList'] = 0;
			}
	      	return $search;
	    } else {
	      return false;
	    }
	}

	function send_command($cmd) {
	    if(Configure::read('server.online')) {
	      $this->call(array('performCommand' => $cmd), true);
	    }
	  }

	function commands($commands) {
	    if(Configure::read('server.online')) {
	      App::import('Component', 'ConnectComponent');
	      $this->Connect = new ConnectComponent;
	      $commands = str_replace('{PLAYER}', $this->Connect->get_pseudo(), $commands);
	      $commands = explode('[{+}]', $commands);
	      $performCommands = array();
	      foreach ($commands as $key => $value) {
	        $result[] = $this->call(array('performCommand' => $value), true);
	      }
	      return $result;
	    }
	}

}