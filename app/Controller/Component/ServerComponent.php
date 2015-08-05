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
	    $this->url_key = 'key='.sha1($this->secretkey);
	    //$this->url_key = 'key='.$this->secretkey;
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
			$url = 'http://mineweb.org/api/get_secret_key/';
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
			        $return = json_decode($return, true);
			        if($return['status'] == "success") {
			        	$key = @rsa_decrypt($return['secret_key'], '-----BEGIN RSA PRIVATE KEY-----
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
			        	return $key;
			        } elseif($return['status'] == "error") {
			        	return false;
			        }
			}
		} else {
			return false;
		}
	}

	function check($type, $value) {
		if($type == 'connection') {
			if(!empty($value) AND is_array($value)) {
				$url = 'http://'.$value['host'].':'.$value['port'].'?key='.sha1($value['secret_key']).'&domain='.rawurlencode(Router::url('/', true));
				$opts = array('http' => array('timeout' => $value['timeout']));
				@$get = file_get_contents($url, false, stream_context_create($opts));
				if($get != false) {
					$response = json_decode($get, true);
					if(isset($response['REQUEST']) AND $response['REQUEST'] == 'INSTALLATION_COMPLETED') {
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
			if($search['getPlayerCount'] == "null") {
				$search['getPlayerCount'] = 0;
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