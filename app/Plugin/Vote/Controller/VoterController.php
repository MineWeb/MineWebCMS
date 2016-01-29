<?php
class VoterController extends VoteAppController {

	public $components = array('Configuration', 'Configuration');

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
                        $this->loadModel('Vote.Vote');
                        $get_last_vote = $this->Vote->find('first', array('conditions' => array('OR' => array('username' => $this->request->data['pseudo'], 'ip' => $_SERVER['REMOTE_ADDR']), 'website' => $this->Session->read('vote.website'))));

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
                                echo $this->Lang->get('VOTE_LOGIN_SUCCESS').'|true';

                            } else {
                                echo $this->Lang->get('ALREADY_VOTED').'|false';
                            }
                        } else {
                            echo $this->Lang->get('ALREADY_VOTED').'|false';
                        }
                    } else {
                        echo $this->Lang->get('UNKNOWN_USERNAME').'|false';
                    }
                }
            } else {
                echo $this->Lang->get('UNKNOWN_USERNAME').'|false';
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
                    echo $this->Lang->get('OUT_SUCCESS').'|true';

                } else {
                    echo $this->Lang->get('OUT_INVALID').'|false';
                }

            }
        } else {
            throw new InternalErrorException();
        }
    }

    public function getRewards() {
        $when = (isset($this->request->data['when'])) ? $this->request->data['when'] : 'now';
        $this->autoRender = false;
        if($this->request->is('ajax')) {
            if($this->Session->check('vote.website') && $this->Session->check('vote.pseudo')) {
                $this->loadModel('Vote.VoteConfiguration');
                $config = $this->VoteConfiguration->find('first');
                $websites = unserialize($config['VoteConfiguration']['websites']);
                $website = $websites[$this->Session->read('vote.website')];
                if($this->Session->check('vote.out') || $website['website_type'] == "other") {

                    // check si il a pas déjà voté sur ce site
                    $this->loadModel('Vote.Vote');
                    $get_last_vote = $this->Vote->find('first', array('conditions' => array('OR' => array('username' => $this->Session->read('vote.pseudo'), 'ip' => $_SERVER['REMOTE_ADDR']), 'website' => $this->Session->read('vote.website'))));

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
                                    'username' => $this->Session->read('vote.pseudo'),
                                    'ip' => $_SERVER['REMOTE_ADDR'],
                                    'website' => $this->Session->read('vote.website')
                                ));
                                $this->Vote->save();
                            } else {
                                $this->Vote->read(null, $get_last_vote['Vote']['id']);
                                $this->Vote->set(array(
                                    'username' => $this->Session->read('vote.pseudo'),
                                    'ip' => $_SERVER['REMOTE_ADDR'],
                                    'website' => $this->Session->read('vote.website'),
                                    'created' => date('Y-m-d H:i:s')
                                ));
                                $this->Vote->save();
                            }
                            $this->loadModel('User');
                            $user_id = $this->User->find('all', array('conditions' => array('pseudo' => $this->Session->read('vote.pseudo'))));
                            $vote_nbr = $user_id[0]['User']['vote'] + 1;
                            $this->User->read(null, $user_id['0']['User']['id']);
                            $this->User->set(array(
                                'vote' => $vote_nbr
                            ));
                            $this->User->save();

                            // on cast l'event
                            $this->getEventManager()->dispatch(new CakeEvent('onVote', $this));


                            if($when == 'now') { // si c'est maintenant
                                // on lui donne ses récompenses
                                if($config['VoteConfiguration']['rewards_type'] == 1) { // toutes les récompenses

                                    $rewards = unserialize($config['VoteConfiguration']['rewards']);

                                    $this->getEventManager()->dispatch(new CakeEvent('beforeRecieveRewards', $this, $rewards));

                                    foreach ($rewards as $key => $value) { // on le fais pour toute les commandes

                                        if($value['type'] == 'server') { // si c'est une commande serveur

                                            $config['0']['VoteConfiguration']['servers'] = unserialize($config['VoteConfiguration']['servers']);
                                            if(!empty($config['0']['VoteConfiguration']['servers'])) {
                                                foreach ($config['0']['VoteConfiguration']['servers'] as $k => $v) {
                                                    $servers_online[] = $this->Server->online($v);
                                                }
                                            } else {
                                                $servers_online = array($this->Server->online());
                                            }
                                            if(!in_array(false, $servers_online)) {
                                                if(empty($config['0']['VoteConfiguration']['servers'])) {
                                                    $cmd = str_replace('{PLAYER}', $this->Session->read('vote.pseudo'), $value['command']);
                                                    $this->Server->send_command($cmd); // on envoie la commande puis enregistre le vote
                                                    $msg = str_replace('{PLAYER}', $this->Session->read('vote.pseudo'), $this->Lang->get('VOTE_SUCCESS_SERVER'));
                                                    $this->Server->send_command('broadcast '.$msg);
                                                } else {
                                                    foreach ($config['0']['VoteConfiguration']['servers'] as $k2 => $v2) {
                                                        $cmd = str_replace('{PLAYER}', $this->Session->read('vote.pseudo'), $value['command']);
                                                        $this->Server->send_command($cmd, $v2); // on envoie la commande puis enregistre le vote
                                                        $msg = str_replace('{PLAYER}', $this->Session->read('vote.pseudo'), $this->Lang->get('VOTE_SUCCESS_SERVER'));
                                                        $this->Server->send_command('broadcast '.$msg, $v2);
                                                    }
                                                }

                                                $success_msg[] = $value['name'];

                                            } else {

                                                $success_msg[] = 'server_error';
                                            }

                                        } elseif($value['type'] == 'money') { // si c'est des points boutique

                                            $money = $this->User->getFromUser('money', $this->Session->read('vote.pseudo'));
                                            $money = $money + intval($value['how']);
                                            $this->User->setToUser('money', $money, $this->Session->read('vote.pseudo'));

                                            $success_msg[] = $value['how'].' '.$this->Configuration->get_money_name();

                                        } else {
                                            $success_msg[] = 'internal_error';
                                        }

                                    }

                                    if(in_array('server_error', $success_msg)) {
                                        echo $this->Lang->get('SERVER__MUST_BE_ON').'|false';
                                    } elseif (in_array('internal_error', $success_msg)) {
                                        echo $this->Lang->get('INTERNAL_ERROR').'|false';
                                    } else {
                                        echo $this->Lang->get('VOTE_SUCCESS').' ! ';
                                        if(!empty($success_msg)) {
                                            echo $this->Lang->get('REWARDS').' : ';

                                            $i = 0;
                                            $count = count($success_msg);
                                            foreach ($success_msg as $k => $v) {
                                                $i++;
                                                echo '<b>'.$v.'</b>';
                                                if($i < $count) {
                                                    echo ', ';
                                                } else {
                                                    echo '.|true';
                                                }
                                            }
                                        } else {
                                            echo '|true';
                                        }
                                    }

                                } else { // récompenses aléatoire selon probabilité
                                    $rewards = unserialize($config['VoteConfiguration']['rewards']);
                                    $probability_all = 0;
                                    foreach ($rewards as $key => $value) {
                                        $probability_all = $probability_all + $value['proba'];
                                        $rewards_rand[$key] = $value['proba'];
                                    }

                                    $reward = $this->random($rewards_rand, $probability_all);

                                    $this->getEventManager()->dispatch(new CakeEvent('beforeRecieveRewards', $this, $reward));

                                    if($rewards[$reward]['type'] == 'server') { // si c'est une commande serveur
                                        $config['0']['VoteConfiguration']['servers'] = unserialize($config['VoteConfiguration']['servers']);
                                        if(!empty($config['0']['VoteConfiguration']['servers'])) {
                                            foreach ($config['0']['VoteConfiguration']['servers'] as $key => $value) {
                                                $servers_online[] = $this->Server->online($value);
                                            }
                                        } else {
                                            $servers_online = array($this->Server->online());
                                        }
                                        if(!in_array(false, $servers_online)) {

                                            if(empty($config['0']['VoteConfiguration']['servers'])) {
                                                $cmd = str_replace('{PLAYER}', $this->Session->read('vote.pseudo'), $rewards[$random]['command']);
                                                $this->Server->send_command($cmd); // on envoie la commande puis enregistre le vote
                                                $msg = str_replace('{PLAYER}', $this->Session->read('vote.pseudo'), $this->Lang->get('VOTE_SUCCESS_SERVER'));
                                                $this->Server->send_command('broadcast '.$msg);
                                            } else {
                                                foreach ($config['0']['VoteConfiguration']['servers'] as $key => $value) {
                                                    $cmd = str_replace('{PLAYER}', $this->Session->read('vote.pseudo'), $rewards[$reward]['command']);
                                                    $this->Server->send_command($cmd, $value); // on envoie la commande puis enregistre le vote
                                                    $msg = str_replace('{PLAYER}', $this->Session->read('vote.pseudo'), $this->Lang->get('VOTE_SUCCESS_SERVER'));
                                                    $this->Server->send_command('broadcast '.$msg, $value);
                                                }
                                            }

                                            echo $this->Lang->get('VOTE_SUCCESS').' '.$this->Lang->get('REWARD').' : <b>'.$rewards[$reward]['name'].'</b>.|true';

                                        } else {
                                            echo $this->Lang->get('SERVER__MUST_BE_ON').'|false';
                                        }

                                    } elseif($rewards[$reward]['type'] == 'money') { // si c'est des points boutique

                                        $money = $this->User->getFromUser('money', $this->Session->read('vote.pseudo'));
                                        $money = $money + intval($rewards[$reward]['how']);
                                        $this->User->setToUser('money', $money, $this->Session->read('vote.pseudo'));

                                        echo $this->Lang->get('VOTE_SUCCESS').' '.$this->Lang->get('REWARDS').' : <b>'.$rewards[$reward]['how'].' '.$this->Configuration->get_money_name().'</b>.|true';

                                    } else {
                                        echo $this->Lang->get('INTERNAL_ERROR').'|false';
                                    }

                                }
                            } else { // si c'est plus tard
                                $this->User->setKey('rewards_waited', ($this->User->getKey('rewards_waited') + 1));
                                 echo $this->Lang->get('REWARDS_SUCCESS_SET_WAITED').'|true';
                            }

                            $this->Session->delete('vote');

                        } else {
                            echo $this->Lang->get('ALREADY_VOTED').'|false';

                            $this->Session->delete('vote');
                        }
                    } else {
                        echo $this->Lang->get('ALREADY_VOTED').'|false';
                    }

                } else {
                    echo $this->Lang->get('OUT_INVALID').'|false';
                }
            } else {
                echo $this->Lang->get('UNKNOWN_USERNAME').'|false';
            }
        } else {
            throw new InternalErrorException();
        }
    }

    private function random($rewards, $probability_all) {
        $pct = 1000;
        $rand = mt_rand(0, $pct);
        $items = array();

        foreach ($rewards as $key => $value) {
            $items[$key] = $value / $probability_all;
        }

        $i = 0;
        asort($items);

        foreach ($items as $name => $value) {
            if ($rand <= $i+=($value * $pct)) {
                $reward = $name;
                break;
            }
        }
        return $reward;
    }

    public function get_reward() {
        $this->autoRender = false;
        if($this->isConnected && $this->User->getKey('rewards_waited') > 0) {
            $this->loadModel('Vote.VoteConfiguration');
            $config = $this->VoteConfiguration->find('first');

            // on lui donne ses récompenses
            if($config['VoteConfiguration']['rewards_type'] == 1) { // toutes les récompenses

                $rewards = unserialize($config['VoteConfiguration']['rewards']);

                $this->getEventManager()->dispatch(new CakeEvent('beforeRecieveRewards', $this, $rewards));

                foreach ($rewards as $key => $value) { // on le fais pour toute les commandes

                    if($value['type'] == 'server') { // si c'est une commande serveur

                        $config['0']['VoteConfiguration']['servers'] = unserialize($config['VoteConfiguration']['servers']);
                        if(!empty($config['0']['VoteConfiguration']['servers'])) {
                            foreach ($config['0']['VoteConfiguration']['servers'] as $k => $v) {
                                $servers_online[] = $this->Server->online($v);
                            }
                        } else {
                            $servers_online = array($this->Server->online());
                        }
                        if(!in_array(false, $servers_online)) {
                            if(empty($config['0']['VoteConfiguration']['servers'])) {
                                $cmd = str_replace('{PLAYER}', $this->Session->read('vote.pseudo'), $value['command']);
                                $this->Server->send_command($cmd); // on envoie la commande puis enregistre le vote
                                $msg = str_replace('{PLAYER}', $this->Session->read('vote.pseudo'), $this->Lang->get('VOTE_SUCCESS_SERVER'));
                                $this->Server->send_command('broadcast '.$msg);
                            } else {
                                foreach ($config['0']['VoteConfiguration']['servers'] as $k2 => $v2) {
                                    $cmd = str_replace('{PLAYER}', $this->Session->read('vote.pseudo'), $value['command']);
                                    $this->Server->send_command($cmd, $v2); // on envoie la commande puis enregistre le vote
                                    $msg = str_replace('{PLAYER}', $this->Session->read('vote.pseudo'), $this->Lang->get('VOTE_SUCCESS_SERVER'));
                                    $this->Server->send_command('broadcast '.$msg, $v2);
                                }
                            }

                            $success_msg[] = $value['name'];

                        } else {

                            $success_msg[] = 'server_error';
                        }

                    } elseif($value['type'] == 'money') { // si c'est des points boutique

                        $money = $this->User->getFromUser('money', $this->Session->read('vote.pseudo'));
                        $money = $money + intval($value['how']);
                        $this->User->setToUser('money', $money, $this->Session->read('vote.pseudo'));

                        $success_msg[] = $value['how'].' '.$this->Configuration->get_money_name();

                    } else {
                        $success_msg[] = 'internal_error';
                    }

                }

                if(in_array('server_error', $success_msg)) {
                    $this->Session->setFlash($this->Lang->get('SERVER__MUST_BE_ON'), 'default.error');
                    $this->redirect(array('controller' => 'user', 'action' => 'profile', 'plugin' => false));
                } elseif (in_array('internal_error', $success_msg)) {
                    $this->Session->setFlash($this->Lang->get('INTERNAL_ERROR'), 'default.error');
                    $this->redirect(array('controller' => 'user', 'action' => 'profile', 'plugin' => false));
                } else {
                    $flash = $this->Lang->get('VOTE_SUCCESS').' ! ';
                    if(!empty($success_msg)) {
                        $flash = $this->Lang->get('REWARDS').' : ';

                        $i = 0;
                        $count = count($success_msg);
                        foreach ($success_msg as $k => $v) {
                            $i++;
                            $flash = '<b>'.$v.'</b>';
                            if($i < $count) {
                                $flash = ', ';
                            } else {
                                $flash = '.|true';
                            }
                        }
                    }

                    // on lui enlève la récompense en attente
                    $this->User->setKey('rewards_waited', ($this->User->getKey('rewards_waited') - 1));
                    // puis on redirige
                    $this->Session->setFlash($flash, 'default.success');
                    $this->redirect(array('controller' => 'user', 'action' => 'profile', 'plugin' => false));
                }

            } else { // récompenses aléatoire selon probabilité
                $rewards = unserialize($config['VoteConfiguration']['rewards']);
                $probability_all = 0;
                foreach ($rewards as $key => $value) {
                    $probability_all = $probability_all + $value['proba'];
                    $rewards_rand[$key] = $value['proba'];
                }

                $reward = $this->random($rewards_rand, $probability_all);

                $this->getEventManager()->dispatch(new CakeEvent('beforeRecieveRewards', $this, $reward));

                if($rewards[$reward]['type'] == 'server') { // si c'est une commande serveur
                    $config['0']['VoteConfiguration']['servers'] = unserialize($config['VoteConfiguration']['servers']);
                    if(!empty($config['0']['VoteConfiguration']['servers'])) {
                        foreach ($config['0']['VoteConfiguration']['servers'] as $key => $value) {
                            $servers_online[] = $this->Server->online($value);
                        }
                    } else {
                        $servers_online = array($this->Server->online());
                    }
                    if(!in_array(false, $servers_online)) {

                        if(empty($config['0']['VoteConfiguration']['servers'])) {
                            $cmd = str_replace('{PLAYER}', $this->Session->read('vote.pseudo'), $rewards[$random]['command']);
                            $this->Server->send_command($cmd); // on envoie la commande puis enregistre le vote
                            $msg = str_replace('{PLAYER}', $this->Session->read('vote.pseudo'), $this->Lang->get('VOTE_SUCCESS_SERVER'));
                            $this->Server->send_command('broadcast '.$msg);
                        } else {
                            foreach ($config['0']['VoteConfiguration']['servers'] as $key => $value) {
                                $cmd = str_replace('{PLAYER}', $this->Session->read('vote.pseudo'), $rewards[$reward]['command']);
                                $this->Server->send_command($cmd, $value); // on envoie la commande puis enregistre le vote
                                $msg = str_replace('{PLAYER}', $this->Session->read('vote.pseudo'), $this->Lang->get('VOTE_SUCCESS_SERVER'));
                                $this->Server->send_command('broadcast '.$msg, $value);
                            }
                        }

                        $this->User->setKey('rewards_waited', ($this->User->getKey('rewards_waited') - 1));

                        $this->Session->setFlash($this->Lang->get('VOTE_SUCCESS').' '.$this->Lang->get('REWARD').' : <b>'.$rewards[$reward]['name'].'</b>.', 'default.success');
                        $this->redirect(array('controller' => 'user', 'action' => 'profile', 'plugin' => false));

                    } else {
                        $this->Session->setFlash($this->Lang->get('SERVER__MUST_BE_ON'), 'default.error');
                        $this->redirect(array('controller' => 'user', 'action' => 'profile', 'plugin' => false));
                    }

                } elseif($rewards[$reward]['type'] == 'money') { // si c'est des points boutique

                    $money = $this->User->getFromUser('money', $this->Session->read('vote.pseudo'));
                    $money = $money + intval($rewards[$reward]['how']);
                    $this->User->setToUser('money', $money, $this->Session->read('vote.pseudo'));

                    $this->User->setKey('rewards_waited', ($this->User->getKey('rewards_waited') - 1));

                    $this->Session->setFlash($this->Lang->get('VOTE_SUCCESS').' '.$this->Lang->get('REWARDS').' : <b>'.$rewards[$reward]['how'].' '.$this->Configuration->get_money_name().'</b>.', 'default.success');
                    $this->redirect(array('controller' => 'user', 'action' => 'profile'));

                } else {
                    $this->Session->setFlash($this->Lang->get('INTERNAL_ERROR'), 'default.error');
                    $this->redirect(array('controller' => 'user', 'action' => 'profile', 'plugin' => false));
                }

            }
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

						$vote['servers'] = unserialize($vote['servers']);
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

            $this->set('title_for_layout',$this->Lang->get('VOTE_TITLE'));
        } else {
            $this->redirect('/');
        }
    }

    public function admin_reset() {
        if($this->isConnected AND $this->User->isAdmin()) {
            $this->layout = null;

            $this->loadModel('Vote.Vote');
            $this->Vote->deleteAll(array('1' => '1'));
            $this->loadModel('User');
            $this->User->updateAll(array('vote' => 0));
            $this->History->set('RESET', 'vote');
            $this->Session->setFlash($this->Lang->get('RESET_VOTE_SUCCESS'), 'default.success');
            $this->redirect(array('controller' => 'voter', 'action' => 'index', 'admin' => true));
        }
    }

    public function admin_add_ajax() {
        if($this->isConnected AND $this->User->isAdmin()) {
            $this->layout = null;

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
                        $this->Session->setFlash($this->Lang->get('CONFIGURATION_SAVE'), 'default.success');
                        echo $this->Lang->get('CONFIGURATION_SAVE').'|true';
                    } else {
                        echo $this->Lang->get('ERROR__FILL_ALL_FIELDS').'|false';
                    }
                } else {
                    echo $this->Lang->get('ERROR__FILL_ALL_FIELDS').'|false';
                }
            } else {
                echo $this->Lang->get('NOT_POST').'|false';
            }
        } else {
            $this->redirect('/');
        }
    }
}
