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
		 
		$this->layout = null;
		if($this->request->is('Post')) { // si la requête est bien un post
			if(!empty($this->request->data['pseudo']) && !empty($this->request->data['password']) && !empty($this->request->data['password_confirmation']) && !empty($this->request->data['email']) && !empty($this->request->data['captcha'])) { // si tout les champs sont bien remplis
				$this->request->data['password'] = password($this->request->data['password']);
				$this->request->data['password_confirmation'] =password($this->request->data['password_confirmation']);
				if($this->request->data['password'] == $this->request->data['password_confirmation']) {
					if(filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL)) {
						$search_member_by_pseudo = $this->User->find('all', array('conditions' => array('pseudo' => $this->request->data['pseudo'])));
						$search_member_by_email = $this->User->find('all', array('conditions' => array('email' => $this->request->data['email'])));
						if(empty($search_member_by_pseudo)) {
							if(empty($search_member_by_email)) {
								$captcha = $this->Session->read('captcha_code');
								if($captcha == $this->request->data['captcha']) {

									$this->getEventManager()->dispatch(new CakeEvent('onRegister', $this, $this->request->data));

									$this->request->data['pseudo'] = before_display($this->request->data['pseudo']);
									$this->request->data['email'] = before_display($this->request->data['email']);
									$session = md5(rand());
									$this->request->data['session'] = $session;
									$this->Session->write('user', $session);
									$this->request->data['ip'] = $_SERVER["REMOTE_ADDR"];
									$this->request->data['rank'] = 0;
									$this->User->set($this->request->data);
									$this->User->save();
									echo 'true';
								} else {
									echo $this->Lang->get('INVALID_CAPTCHA');
								}
							} else {
								echo $this->Lang->get('EMAIL_ALREADY_EXIST');
							}
						} else {
							echo $this->Lang->get('PSEUDO_ALREADY_EXIST');
						}
					} else {
						echo $this->Lang->get('EMAIL_NOT_VALIDATE');
					}
				} else {
					echo $this->Lang->get('PASSWORD_NOT_SAME');
				}
			} else {
				echo $this->Lang->get('COMPLETE_ALL_FIELDS');
			}
		} else {
			echo $this->Lang->get('PAGE_BAD_EXECUTED');
		}
	}

	function ajax_login() {
		 
		$this->layout=null;
		if($this->request->is('Post')) {
			if(!empty($this->request->data['pseudo']) && !empty($this->request->data['password'])) {
				$search_user = $this->User->find('all', array('conditions' => array('pseudo' => $this->request->data['pseudo'], 'password' => password($this->request->data['password']))));
				if(!empty($search_user)) {

					$this->getEventManager()->dispatch(new CakeEvent('onLogin', $this, $this->request->data));

					$session = md5(rand());
					$this->User->read(null, $search_user['0']['User']['id']);
					$this->User->set(array('session' => $session));
					$this->User->save();
					$this->Session->write('user', $session);
					echo 'true';
				} else {
					echo $this->Lang->get('BAD_PSEUDO_OR_PASSWORD');
				}
			} else {
				echo $this->Lang->get('COMPLETE_ALL_FIELDS');
			}
		} else {
			echo $this->Lang->get('PAGE_BAD_EXECUTED');
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
						$headers = 'From: '.$this->Configuration->get('name').' <'.$this->Configuration->get('email').'>' . "\r\n" .
     						'Reply-To: '.$this->Configuration->get('email').'' . "\r\n" .
     						'X-Mailer: PHP/' . phpversion();
						if(mail($to, $subject, $message, $headers)) {
							$this->Lostpassword->create();
							$this->Lostpassword->set(array(
								'email' => $this->request->data['email'],
								'key' => $key
							));
							$this->Lostpassword->save();
							echo 'true';
						} else {
							echo $this->Lang->get('INTERNAL_ERROR');
						}
					} else {
						echo $this->Lang->get('UNKNONWN_USER');
					}
				} else {
					echo $this->Lang->get('EMAIL_NOT_VALIDATE');
				}
			} else {
				echo $this->Lang->get('COMPLETE_ALL_FIELDS');
			}
		} else {
			echo $this->Lang->get('PAGE_BAD_EXECUTED');
		}
	}

	function ajax_resetpasswd() {
		$this->autoRender = false;
		if($this->request->is('ajax')) {
			if(!empty($this->request->data['password']) AND !empty($this->request->data['password2']) AND !empty($this->request->data['email'])) {
				$this->request->data['password'] = password($this->request->data['password']);
				$this->request->data['password2'] =password($this->request->data['password2']);
				if($this->request->data['password'] == $this->request->data['password2']) {
					unset($this->request->data['password2']);
					$this->loadModel('User');
					$search = $this->User->find('all', array('conditions' => array('email' => $this->request->data['email'])));
					if(!empty($search)) {

						$this->loadModel('Lostpassword');
						$Lostpassword = $this->Lostpassword->find('all', array('conditions' => array('email' => $this->request->data['email'])));
						if(!empty($Lostpassword)) {

							$this->Lostpassword->delete($Lostpassword[0]['Lostpassword']['id']);

							$session = md5(rand());
							$this->request->data['session'] = $session; // on connecte l'utilisateur
							$this->Session->write('user', $session);

							$this->User->read(null, $search['0']['User']['id']);
							$this->User->set($this->request->data);
							$this->User->save();

							$this->History->set('RESET_PASSWORD', 'user');

							echo 'true';

						} else {
							echo $this->Lang->get('INVALID_KEY_FOR_RESET');
						}
					} else {
						echo $this->Lang->get('INTERNAL_ERROR');
					}
				} else {
					echo $this->Lang->get('PASSWORD_NOT_SAME');
				}
			} else {
				echo $this->Lang->get('COMPLETE_ALL_FIELDS');
			}
		} else {
			echo $this->Lang->get('PAGE_BAD_EXECUTED');
		}
	}

	function logout() {
		$this->layout = null;
		$this->Session->delete('user');
     	$this->redirect($this->referer());
	}

	function profile() {
		if($this->Connect->connect()) {
			 
			$this->set('title_for_layout', $this->Connect->get_pseudo());
			$this->layout= $this->Configuration->get_layout();
			if($this->EyPlugin->is_installed('Shop')) {

				$this->set('shop_active', true);

				$this->loadModel('PaysafecardMessage');
				$search_psc_msg = $this->PaysafecardMessage->find('all', array('conditions' => array('to' => $this->Connect->get_pseudo())));
				if(!empty($search_psc_msg)) {
					$this->set(compact('search_psc_msg'));
					$this->PaysafecardMessage->deleteAll(array('to' => $this->Connect->get_pseudo()));
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

			$api = $this->API->getIp($this->Connect->get('pseudo'));
			$this->set(compact('api'));

			$this->set('can_cape', $this->API->can_cape());
			$this->set('can_skin', $this->API->can_skin());

			$skin_max_size = 10000000; // en octet
			$this->set(compact('skin_max_size'));
			$cape_max_size = 10000000;
			$this->set(compact('cape_max_size'));

			/* SKIN & CAPE */

			if($this->request->is('post')) {
				/* SKIN */
				if(!empty($this->request->data['skin_form']) AND $this->API->can_skin()) {

					$this->loadModel('ApiConfiguration');
					$ApiConfiguration = $this->ApiConfiguration->find('first');
					$target_config = $ApiConfiguration['ApiConfiguration']['skin_filename'];
					$filename = substr($target_config, (strrpos($target_config, '/') + 1));
					$target = substr($target_config, 0, (strrpos($target_config, '/') + 1));
			        $target = WWW_ROOT.'/'.$target;
			        $max_size = $skin_max_size; // en octet
			        $width_max = 64; // pixel
			        $height_max = 32; // pixel
		               
		            $extensions = array('png');    // Extensions autorisees
		               
			        if(!is_dir($target)) {
						if(!mkdir($target, 0755)) {
			            	$this->Session->setFlash($this->Lang->get('INTERNAL_ERROR').' Code : 1');
			        	}
			        }

	                $extension  = pathinfo($_FILES['skin']['name'], PATHINFO_EXTENSION);
	                if(in_array(strtolower($extension),$extensions)) {
	                  	$infosImg = getimagesize($_FILES['skin']['tmp_name']);
	                  	if($infosImg[2] >= 1 && $infosImg[2] <= 14) {
	                  		if(($infosImg[0] <= $width_max) && ($infosImg[1] <= $height_max) && (filesize($_FILES['skin']['tmp_name']) <= $max_size)) {
	                    		if(isset($_FILES['skin']['error']) && UPLOAD_ERR_OK === $_FILES['skin']['error']) {
	                    			$filename = str_replace('{PLAYER}', $this->Connect->get_pseudo(), $filename);
	                    			$filename = str_replace('php', '', $filename);
	                    			$filename = str_replace('.', '', $filename);
	                    			$filename = $filename.'.png';
	                    			if(move_uploaded_file($_FILES['skin']['tmp_name'], $target.$filename)) {
	                      					$this->Session->setFlash($this->Lang->get('SKIN_SUCCESS_UPLOAD'), 'default.success');
	                    			} else {
	                      					$this->Session->setFlash($this->Lang->get('INTERNAL_ERROR').' Code : 2', 'default.error');
	                    			}
	                    		} else {
	                      			$this->Session->setFlash($this->Lang->get('INTERNAL_ERROR').' Code : 3', 'default.error');
	                    		}
	                  		} else {
	                    		$this->Session->setFlash($this->Lang->get('DIMENSION_OVERSIZE'), 'default.error');
	                  		}
	                  	} else {
	                  		$this->Session->setFlash($this->Lang->get('INVALID_IMG'), 'default.error');
	                  	}
	                } else {
	                  $this->Session->setFlash($this->Lang->get('INVALID_EXTENSION'), 'default.error');
	                }
				}
				/* CAPE */
				if(!empty($this->request->data['cape_form']) AND $this->API->can_cape()) {
											$this->loadModel('ApiConfiguration');
					$ApiConfiguration = $this->ApiConfiguration->find('first');
					$target_config = $ApiConfiguration['ApiConfiguration']['cape_filename'];
					$filename = substr($target_config, (strrpos($target_config, '/') + 1));
					$target = substr($target_config, 0, (strrpos($target_config, '/') + 1));
			        $target = WWW_ROOT.'/'.$target;
			        $max_size = $cape_max_size; // en octet
			        $width_max = 64; // pixel
			        $height_max = 32; // pixel
		               
		            $extensions = array('png');    // Extensions autorisees
		               
			        if(!is_dir($target)) {
						if(!mkdir($target, 0755)) {
			            	$this->Session->setFlash($this->Lang->get('INTERNAL_ERROR').' Code : 1');
			        	}
			        }

	                $extension  = pathinfo($_FILES['cape']['name'], PATHINFO_EXTENSION);
	                if(in_array(strtolower($extension),$extensions)) {
	                  	$infosImg = getimagesize($_FILES['cape']['tmp_name']);
	                  	if($infosImg[2] >= 1 && $infosImg[2] <= 14) {
	                  		if(($infosImg[0] <= $width_max) && ($infosImg[1] <= $height_max) && (filesize($_FILES['cape']['tmp_name']) <= $max_size)) {
	                    		if(isset($_FILES['cape']['error']) && UPLOAD_ERR_OK === $_FILES['cape']['error']) {
	                    			$filename = str_replace('{PLAYER}', $this->Connect->get_pseudo(), $filename);
	                    			$filename = str_replace('php', '', $filename);
	                    			$filename = str_replace('.', '', $filename);
	                    			$filename = $filename.'.png';
	                    			if(move_uploaded_file($_FILES['cape']['tmp_name'], $target.$filename)) {
	                      					$this->Session->setFlash($this->Lang->get('CAPE_SUCCESS_UPLOAD'), 'default.success');
	                    			} else {
	                      					$this->Session->setFlash($this->Lang->get('INTERNAL_ERROR').' Code : 2', 'default.error');
	                    			}
	                    		} else {
	                      			$this->Session->setFlash($this->Lang->get('INTERNAL_ERROR').' Code : 3', 'default.error');
	                    		}
	                  		} else {
	                    		$this->Session->setFlash($this->Lang->get('DIMENSION_OVERSIZE'), 'default.error');
	                  		}
	                  	} else {
	                  		$this->Session->setFlash($this->Lang->get('INVALID_IMG'), 'default.error');
	                  	}
	                } else {
	                  $this->Session->setFlash($this->Lang->get('INVALID_EXTENSION'), 'default.error');
	                }
				}
			}
		} else {
			$this->redirect(array('controller' => 'pages', 'action' => 'home'));
		}
	}

	function change_pw() {
		if($this->Connect->connect()) {
			 
			$this->layout = null;
			if($this->request->is('ajax')) {
				if(!empty($this->request->data['password']) AND !empty($this->request->data['password_confirmation'])) {
					$password = password($this->request->data['password']);
					$password_confirmation = password($this->request->data['password_confirmation']);
					if($password == $password_confirmation) {
						$this->Connect->set('password', $password);
						echo $this->Lang->get('PASSWORD_CHANGE_SUCCESS').'|true';
					} else {
						echo $this->Lang->get('PASSWORD_NOT_SAME').'|false';
					}
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST').'|false';
			}
		} else {
			$this->redirect(array('controller' => 'pages', 'action' => 'home'));
		}
	}

	function change_email() {
		if($this->Connect->connect()) {
			 
			$this->layout = null;
			if($this->request->is('ajax')) {
				if(!empty($this->request->data['email']) AND !empty($this->request->data['email_confirmation'])) {
					if($this->request->data['email'] == $this->request->data['email_confirmation']) {
						if(filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL)) {
							$this->Connect->set('email', $this->request->data['email']);
							echo $this->Lang->get('EMAIL_CHANGE_SUCCESS').'|true';
						} else {
							echo $this->Lang->get('EMAIL_NOT_VALIDATE').'|false';
						}
					} else {
						echo $this->Lang->get('EMAIL_NOT_SAME').'|false';
					}
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST').'|false';
			}
		} else {
			$this->redirect(array('controller' => 'pages', 'action' => 'home'));
		}
	}

	function send_points() {
		if($this->Connect->connect()) {
			 
			$this->layout = null;
			if($this->request->is('ajax')) {
				if(!empty($this->request->data['to']) AND !empty($this->request->data['how'])) {
					if($this->Connect->user_exist($this->request->data['to'])) {
						$how = intval($this->request->data['how']);
						if($how > 0) {
							$money_user = $this->Connect->get('money') - $how;
							if($money_user >= 0) {
								$this->Connect->set('money', $money_user);
								$to_money = $this->Connect->get_to_user('money', $this->request->data['to']) + $how;
								$this->Connect->set_to_user('money', $to_money, $this->request->data['to']);
								$this->History->set('SEND_MONEY', 'shop', $this->request->data['to'].'|'.$how);
								echo $this->Lang->get('POINTS_SUCCESS_SEND').'|true';
							} else {
								echo $this->Lang->get('NO_ENOUGH_MONEY').'|false';
							}
						} else {
							echo $this->Lang->get('CANT_SEND_EMPTY_POINTS').'|false';
						}
					} else {
						echo $this->Lang->get('USER_NOT_EXIST').'|false';
					}
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST').'|false';
			}
		} else {
			$this->redirect(array('controller' => 'pages', 'action' => 'home'));
		}
	}

	function modify_profile() {
		 
		$this->layout= $this->Configuration->get_layout();
	}

	function admin_index() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
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
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
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

	function admin_edit_ajax() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->layout = null;
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
					}
					foreach ($this->request->data as $key => $value) {
						if($key == "rank" AND $value == "member") {
							$value = 0;
						}
						$this->Connect->set_to_user($key, $value, $this->request->data['pseudo']);
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
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			if($id != false) {
				 
				$this->layout = null;
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