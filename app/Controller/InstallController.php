<?php

class InstallController extends AppController {

	public function beforeFilter() {

		$this->Security->blackHoleCallback = 'blackhole';
		$this->Security->validatePost = false;
		$this->Security->csrfUseOnce = false;

		if(file_exists(ROOT.DS.'config'.DS.'installed.txt')) {
			echo $this->Lang->get('INSTALL__ALREADY_INSTALLED');
			$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$url = substr($url, 0, -7);
			echo ' <a href="http://'.$url.'">'.$this->Lang->get('GLOBAL__BACK_TO_INDEX').'</a>';
            exit();
		}
	}

	public function index() {
		if(!file_exists(ROOT.DS.'config'.DS.'installed.txt')) {
			$this->layout = 'install';

			$this->set('title_for_layout',$this->Lang->get('INSTALL__INSTALL'));

			$url = 'http://mineweb.org/api/v1/key_verif/';
			$secure = file_get_contents(ROOT.'/config/secure');
			$secure = @json_decode($secure, true);
			if($secure['key'] != "NOT_INSTALL") {
				$this->set('step1_ok', true);
			} else {
				$this->set('step1_ok', false);
			}

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
		if(!file_exists(ROOT.DS.'config'.DS.'installed.txt')) {
			$this->layout = null;
			if($this->request->is('ajax')) {

				if(!empty($this->request->data['key'])) {

					$return = $this->sendToAPI(
		                  array(),
		                  'key_verif',
		                  true
		                );

					if($return['code'] == 200) {
		        $return = json_decode($return['content'], true);
		        if($return['status'] == "success") {
		        	file_put_contents(ROOT.'/config/last_check', $return['time']);
		        	file_put_contents(ROOT.'/config/secure', json_encode(array('id' => $secure['id'], 'key' => $this->request->data['key'])));
		        	echo $this->Lang->get('INSTALL__API_CONNECT_SUCCESS').'|true';
		        } elseif($return['status'] == "error") {
		        	echo $this->Lang->get('LICENSE_ERROR__'.$return['msg']).'|false';
		        }
					} else {
						echo 'error';
					}

				} else {
					echo $this->Lang->get('ERROR__FILL_ALL_FIELDS').'|false';
				}

			} else {
				echo $this->Lang->get('NOT_POST' ,$language).'|false';
			}
		} else {
			echo $this->Lang->get('ALREADY_INSTALL' ,$language).'|false';
		}
	}

	public function step_3() {
		$this->autoRender = false;
		if(!file_exists(ROOT.DS.'config'.DS.'installed.txt')) {
			if($this->request->is('ajax')) {

				if(!empty($this->request->data['pseudo']) AND !empty($this->request->data['password']) AND !empty($this->request->data['password_confirmation']) AND !empty($this->request->data['email'])) {
					if($this->request->data['password'] == $this->request->data['password_confirmation']) {
						if(filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL)) {
							$this->request->data['ip'] = $_SERVER["REMOTE_ADDR"];
							$this->request->data['rank'] = 4;
							$this->request->data['password'] = $this->Util->password($this->request->data['password'], $this->request->data['pseudo']);
							$this->loadModel('User');
							$this->User->set($this->request->data);
							$this->User->save();
							echo $this->Lang->get('USER__REGISTER_SUCCESS').'|true';
						} else {
							echo $this->Lang->get('USER__ERROR_EMAIL_NOT_VALID').'|false';
						}
					} else {
						echo $this->Lang->get('USER__ERROR_PASSWORDS_NOT_SAME').'|false';
					}
				} else {
					echo $this->Lang->get('ERROR__FILL_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('ERROR__BAD_REQUEST').'|false';
			}
		} else {
			echo $this->Lang->get('INSTALL__ALREADY_INSTALLED').'|false';
		}
	}

	public function end() {
		if(!file_exists(ROOT.DS.'config'.DS.'installed.txt')) {
			$create = fopen(ROOT.DS.'config'.DS.'installed.txt', "w+");
			if(!$create) {
				echo $this->Lang->get('ERROR_CHMOD');
			}
			$this->layout = null;
			$this->redirect('/');
		} else {
			$this->redirect(array('controller' => 'install', 'action' => 'index'));
		}
	}

}
