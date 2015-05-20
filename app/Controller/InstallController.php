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
			$data = file_get_contents('../Config/database.php');
			 
			$this->set('title_for_layout',$this->Lang->get('INSTALL'));
			$data = explode("'", $data);
			$host = $data['9']; $this->set(compact('host'));
			$login = $data['13']; $this->set(compact('login'));
			$database = $data['21']; $this->set(compact('database'));

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

	public function step_2() {
		if(!file_exists('../../config/installed.txt')) {
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