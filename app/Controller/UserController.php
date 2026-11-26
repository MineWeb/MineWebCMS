<?php

class UserController extends AppController
{
    public $components = ['Session', 'Captcha', 'API'];

    function get_captcha()
    {
        $this->autoRender = false;
        App::import('Component', 'Captcha');
        //generate random charcters for captcha
        $random = mt_rand(100, 99999);
        //save characters in session
        $this->Session->write('captcha_code', $random);
        $settings = [
            'characters' => $random,
            'winHeight' => 50,         // captcha image height
            'winWidth' => 220,           // captcha image width
            'fontSize' => 25,          // captcha image characters fontsize
            'fontPath' => WWW_ROOT . 'tahomabd.ttf',    // captcha image font
            'noiseColor' => '#ccc',
            'bgColor' => '#fff',
            'noiseLevel' => '100',
            'textColor' => '#000'
        ];
        $img = $this->Captcha->ShowImage($settings);
        echo $img;
    }

    function ajax_register()
    {
        $this->autoRender = false;
        $this->response->type('json');
        if ($this->request->is('Post')) { // si la requête est bien un post
            $conditionsChecked = !empty($this->request->data['condition']) || !$this->Configuration->getKey('condition');
            if (!empty($this->request->data['pseudo']) && !empty($this->request->data['password']) && $conditionsChecked && !empty($this->request->data['password_confirmation']) && !empty($this->request->data['email'])) { // si tout les champs sont bien remplis
                //check uuid if needed
                $this->request->data = $this->request->data['xss'];
                if ($this->Configuration->getKey('check_uuid')) {
                    $pseudoToUUID = file_get_contents("https://api.mojang.com/users/profiles/minecraft/" . htmlentities($this->request->data['pseudo']));
                    if (!$pseudoToUUID) {
                        return $this->response->body(json_encode([
                            'statut' => false,
                            'msg' => $this->Lang->get('USER__ERROR_UUID')
                        ]));
                    }

                    $this->request->data['uuid'] = json_decode($pseudoToUUID, true)['id'];
                }
                // Captcha
                if ($this->Configuration->getKey('captcha_type') == "2" || $this->Configuration->getKey('captcha_type') == "3") { // ReCaptcha and h-captcha
                    $validCaptcha = $this->Util->isValidReCaptcha($this->request->data['recaptcha'], $this->Util->getIP(), $this->Configuration->getKey('captcha_secret'), $this->Configuration->getKey('captcha_type'));
                } else {
                    $captcha = $this->Session->read('captcha_code');
                    $validCaptcha = (!empty($captcha) && $captcha == $this->request->data['captcha']);
                }
                //
                if ($validCaptcha) { // on check le captcha déjà
                    $this->loadModel('User');
                    $isValid = $this->User->validRegister($this->request->data, $this->Util);
                    if ($isValid === true) { // on vérifie si y'a aucune erreur
                        $eventData = $this->request->data;
                        $eventData['password'] = $this->Util->password($eventData['password'], $eventData['pseudo']);
                        $event = new CakeEvent('beforeRegister', $this, ['data' => $eventData]);
                        $this->getEventManager()->dispatch($event);
                        if ($event->isStopped()) {
                            return $event->result;
                        }
                        // we record
                        $this->request->data['microsoft_user_id'] = null;
                        $this->request->data['registered_by_microsoft'] = false;
                        $userSession = $this->User->register(
                            $this->request->data,
                            $this->Util,
                            $this->Configuration->getKey('confirm_mail_signup')
                        );
                        if (!$this->Configuration->getKey('confirm_mail_signup_block')) { // si on doit pas bloquer le compte si non confirmé
                            // on prépare la connexion
                            $this->renewUserSession($userSession);
                            $event = new CakeEvent('onLogin', $this, ['user' => $this->User->getAllFromCurrentUser(), 'register' => true]);
                            $this->getEventManager()->dispatch($event);
                            if ($event->isStopped()) {
                                return $event->result;
                            }
                        }
                        // on dis que c'est bon
                        $this->response->body(json_encode([
                            'statut' => true,
                            'msg' => $this->Lang->get('USER__REGISTER_SUCCESS')
                        ]));
                    } else { // si c'est pas bon, on envoie le message d'erreur retourné par l'étape de validation
                        $this->response->body(json_encode([
                            'statut' => false,
                            'msg' => $this->Lang->get($isValid)
                        ]));
                    }
                } else {
                    $this->response->body(json_encode([
                        'statut' => false,
                        'msg' => $this->Lang->get('FORM__INVALID_CAPTCHA')
                    ]));
                }
            } else {
                $this->response->body(json_encode([
                    'statut' => false,
                    'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')
                ]));
            }
        } else {
            $this->response->body(json_encode([
                'statut' => false,
                'msg' => $this->Lang->get('ERROR__BAD_REQUEST')
            ]));
        }
    }

    function ajax_login()
    {
        if (!$this->request->is('post'))
            throw new BadRequestException();
        if (empty($this->request->data['pseudo']) || empty($this->request->data['password']))
            return $this->sendJSON(['statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')]);
        $this->autoRender = false;
        $this->response->type('json');
        $this->loadModel('User');
        $this->request->data = $this->request->data['xss'];
        $user_login = $this->User->getAllFromUser($this->request->data['pseudo']);

        $confirmEmailIsNeeded = ($this->Configuration->getKey('confirm_mail_signup') && $this->Configuration->getKey('confirm_mail_signup_block'));
        $login = $this->User->login($user_login, $this->request->data, $confirmEmailIsNeeded, $this->Configuration->getKey('check_uuid'), $this);
        if (!isset($login['status']) || $login['status'] !== true) {
            return $this->sendJSON([
                'statut' => false,
                'msg' => $this->Lang->get($login, ['{URL_RESEND_EMAIL}' => Router::url(['action' => 'resend_confirmation'])])
            ]);
        }

        if ($this->beginTwoFactorLogin($login['session'])) {
            return $this->sendJSON([
                'statut' => true,
                'msg' => $this->Lang->get('USER__REGISTER_LOGIN'),
                'two-factor-auth' => true
            ]);
        }
        $event = new CakeEvent('onLogin', $this, ['user' => $user_login]);
        $this->getEventManager()->dispatch($event);
        if ($event->isStopped())
            return $event->result;
        $this->setRememberMe($login['session'], !empty($this->request->data['remember_me']));
        $this->renewUserSession($login['session']);
        return $this->sendJSON(['statut' => true, 'msg' => $this->Lang->get('USER__REGISTER_LOGIN')]);

    }

    function confirm($code = null)
    {
        $this->autoRender = false;
        if (!is_string($code) || !in_array(strlen($code), [12, 24], true) || !ctype_xdigit($code)) {
            throw new NotFoundException();
        }

        $user = $this->User->find('first', [
            'fields' => ['User.id', 'User.confirmed'],
            'conditions' => ['User.confirmed' => $code]
        ]);
        if (empty($user) || $user['User']['confirmed'] !== $code) {
            throw new NotFoundException();
        }

        $userId = $user['User']['id'];
        $event = new CakeEvent('beforeConfirmAccount', $this, ['user_id' => $userId]);
        $this->getEventManager()->dispatch($event);
        if ($event->isStopped()) {
            return $event->result;
        }
        if (!$this->User->confirmEmail($userId, $code)) {
            throw new NotFoundException();
        }

        $message = $this->Lang->get('USER__CONFIRM_NOTIFICATION');
        $this->loadModel('Notification');
        $this->Notification->setToUser($message, $userId);
        $this->Session->setFlash($message, 'default.success');
        return $this->redirect($this->isConnected ? ['action' => 'profile'] : '/');
    }

    function ajax_lostpasswd()
    {
        $this->layout = null;
        $this->autoRender = false;
        $this->response->type('json');
        if ($this->request->is('ajax')) {
            if (!empty($this->request->data['email'])) {
                $this->loadModel('User');
                if (filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL)) {
                    $search = $this->User->find('first', ['conditions' => ['email' => $this->request->data['email']]]);
                    if (!empty($search)) {
                        if ($search['User']['registered_by_microsoft'])
                            return $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('USER__AUTH_MICROSOFT_CANNOT_RESET_PASSWORD')]));
                        $this->loadModel('Lostpassword');
                        try {
                            $key = $this->Lostpassword->generateKey();
                        } catch (Exception $e) {
                            $this->log($e->getMessage(), 'error');
                            return $this->sendJSON(['statut' => false, 'msg' => $this->Lang->get('ERROR__INTERNAL_ERROR')]);
                        }
                        $to = $this->request->data['email'];
                        $subject = $this->Lang->get('USER__PASSWORD_RESET_LINK');
                        $message = $this->Lang->get('USER__PASSWORD_RESET_EMAIL_CONTENT', [
                            '{EMAIL}' => $this->request->data['email'],
                            '{PSEUDO}' => $search['User']['pseudo'],
                            '{LINK}' => $this->Configuration->getKey('website_url') . "/?resetpasswd_$key"
                        ]);
                        $event = new CakeEvent('beforeSendResetPassMail', $this, ['user_id' => $search['User']['id'], 'key' => $key]);
                        $this->getEventManager()->dispatch($event);
                        if ($event->isStopped()) {
                            return $event->result;
                        }
                        if ($this->Util->prepareMail($to, $subject, $message)->sendMail()) {
                            $this->Lostpassword->create();
                            $this->Lostpassword->set([
                                'email' => $this->request->data['email'],
                                'key' => $key
                            ]);
                            $this->Lostpassword->save();
                            $this->response->body(json_encode([
                                'statut' => true,
                                'msg' => $this->Lang->get('USER__PASSWORD_FORGOT_EMAIL_SUCCESS')
                            ]));
                        } else {
                            $this->response->body(json_encode([
                                'statut' => false,
                                'msg' => $this->Lang->get('ERROR__INTERNAL_ERROR')
                            ]));
                        }
                    } else {
                        $this->response->body(json_encode([
                            'statut' => false,
                            'msg' => $this->Lang->get('USER__ERROR_NOT_FOUND')
                        ]));
                    }
                } else {
                    $this->response->body(json_encode([
                        'statut' => false,
                        'msg' => $this->Lang->get('USER__ERROR_EMAIL_NOT_VALID')
                    ]));
                }
            } else {
                $this->response->body(json_encode([
                    'statut' => false,
                    'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')
                ]));
            }
        } else {
            $this->response->body(json_encode([
                'statut' => false,
                'msg' => $this->Lang->get('ERROR__BAD_REQUEST')
            ]));
        }
    }

    function ajax_resetpasswd()
    {
        $this->autoRender = false;
        $this->response->type('json');
        if ($this->request->is('ajax')) {
            if (!empty($this->request->data['password']) and !empty($this->request->data['password2']) and !empty($this->request->data['email']) && !empty($this->request->data['key'])) {
                $this->request->data = $this->request->data['xss'];
                $reset = $this->User->resetPass($this->request->data, $this);
                if (isset($reset['status']) && $reset['status'] === true) {
                    $this->loadModel('Authentification');
                    if (!$this->Authentification->requiresCode($reset['session'])) {
                        $this->renewUserSession($reset['session']);
                    }
                    $this->History->set('RESET_PASSWORD', 'user', null, $reset['session']);
                    $this->response->body(json_encode([
                        'statut' => true,
                        'msg' => $this->Lang->get('USER__PASSWORD_RESET_SUCCESS')
                    ]));
                } else {
                    $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get($reset)]));
                }
            } else {
                $this->response->body(json_encode([
                    'statut' => false,
                    'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')
                ]));
            }
        } else {
            $this->response->body(json_encode([
                'statut' => false,
                'msg' => $this->Lang->get('ERROR__BAD_REQUEST')
            ]));
        }
    }

    function logout()
    {
        $this->autoRender = false;
        $event = new CakeEvent('onLogout', $this, ['session' => $this->Session->read('user')]);
        $this->getEventManager()->dispatch($event);
        if ($event->isStopped()) {
            return $event->result;
        }

        if ($this->Cookie->read('microsoft_user_id')) {
            $this->Cookie->delete('microsoft_user_id');
        }

        $this->renewUserSession();
        $this->forgetRememberedUser();
        $this->redirect($this->referer());
    }

    function uploadSkin()
    {
        $this->autoRender = false;
        $this->response->type('json');
        if ($this->isConnected && $this->API->can_skin()) {
            if ($this->request->is('post')) {
                $username = $this->User->getKey('pseudo');
                $this->loadModel('ApiConfiguration');
                $ApiConfiguration = $this->ApiConfiguration->find('first');

                $useSkinRestorer = $ApiConfiguration['ApiConfiguration']['use_skin_restorer'];
                $serverSkinRestorerID = $ApiConfiguration['ApiConfiguration']['skin_restorer_server_id'];

                if ($useSkinRestorer & !$this->Server->userIsConnected($username, $serverSkinRestorerID)) {
                    $this->response->body(json_encode(['statut' => false, 'msg' => $this->Lang->get('API__SKIN_RESTORER_NOT_CONNECTED')]));
                    return;
                }
                $skin_max_size = 10000000; // octet

                $target_config = $ApiConfiguration['ApiConfiguration']['skin_filename'];
                $filename = substr($target_config, (strrpos($target_config, '/') + 1));
                $filename = str_replace('{PLAYER}', $username, $filename);
                $filename = str_replace('php', '', $filename);
                $filename = str_replace('.', '', $filename);
                $filename = $filename . '.png';
                $target = substr($target_config, 0, (strrpos($target_config, '/') + 1));
                $target = WWW_ROOT . '/' . $target;
                $width_max = $ApiConfiguration['ApiConfiguration']['skin_width']; // pixel
                $height_max = $ApiConfiguration['ApiConfiguration']['skin_height']; // pixel
                $isValidImg = $this->Util->isValidImage($this->request, ['png'], $width_max, $height_max, $skin_max_size);
                if (!$isValidImg['status']) {
                    $this->response->body(json_encode(['statut' => false, 'msg' => $isValidImg['msg']]));
                    return;
                }
                if (!$this->Util->uploadImage($this->request, $target . $filename)) {
                    $this->response->body(json_encode([
                        'statut' => false,
                        'msg' => $this->Lang->get('FORM__ERROR_WHEN_UPLOAD')
                    ]));
                    return;
                }

                $skinURL = Router::url(['action' => str_replace("{PLAYER}", $username, $ApiConfiguration['ApiConfiguration']['skin_filename']) . ".png", 'controller' => '', 'admin' => false], true);

                $skinRestorerCommand = str_replace(['{PLAYER}', '{URL}'], [$username, $skinURL], "skin set {PLAYER} {URL}");
                $this->Server->commands($skinRestorerCommand, $serverSkinRestorerID);

                $this->response->body(json_encode([
                    'statut' => true,
                    'msg' => $this->Lang->get('API__UPLOAD_SKIN_SUCCESS')
                ]));
            }
        } else {
            throw new ForbiddenException();
        }
    }

    function uploadCape()
    {
        $this->autoRender = false;
        $this->response->type('json');
        if ($this->isConnected && $this->API->can_cape()) {
            if ($this->request->is('post')) {
                $cape_max_size = 10000000; // octet
                $this->loadModel('ApiConfiguration');
                $ApiConfiguration = $this->ApiConfiguration->find('first');
                $target_config = $ApiConfiguration['ApiConfiguration']['cape_filename'];
                $filename = substr($target_config, (strrpos($target_config, '/') + 1));
                $filename = str_replace('{PLAYER}', $this->User->getKey('pseudo'), $filename);
                $filename = str_replace('php', '', $filename);
                $filename = str_replace('.', '', $filename);
                $filename = $filename . '.png';
                $target = substr($target_config, 0, (strrpos($target_config, '/') + 1));
                $target = WWW_ROOT . '/' . $target;
                $width_max = $ApiConfiguration['ApiConfiguration']['cape_width']; // pixel
                $height_max = $ApiConfiguration['ApiConfiguration']['cape_height']; // pixel
                $isValidImg = $this->Util->isValidImage($this->request, ['png'], $width_max, $height_max, $cape_max_size);
                if (!$isValidImg['status']) {
                    $this->response->body(json_encode(['statut' => false, 'msg' => $isValidImg['msg']]));
                    return;
                }
                if (!$this->Util->uploadImage($this->request, $target . $filename)) {
                    $this->response->body(json_encode([
                        'statut' => false,
                        'msg' => $this->Lang->get('FORM__ERROR_WHEN_UPLOAD')
                    ]));
                    return;
                }
                $this->response->body(json_encode([
                    'statut' => true,
                    'msg' => $this->Lang->get('API__UPLOAD_CAPE_SUCCESS')
                ]));
            }
        } else {
            throw new ForbiddenException();
        }
    }

    function profile()
    {
        if ($this->isConnected) {
            $this->loadModel('Authentification');
            $this->set('twoFactorAuthStatus', $this->Authentification->requiresCode($this->User->getKey('id')));
            $this->loadModel('User');
            $this->set('title_for_layout', $this->User->getKey('pseudo'));
            $this->layout = $this->Configuration->getKey('layout');
            if ($this->EyPlugin->isInstalled('eywek.shop')) {
                $this->loadModel('Shop.ItemsBuyHistory');
                $this->loadModel('Shop.Item');
                $histories = $this->ItemsBuyHistory->find('all', [
                    'recursive' => 1,
                    'order' => 'ItemsBuyHistory.created DESC',
                    'conditions' => ['user_id' => $this->User->getKey('id')]
                ]);
                $this->set(compact('histories'));
                $this->set('shop_active', true);
            } else {
                $this->set('shop_active', false);
            }
            $available_ranks = [
                0 => $this->Lang->get('USER__RANK_MEMBER'),
                2 => $this->Lang->get('USER__RANK_MODERATOR'),
                3 => $this->Lang->get('USER__RANK_ADMINISTRATOR'),
                4 => $this->Lang->get('USER__RANK_ADMINISTRATOR')
            ];
            $this->loadModel('Rank');
            $custom_ranks = $this->Rank->find('all');
            foreach ($custom_ranks as $value) {
                $available_ranks[$value['Rank']['rank_id']] = $value['Rank']['name'];
            }
            $this->set(compact('available_ranks'));
            $this->set('can_cape', $this->API->can_cape());
            $this->set('can_skin', $this->API->can_skin());
            $this->loadModel('ApiConfiguration');
            $configAPI = $this->ApiConfiguration->find('first');
            $skin_width_max = $configAPI['ApiConfiguration']['skin_width'];
            $skin_height_max = $configAPI['ApiConfiguration']['skin_height'];
            $cape_width_max = $configAPI['ApiConfiguration']['cape_width'];
            $cape_height_max = $configAPI['ApiConfiguration']['cape_height'];
            $this->set(compact('skin_width_max', 'skin_height_max', 'cape_width_max', 'cape_height_max'));
            $confirmed = $this->User->getKey('confirmed');
            if ($this->Configuration->getKey('confirm_mail_signup') && !empty($confirmed) && date('Y-m-d H:i:s', strtotime($confirmed)) != $confirmed) { // si ca ne correspond pas à une date -> compte non confirmé
                $this->Session->setFlash($this->Lang->get('USER__MSG_NOT_CONFIRMED_EMAIL', ['{URL_RESEND_EMAIL}' => Router::url(['action' => 'resend_confirmation'])]), 'default.warning');
            }
            $connected_by_microsoft = false;
            $microsoft_user_id = $this->Cookie->read('microsoft_user_id');
            if (isset($microsoft_user_id))
                $connected_by_microsoft = true;
            $this->set(compact('connected_by_microsoft'));
        } else {
            $this->redirect('/');
        }
    }

    function resend_confirmation()
    {
        if (!$this->isConnected && !$this->Session->check('email.confirm.user.id'))
            throw new ForbiddenException();
        if ($this->isConnected)
            $user = $this->User->getAllFromCurrentUser();
        else
            $user = $this->User->find('first', ['conditions' => ['id' => $this->Session->read('email.confirm.user.id')]]);
        $this->Session->delete('email.confirm.user.id');
        if (!$user || empty($user))
            throw new NotFoundException();
        if (isset($user['User']))
            $user = $user['User'];
        $confirmed = $user['confirmed'];
        if (!$this->Configuration->getKey('confirm_mail_signup') || empty($confirmed) || date('Y-m-d H:i:s', strtotime($confirmed)) == $confirmed)
            throw new NotFoundException();
        $email = $this->Util->sendConfirmationEmail($user['email'], $user['pseudo'], $confirmed);
        if ($email)
            $this->Session->setFlash($this->Lang->get('USER__CONFIRM_EMAIL_RESEND_SUCCESS'), 'default.success');
        else
            $this->Session->setFlash($this->Lang->get('USER__CONFIRM_EMAIL_RESEND_FAIL'), 'default.error');
        if ($this->isConnected)
            $this->redirect(['action' => 'profile']);
        else
            $this->redirect('/');
    }

    function change_pw()
    {
        $this->autoRender = false;
        $this->response->type('json');
        if ($this->isConnected) {
            if ($this->request->is('ajax')) {
                if (!empty($this->request->data['password']) and !empty($this->request->data['password_confirmation'])) {
                    $this->request->data = $this->request->data['xss'];
                    if (is_string($this->request->data['password']) && $this->request->data['password'] === $this->request->data['password_confirmation']) {
                        $password = $this->Util->password($this->request->data['password'], $this->User->getKey('pseudo'));
                        $event = new CakeEvent('beforeUpdatePassword', $this, ['user' => $this->User->getAllFromCurrentUser(), 'new_password' => $password]);
                        $this->getEventManager()->dispatch($event);
                        if ($event->isStopped()) {
                            return $event->result;
                        }
                        $this->User->setKey('password', $password);
                        $this->User->setKey('password_hash', $this->Util->getPasswordHashType());
                        $this->response->body(json_encode([
                            'statut' => true,
                            'msg' => $this->Lang->get('USER__PASSWORD_UPDATE_SUCCESS')
                        ]));
                    } else {
                        $this->response->body(json_encode([
                            'statut' => false,
                            'msg' => $this->Lang->get('USER__ERROR_PASSWORDS_NOT_SAME')
                        ]));
                    }
                } else {
                    $this->response->body(json_encode([
                        'statut' => false,
                        'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')
                    ]));
                }
            } else {
                $this->response->body(json_encode([
                    'statut' => false,
                    'msg' => $this->Lang->get('ERROR__BAD_REQUEST')
                ]));
            }
        } else {
            $this->response->body(json_encode([
                'statut' => false,
                'msg' => $this->Lang->get('USER__ERROR_MUST_BE_LOGGED')
            ]));
        }
    }

    function change_email()
    {
        $this->autoRender = false;
        $this->response->type('json');
        if ($this->isConnected && $this->Permissions->can('EDIT_HIS_EMAIL')) {
            if ($this->request->is('ajax')) {
                if (!empty($this->request->data['email']) and !empty($this->request->data['email_confirmation'])) {
                    if ($this->request->data['email'] == $this->request->data['email_confirmation']) {
                        if (filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL)) {
                            $event = new CakeEvent('beforeUpdateEmail', $this, [
                                'user' => $this->User->getAllFromCurrentUser(),
                                'new_email' => $this->request->data['email']
                            ]);
                            $this->getEventManager()->dispatch($event);
                            if ($event->isStopped()) {
                                return $event->result;
                            }
                            $this->User->setKey('email', htmlentities($this->request->data['email']));
                            $this->response->body(json_encode([
                                'statut' => true,
                                'msg' => $this->Lang->get('USER__EMAIL_UPDATE_SUCCESS')
                            ]));
                        } else {
                            $this->response->body(json_encode([
                                'statut' => false,
                                'msg' => $this->Lang->get('USER__ERROR_EMAIL_NOT_VALID')
                            ]));
                        }
                    } else {
                        $this->response->body(json_encode([
                            'statut' => false,
                            'msg' => $this->Lang->get('USER__ERROR_EMAIL_NOT_SAME')
                        ]));
                    }
                } else {
                    $this->response->body(json_encode([
                        'statut' => false,
                        'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')
                    ]));
                }
            } else {
                $this->response->body(json_encode([
                    'statut' => false,
                    'msg' => $this->Lang->get('ERROR__BAD_REQUEST')
                ]));
            }
        } else {
            throw new ForbiddenException();
        }
    }

    function admin_index()
    {
        if ($this->isConnected and $this->Permissions->can('MANAGE_USERS')) {
            $this->set('title_for_layout', $this->Lang->get('USER__TITLE'));
            $this->layout = 'admin';
            $this->set('type', $this->Configuration->getKey('member_page_type'));
        } else {
            $this->redirect('/');
        }
    }

    function admin_liveSearch($query = false)
    {
        $this->autoRender = false;
        $this->response->type('json');
        if ($this->isConnected and $this->Permissions->can('MANAGE_USERS')) {
            if ($query != false) {
                $result = $this->User->find('all', ['conditions' => ['pseudo LIKE' => $query . '%']]);
                $users = [];
                foreach ($result as $value) {
                    $users[] = ['pseudo' => $value['User']['pseudo'], 'id' => $value['User']['id']];
                }
                $response = (empty($result)) ? ['status' => false] : ['status' => true, 'data' => $users];
                $this->response->body($response);
            } else {
                $this->response->body(json_encode(['status' => false]));
            }
        } else {
            $this->response->body(json_encode(['status' => false]));
        }
    }

    public function admin_get_users()
    {
        if ($this->isConnected and $this->Permissions->can('MANAGE_USERS')) {
            $this->autoRender = false;
            $this->response->type('json');
            if ($this->request->is('ajax')) {
                $available_ranks = [
                    0 => ['label' => 'success', 'name' => $this->Lang->get('USER__RANK_MEMBER')],
                    2 => ['label' => 'warning', 'name' => $this->Lang->get('USER__RANK_MODERATOR')],
                    3 => ['label' => 'danger', 'name' => $this->Lang->get('USER__RANK_ADMINISTRATOR')],
                    4 => ['label' => 'danger', 'name' => $this->Lang->get('USER__RANK_ADMINISTRATOR')]
                ];
                $this->loadModel('Rank');
                $custom_ranks = $this->Rank->find('all');
                foreach ($custom_ranks as $value) {
                    $available_ranks[$value['Rank']['rank_id']] = [
                        'label' => 'info',
                        'name' => $value['Rank']['name']
                    ];
                }
                $this->DataTable = $this->Components->load('DataTable');
                $this->modelClass = 'User';
                $this->DataTable->initialize($this);
                $this->paginate = [
                    'fields' => ['User.id', 'User.pseudo', 'User.email', 'User.created', 'User.rank'],
                ];
                $this->DataTable->mDataProp = true;
                $response = $this->DataTable->getResponse();
                $users = $response['aaData'];
                $data = [];
                foreach ($users as $value) {
                    $username = h($value['User']['pseudo'], false);
                    $date = 'Le ' . $this->Lang->date($value['User']['created']);
                    $rank_label = (isset($available_ranks[$value['User']['rank']])) ? $available_ranks[$value['User']['rank']]['label'] : $available_ranks[0]['label'];
                    $rank_name = (isset($available_ranks[$value['User']['rank']])) ? $available_ranks[$value['User']['rank']]['name'] : $available_ranks[0]['name'];
                    $rank = '<span class="label label-' . $rank_label . '">' . h($rank_name, false) . '</span>';
                    $btns = '<a href="' . Router::url([
                            'controller' => 'user',
                            'action' => 'edit/' . $value["User"]["id"],
                            'admin' => true
                        ]) . '" class="btn btn-info">' . $this->Lang->get('GLOBAL__EDIT') . '</a>';
                    $btns .= '&nbsp;<a onClick="confirmDel(\'' . Router::url([
                            'controller' => 'user',
                            'action' => 'delete/' . $value["User"]["id"],
                            'admin' => true
                        ]) . '\')" class="btn btn-danger">' . $this->Lang->get('GLOBAL__DELETE') . '</button>';
                    $data[] = [
                        'User' => [
                            'pseudo' => $username,
                            'email' => h($value['User']['email'], false),
                            'created' => $date,
                            'rank' => $rank
                        ],
                        'actions' => $this->Permissions->canManageRank($value['User']['rank']) ? $btns : ''
                    ];
                }
                $response['aaData'] = $data;
                $this->response->body(json_encode($response));
            }
        }
    }

    function admin_edit($search = false)
    {
        if ($this->isConnected and $this->Permissions->can('MANAGE_USERS')) {
            if ($search != false) {
                $this->layout = 'admin';
                $this->set('title_for_layout', $this->Lang->get('USER__EDIT_TITLE'));
                $this->loadModel('User');
                $search_user = $this->User->getAllFromUser($search);
                if ($search_user) {
                    if (!$this->Permissions->canManageRank($search_user['rank']))
                        throw new ForbiddenException();
                    $this->loadModel('History');
                    $findHistory = $this->History->getLastFromUser($search_user['id']);
                    $search_user['History'] = $this->History->format($findHistory, $this->Lang);
                    $options_ranks = [
                        0 => $this->Lang->get('USER__RANK_MEMBER'),
                        2 => $this->Lang->get('USER__RANK_MODERATOR'),
                        3 => $this->Lang->get('USER__RANK_ADMINISTRATOR'),
                        4 => $this->Lang->get('USER__RANK_SUPER_ADMINISTRATOR')
                    ];
                    $this->loadModel('Rank');
                    $custom_ranks = $this->Rank->find('all');
                    foreach ($custom_ranks as $key => $value) {
                        $options_ranks[$value['Rank']['rank_id']] = $value['Rank']['name'];
                    }
                    foreach ($options_ranks as $rank => $name) {
                        if (!$this->Permissions->canManageRank($rank))
                            unset($options_ranks[$rank]);
                    }
                    if ($this->Configuration->getKey('confirm_mail_signup') && !empty($search_user['confirmed']) && date('Y-m-d H:i:s', strtotime($search_user['confirmed'])) != $search_user['confirmed']) {
                        $search_user['confirmed'] = false;
                    } else {
                        $search_user['confirmed'] = true;
                    }
                    $this->set(compact('options_ranks'));
                    $this->set(compact('search_user'));
                } else {
                    throw new NotFoundException();
                }
            } else {
                throw new NotFoundException();
            }
        } else {
            $this->redirect('/');
        }
    }

    function admin_confirm($user_id = false)
    {
        $this->autoRender = false;
        if (isset($user_id) && $this->isConnected and $this->Permissions->can('MANAGE_USERS')) {
            $find = $this->User->find('first', ['conditions' => ['id' => $user_id]]);
            if (!empty($find)) {
                if (!$this->Permissions->canManageRank($find['User']['rank']))
                    throw new ForbiddenException();
                $event = new CakeEvent('beforeConfirmAccount', $this, ['user_id' => $find['User']['id'], 'manual' => true]);
                $this->getEventManager()->dispatch($event);
                if ($event->isStopped()) {
                    return $event->result;
                }
                $this->User->read(null, $find['User']['id']);
                $this->User->set(['confirmed' => date('Y-m-d H:i:s')]);
                $this->User->save();
                $this->redirect(['action' => 'edit', $user_id]);
            } else {
                throw new NotFoundException();
            }
        } else {
            throw new NotFoundException();
        }
    }

    function admin_edit_ajax()
    {
        $this->autoRender = false;
        $this->response->type('json');
        if (!$this->Permissions->can('MANAGE_USERS')) {
            throw new ForbiddenException();
        }
        if (!$this->request->is('post')) {
            throw new NotFoundException();
        }
        $this->request->data = $this->request->data['xss'];
        $input = $this->request->data;
        if (empty($input['id']) || empty($input['email']) || empty($input['pseudo']) || !isset($input['rank'])) {
            return $this->sendJSON(['statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')]);
        }
        $record = $this->User->findById((int)$input['id']);
        if (!$record) {
            return $this->sendJSON(['statut' => false, 'msg' => $this->Lang->get('USER__EDIT_ERROR_UNKNOWN')]);
        }
        $user = $record['User'];
        $rank = filter_var($input['rank'], FILTER_VALIDATE_INT);
        if ($rank === false || !$this->Permissions->canManageRank($user['rank']) || !$this->Permissions->canManageRank($rank))
            throw new ForbiddenException();
        if ($rank !== (int)$user['rank']) {
            if ($user['id'] == $this->User->getKey('id')) {
                return $this->sendJSON(['statut' => false, 'msg' => $this->Lang->get('USER__EDIT_ERROR_YOURSELF')]);
            }
            if (!in_array($rank, [0, 2, 3, 4], true)) {
                $this->loadModel('Rank');
                if (!$this->Rank->hasAny(['rank_id' => $rank])) {
                    throw new ForbiddenException();
                }
            }
        }
        $pseudo = $input['pseudo'];
        if (!is_string($pseudo) || ($pseudo !== $user['pseudo'] && !preg_match('/^[a-zA-Z0-9_]{2,16}\z/', $pseudo)))
            return $this->sendJSON(['statut' => false, 'msg' => $this->Lang->get('USER__ERROR_PSEUDO_INVALID_FORMAT')]);
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL) && $input['email'] !== $user['email'])
            return $this->sendJSON(['statut' => false, 'msg' => $this->Lang->get('USER__ERROR_EMAIL_NOT_VALID')]);
        $data = [
            'email' => $input['email'],
            'rank' => $rank,
            'pseudo' => $pseudo,
            'uuid' => isset($input['uuid']) ? $input['uuid'] : $user['uuid']
        ];
        $identityFields = [
            'pseudo' => 'USER__ERROR_PSEUDO_ALREADY_REGISTERED',
            'email' => 'USER__ERROR_EMAIL_ALREADY_REGISTERED',
            'uuid' => 'USER__ERROR_UUID_ALREADY_REGISTERED'
        ];
        foreach ($identityFields as $field => $message) {
            $value = $data[$field];
            if ($value === $user[$field] || $value === '' || $value === null)
                continue;
            if ($field === 'uuid') {
                if (!is_string($value) || !preg_match('/^([a-f0-9]{8})-?([a-f0-9]{4})-?([a-f0-9]{4})-?([a-f0-9]{4})-?([a-f0-9]{12})\z/i', $value, $groups))
                    return $this->sendJSON(['statut' => false, 'msg' => $this->Lang->get('USER__ERROR_UUID')]);
                $groups = array_slice($groups, 1);
                $data['uuid'] = strtolower(implode('', $groups));
                $value = [$data['uuid'], strtolower(implode('-', $groups))];
            }
            if ($this->User->hasAny(['id !=' => $user['id'], $field => $value]))
                return $this->sendJSON(['statut' => false, 'msg' => $this->Lang->get($message)]);
        }

        $passwordUpdated = !empty($input['password']);
        if ($passwordUpdated) {
            if (!is_string($input['password'])) {
                return $this->sendJSON(['statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')]);
            }
            $data['password'] = $this->Util->password($input['password'], $user['pseudo']);
            $data['password_hash'] = $this->Util->getPasswordHashType();
        }
        if ($this->EyPlugin->isInstalled('eywek.shop') && isset($input['money'])) {
            $data['money'] = $input['money'];
        }
        $event = new CakeEvent('beforeEditUser', $this, [
            'user_id' => $user['id'],
            'data' => $data,
            'password_updated' => $passwordUpdated
        ]);
        $this->getEventManager()->dispatch($event);
        if ($event->isStopped()) {
            return $event->result;
        }
        $this->User->id = $user['id'];
        if (!$this->User->save($data, true, array_keys($data))) {
            return $this->sendJSON(['statut' => false, 'msg' => $this->Lang->get('ERROR__INTERNAL_ERROR')]);
        }
        $this->History->set('EDIT_USER', 'user');
        $message = $this->Lang->get('USER__EDIT_SUCCESS');
        $this->Session->setFlash($message, 'default.success');
        return $this->sendJSON(['statut' => true, 'msg' => $message]);
    }

    function admin_delete($id = false)
    {
        $this->autoRender = false;
        if ($this->isConnected and $this->Permissions->can('MANAGE_USERS')) {
            if ($id != false) {
                $this->loadModel('User');
                $find = $this->User->find('first', ['conditions' => ['id' => $id]]);
                if (!empty($find)) {
                    if (!$this->Permissions->canManageRank($find['User']['rank']))
                        throw new ForbiddenException();
                    $event = new CakeEvent('beforeDeleteUser', $this, ['user' => $find['User']]);
                    $this->getEventManager()->dispatch($event);
                    if ($event->isStopped()) {
                        return $event->result;
                    }
                    $this->User->delete($id);
                    $this->History->set('DELETE_USER', 'user');
                    $this->Session->setFlash($this->Lang->get('USER__DELETE_SUCCESS'), 'default.success');
                } else {
                    $this->Session->setFlash($this->Lang->get('UNKNONW_ID'), 'default.error');
                }
            }
            $this->redirect(['controller' => 'user', 'action' => 'index', 'admin' => true]);
        } else {
            $this->redirect('/');
        }
    }
}
