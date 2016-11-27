<?php
class ServerComponent extends Object {

	private $timeout = NULL;
	private $config = NULL;
	private $online = NULL;

	public $controller;
  public $components = array('Session', 'Configuration');

	function initialize(&$controller) {
	  $this->controller =& $controller;
		$this->controller->set('Server', $this);
	}

	function startup(&$controller) {}

	function beforeRender(&$controller) {}

	function shutdown(&$controller) {}

	function beforeRedirect() {}

	function call($method = false, $needsecretkey = false, $server_id = false, $debug = false) {

		if(!$server_id) {
			$server_id = $this->getFirstServerID();
		}

    if($this->online($server_id)) {
			$config = $this->getConfig($server_id);
			if($config['type'] == 2) { // si query

				$authorised_method_for_query = array('getMOTD', 'getPlayerCount', 'getVersion', 'getPlayerMax');

				// si c'est des méthodes autorisés pour le query
				if(is_array($method)) {

					$result = array();
					$ping = $this->ping(array('ip' => $config['ip'], 'port' => $config['port']));

					foreach ($method as $key => $value) {
						if(!in_array($key, $authorised_method_for_query)) {
							return array('status' => 'error', 'code' => '6', 'msg' => 'This server type can\'t accept this methods');
						}
						$result[$key] = $ping[$key];
					}

					return $result;

				} else {
					if(!in_array($method, $authorised_method_for_query)) {
						return array('status' => 'error', 'code' => '6', 'msg' => 'This server type can\'t accept this method');
					}
					return $this->ping(array('ip' => $config['ip'], 'port' => $config['port']))[$method];
				}

			}

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
						$url = urlencode($url);
            $opts = array('http' => array('timeout' => $this->getTimeout()));
            $get = @file_get_contents($url, false, stream_context_create($opts));

						if($debug) {
							if(json_decode($get) != false) {
								$get = json_decode($get);
							}
							return array('get' => $url, 'return' => $get);
						}

            if($get) {
	            $result = json_decode($get, true);
							if(isset($result['REQUEST']) && $result['REQUEST'] == "IP_NOT_ALLOWED") {
								return array('status' => 'error', 'code' => '4', 'msg' => 'Request not allowed');
							}
							if(isset($result['REQUEST']) && $result['REQUEST'] == "NEED_PARAMATER_FOR_INSTALLATION") {
								return array('status' => 'error', 'code' => '5', 'msg' => 'Plugin not installed');
							}
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

	public function getFirstServerID() {
		$get = ClassRegistry::init('Server')->find('first');
		return (!empty($get)) ? $get['Server']['id'] : null;
	}

	private function getTimeout() {
		if(empty($this->timeout)) {
    		return ClassRegistry::init('Configuration')->find('first')['Configuration']['server_timeout'];
    	} else {
    		return $this->timeout;
    	}
	}

	public function getConfig($server_id = false) {

		if(!$server_id) {
			$server_id = $this->getFirstServerID();
		}

		if(empty($this->config[$server_id])) {
		    if(!empty($server_id)) {
		        $this->Configuration = ClassRegistry::init('Configuration');
		        $configuration = $this->Configuration->find('first');
		        if($configuration['Configuration']['server_state'] == 1) {
		        	$this->Timeout = $configuration['Configuration']['server_timeout'];
		            $this->Server = ClassRegistry::init('Server');
		            $search = $this->Server->find('first', array('conditions' => array('id' => $server_id)));
		            if(!empty($search)) {
		            	$this->config[$server_id] = array('ip' => $search['Server']['ip'], 'port' => $search['Server']['port'], 'type' => $search['Server']['type']);
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

	public function ping($config = false) {

		if(!$config || !isset($config['ip']) || !isset($config['port'])) {
			return false;
		}

		App::import('Vendor', 'MinecraftPing', array('file' => 'ping-xpaw/MinecraftPing.php'));
		App::import('Vendor', 'MinecraftPingException', array('file' => 'ping-xpaw/MinecraftPingException.php'));

		try {
			$Query = new MinecraftPing($config['ip'], $config['port'], $this->getTimeout());
			$Info = $Query->Query();
		}
		catch(MinecraftPingException $e)
		{
			$Exception = $e;
		}

		if(isset($Query)) {
			$Query->Close();
		}

		return (empty($Exception) && isset($Info['players'])) ? array('getMOTD' => $Info['description'], 'getVersion' => $Info['version']['name'], 'getPlayerCount' => $Info['players']['online'], 'getPlayerMax' => $Info['players']['max'])  : false;

	}

	public function getUrl($server_id, $key = false) {
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

	public function online($server_id = false, $debug = false) {

		if(!$server_id) {
			$server_id = $this->getFirstServerID();
		}

		if(ClassRegistry::init('Configuration')->find('first')['Configuration']['server_state'] == '0') {
			$this->online[$server_id] = false;
			return false;
		}

		if(empty($this->online[$server_id])) {

		    if(!empty($server_id)) {
		        $config = $this->getConfig($server_id);
		        if($config) {

							if($config['type'] == 2) {
								return ($this->ping(array('ip' => $config['ip'], 'port' => $config['port']))) ? true : false;
							} else {
		            $url = $this->getUrl($server_id).'getPlayerMax=server';
		            $opts = array('http' => array('timeout' => $this->getTimeout()));
		            @$get = file_get_contents($url, false, stream_context_create($opts));

								if($debug) {
									if(json_decode($get) != false) {
										$get = json_decode($get);
									}
									return array('get' => $url, 'return' => $get);
								}

		            if($get != false) {
									$get = json_decode($get, true);
									if(!$get) {
										$this->online[$server_id] = false;
			              return false;
									}
									if(isset($get['REQUEST']) && $get['REQUEST'] == "IP_NOT_ALLOWED") {
										$this->online[$server_id] = false;
			              return false;
									}
		            	$this->online[$server_id] = true;
		              return true;
		            } else {
		            	$this->online[$server_id] = false;
		                return false;
		            }
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
		if($type !== "secret_key") return false;

		$return = $this->controller->sendToAPI(array(), 'key', true);
    if ($return['code'] !== 200) return false;

    $return = json_decode($return['content'], true);
    if ($return['status'] !== "success" || $return['status'] == "error") return false;
      
    return @rsa_decrypt($return['secret_key']);
	}

	function check($info) {
		if(empty($info) OR !is_array($value)) return false;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'http://localhost:3000/api/v2/' . $path);
    curl_setopt($curl, CURLOPT_COOKIESESSION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                          
      'Content-Type: application/json',                                                                                
      'Content-Length: ' . strlen($data))                                                                       
    );

    $return = curl_exec($curl);
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_errno($curl);
    curl_close($curl);

	  $url = 'http://' . $value['host'] . ':' . $value['port'] . '?key=' . sha1($value['secret_key']).'&domain='.rawurlencode(Router::url('/', true));
		$opts = array('http' => array('timeout' => $value['timeout']));

		@$get = file_get_contents($url, false, stream_context_create($opts));
		if($get != false) {
			$response = json_decode($get, true);
			if(isset($response['REQUEST']) AND $response['REQUEST'] == 'INSTALLATION_COMPLETED') {
				return true; // response
			} else {
				$this->log('Server connection result : '.json_encode($response));
				return false;
			}
		} else {
			$this->log('Server connection failed');
			return false; // timeout
		}
	}

	function banner_infos($server_id = false) { // On précise l'ID du serveur ou vont veux les infos, ou alors on envoie "all" qui va aller chercher les infos sur tout les serveurs

		if(!$server_id) {
			$server_id = $this->getFirstServerID();
		}

		$ModelConfig = ClassRegistry::init('Configuration')->find('first');
		if(isset($ModelConfig['Configuration']['server_cache'])) {
			if($ModelConfig['Configuration']['server_cache']) {
				$cacheFolder = ROOT.DS.'app'.DS.'tmp'.DS.'cache'.DS;
				$cacheFile = $cacheFolder.'server.cache';
				if(is_array($server_id)) {
					$server_id_implode = implode('-', $server_id);
				} else {
					$server_id_implode = $server_id;
				}
				if(file_exists($cacheFile) && strtotime('+1 min', filemtime($cacheFile)) > time() && isset(unserialize(file_get_contents($cacheFile))[$server_id_implode])) {
					return unserialize(file_get_contents($cacheFile))[$server_id_implode];
				}
			}
		} else {
			return false;
		}

    if(!is_array($server_id)) {

      if($this->online($server_id)) {
          $search = $this->call(array('getMOTD' => 'server', 'getVersion' => 'server', 'getPlayerMax' => 'server', 'getPlayerCount' => 'server'), false, $server_id);

					if(isset($search['status']) && $search['status'] == "error") {
						return false;
					}

          if($search['getPlayerCount'] == "null") {
              $search['getPlayerCount'] = 0;
          }

					if(ClassRegistry::init('Configuration')->find('first')['Configuration']['server_cache']) {
						if(!is_dir($cacheFolder)) {
							mkdir($cacheFolder, 0755, true);
						}
						if(is_dir($cacheFolder)) {
							@file_put_contents($cacheFile, serialize(array($server_id => $search)));
						}
					}

          return $search;
      } else {
        return false;
      }
    } else {
        $servers = $server_id;
        $online[] = false;
				$return['getPlayerMax'] = 0;
				$return['getPlayerCount'] = 0;
        foreach ($servers as $key => $value) {
          if($this->online($value)) {
						$online[] = true;
            $search = $this->call(array('getPlayerMax' => 'server', 'getPlayerCount' => 'server'), false, $value);
            if($search['getPlayerCount'] == "null") {
                $search['getPlayerCount'] = 0;
            }
            $return['getPlayerMax'] = $return['getPlayerMax'] + $search['getPlayerMax'];
            $return['getPlayerCount'] = $return['getPlayerCount'] + $search['getPlayerCount'];
          }
        }

				if(in_array(true, $online) && ClassRegistry::init('Configuration')->find('first')['Configuration']['server_cache']) {
					if(!is_dir($cacheFolder)) {
						mkdir($cacheFolder, 0755, true);
					}
					if(is_dir($cacheFolder)) {
						$server_id_implode = implode('-', $server_id);
						@file_put_contents($cacheFile, serialize(array($server_id_implode => $return)));
					}
				}

        return (in_array(true, $online)) ? $return : false;
    }
	}

	function send_command($cmd, $server_id = false) {

		if(!$server_id) {
			$server_id = $this->getFirstServerID();
		}

 		if($this->online($server_id)) {
      	$this->call(array('performCommand' => $cmd), true, $server_id);
    }
  }

	function commands($commands, $server_id = false) {

		if(!$server_id) {
			$server_id = $this->getFirstServerID();
		}

    if($this->online($server_id)) {
      $this->User = ClassRegistry::init('User');
      $commands = str_replace('{PLAYER}', $this->User->getKey('pseudo'), $commands);
      $commands = explode('[{+}]', $commands);
      $performCommands = array();
      foreach ($commands as $key => $value) {
        $result[] = $this->call(array('performCommand' => $value), true, $server_id);
      }
      return $result;
    }
	}

}
