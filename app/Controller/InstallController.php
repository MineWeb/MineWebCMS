<?php 

class InstallController extends AppController {

	public $components = array('Session');

	public function beforeFilter() {
		if(!file_exists('../../config/install.txt')) {
			App::import('Model', 'ConnectionManager');
			$db = ConnectionManager::getDataSource('default');
			if(!$db->isConnected()) {
            	exit('Could not connect to database. Please check the settings in app/config/database.php and try again');
        	} else {
				$tables = file_get_contents('../../install/sql.txt');
		        $tables = explode('|', $tables);
		        foreach ($tables as $do) {
		          $db->query($do);
		        }
		        $data = "CREATED AT ".date('H:i:s d/m/Y');
				$fp = fopen("../../install.txt","w+");
				fwrite($fp, $data);
				fclose($fp);
			}
		} elseif(file_exists('../../config/installed.txt')) {
			 
			echo $this->Lang->get('ALREADY_INSTALL');
			$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$url = substr($url, 0, -7);
			echo ' <a href="http://'.$url.'">'.$this->Lang->get('RETURN_TO_HOME').'</a>';
            exit();
		}
	}

	public function index() {
		if(!file_exists('../../config/installed.txt')) {
			$this->layout = 'install';
			//$data = file_get_contents('../Config/database.php');
			 
			$this->set('title_for_layout',$this->Lang->get('INSTALL'));
			/*$data = explode("'", $data);
			$host = $data['9']; $this->set(compact('host'));
			$login = $data['13']; $this->set(compact('login'));
			$database = $data['21']; $this->set(compact('database'));*/

			$url = 'http://mineweb.org/api/key_verif/';
			$secure = file_get_contents(ROOT.'/config/secure');
			$secure = @json_decode($secure, true);
			if($secure['key'] != "NOT_INSTALL") {
				$this->set('step1_ok', true);
			} else {
				$this->set('step1_ok', false);
			}

			$this->set('server_host', $this->Configuration->get('server_host'));
			$this->set('port', $this->Configuration->get('server_port'));
			$this->set('secret_key', $this->Configuration->get('server_secretkey'));
			$this->set('timeout', $this->Configuration->get('server_timeout'));

			$this->loadModel('User');
			$admin = $this->User->find('first');
			if(!empty($admin)) {
				$this->set('admin_pseudo', $admin['User']['pseudo']);
				$this->set('admin_password', 1);
				$this->set('admin_email', $admin['User']['email']);
			}
		} else {
			$this->redirect(array('controller' => 'install', 'action' => 'end'));
		}
	}

	public function step_1() {
		$this->autoRender = false;
		if(!file_exists('../../config/installed.txt')) {
			$this->layout = null;
			if($this->request->is('ajax')) {
				 
				if(!empty($this->request->data['key'])) {
					
					$url = 'http://mineweb.org/api/key_verif/';
					$secure = file_get_contents(ROOT.'/config/secure');
					$secure = json_decode($secure, true);
					$postfields = array(
						'id' => $secure['id'],
					    'key' => $this->request->data['key'],
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
				        	file_put_contents(ROOT.'/config/last_check', $return['time']);
				        	file_put_contents(ROOT.'/config/secure', json_encode(array('id' => $secure['id'], 'key' => $this->request->data['key'])));
				        	echo $this->Lang->get('SUCCESS_CONNECTED_TO_API').'|true';
				        } elseif($return['status'] == "error") {
				        	echo $this->Lang->get('LICENSE_ERROR__'.$return['msg']).'|false';
				        }
					} else {
						echo 'error';
					}

				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}

			} else {
				echo $this->Lang->get('NOT_POST' ,$language).'|false';
			}
		} else {
			echo $this->Lang->get('ALREADY_INSTALL' ,$language).'|false';
		}
	}


	public function step_2() {
		if(!file_exists('../../config/installed.txt')) {
			$this->layout = null;
			if($this->request->is('ajax')) {
				 
				if(!empty($this->request->data['host']) AND !empty($this->request->data['port']) AND !empty($this->request->data['timeout'])) {
					$secret_key = $this->Server->get('secret_key');
					if($secret_key !== false) {
						if($this->Server->check('connection', array('host' => $this->request->data['host'], 'port' => $this->request->data['port'], 'timeout' => $this->request->data['timeout'], 'secret_key' => $secret_key))) {
							$this->Configuration->set('server_state', 1);
							$this->Configuration->set('server_host', $this->request->data['host']);
							$this->Configuration->set('server_port', $this->request->data['port']);
							$this->Configuration->set('server_secretkey', $secret_key);
							$this->Configuration->set('server_timeout', $this->request->data['timeout']);
							echo $this->Lang->get('SUCCESS_CONNECTION_SERVER').'|true';
						} else {
							echo $this->Lang->get('SERVER_CONNECTION_FAILED').'|false';
						}
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
			echo $this->Lang->get('ALREADY_INSTALL' ,$language).'|false';
		}
	}

	public function step_3() {
		if(!file_exists('../../config/installed.txt')) {
			$this->layout = null;
			if($this->request->is('ajax')) {
				 
				if(!empty($this->request->data['pseudo']) AND !empty($this->request->data['password']) AND !empty($this->request->data['password_confirmation']) AND !empty($this->request->data['email'])) {
					$this->request->data['password'] = password($this->request->data['password']);
					$this->request->data['password_confirmation'] =password($this->request->data['password_confirmation']);
					if($this->request->data['password'] == $this->request->data['password_confirmation']) {
						if(filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL)) {
							$this->request->data['ip'] = $_SERVER["REMOTE_ADDR"];
							$this->request->data['rank'] = 4;
							$this->loadModel('User');
							$this->User->set($this->request->data);
							$this->User->save();
							echo $this->Lang->get('SUCCESS_REGISTER').'|true';
						} else {
							echo $this->Lang->get('EMAIL_NOT_VALIDATE').'|false';
						}
					} else {
						echo $this->Lang->get('PASSWORD_NOT_SAME').'|false';
					}
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST' ,$language).'|false';
			}
		} else {
			echo $this->Lang->get('ALREADY_INSTALL' ,$language).'|false';
		}
	}

	public function end() {
		if(!file_exists('../../config/installed.txt')) {
			$create = fopen("../../config/installed.txt", "w+");
			if(!$create) {
				echo $this->Lang->get('ERROR_CHMOD');
			}
			$this->layout = null;
			$this->redirect(array('controller' => 'pages', 'action' => 'home'));
		} else {
			$this->redirect(array('controller' => 'install', 'action' => 'index'));
		}
	}

}