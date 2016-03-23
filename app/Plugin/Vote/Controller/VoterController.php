<?php
class VoterController extends VoteAppController {

    public function index() {
        $this->loadModel('Vote.VoteConfiguration');
        $search = $this->VoteConfiguration->find('all');
        if(!empty($search)) {

            $this->set('websites', unserialize($search[0]['VoteConfiguration']['websites']));

            $rewards = $search[0]['VoteConfiguration']['rewards'];
            $rewards = unserialize($rewards);
            $this->set(compact('rewards'));

            $this->loadModel('User');
            $ranking = $this->User->find('all', array('limit' => '10', 'order' => 'vote desc'));
            $this->set(compact('ranking'));
        } else {
            throw new NotFoundException();
        }
    }

    public function setWebsite() {
        $this->autoRender = false;
        if($this->request->is('ajax')) {
            if(!empty($this->request->data['website']) OR $this->request->data['website'] == 0) {
                $this->Session->write('vote.website', $this->request->data['website']);

                $this->loadModel('Vote.VoteConfiguration');
                $config = $this->VoteConfiguration->find('first');
                $websites = unserialize($config['VoteConfiguration']['websites']);

                if($websites[$this->request->data['website']]['website_type'] == 'rpg') {
                    echo json_encode(array('page' => 'http://rpg-paradize.com/?page=vote&vote='.@$websites[$this->request->data['website']]['rpg_id'], 'website_type' => @$websites[$this->request->data['website']]['website_type']));
                } else {
                    echo json_encode(array('page' => @$websites[$this->request->data['website']]['page_vote'], 'website_type' => @$websites[$this->request->data['website']]['website_type']));
                }
            }
        }
    }

    public function setPseudo() {
        $this->autoRender = false;
        if($this->request->is('ajax')) {
            $this->loadModel('User');
            $user_rank = $this->User->find('first', array('conditions' => array('pseudo' => $this->request->data['pseudo'])));
            if(!empty($user_rank) && $this->Permissions->have($user_rank['User']['rank'], 'VOTE') == "true") {
                if(!empty($this->request->data['pseudo'])) {
                    if($this->User->exist($this->request->data['pseudo'])) {

											$user_id = $this->User->getFromUser('id', $this->request->data['pseudo']);

                        $this->loadModel('Vote.Vote');
                        $get_last_vote = $this->Vote->find('first',
													array('conditions' =>
														array(
															'OR' => array(
																	'user_id' => $user_id,
																	'ip' => $this->Util->getIP()
																),
															'website' => $this->Session->read('vote.website')
															)
														)
													);

                        if(!empty($get_last_vote['Vote']['created'])) {
                            $now = time();
                            $last_vote = ($now - strtotime($get_last_vote['Vote']['created']))/60;
                        } else {
                            $last_vote = null;
                        }

                        $this->loadModel('Vote.VoteConfiguration');
                        $config = $this->VoteConfiguration->find('first');


                        $websites = unserialize($config['VoteConfiguration']['websites']);
                        if(isset($websites[$this->Session->read('vote.website')])) {
                            $time_vote = $websites[$this->Session->read('vote.website')]['time_vote'];

                            if(empty($last_vote) OR $last_vote > $time_vote) {

                                $this->Session->write('vote.pseudo', $this->request->data['pseudo']);
                                echo json_encode(array('statut' => true , 'msg' => $this->Lang->get('VOTE__STEP_1_SUCCESS')));

                            } else {

                              $calcul_wait_time = ($time_vote - $last_vote)*60;
                              $calcul_wait_time = $this->Util->secondsToTime($calcul_wait_time); //On le sort en jolie

                              $wait_time = array();
                              if($calcul_wait_time['d'] > 0) {
                                $wait_time[] = $calcul_wait_time['d'].' '.$this->Lang->get('GLOBAL__DATE_R_DAYS');
                              }
                              if($calcul_wait_time['h'] > 0) {
                                $wait_time[] = $calcul_wait_time['h'].' '.$this->Lang->get('GLOBAL__DATE_R_HOURS');
                              }
                              if($calcul_wait_time['m'] > 0) {
                                $wait_time[] = $calcul_wait_time['m'].' '.$this->Lang->get('GLOBAL__DATE_R_MINUTES');
                              }
                              if($calcul_wait_time['s'] > 0) {
                                $wait_time[] = $calcul_wait_time['s'].' '.$this->Lang->get('GLOBAL__DATE_R_SECONDS');
                              }

                              $wait_time = implode(', ', $wait_time);

                              echo json_encode(array('statut' => false , 'msg' => $this->Lang->get('VOTE__VOTE_ERROR_WAIT', array('{WAIT_TIME}' => $wait_time))));
                            }
                        } else {
                            echo json_encode(array('statut' => false , 'msg' => $this->Lang->get('VOTE__VOTE_ERROR_UNKNOWN_WEBSITE')));
                        }
                    } else {
                        echo json_encode(array('statut' => false , 'msg' =>$this->Lang->get('VOTE__VOTE_ERROR_USER_UNKNOWN')));
                    }
                }
            } else {
                echo json_encode(array('statut' => false , 'msg' =>$this->Lang->get('VOTE__VOTE_ERROR_USER_UNKNOWN')));
            }
        } else {
            throw new InternalErrorException();
        }
    }

    public function checkOut() {
      $this->autoRender = false;
      if($this->request->is('ajax')) {
        if(!empty($this->request->data['out']) && $this->Session->check('vote.website') && $this->Session->check('vote.pseudo')) {

          $this->loadModel('Vote.VoteConfiguration');
          $config = $this->VoteConfiguration->find('first');
          $websites = unserialize($config['VoteConfiguration']['websites']);
          $url = $websites[$this->Session->read('vote.website')]['page_vote'];
          // exemple : http://rpg-paradize.com/site-+FR+++RESET++ObsiFight+Serveur+PvP+Faction+2424+1.8-44835

          $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1'; // simule Firefox 4.
            $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
            $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
            $header[] = "Cache-Control: max-age=0";
            $header[] = "Connection: keep-alive";
            $header[] = "Keep-Alive: 300";
            $header[] = "Accept-Charset: utf-8";
            $header[] = "Accept-Language: fr"; // langue fr.
            $header[] = "Pragma: "; // Simule un navigateur

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url); // l'url visité
          curl_setopt($ch, CURLOPT_FAILONERROR, 1);// Gestion d'erreur
          //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // autorise la redirection
          curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // stock la response dans une variable
          curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
          curl_setopt($ch, CURLOPT_PORT, 80); // set port 80
          curl_setopt($ch, CURLOPT_TIMEOUT, 15); //  timeout curl à 15 secondes.

          curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
          $result=curl_exec($ch);

          $str = substr($result, strpos($result, 'Clic Sortant'), 20);
          $out = filter_var($str, FILTER_SANITIZE_NUMBER_INT);
          $array = array($out, $out-1, $out-2, $out-3, $out+1, $out+2, $out+3);

          if(in_array($this->request->data['out'], $array)) {

            $this->Session->write('vote.out', true);
            echo json_encode(array('statut' => true, 'msg' =>$this->Lang->get('VOTE__STEP_3_SUCCESS')));

          } else {
            echo json_encode(array('statut' => false, 'msg' =>$this->Lang->get('VOTE__STEP_3_ERROR')));
          }
        }
      } else {
          throw new InternalErrorException();
      }
    }

    public function getRewards() {
        $when = (isset($this->request->data['when'])) ? $this->request->data['when'] : 'now';
        $this->autoRender = false;
        $this->response->type('json');
        if($this->request->is('ajax')) {
            if($this->Session->check('vote.website') && $this->Session->check('vote.pseudo')) {
                $this->loadModel('Vote.VoteConfiguration');
                $config = $this->VoteConfiguration->find('first');
                $websites = unserialize($config['VoteConfiguration']['websites']);
                $website = $websites[$this->Session->read('vote.website')];
                if($this->Session->check('vote.out') || $website['website_type'] == "other") {

                    // check si il a pas déjà voté sur ce site
                    $this->loadModel('Vote.Vote');
										$user_id = $this->User->getFromUser('id', $this->Session->read('vote.pseudo'));
                    $get_last_vote = $this->Vote->find('first', array('conditions' => array('OR' => array('user_id' => $user_id, 'ip' => $this->Util->getIP()), 'website' => $this->Session->read('vote.website'))));

                    if(!empty($get_last_vote['Vote']['created'])) {
                        $now = time();
                        $last_vote = ($now - strtotime($get_last_vote['Vote']['created']))/60;
                    } else {
                        $last_vote = null;
                    }

                    $this->loadModel('Vote.VoteConfiguration');
                    $config = $this->VoteConfiguration->find('first');
                    $websites = unserialize($config['VoteConfiguration']['websites']);
                    if(isset($websites[$this->Session->read('vote.website')])) {
                        $time_vote = $websites[$this->Session->read('vote.website')]['time_vote'];

                        if(empty($last_vote) OR $last_vote > $time_vote) {

                            // on incrémente le vote
                            if(empty($get_last_vote)) {
                                $this->Vote->read(null, null);
                                $this->Vote->set(array(
                                    'user_id' => $user_id,
                                    'ip' => $this->Util->getIP(),
                                    'website' => $this->Session->read('vote.website')
                                ));
                                $this->Vote->save();
                            } else {
                                $this->Vote->read(null, $get_last_vote['Vote']['id']);
                                $this->Vote->set(array(
                                    'user_id' => $user_id,
                                    'ip' => $this->Util->getIP(),
                                    'website' => $this->Session->read('vote.website'),
                                    'created' => date('Y-m-d H:i:s')
                                ));
                                $this->Vote->save();
                            }
                            $this->loadModel('User');
                            $userData = $this->User->find('first', array('conditions' => array('pseudo' => $this->Session->read('vote.pseudo'))));
                            $vote_nbr = $userData['User']['vote'] + 1;
                            $this->User->read(null, $userData['User']['id']);
                            $data = array(
                                'vote' => $vote_nbr
                            );

                            if($when != 'now') {
                              $data['rewards_waited'] = intval($userData['User']['rewards_waited']) + 1;
                            }

                            $this->User->set($data);

                            // on cast l'event
                            $event = new CakeEvent('onVote', $this, array('when' => $when, 'website' => $this->Session->read('vote.website'), 'config' => $config['VoteConfiguration'], 'user' => $userData));
                  					$this->getEventManager()->dispatch($event);
                  					if($event->isStopped()) {
                  						return $event->result;
                  					}


                            if($when == 'now') { // si c'est maintenant

															$rewardStatus = $this->processRewards($config['VoteConfiguration'], $userData['User']);

															if(!$rewardStatus['status']) {
																echo json_encode(array('statut' => false, 'msg' => $this->Lang->get($rewardStatus['msg'])));
                                return;
															}
                              $this->User->save(); // on sauvegarde le vote
															echo json_encode(array('statut' => true, 'msg' => $rewardStatus['msg']));

                            } else { // si c'est plus tard
                              $this->User->save(); // on sauvegarde le vote
                              echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('VOTE__STEP_4_REWARD_SUCCESS_SAVE')));
                            }

                            $this->Session->delete('vote');

                        } else {
                          echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('VOTE__VOTE_ERROR_WAIT')));

                          $this->Session->delete('vote');
                        }
                    } else {
                        echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('VOTE__VOTE_ERROR_WAIT')));
                    }

                } else {
                    echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('VOTE__STEP_3_ERROR')));
                }
            } else {
                echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('VOTE__VOTE_ERROR_USER_UNKNOWN')));
            }
        } else {
            throw new InternalErrorException();
        }
    }

		private function processRewards($config, $user) { // Donne les récompenses au user passé selon la configuration donnée

			/* ====
					Toutes les récompenses
			 	 ==== */
			if($config['rewards_type'] == 1) { // toutes les récompenses

				$rewards = unserialize($config['VoteConfiguration']['rewards']); // on récupére la liste

        $event = new CakeEvent('beforeReceiveRewards', $this, array('rewards' => $id, 'type' => 'all', 'user' => $user));
        $this->getEventManager()->dispatch($event);
        if($event->isStopped()) {
          return $event->result;
        }

				foreach ($rewards as $key => $value) { // on parcoure les récompenses

					if($value['type'] == 'server') { // si c'est une commande serveur

						$server = $this->executeServerReward($config, $user, $value);
						if($server) {

							$rewardsSended[] = $value['name'];

						} else {
							return array('status' => false, 'msg' => $server);
						}

					} elseif($value['type'] == 'money') { // si c'est des points boutique

						$money = intval($user['money']) + intval($value['how']);
						$this->loadModel('User');
						$this->User->setToUser('money', $money, $user['pseudo']);

						$rewardsSended[] = $value['how'].' '.$this->Configuration->getMoneyName();

					} else {
						return array('status' => false, 'msg' => 'VOTE__UNKNOWN_REWARD_TYPE');
					}

				}

				$return = $this->Lang->get('VOTE__VOTE_SUCCESS').' ! ';
			 	if(!empty($success_msg)) {
					$return .= $this->Lang->get('VOTE__REWARDS_TITLE').' : ';
					$return .= '<b>'.implode('</b>, <b>', $rewardsSended).'</b>.';
			 }

			 return array('status' =>true, 'msg' => $return);

		} else { // récompenses aléatoire selon probabilité

			$rewards = unserialize($config['rewards']); // on récupère la liste des récompenses
			$probability_all = 0; // on met la probabilité totale à 0 de base
		 	foreach ($rewards as $key => $value) {
				$probability_all = $probability_all + $value['proba'];   // Puis on la calcule
				$rewards_rand[$key] = $value['proba'];
		 	}

			$reward = $this->Util->random($rewards_rand, $probability_all); // on récupère la reward tiré au sort

      $event = new CakeEvent('beforeReceiveRewards', $this, array('rewards' => $reward, 'type' => 'random', 'user' => $user));
      $this->getEventManager()->dispatch($event);
      if($event->isStopped()) {
        return $event->result;
      }

			if($rewards[$reward]['type'] == 'server') { // si c'est une commande serveur

				$server = $this->executeServerReward($config, $user, $rewards[$reward]);
				if($server) {

					return array('status' => true, 'msg' => $this->Lang->get('VOTE__VOTE_SUCCESS').' ! '.$this->Lang->get('VOTE__MESSAGE_VOTE_SUCCESS_REWARD').' : <b>'.$rewards[$reward]['name'].'</b>.');

				} else {
					return array('status' => false, 'msg' => $server);
				}

			 } elseif($rewards[$reward]['type'] == 'money') { // si c'est des points boutique

					 $money = intval($user['money']) + intval($rewards[$reward]['how']);
					 $this->loadModel('User');
					 $this->User->setToUser('money', $money, $user['pseudo']);

					 return array('status' => true, 'msg' =>$this->Lang->get('VOTE__VOTE_SUCCESS').' ! '.$this->Lang->get('VOTE__REWARDS_TITLE').' : <b>'.$rewards[$reward]['how'].' '.$this->Configuration->getMoneyName().'</b>.');

			 } else {
					 return array('status' => false, 'msg' => 'ERROR__INTERNAL_ERROR');
			 }

	 		}

		}

		private function executeServerReward($config, $user, $reward) { // execute la commande d'une récompense si les serveurs de la config sont ouverts
			$config['servers'] = unserialize($config['servers']); // on récupére la liste des serveurs configurés
			if(!empty($config['servers'])) { // si la liste n'est pas vide
				foreach ($config['servers'] as $k => $v) { //on parcours les serveurs pour voir si ils sont tous allumés
					$servers_online[] = $this->Server->online($v);
				}
			} else { // sinon on demande au component de prendre celui par défaut, le premier de ceux liés
				$servers_online = array($this->Server->online());
			}
			if(!in_array(false, $servers_online)) { // si tous les serveurs sont allumés

        $cmd = $reward['command'];
        $cmd = str_replace('{PLAYER}', $user['pseudo'], $cmd);
        $cmd = str_replace('{PROBA}', $reward['proba'], $cmd);
        $cmd = str_replace('{REWARD}', $reward['name'], $cmd);

				if(empty($config['servers'])) { // si on a pas de liste, on prend celui par défaut

          if($reward['need_connect_on_server'] == "true") {
            $call = $this->Server->call(array('isConnected' => $user['pseudo']), true, false);
            if($call['isConnected'] != 'true') {
              return 'VOTE__ERROR_NEED_CONNECT_ON_SERVER';
            }
          }

					$this->Server->commands($cmd); // on envoie la commande puis enregistre le vote
				} else {
					foreach ($config['servers'] as $k2 => $v2) { // on parcours les serveurs

            if($reward['need_connect_on_server'] == "true") {
              $call = $this->Server->call(array('isConnected' => $user['pseudo']), true, $v2);
              if($call['isConnected'] == 'true') {
                $continue = true;
                break;
              }
            }

					}
          if($continue) {
            unset($k2);
            unset($v2);
            foreach ($config['servers'] as $k2 => $v2) {
              $this->Server->commands($cmd, $v2); // on envoie la commande puis enregistre le vote
            }
          } else {
            return 'VOTE__ERROR_NEED_CONNECT_ON_SERVER';
          }
				}
			} else { //le serveur est éteint
				return 'SERVER__MUST_BE_ON';
			}
			return true;
		}

    public function get_reward() {
        $this->autoRender = false;
        if($this->isConnected && $this->User->getKey('rewards_waited') > 0) {
            $this->loadModel('Vote.VoteConfiguration');
            $config = $this->VoteConfiguration->find('first');

            $event = new CakeEvent('beforeGetWaitingReward', $this, array('user' => $this->User->getAllFromCurrentUser()));
            $this->getEventManager()->dispatch($event);
            if($event->isStopped()) {
              return $event->result;
            }

						$rewardStatus = $this->processRewards($config['VoteConfiguration'], $this->User->getAllFromCurrentUser());

						if(!$rewardStatus['status']) {
							$this->Session->setFlash($this->Lang->get($rewardStatus['msg']), 'default.error');
	            $this->redirect(array('controller' => 'user', 'action' => 'profile', 'plugin' => false));
						}

						$this->User->setKey('rewards_waited', (intval($this->User->getKey('rewards_waited')) - 1));
						$this->Session->setFlash($rewardStatus['msg'], 'default.success');
            $this->redirect(array('controller' => 'user', 'action' => 'profile', 'plugin' => false));

        } else {
            $this->redirect('/');
        }
    }

    public function admin_index() {
        if($this->isConnected AND $this->User->isAdmin()) {
            $this->layout = "admin";

            $this->loadModel('Vote.VoteConfiguration');
            $vote = $this->VoteConfiguration->find('first');
            if(!empty($vote)) {
                $vote = $vote['VoteConfiguration'];
                $vote['rewards'] = unserialize($vote['rewards']);
                $vote['websites'] = unserialize($vote['websites']);
            } else {
                $vote = array();
            }
            //debug($vote['rewards']);
            $this->set(compact('vote'));

            $this->loadModel('Server');

            $servers = $this->Server->findSelectableServers(true);
						$this->set(compact('servers'));

						$vote['servers'] = (isset($vote['servers'])) ? unserialize($vote['servers']) : array();
            if(!empty($vote['servers'])) {
                $selected_server = array();
                foreach ($vote['servers'] as $key => $value) {
                    if(isset($servers[$value])) {
                        $selected_server[] = $value;
                    }
                }
            } else {
                $selected_server = array();
            }
            $this->set(compact('selected_server'));

            $this->loadModel('User');
            $ranking = $this->User->find('all', array('limit' => '15', 'order' => 'vote desc'));
            $this->set(compact('ranking'));

            $this->set('title_for_layout',$this->Lang->get('VOTE__TITLE'));
        } else {
            $this->redirect('/');
        }
    }

    public function admin_reset() {
			$this->autoRender = false;
        if($this->isConnected AND $this->User->isAdmin()) {

          $event = new CakeEvent('beforeResetVotes', $this, array('user' => $this->User->getAllFromCurrentUser()));
          $this->getEventManager()->dispatch($event);
          if($event->isStopped()) {
            return $event->result;
          }

          $this->loadModel('Vote.Vote');
          $this->Vote->deleteAll(array('1' => '1'));
          $this->loadModel('User');
          $this->User->updateAll(array('vote' => 0));
          $this->History->set('RESET', 'vote');
          $this->Session->setFlash($this->Lang->get('VOTE__RESET_SUCCESS'), 'default.success');
          $this->redirect(array('controller' => 'voter', 'action' => 'index', 'admin' => true));
        }
    }

    public function admin_add_ajax() {
			$this->autoRender = false;
        if($this->isConnected AND $this->User->isAdmin()) {

            if($this->request->is('post')) {
                if(!empty($this->request->data['servers']) AND !empty($this->request->data['website'][0]['page_vote']) AND !empty($this->request->data['website'][0]['time_vote']) AND !empty($this->request->data['website'][0]['website_type']) AND $this->request->data['rewards_type'] == '0' OR $this->request->data['rewards_type'] == '1') {
                    if(!empty($this->request->data['rewards'][0]['name']) && $this->request->data['rewards'][0]['name'] != "undefined" && !empty($this->request->data['rewards'][0]['type']) && $this->request->data['rewards'][0]['type'] != "undefined") {
                        $this->loadModel('Vote.VoteConfiguration');
                        /*
                        REWARDS -> serialize();

                        Structure = array(
                            array(
                                'type' => money/server
                                - 'how' => 10 - pour la money
                                - 'command' => say e - pour le server
                            )

                        )

                        */

                        if($this->request->data['rewards_type'] == 0) {
                            foreach ($this->request->data['rewards'] as $key => $value) {
                                if(!isset($value['proba']) || empty($value['proba'])) {
                                    echo $this->Lang->get('ERROR__FILL_ALL_FIELDS').'|false';
                                    die();
                                }
                            }
                        }

                        $rewards = serialize($this->request->data['rewards']);

                        $vote = $this->VoteConfiguration->find('first');
                        if(!empty($vote)) {
                            $this->VoteConfiguration->read(null, 1);
                        } else {
                            $this->VoteConfiguration->create();
                        }
                        $this->VoteConfiguration->set(array(
                            'rewards_type' => $this->request->data['rewards_type'],
                            'rewards' => $rewards,
                            'websites' => serialize($this->request->data['website']),
                            'servers' => serialize($this->request->data['servers'])
                        ));
                        $this->VoteConfiguration->save();
                        $this->History->set('EDIT_CONFIG', 'vote');
                        $this->Session->setFlash($this->Lang->get('VOTE__CONFIGURATION_SUCCESS'), 'default.success');
                        echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('VOTE__CONFIGURATION_SUCCESS')));
                    } else {
                        echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')));
                    }
                } else {
                    echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')));
                }
            } else {
                echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST')));
            }
        } else {
            throw new ForbiddenException();
        }
    }
}
