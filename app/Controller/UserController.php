<?php

class UserController extends AppController {

	public $components = array('Session', 'Captcha', 'API');

	function get_captcha() {
		$this->autoRender = false;
		App::import('Component','Captcha');

		//generate random charcters for captcha
		$random = mt_rand(100, 99999);

		//save characters in session
		$this->Session->write('captcha_code', $random);

		$settings = array(
			'characters' => $random,
			'winHeight' => 50,         // captcha image height
			'winWidth' => 220,		   // captcha image width
			'fontSize' => 25,          // captcha image characters fontsize
			'fontPath' => WWW_ROOT.'tahomabd.ttf',    // captcha image font
			'noiseColor' => '#ccc',
			'bgColor' => '#fff',
			'noiseLevel' => '100',
			'textColor' => '#000'
		);

		$img = $this->Captcha->ShowImage($settings);
		echo $img;
	}

	function ajax_register() {
		$this->autoRender = false;
		if($this->request->is('Post')) { // si la requête est bien un post
			if(!empty($this->request->data['pseudo']) && !empty($this->request->data['password']) && !empty($this->request->data['password_confirmation']) && !empty($this->request->data['email'])) { // si tout les champs sont bien remplis

				// Captcha
				if($this->Configuration->get('captcha_type') == "2") { // ReCaptcha

					$validCaptcha = $this->Util->isValidReCaptcha($this->request->data['recaptcha'], $_SERVER['REMOTE_ADDR'], $this->Configuration->get('captcha_google_secret'));

				} else {

					$captcha = $this->Session->read('captcha_code');
					$validCaptcha = (!empty($captcha) && $captcha == $this->request->data['captcha']);

				}
				//

				if($validCaptcha) { // on check le captcha déjà
					$this->loadModel('User');
					$isValid = $this->User->validRegister($this->request->data);
					if($isValid === true) { // on vérifie si y'a aucune erreur

						// on enregistre
						$userSession = $this->User->register($this->request->data);

						// on prépare la connexion
						$this->Session->write('user', $userSession);

						// on dis que c'est bon
						echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('SUCCESS_REGISTER')));

					} else { // si c'est pas bon, on envoie le message d'erreur retourné par l'étape de validation
						echo json_encode(array('statut' => false, 'msg' => $this->Lang->get($isValid)));
					}
				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('INVALID_CAPTCHA')));
				}
			} else {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('COMPLETE_ALL_FIELDS')));
			}
		} else {
			echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('PAGE_BAD_EXECUTED')));
		}
	}

	function ajax_login() {
		$this->autoRender = false;
		if($this->request->is('Post')) {
			if(!empty($this->request->data['pseudo']) && !empty($this->request->data['password'])) {

				$login = $this->User->login($this->request->data);
				if(isset($login['status']) && $login['status'] === true) {

					$this->Session->write('user', $login['session']);

					$this->getEventManager()->dispatch(new CakeEvent('afterLogin', $this));

					echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('SUCCESS_LOGIN')));

				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get($login)));
				}

			} else {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('COMPLETE_ALL_FIELDS')));
			}
		} else {
			echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('PAGE_BAD_EXECUTED')));
		}
	}

	function ajax_lostpasswd() {
		$this->layout = null;
		$this->autoRender = false;
		if($this->request->is('ajax')) {
			if(!empty($this->request->data['email'])) {
				$this->loadModel('User');
				if(filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL)) {
					$search = $this->User->find('first', array('conditions' => array('email' => $this->request->data['email'])));
					if(!empty($search)) {
						$this->loadModel('Lostpassword');
						$key = substr(md5(rand().date('sihYdm')), 0, 10);

						$to = $this->request->data['email'];
						$subject = $this->Lang->get('RESET_PASSWORD').' | '.$this->Configuration->get('name').'';
						$message = $this->Lang->email_reset($this->request->data['email'], $search['User']['pseudo'], $key);
						if($this->Util->prepareMail($to, $subject, $message)->sendMail()) {
							$this->Lostpassword->create();
							$this->Lostpassword->set(array(
								'email' => $this->request->data['email'],
								'key' => $key
							));
							$this->Lostpassword->save();
							echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('SUCCESS_SEND_RESET_MAIL')));
						} else {
							echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('INTERNAL_ERROR')));
						}
					} else {
						echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('UNKNONWN_USER')));
					}
				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('EMAIL_NOT_VALIDATE')));
				}
			} else {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('COMPLETE_ALL_FIELDS')));
			}
		} else {
			echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('PAGE_BAD_EXECUTED')));
		}
	}

	function ajax_resetpasswd() {
		$this->autoRender = false;
		if($this->request->is('ajax')) {
			if(!empty($this->request->data['password']) AND !empty($this->request->data['password2']) AND !empty($this->request->data['email'])) {

				$reset = $this->User->resetPass($this->request->data);
				if($reset['status'] && $reset['status'] === true) {
					$this->Session->write('user', $reset['session']);

					$this->History->set('RESET_PASSWORD', 'user');

					echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('SUCCESS_RESET_PASSWORD')));
				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get($reset)));
				}
			} else {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('COMPLETE_ALL_FIELDS')));
			}
		} else {
			echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('PAGE_BAD_EXECUTED')));
		}
	}

	function logout() {
		$this->autoRender = false;
		$this->Session->delete('user');
     	$this->redirect($this->referer());
	}

	function uploadSkin() {
		$this->autoRender = false;

		if($this->isConnected && $this->API->can_skin()) {
			if($this->request->is('post')) {

				$skin_max_size = 10000000; // octet

				$this->loadModel('ApiConfiguration');
				$ApiConfiguration = $this->ApiConfiguration->find('first');
				$target_config = $ApiConfiguration['ApiConfiguration']['skin_filename'];

				$filename = substr($target_config, (strrpos($target_config, '/') + 1));
				$filename = str_replace('{PLAYER}', $this->User->getKey('pseudo'), $filename);
				$filename = str_replace('php', '', $filename);
				$filename = str_replace('.', '', $filename);
				$filename = $filename.'.png';

				$target = substr($target_config, 0, (strrpos($target_config, '/') + 1));
				$target = WWW_ROOT.'/'.$target;

				$width_max = $ApiConfiguration['ApiConfiguration']['skin_width']; // pixel
				$height_max = $ApiConfiguration['ApiConfiguration']['skin_height']; // pixel

				$isValidImg = $this->Util->isValidImage($this->request, array('png'), $width_max, $height_max, $skin_max_size);

				if(!$isValidImg['status']) {
					echo json_encode(array('statut' => false, 'msg' => $isValidImg['msg']));
					exit;
				} else {
					$infos = $isValidImg['infos'];
				}

				if(!$this->Util->uploadImage($this->request, $target.$filename)) {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('FORM__ERROR_WHEN_UPLOAD')));
					exit;
				}

	     echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('SKIN_SUCCESS_UPLOAD')));

			}

		} else {
			new ForbiddenException();
		}
	}

	function uploadCape() {
		$this->autoRender = false;

		if($this->isConnected && $this->API->can_cape()) {
			if($this->request->is('post')) {

				$cape_max_size = 10000000; // octet

				$this->loadModel('ApiConfiguration');
				$ApiConfiguration = $this->ApiConfiguration->find('first');
				$target_config = $ApiConfiguration['ApiConfiguration']['cape_filename'];

				$filename = substr($target_config, (strrpos($target_config, '/') + 1));
				$filename = str_replace('{PLAYER}', $this->User->getKey('pseudo'), $filename);
				$filename = str_replace('php', '', $filename);
				$filename = str_replace('.', '', $filename);
				$filename = $filename.'.png';

				$target = substr($target_config, 0, (strrpos($target_config, '/') + 1));
				$target = WWW_ROOT.'/'.$target;

				$width_max = $ApiConfiguration['ApiConfiguration']['cape_width']; // pixel
				$height_max = $ApiConfiguration['ApiConfiguration']['cape_height']; // pixel

				$isValidImg = $this->Util->isValidImage($this->request, array('png'), $width_max, $height_max, $cape_max_size);

				if(!$isValidImg['status']) {
					echo json_encode(array('statut' => false, 'msg' => $isValidImg['msg']));
					exit;
				} else {
					$infos = $isValidImg['infos'];
				}

				if(!$this->Util->uploadImage($this->request, $target.$filename)) {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('FORM__ERROR_WHEN_UPLOAD')));
					exit;
				}

	     echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('CAPE_SUCCESS_UPLOAD')));

			}

		} else {
			new ForbiddenException();
		}
	}

	function profile() {
		if($this->isConnected) {

			$this->loadModel('User');

			$this->set('title_for_layout', $this->User->getKey('pseudo'));
			$this->layout= $this->Configuration->get_layout();
			if($this->EyPlugin->isInstalled('Shop')) {

				$this->set('shop_active', true);

				$this->loadModel('Shop.PaysafecardMessage');
				$search_psc_msg = $this->PaysafecardMessage->find('all', array('conditions' => array('to' =>  $this->User->getKey('pseudo'))));
				if(!empty($search_psc_msg)) {
					$this->set(compact('search_psc_msg'));
					$this->PaysafecardMessage->deleteAll(array('to' =>  $this->User->getKey('pseudo')));
				} else {
					$this->set('search_psc_msg', false);
				}

			} else {
				$this->set('shop_active', false);
				$this->set('search_psc_msg', false);
			}

			$available_ranks = array(0 => $this->Lang->get('MEMBER'), 2 => $this->Lang->get('MODERATOR'), 3 => $this->Lang->get('ADMINISTRATOR'), 4 => $this->Lang->get('ADMINISTRATOR'), 5 => $this->Lang->get('BANNED'));
			$this->loadModel('Rank');
			$custom_ranks = $this->Rank->find('all');
			foreach ($custom_ranks as $key => $value) {
				$available_ranks[$value['Rank']['rank_id']] = $value['Rank']['name'];
			}
			$this->set(compact('available_ranks'));

			$api = $this->API->getIp($this->User->getKey('pseudo'));
			$this->set(compact('api'));

			$this->set('can_cape', $this->API->can_cape());
			$this->set('can_skin', $this->API->can_skin());

		} else {
			$this->redirect('/');
		}
	}

	function change_pw() {
		$this->autoRender = false;
		if($this->isConnected) {
			if($this->request->is('ajax')) {
				if(!empty($this->request->data['password']) AND !empty($this->request->data['password_confirmation'])) {
					$password = password($this->request->data['password']);
					$password_confirmation = password($this->request->data['password_confirmation']);
					if($password == $password_confirmation) {
						$this->User->setKey('password', $password);
						echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('PASSWORD_CHANGE_SUCCESS')));
					} else {
						echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('PASSWORD_NOT_SAME')));
					}
				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('COMPLETE_ALL_FIELDS')));
				}
			} else {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('NOT_POST')));
			}
		} else {
			echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('NEED_CONNECT')));
		}
	}

	function change_email() {
		$this->autoRender = false;
		if($this->isConnected) {
			if($this->request->is('ajax')) {
				if(!empty($this->request->data['email']) AND !empty($this->request->data['email_confirmation'])) {
					if($this->request->data['email'] == $this->request->data['email_confirmation']) {
						if(filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL)) {
							$this->User->setKey('email', $this->request->data['email']);
							echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('EMAIL_CHANGE_SUCCESS')));
						} else {
							echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('EMAIL_NOT_VALIDATE')));
						}
					} else {
						echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('EMAIL_NOT_SAME')));
					}
				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('COMPLETE_ALL_FIELDS')));
				}
			} else {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('NOT_POST')));
			}
		} else {
			echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('NEED_CONNECT')));
		}
	}

	function send_points() {
		$this->autoRender = false;
		if($this->isConnected) {
			if($this->request->is('ajax')) {
				if(!empty($this->request->data['to']) AND !empty($this->request->data['how'])) {
					if($this->User->exist($this->request->data['to'])) {
						$how = intval($this->request->data['how']);
						if($how > 0) {
							$money_user = $this->User->getKey('money') - $how;
							if($money_user >= 0) {
								$this->User->setKey('money', $money_user);
								$to_money = $this->User->getFromUser('money', $this->request->data['to']) + $how;
								$this->User->setToUser('money', $to_money, $this->request->data['to']);
								$this->History->set('SEND_MONEY', 'shop', $this->request->data['to'].'|'.$how);
								echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('POINTS_SUCCESS_SEND')));
							} else {
								echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('NO_ENOUGH_MONEY')));
							}
						} else {
							echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('CANT_SEND_EMPTY_POINTS')));
						}
					} else {
						echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER_NOT_EXIST')));
					}
				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('COMPLETE_ALL_FIELDS')));
				}
			} else {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('NOT_POST')));
			}
		} else {
			echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('NEED_CONNECT')));
		}
	}

	function admin_index() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->set('title_for_layout',$this->Lang->get('USER'));
			$this->layout = 'admin';
			$this->loadModel('User');
			$users = $this->User->find('all');
			$this->set(compact('users'));

			$available_ranks = array(0 => $this->Lang->get('MEMBER'), 2 => $this->Lang->get('MODERATOR'), 3 => $this->Lang->get('ADMINISTRATOR'), 4 => $this->Lang->get('ADMINISTRATOR'), 5 => $this->Lang->get('BANNED'));
			$this->loadModel('Rank');
			$custom_ranks = $this->Rank->find('all');
			foreach ($custom_ranks as $key => $value) {
				$available_ranks[$value['Rank']['rank_id']] = $value['Rank']['name'];
			}
			$this->set(compact('available_ranks'));
		} else {
			$this->redirect('/');
		}
	}

	function admin_edit($id = false) {
		if($this->isConnected AND $this->User->isAdmin()) {
			if($id != false) {

				$this->layout = 'admin';
				$this->set('title_for_layout',$this->Lang->get('EDIT_USER'));
				$this->loadModel('User');
				$find = $this->User->find('all', array('conditions' => array('id' => $id)));
				if(!empty($find)) {
					$user = $find[0]['User'];

					$options_ranks = array('member' => $this->Lang->get('MEMBER'), 2 => $this->Lang->get('MODERATOR'), 3 => $this->Lang->get('ADMINISTRATOR'), 5 => $this->Lang->get('BANNED'));
					$this->loadModel('Rank');
					$custom_ranks = $this->Rank->find('all');
					foreach ($custom_ranks as $key => $value) {
						$options_ranks[$value['Rank']['rank_id']] = $value['Rank']['name'];
					}

					foreach ($options_ranks as $k => $v) {
						if($user['rank'] == $k OR $k == "member" && $user['rank'] == 0) {
							$user['rank'] = $v;
							unset($options_ranks[$k]);
						}
					}

					$this->set(compact('options_ranks'));

					$this->set(compact('user'));
				} else {
					$this->Session->setFlash($this->Lang->get('UNKNOWN_ID'), 'default.error');
					$this->redirect(array('controller' => 'user', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'user', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	function admin_edit_ajax() {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {
			if($this->request->is('post')) {
				$this->loadModel('User');
				if(!empty($this->request->data['pseudo']) AND !empty($this->request->data['email'])) {
					if(empty($this->request->data['rank'])) {
						$rank = $this->User->find('all', array('conditions' => array('pseudo' => $this->request->data['pseudo'])));
						$this->request->data['rank'] = $rank[0]['User']['rank'];
					}
					if(empty($this->request->data['password'])) {
						$password = $this->User->find('all', array('conditions' => array('pseudo' => $this->request->data['pseudo'])));
						$this->request->data['password'] = $password[0]['User']['password'];
					} else {
						$this->request->data['password'] = password($this->request->data['password']);
					}
					foreach ($this->request->data as $key => $value) {
						if($key == "rank" AND $value == "member") {
							$value = 0;
						}
						$this->User->setToUser($key, $value, $this->request->data['pseudo']);
					}
					$this->History->set('EDIT_USER', 'user');
					$this->Session->setFlash($this->Lang->get('USER_SUCCESS_EDIT'), 'default.success');
					echo $this->Lang->get('USER_SUCCESS_EDIT').'|true';
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

	function admin_delete($id = false) {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {
			if($id != false) {
				$this->loadModel('User');
				$find = $this->User->find('all', array('conditions' => array('id' => $id)));
				if(!empty($find)) {
					$this->User->delete($id);
					$this->History->set('DELETE_USER', 'user');
					$this->Session->setFlash($this->Lang->get('DELETE_USER_SUCCESS'), 'default.success');
					$this->redirect(array('controller' => 'user', 'action' => 'index', 'admin' => true));
				} else {
					$this->Session->setFlash($this->Lang->get('UNKNONW_ID'), 'default.error');
					$this->redirect(array('controller' => 'user', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'user', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

}
