<?php

class MicroSoftController extends AppController
{
    public function auth()
    {

        $this->response->type('json');
        $this->autoRender = false;

        if ($this->isConnected) {
            return $this->redirect($this->referer());
        }

        $microsoft_code = $this->params['url']["code"];
        $microsoft_client_id = $this->Configuration->getKey('microsoft_client_id');
        $microsoft_client_secret = $this->Configuration->getKey('microsoft_client_secret');

        $microsoft_redirect_uri = urlencode(Router::url('/microsoft/auth', true));
        $login_page = $this->EyPlugin->isInstalled('phpierre.signinup') ? '/login' : '/';

        $microsoft_token_url = "https://login.live.com/oauth20_token.srf";

        $xbox_result = json_decode($this->Util->sendHTTPPostRequest($microsoft_token_url, "client_id=" . $microsoft_client_id . "&client_secret=" . $microsoft_client_secret . "&scope=XboxLive.signin%20&code=" . $microsoft_code . "&grant_type=authorization_code&redirect_uri=$microsoft_redirect_uri", ['Content-Type: application/x-www-form-urlencoded']), true);
        $xbox_user_id = $xbox_result["user_id"];
        $xbox_access_token = $xbox_result["access_token"];

        $xbox_refresh_token = $xbox_result["refresh_token"];

        if (!isset($xbox_user_id) || !isset($xbox_access_token) | !isset($xbox_refresh_token))
            return $this->redirect($this->referer());

        $microsoft_result = json_decode($this->Util->sendHTTPPostRequest($microsoft_token_url, "client_id=" . $microsoft_client_id . "&client_secret=" . $microsoft_client_secret . "&scope=User.Read&refresh_token=" . $xbox_refresh_token . "&grant_type=refresh_token&redirect_uri=$microsoft_redirect_uri", ['Content-Type: application/x-www-form-urlencoded']), true);
        $microsoft_access_token = $microsoft_result["access_token"];
        $microsoft_emails = json_decode($this->Util->sendHTTPGetRequest('https://graph.microsoft.com/v1.0/users?$select=identities', ["Authorization: Bearer $microsoft_access_token", 'Content-Type: application/json']), true);
        $microsoft_email = $microsoft_emails["value"][0]["userPrincipalName"];
        if (!isset($microsoft_email))
            return $this->redirect($this->referer());

        $user = $this->User->find('first', ['conditions' => ['microsoft_user_id' => $xbox_user_id]]);
        if (!$user) {

            $xbl_params = [
                "Properties" => [
                    "AuthMethod" => "RPS",
                    "SiteName" => "user.auth.xboxlive.com",
                    "RpsTicket" => "d=$xbox_access_token"
                ],
                "RelyingParty" => "http://auth.xboxlive.com",
                "TokenType" => "JWT"
            ];
            $xbl_result = json_decode($this->Util->sendHTTPPostRequest("https://user.auth.xboxlive.com/user/authenticate", json_encode($xbl_params), ['Content-Type: application/json', 'Accept: application/json']), true);
            $xbl_token = $xbl_result["Token"];
            $xbl_uhs = $xbl_result["DisplayClaims"]["xui"][0]["uhs"];

            $xsts_params = [
                "Properties" => [
                    "SandboxId" => "RETAIL",
                    "UserTokens" => [
                        $xbl_token
                    ]
                ],
                "RelyingParty" => "rp://api.minecraftservices.com/",
                "TokenType" => "JWT"
            ];

            $xsts_result = json_decode($this->Util->sendHTTPPostRequest("https://xsts.auth.xboxlive.com/xsts/authorize", json_encode($xsts_params), ['Content-Type: application/json', 'Accept: application/json']), true);
            $xsts_token = $xsts_result["Token"];
            $mc_params = [
                "identityToken" => "XBL3.0 x=" . $xbl_uhs . ";" . $xsts_token
            ];

            $mc_result = json_decode($this->Util->sendHTTPPostRequest("https://api.minecraftservices.com/authentication/login_with_xbox", json_encode($mc_params), ['Content-Type: application/json', 'Accept: application/json']), true);
            $mc_token = $mc_result["access_token"];

            $mc_profile = json_decode($this->Util->sendHTTPGetRequest("https://api.minecraftservices.com/minecraft/profile", ["Authorization: Bearer $mc_token"]), true);
            $uuid = $mc_profile["id"];
            if (!isset($uuid)) {
                $this->Session->setFlash($this->Lang->get('USER__AUTH_MICROSOFT_DOESNT_HAVE_MINECRAFT'), 'default.error');
                return $this->redirect(Router::url($login_page, true));
            }
            $username = $mc_profile["name"];

            $user_by_pseudo = $this->User->find('first', ['conditions' => ['pseudo' => $username]]);
            if (!empty($user_by_pseudo) && $user_by_pseudo["User"]['uuid'] != $uuid) {
                $this->Session->setFlash($this->Lang->get('USER__AUTH_MICROSOFT_USERNAME_ALREADY_EXIST'), 'default.error');
                return $this->redirect(Router::url($login_page, true));
            }

            $user = $this->User->find('first', ['conditions' => ['uuid' => $uuid]]);
            if ($user) {
                if (isset($user['User']['microsoft_user_id'])) {
                    $this->Session->setFlash($this->Lang->get('USER__AUTH_MICROSOFT_USER_ALREADY_LINK'), 'default.error');
                    return $this->redirect(Router::url($login_page, true));
                }
                $this->User->id = $user['User']['id'];
                if (!$this->User->saveField('microsoft_user_id', $xbox_user_id)) {
                    throw new InternalErrorException();
                }
            } else {

                $data_to_save['pseudo'] = $username;
                $data_to_save['email'] = $microsoft_email;

                $data_to_save['uuid'] = $uuid;

                $data_to_save['password'] = $this->Util->generateRandomString(40);

                $id = $this->User->register(
                    $data_to_save,
                    $this->Util,
                    $this->Configuration->getKey('confirm_mail_signup'),
                    $xbox_user_id
                );
                $user = $this->User->read(null, $id);
            }
        }
        $user = $user["User"];
        $user['microsoft_connection'] = true;
        $login = $this->User->login($user, $user, ($this->Configuration->getKey('confirm_mail_signup') && $this->Configuration->getKey('confirm_mail_signup_block')), $this->Configuration->getKey('check_uuid'), $this);
        if (!isset($login['status']) || !$login['status'] ) {
            $message = $this->Lang->get($login, [
                '{URL_RESEND_EMAIL}' => Router::url(['controller' => 'user', 'action' => 'resend_confirmation'])
            ]);
            $this->Session->setFlash($message, 'default.error');
            return $this->redirect(Router::url($login_page, true));
        }
        $this->setRememberMe($login['session'], true);
        $this->Session->write('user', $user['id']);

        $this->Cookie->write('microsoft_user_id', $xbox_user_id);

        return $this->redirect($this->referer());
    }
}