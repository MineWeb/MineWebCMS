<?php
App::uses('CakeObject', 'Core');

class ServerComponent extends CakeObject
{

    private $timeout = NULL;
    private $config = NULL;
    private $online = NULL;
    public $lastErrorMessage = null;
    public $linkErrorCode = null;

    public $controller;
    public $components = array('Session', 'Configuration');

    public function initialize($controller)
    {
        $this->controller = $controller;
        $this->controller->set('Server', $this);
        $this->configModel = ClassRegistry::init('Configuration');
    }

    public function startup($controller)
    {
    }

    public function beforeRender($controller)
    {
    }

    public function shutdown($controller)
    {
    }

    public function beforeRedirect()
    {
    }

    private function parse($methods)
    {
        $result = array();
        foreach ($methods as $method) {
            if (!is_array($method)) {
                $result[] = array(
                    'name' => $method,
                    'args' => []
                );
                continue;
            }
            foreach ($method as $name => $args) {
                $result[] = array(
                    'name' => $name,
                    'args' => (is_array($args)) ? $args : [$args]
                );
            }
        }
        return $result;
    }

    function getServerIdConnected($username)
    {
        $servers = ClassRegistry::init('Server')->find('all', ['conditions' => ['type' => 0]]);
        foreach ($servers as $srv) {
            $server_id = $srv['Server']['id'];
            if (!$this->userIsConnected($username, $server_id))
                continue;
            return $server_id;
        }
        return false;
    }

    private function parseResult($result)
    {
        $methods = array();
        foreach ($result as $method) {
            $methods[] = [$method['name'] => $method['response']];
        }
        return $methods;
    }

    public function call($methods = [], $server_id = false, $debug = false)
    {
        $multi = true;
        if (!$server_id)
            $server_id = $this->getFirstServerID();
        if (!$methods) {
            $this->lastErrorMessage = 'Unknown method.';
            return false;
        }
        $config = $this->getConfig($server_id);

        if (!is_array($methods)) {// transform into array
            $methods = array(array($methods => array()));
            $multi = false;
        } else if (!isset($methods[0])) {
            $result = array();
            foreach ($methods as $name => $args)
                $result[] = [$name => (is_array($args)) ? $args : [$args]];
            $methods = $result;
            $multi = false;
        }

        if ($config['type'] == 1 || $config['type'] == 2) {
            $methodsName = array_map(function ($method) {
                return array_keys($method)[0];
            }, $methods);
            $result = [];

            if (in_array('RUN_COMMAND', $methodsName) && $config['type'] == 2) {
                foreach ($methods as $key => $method) {
                    if (array_keys($method)[0] === 'RUN_COMMAND')
                        $result[$key]['RUN_COMMAND'] = $this->rcon(['ip' => $config['ip'], 'port' => $config['data']['rcon_port'], 'password' => $config['data']['rcon_password']], $method['RUN_COMMAND']) !== false;
                }
                $methodsName = array_delete_value($methodsName, 'RUN_COMMAND');
            }

            if (count($methodsName) > 0) {
                $ping = $this->ping(array('ip' => $config['ip'], 'port' => $config['port']));
                foreach ($methods as $key => $method) {
                    $name = array_keys($method)[0];
                    if (isset($ping[$name]))
                        $result[$key][$name] = $ping[$name];
                }
            }

            if (!$multi) {
                $parsedResult = [];
                foreach ($result as $item) {
                    foreach ($item as $key => $value) {
                        $parsedResult[$key] = $value;
                    }
                }
                $result = $parsedResult;
            }
            return $result;
        }

        // plugin
        $url = $this->getUrl($server_id);
        $data = $this->encryptWithKey(json_encode($this->parse($methods)));

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
            $return = @json_decode($return, true);
            $return = $this->decryptWithKey($return['signed'], $return['iv']);
            $return = $this->parseResult(@json_decode($return, true));
            if (!$multi) {
                $result = [];
                foreach ($return as $item) {
                    foreach ($item as $key => $value) {
                        $result[$key] = $value;
                    }
                }
                $return = $result;
            }
            return $return;
        } else if ($code === 403 || $code === 500) {
            $this->lastErrorMessage = 'Request not allowed.';
            return false;
        } else if ($code === 400) {
            $this->lastErrorMessage = 'Plugin not installed or bad request.';
            return false;
        } else {
            $this->lastErrorMessage = 'Request timeout.';
            return false;
        }
    }

    private function request($url, $data, $timeout = false)
    {
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

    private function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    private function encryptWithKey($data)
    {
        if (!isset($this->key))
            $this->key = $this->configModel->find('first')['Configuration']['server_secretkey'];
        $iv_size = openssl_cipher_iv_length('aes-128-cbc'); // AES-128-CBC or  AES-256-CBC
        $iv = openssl_random_pseudo_bytes($iv_size);

        $data = $this->pkcs5_pad($data, 16);

        $signed = openssl_encrypt($data, 'aes-128-cbc', substr($this->key, 0, 16), OPENSSL_ZERO_PADDING, $iv);
        if ($signed === false)
            $this->log('Server: openssl_encrypt failed.');
        return json_encode(array('signed' => ($signed), 'iv' => base64_encode($iv)));
    }

    private function decryptWithKey($data, $iv)
    {
        if (!isset($this->key))
            $this->key = $this->configModel->find('first')['Configuration']['server_secretkey'];
        $iv = base64_decode($iv);
        $data = base64_decode($data);
        return openssl_decrypt($data, 'AES-128-CBC', substr($this->key, 0, 16), OPENSSL_RAW_DATA, $iv);
    }

    public function getFirstServerID()
    {
        $get = ClassRegistry::init('Server')->find('first');
        return (!empty($get)) ? $get['Server']['id'] : null;
    }

    private function getTimeout()
    {
        if (!empty($this->timeout))
            return $this->timeout;
        return $this->configModel->find('first')['Configuration']['server_timeout'];
    }

    public function getConfig($server_id = false)
    {
        if ($server_id === false)
            $server_id = $this->getFirstServerID();
        if (!empty($this->config[$server_id]))
            return $this->config[$server_id];

        $this->Configuration = $this->configModel;
        $configuration = $this->Configuration->find('first');
        if ($configuration['Configuration']['server_state'] != 1)
            return $this->config[$server_id] = false;

        $this->Timeout = $configuration['Configuration']['server_timeout'];
        $this->Server = ClassRegistry::init('Server');
        $search = $this->Server->find('first', array('conditions' => array('id' => $server_id)));
        if (empty($search))
            return $this->config[$server_id] = false;

        return $this->config[$server_id] = array(
            'ip' => $search['Server']['ip'],
            'port' => $search['Server']['port'],
            'type' => $search['Server']['type'],
            'data' => json_decode($search['Server']['data'], true)
        );
    }

    public function ping($config = false)
    {
        if (!$config || !isset($config['ip']) || !isset($config['port']))
            return false;

        App::import('Vendor', 'MinecraftPing', array('file' => 'ping-xpaw/MinecraftPing.php'));
        App::import('Vendor', 'MinecraftPingException', array('file' => 'ping-xpaw/MinecraftPingException.php'));

        try {
            $Query = new MinecraftPing($config['ip'], $config['port'], $this->getTimeout());
            $Info = $Query->Query();
        } catch (MinecraftPingException $e) {
            return false;
        }
        $Query->Close();

        return (isset($Info['players'])) ? array(
            'GET_MOTD' => $Info['description'],
            'GET_VERSION' => $Info['version']['name'],
            'GET_PLAYER_COUNT' => $Info['players']['online'],
            'GET_MAX_PLAYERS' => $Info['players']['max']) : false;
    }

    public function rcon($config = false, $cmd = '')
    {
        if (!$config || !isset($config['ip']) || !isset($config['port']) || !isset($config['password']))
            return false;

        App::import('Vendor', 'Rcon', array('file' => 'rcon/Rcon.php'));

        $rcon = new Thedudeguy\Rcon($config['ip'], $config['port'], $config['password'], $this->getTimeout());
        if ($rcon->connect())
            return $rcon->sendCommand($cmd);
        return false;
    }

    public function getUrl($server_id)
    {
        if (empty($server_id)) return false;

        $config = $this->getConfig($server_id);
        if (!$config) return false;

        return 'http://' . $config['ip'] . ':' . $config['port'] . '/ask';
    }

    public function online($server_id = false, $debug = false)
    {
        if (!$server_id) // first server config
            $server_id = $this->getFirstServerID();
        if ($this->configModel->find('first')['Configuration']['server_state'] == '0') // enabled
            return $this->online[$server_id] = false;
        if (!empty($this->online[$server_id])) // already called
            return $this->online[$server_id];
        if (empty($server_id)) // need id
            return $this->online[$server_id] = false;

        // get config
        $config = $this->getConfig($server_id);
        if (!$config) // server not found
            return $this->online[$server_id] = false;

        if ($config['type'] == 1 || $config['type'] == 2) // ping only
            return $this->online[$server_id] = ($this->ping(array('ip' => $config['ip'], 'port' => $config['port']))) ? true : false;

        list($return, $code, $error) = $this->request($this->getUrl($server_id), $this->encryptWithKey("[]"));
        if ($return && $code === 200)
            return $this->online[$server_id] = true;
        else
            return $this->online[$server_id] = false;
    }

    public function getAllServers()
    {
        $search = ClassRegistry::init('Server')->find('all');
        $return = array();
        foreach ($search as $key => $value) {
            $return[]['server_id'] = $value['Server']['id'];
        }
        return $return;
    }

    public function serversOnline()
    { // savoir si au moins 1 serveur est en ligne
        $allServers = $this->getAllServers();
        $return = array();
        foreach ($allServers as $server) {
            if ($return[][$server['server_id']] = $this->online($server['server_id']) === true)
                return true;
        }
        return false;
    }

    public function getSecretKey()
    {
        $config = $this->configModel->find('first');
        $key = $config['Configuration']['server_secretkey'];
        if (isset($key) && !empty($key))
            return $key;
        $key = "";
        $possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for ($i = 0; $i < 32; $i++)
            $key .= $possible[rand(0, 61)];
        // save
        $this->configModel->read(null, $config['Configuration']['id']);
        $this->configModel->set(['server_secretkey' => $key]);
        $this->configModel->save();
        return $key;
    }

    public function check($info, $value)
    {
        if (empty($info) OR !is_array($value)) return false;

        $path = 'http://' . $value['host'] . ':' . $value['port'] . '/handshake';
        $data = json_encode(array(
            'secretKey' => substr($this->getSecretKey(), 0, 16),
            'domain' => Router::url('/', true)
        ));

        list($return, $code, $error) = $this->request($path, $data, $value['timeout']);

        if ($return && $code === 200)
            return true;

        switch ($code) {
            case 403:
                $this->lastErrorMessage = 'Already link';
                $this->linkErrorCode = 'ALREADY_LINKED';
                break;
            case 400:
                $this->lastErrorMessage = 'Invalid params';
                $this->linkErrorCode = 'INVALID_PARAMS';
                break;

            default:
                $this->lastErrorMessage = 'Server connection failed: ' . $code;
                $this->linkErrorCode = 'FAILED';
                break;
        }
        $this->log('Link server: ' . $this->lastErrorMessage);
        return false;
    }

    public function banner_infos($serverId = false)
    { // On précise l'ID du serveur ou vont veux les infos, ou alors on envoie "all" qui va aller chercher les infos sur tout les serveurs
        if (!$serverId)
            $serverId = $this->getFirstServerID();
        if (!is_array($serverId))
            $serverId = array($serverId);

        // get configuration
        $configuration = $this->configModel->find('first')['Configuration'];
        // setup cache
        if ($configuration['server_cache']) {
            $cacheFolder = ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache' . DS;
            $cacheFile = $cacheFolder . 'server.cache';
            $serverIdString = implode('-', $serverId);
            // check cache
            if (file_exists($cacheFile) && strtotime('+1 min', filemtime($cacheFile)) > time()) {
                $cacheContent = @unserialize(@file_get_contents($cacheFile));
                if ($cacheContent && isset($cacheContent[$serverIdString]))
                    return $cacheContent[$serverIdString];
            }
        }

        // request
        $data = array(
            'GET_PLAYER_COUNT' => 0,
            'GET_MAX_PLAYERS' => 0
        );
        foreach ($serverId as $id) {
            $req = $this->call(['GET_PLAYER_COUNT' => [], 'GET_MAX_PLAYERS' => []], $id);
            if (!$req) continue;
            $data['GET_PLAYER_COUNT'] += intval($req['GET_PLAYER_COUNT']);
            $data['GET_MAX_PLAYERS'] += intval($req['GET_MAX_PLAYERS']);
        }

        // cache
        if ($configuration['server_cache']) {
            if (!is_dir($cacheFolder)) mkdir($cacheFolder, 0755, true); // create folder
            @file_put_contents($cacheFile, serialize(array($serverIdString => $data)));
        }
        // return
        return $data;
    }

    public function userIsConnected($username, $server_id = false)
    {
        $result = $this->call(['IS_CONNECTED' => $username], $server_id);
        if ($result && isset($result['IS_CONNECTED']) && $result['IS_CONNECTED'])
            return true;
        else if (!isset($result['IS_CONNECTED']))
            return true;
        return false;
    }

    public function send_command($cmd, $server_id = false)
    {
        return $this->commands(array($cmd), $server_id);
    }

    public function commands($commands, $server_id = false)
    {
        if (!is_array($commands)) {
            $this->User = ClassRegistry::init('User');
            $commands = str_replace('{PLAYER}', $this->User->getKey('pseudo'), $commands);
            $commands = explode('[{+}]', $commands);
        }
        $calls = [];
        foreach ($commands as $command)
            $calls[] = ['RUN_COMMAND' => $command];
        return $this->call($calls, $server_id);
    }

    public function scheduleCommands($commands, $time, $servers = array())
    {
        if (empty($servers))
            $servers[] = $this->getFirstServerID();
        foreach ($servers as $server) {
            // Get timestamp
            $serverTimestamp = $this->call('GET_SERVER_TIMESTAMP', $server);
            if (!$serverTimestamp)
                return false;
            $serverTimestamp = $serverTimestamp['GET_SERVER_TIMESTAMP'];

            // Calcul
            $time = $time * 60000 + $serverTimestamp;

            // Commands
            $this->User = ClassRegistry::init('User');
            if (!is_array($commands)) {
                $commands = str_replace('{PLAYER}', $this->User->getKey('pseudo'), $commands);
                $commands = explode('[{+}]', $commands);
            }

            // Execute
            $calls = array();
            foreach ($commands as $command) {
                $calls[] = ['RUN_SCHEDULED_COMMAND' => [$command, $this->User->getKey('pseudo'), $time]];
            }
            $this->call($calls, $server);
        }
    }
}
