<?php
class ServerComponent extends Object {

	private $timeout = NULL;
	private $config = NULL;
	private $online = NULL;
  	
  	public $components = array('Session', 'Configuration');

	function initialize(&$controller) {
	    $this->controller =& $controller;
		$this->controller->set('Server', new ServerComponent());
	}

	function startup(&$controller) {}
 
	function beforeRender(&$controller) {}

	function shutdown(&$controller) {}

	function beforeRedirect() {}

	function call($method = false, $needsecretkey = false, $server_id = 1) {
	    if($this->online($server_id)) {
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
	                $url = $this->getUrl($server_id).$method;
	            } else {
	                $url = $this->getUrl($server_id, true).'&'.$method;
	            }
	            $opts = array('http' => array('timeout' => $this->getTimeout()));
	            $get = @file_get_contents($url, false, stream_context_create($opts));
	            if($get) {
		            $result = json_decode($get, true);
		            return $result;
		        } else {
		        	return array('status' => 'error', 'code' => '3', 'msg' => 'Request timeout');
		        }
	        } else {
	            return array('status' => 'error', 'code' => '2', 'msg' => 'This method doesn\'t exist');
	        }
	    } else {
	        return array('status' => 'error', 'code' => '1', 'msg' => 'This server is not online');
	    }
	}

	private function getTimeout() {
		if(empty($this->timeout)) {
    		return ClassRegistry::init('Configuration')->find('first')['Configuration']['server_timeout'];
    	} else {
    		return $this->timeout;
    	}
	}

	private function getConfig($server_id = 1) {
		if(empty($this->config[$server_id])) {
		    if(!empty($server_id)) {
		        $this->Configuration = ClassRegistry::init('Configuration');
		        $configuration = $this->Configuration->find('first');
		        if($configuration['Configuration']['server_state'] == 1) {
		        	$this->Timeout = $configuration['Configuration']['server_timeout'];
		            $this->Server = ClassRegistry::init('Server');
		            $search = $this->Server->find('first', array('conditions' => array('id' => $server_id)));
		            if(!empty($search)) {
		            	$this->config[$server_id] = array('ip' => $search['Server']['ip'], 'port' => $search['Server']['port']);
		                return $this->config[$server_id];
		            } else {
		            	$this->config[$server_id] = false;
		                return false;
		            }
		        } else {
		        	$this->config[$server_id] = false;
		            return false;
		        }
		    } else {
		    	$this->config[$server_id] = false;
		        return false;
		    }
		} else {
			return $this->config[$server_id];
		}
	}

	private function getUrl($server_id, $key = false) {
	    if(!empty($server_id)) {
	        $config = $this->getConfig($server_id);
	        if($config) {
	            if($key) {
	                $this->Configuration = ClassRegistry::init('Configuration');
	                $key = $this->Configuration->find('first')['Configuration']['server_secretkey'];
	                return 'http://'.$config['ip'].':'.$config['port'].'?'.'key='.sha1($key);
	            } else {
	                return 'http://'.$config['ip'].':'.$config['port'].'?';
	            }
	        } else {
	            return false;
	        }
	    } else {
	        return false;
	    }
	}

	function online($server_id = 1) {
		if(empty($this->online[$server_id])) {
		    if(!empty($server_id)) {
		        $config = $this->getConfig();
		        if($config) {
		            $url = $this->getUrl($server_id).'getPlayerMax=server';
		            $opts = array('http' => array('timeout' => $this->getTimeout()));
		            @$get = file_get_contents($url, false, stream_context_create($opts));
		            if($get != false) {
		            	$this->online[$server_id] = true;
		                return true;
		            } else {
		            	$this->online[$server_id] = false;
		                return false;
		            }
		        } else {
		        	$this->online[$server_id] = false;
		            return false;
		        }
		    } else {
		    	$this->online[$server_id] = false;
		        return false;
		    }
		} else {
			return $this->online[$server_id];
		}
	}

	function getAllServers() {
    	$this->Server = ClassRegistry::init('Server');
    	$search = $this->Server->find('all');
    	foreach ($search as $key => $value) {
    	    $return[]['server_id'] = $value['Server']['id'];
    	}
    	return $return;
	}

	function serversOnline() { // savoir si au moins 1 serveur est en ligne
	    $all_servers = $this->getAllServers();
	    foreach ($all_servers as $key => $value) {
	        $return[][$value['server_id']] = $this->online($value['server_id']);
	        if(in_array($return, true)) {
	            break;
	            return true;
	        }
	    }
	    return false;
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

	function banner_infos($server_id = 1) { // On précise l'ID du serveur ou vont veux les infos, ou alors on envoie "all" qui va aller chercher les infos sur tout les serveurs
	    if(!is_array($server_id)) {
	        if($this->online($server_id)) {
	            $search = $this->call(array('getMOTD' => 'server', 'getVersion' => 'server', 'getPlayerMax' => 'server', 'getPlayerCount' => 'server'), false, $server_id);
	            if($search['getPlayerCount'] == "null") {
	                $search['getPlayerCount'] = 0;
	            }
	            return $search;
	        } else {
	          return false;
	        }
	    } else {
	        $servers = $server_id;
	        $return['getPlayerMax'] = 0;
	        $return['getPlayerCount'] = 0;
	        foreach ($servers as $key => $value) {
	            if($this->online($value)) {
	                $search = $this->call(array('getPlayerMax' => 'server', 'getPlayerCount' => 'server'), false, $value);
	                if($search['getPlayerCount'] == "null") {
	                    $search['getPlayerCount'] = 0;
	                }
	                $return['getPlayerMax'] = $return['getPlayerMax'] + $search['getPlayerMax'];
	                $return['getPlayerCount'] = $return['getPlayerCount'] + $search['getPlayerCount'];
	            }
	        }
	        return $return;
	    }
	}

	function send_command($cmd, $server_id = 1) {
   		if($this->online($server_id)) {
	      	$this->call(array('performCommand' => $cmd), true, $server_id);
	    }
	  }

	function commands($commands, $server_id = 1) {
	    if($this->online($server_id)) {
	      App::import('Component', 'ConnectComponent');
	      $this->Connect = new ConnectComponent;
	      $commands = str_replace('{PLAYER}', $this->Connect->get_pseudo(), $commands);
	      $commands = explode('[{+}]', $commands);
	      $performCommands = array();
	      foreach ($commands as $key => $value) {
	        $result[] = $this->call(array('performCommand' => $value), true, $server_id);
	      }
	      return $result;
	    }
	}

}