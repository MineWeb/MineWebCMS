<?php
class ServerComponent extends Object {

	private $timeout = NULL;
	private $config = NULL;
	private $online = NULL;
  private $methods = array(
    'getMOTD' => 'GET_MOTD',
    'getPlayerCount' => 'GET_PLAYER_COUNT',
    'getVersion' => 'GET_VERSION',
    'getPlayerMax' => 'GET_MAX_PLAYERS'
  );

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

	function call($methods = false, $needsecretkey = false, $server_id = false, $debug = false) {
    if (!$server_id)
      $server_id = $this->getFirstServerID();
    if (!$methods)
      return array('status' => false, 'error' => 'Unknown method.');

    $config = $this->getConfig($server_id);
    if ($config['type'] == 2) { // only ping
      // ping
      $ping = $this->ping(array('ip' => $config['ip'], 'port' => $config['port']));
      if (!is_array($methods)) // 1 method
        return (isset($ping[$methods])) ? $ping[$methods] : array('status' => false, 'error' => 'Unknown method.');
      // each method
      $result = array();
      foreach ($methods as $method) {
        $result[$method] = (isset($ping[$method])) ? $ping[$method] : array('status' => false, 'error' => 'Unknown method.');
      }
      return $result;
    }

    // plugin
    if(!is_array($methods))
      $methods = array($methods => array());
    $url = $this->getUrl($server_id);
    $data = $this->encryptWithKey(json_encode($methods));

    // request
    list($return, $code, $error) = $this->request($url, $data);

    // debug
    if ($debug) {
      if (json_decode($return) != false)
        $return = json_decode($return);
      return array('get' => $url, 'return' => $return);
    }

    // response
    if ($return && $code === 200) {
      return $this->decryptWithKey(@json_decode($return, true));
    else if ($code === 403)
      return array('status' => false, 'error' => 'Request not allowed.');
    else if ($code === 400)
      return array('status' => false, 'error' => 'Plugin not installed or bad request.');
    else
      return array('status' => false, 'error' => 'Request timeout.');
	}

  public function request($url, $data, $timeout = false) {
    if (!$timeout)
      $timeout = $this->getTimeout();

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
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
    return array($return, $code, $error);
  }

  public function encryptWithKey($data) {
    if (!isset($this->key))
      $this->key = ClassRegistry::init('Configuration')->find('first')['Configuration']['secret_key'];
    $iv_size = mcrypt_get_iv_size(AES_256, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    return mcrypt_encrypt(AES_256, $this->key, $data, MCRYPT_MODE_CBC, $iv);
  }

  public function decryptWithKey($data) {
    if (!isset($this->key))
      $this->key = ClassRegistry::init('Configuration')->find('first')['Configuration']['secret_key'];
    $iv_size = mcrypt_get_iv_size(AES_256, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    return mcrypt_decrypt(AES_256, $this->key, $data, MCRYPT_MODE_CBC, $iv);
  }

  public function getFirstServerID() {
    $get = ClassRegistry::init('Server')->find('first');
    return (!empty($get)) ? $get['Server']['id'] : null;
  }

  private function getTimeout() {
    if (!empty($this->timeout)) {
      return $this->timeout;
    return ClassRegistry::init('Configuration')->find('first')['Configuration']['server_timeout'];
  }

  public function getConfig($server_id = false) {
    if (!$server_id) {
      $server_id = $this->getFirstServerID();
    if (!empty($this->config[$server_id]))
      return $this->config[$server_id];

    $this->Configuration = ClassRegistry::init('Configuration');
    $configuration = $this->Configuration->find('first');
    if ($configuration['Configuration']['server_state'] != 1)
      return $this->config[$server_id] = false;

    $this->Timeout = $configuration['Configuration']['server_timeout'];
    $this->Server = ClassRegistry::init('Server');
    $search = $this->Server->find('first', array('conditions' => array('id' => $server_id)));
    if (empty($search))
      return $this->config[$server_id] = false;

    return $this->config[$server_id] = array('ip' => $search['Server']['ip'], 'port' => $search['Server']['port'], 'type' => $search['Server']['type']);
	}

	public function ping($config = false) {
    if(!$config || !isset($config['ip']) || !isset($config['port'])) {
      return false;

    App::import('Vendor', 'MinecraftPing', array('file' => 'ping-xpaw/MinecraftPing.php'));
    App::import('Vendor', 'MinecraftPingException', array('file' => 'ping-xpaw/MinecraftPingException.php'));

    try {
      $Query = new MinecraftPing($config['ip'], $config['port'], $this->getTimeout());
      $Info = $Query->Query();
    } catch(MinecraftPingException $e) {
      return false;
    }
    $Query->Close();

    return (isset($Info['players'])) ? array('getMOTD' => $Info['description'], 'getVersion' => $Info['version']['name'], 'getPlayerCount' => $Info['players']['online'], 'getPlayerMax' => $Info['players']['max'])  : false;
	}

  public function getUrl($server_id) {
    if(empty($server_id)) return false;

    $config = $this->getConfig($server_id);
    if (!$config) return false;

    return 'http://'.$config['ip'].':'.$config['port'].'?';
	}

  public function online($server_id = false, $debug = false) {
    if (!$server_id) // first server config
      $server_id = $this->getFirstServerID();
    if (ClassRegistry::init('Configuration')->find('first')['Configuration']['server_state'] == '0') // enabled
      return $this->online[$server_id] = false;
    if (!empty($this->online[$server_id])) // already called
      return $this->online[$server_id];
    if (empty($server_id)) // need id
      return $this->online[$server_id] = false;

    // get config
    $config = $this->getConfig($server_id);
    if (!$config) // server not found
      return $this->online[$server_id] = false;

    if ($config['type'] == 2) // ping only
      return ($this->ping(array('ip' => $config['ip'], 'port' => $config['port']))) ? true : false;

    list($return, $code, $error) = $this->request($this->getUrl($server_id), $this->encryptWithKey(json_encode(array())));

    if ($return && $code === 200) {
      return $this->online[$server_id] = true;
    else
      return $this->online[$server_id] = false;
  }

	function getAllServers() {
    $search = ClassRegistry::init('Server')->find('all');
    $return = array();
    foreach ($search as $key => $value) {
      $return[]['server_id'] = $value['Server']['id'];
    }
    return $return;
	}

	function serversOnline() { // savoir si au moins 1 serveur est en ligne
    $allServers = $this->getAllServers();
    $return = array();
    foreach ($allServers as $server) {
      if ($return[][$server['server_id']] = $this->online($server['server_id']) === true)
        return true;
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

  function check($info, $value) {
    if(empty($info) OR !is_array($value)) return false;

    $path = 'http://' . $value['host'] . ':' . $value['port'];
    $secure = $this->getSecure();
    $data = json_encode(array(
      'license_id' =>  $secure['id'],
      'license_key' => $secure['key'],
      'domain' => Router::url('/', true)
    ));

    list($return, $code, $error) = $this->request($path, $data, $value['timeout']);

    if ($return && $code === 200)
      return true;

    switch ($code) {
      case 500:
        $this->log('Link server: MineWeb API down');
        break;
      case 403:
        $this->log('Link server: Already link');
        break;
      case 400:
        $this->log('Link server: Invalid params');
        break;

      default:
        $this->log('Server connection failed');
        break;
    }
    return false;
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
    return $this->call(array('performCommand' => $cmd), true, $server_id);
  }

	function commands($commands, $server_id = false) {
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
