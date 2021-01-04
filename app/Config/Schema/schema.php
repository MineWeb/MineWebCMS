<?php

class AppSchema extends CakeSchema
{

    public $api_configurations = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'],
        'skins' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false],
        'skin_filename' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 150, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'skin_free' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false],
        'skin_width' => ['type' => 'integer', 'null' => true, 'default' => '64', 'unsigned' => false],
        'skin_height' => ['type' => 'integer', 'null' => true, 'default' => '32', 'unsigned' => false],
        'capes' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false],
        'cape_filename' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 150, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'cape_free' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false],
        'cape_width' => ['type' => 'integer', 'null' => true, 'default' => '64', 'unsigned' => false],
        'cape_height' => ['type' => 'integer', 'null' => true, 'default' => '32', 'unsigned' => false],
        'get_premium_skins' => ['type' => 'integer', 'null' => false, 'default' => '1', 'length' => 1, 'unsigned' => false],
        'use_skin_restorer' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false],
        'skin_restorer_server_id' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 8, 'unsigned' => false],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $cake_sessions = [
        'id' => ['type' => 'string', 'null' => false, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1', 'key' => 'primary'],
        'data' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'expires' => ['type' => 'integer', 'null' => true, 'default' => null],
        'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]]
    ];
    public $comments = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'],
        'content' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'user_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false],
        'news_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $configurations = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'],
        'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'email' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'lang' => ['type' => 'string', 'null' => false, 'default' => 'fr', 'length' => 5, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'theme' => ['type' => 'string', 'null' => false, 'default' => 'default', 'length' => 50, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'layout' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'maintenance' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'money_name_singular' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'money_name_plural' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'server_state' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 1, 'unsigned' => false],
        'server_cache' => ['type' => 'integer', 'null' => false, 'default' => 0, 'length' => 1, 'unsigned' => false],
        'server_secretkey' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'server_timeout' => ['type' => 'float', 'null' => false, 'default' => null, 'unsigned' => false],
        'condition' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 250, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'skype' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'youtube' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'twitter' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'facebook' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'banner_server' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'email_send_type' => ['type' => 'integer', 'null' => true, 'default' => '1', 'length' => 1, 'unsigned' => false, 'comment' => '1 = default, 2 = smtp'],
        'smtpHost' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 30, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'smtpUsername' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 150, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'smtpPort' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 5, 'unsigned' => false],
        'smtpPassword' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'google_analytics' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 15, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'end_layout_code' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'check_uuid' => ['type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1, 'unsigned' => false],
        'captcha_type' => ['type' => 'integer', 'null' => true, 'default' => '1', 'length' => 1, 'unsigned' => false, 'comment' => '1 = default, 2 = google, 3 = h-captcha'],
        'captcha_sitekey' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 60, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'captcha_secret' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 60, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'confirm_mail_signup' => ['type' => 'integer', 'null' => false, 'default' => 0, 'length' => 1, 'unsigned' => false],
        'confirm_mail_signup_block' => ['type' => 'integer', 'null' => false, 'default' => 0, 'length' => 1, 'unsigned' => false],
        'member_page_type' => ['type' => 'integer', 'null' => false, 'default' => 0, 'length' => 1, 'unsigned' => false],
        'passwords_hash' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 10, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'passwords_salt' => ['type' => 'integer', 'null' => true, 'default' => 0, 'length' => 1, 'unsigned' => false],
        'forced_updates' => ['type' => 'integer', 'null' => true, 'default' => 1, 'length' => 1, 'unsigned' => false],
        'session_type' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 10, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $histories = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'],
        'action' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'category' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'user_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false],
        'other' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $likes = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'],
        'news_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false],
        'user_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $login_retries = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'],
        'ip' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'count' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'unsigned' => false],
        'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'modified' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $lostpasswords = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'],
        'email' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'key' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 10, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $navbars = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'],
        'order' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 2, 'unsigned' => false],
        'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'icon' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'type' => ['type' => 'integer', 'null' => false, 'default' => '1', 'length' => 1, 'unsigned' => false],
        'url' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 250, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'submenu' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'open_new_tab' => ['type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1, 'unsigned' => false],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $news = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'],
        'title' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'content' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'user_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20],
        'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'img' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'slug' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 150, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'published' => ['type' => 'integer', 'null' => false, 'default' => '1', 'length' => 1, 'unsigned' => false],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $notifications = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'],
        'group' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => 'user', 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'user_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false],
        'from' => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false],
        'content' => ['type' => 'string', 'null' => false, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'type' => ['type' => 'string', 'length' => 5, 'null' => false, 'default' => 'user', 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'seen' => ['type' => 'integer', 'length' => 1, 'null' => false, 'default' => 0, 'unsigned' => false],
        'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $pages = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'],
        'title' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'content' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'slug' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 150, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'user_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20],
        'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'updated' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $permissions = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'],
        'rank' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 1, 'unsigned' => false],
        'permissions' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $plugins = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'],
        'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'author' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'version' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'state' => ['type' => 'integer', 'null' => false, 'default' => '1', 'length' => 1, 'unsigned' => false],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $ranks = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'],
        'rank_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false],
        'name' => ['type' => 'string', 'null' => false, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $seo = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'],
        'title' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 255, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'description' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'favicon_url' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 255, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'img_url' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 255, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'page' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 255, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $servers = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'],
        'name' => ['type' => 'string', 'null' => false, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'ip' => ['type' => 'string', 'null' => false, 'length' => 120, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'port' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 5, 'unsigned' => false],
        'type' => ['type' => 'integer', 'null' => false, 'default' => 0, 'length' => 1, 'unsigned' => false],
        'data' => ['type' => 'string', 'null' => false, 'length' => 120, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $server_cmds = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'],
        'name' => ['type' => 'string', 'null' => false, 'length' => 255, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'server_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'unsigned' => false],
        'cmd' => ['type' => 'string', 'null' => false, 'length' => 255, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $sliders = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'],
        'title' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'subtitle' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'url_img' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $social_buttons = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'],
        'title' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'img' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 120, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'color' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 30, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'url' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 120, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $users = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'],
        'pseudo' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'uuid' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'password' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'password_hash' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'email' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'rank' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 1, 'unsigned' => false],
        'money' => ['type' => 'float', 'null' => false, 'default' => 0, 'unsigned' => false],
        'ip' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'skin' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false],
        'cape' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1, 'unsigned' => false],
        'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'confirmed' => ['type' => 'string', 'length' => 25, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $users__twofactorauth = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'],
        'user_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false],
        'secret' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'enabled' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];
    public $visits = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'unsigned' => false, 'key' => 'primary'],
        'ip' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
        'referer' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'lang' => ['type' => 'string', 'null' => true, 'default' => 'fr', 'length' => 4, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'navigator' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'page' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
        'indexes' => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
    ];

    public function before($event = [])
    {
        return true;
    }

    public function after($event = [], $install = false, $updateContent = [])
    {
        if ($install) {
            App::uses('ClassRegistry', 'Utility');

            /* ******* */
            /*  PERMS  */
            /* ******* */
            App::uses('Permission', 'Model');
            $permission = ClassRegistry::init('Permission');

            $permission->create(); // les permissions du rank de base
            $permission->set([
                'rank' => '0',
                'permissions' => serialize(['COMMENT_NEWS', 'LIKE_NEWS', 'DELETE_HIS_COMMENT', 'EDIT_HIS_EMAIL'])
            ]);
            $permission->save();

            $permission->create(); // les perissions du rank modo
            $permission->set([
                'rank' => '2',
                'permissions' => serialize(['COMMENT_NEWS', 'LIKE_NEWS', 'DELETE_HIS_COMMENT', 'EDIT_HIS_EMAIL'])
            ]);
            $permission->save();

            /* ******* */
            /*   API   */
            /* ******* */
            App::uses('ApiConfiguration', 'Model');
            $api = ClassRegistry::init('ApiConfiguration');

            $api->create(); // la config de base
            $api->set([
                'skins' => 0,
                'skin_filename' => 'skins/{PLAYER}_skin',
                'skin_free' => 0,
                'skin_width' => 64,
                'skin_height' => 32,
                'capes' => 0,
                'cape_filename' => 'skins/capes/{PLAYER}_cape',
                'cape_free' => 0,
                'cape_width' => '64',
                'cape_height' => '32',
                'get_premium_skins' => 1,
                'use_skin_restorer' => 0,
                'skin_restorer_server_id' => 0,
            ]);
            $api->save();

            /* ******* */
            /* CONFIG  */
            /* ******* */
            App::uses('Configuration', 'Model');
            $configuration = ClassRegistry::init('Configuration');

            $configuration->create(); // la config de base
            $configuration->set([
                'name' => 'MineWeb',
                'email' => 'noreply@mineweb.org',
                'lang' => 'fr_FR',
                'theme' => 'default',
                'layout' => 'default',
                'maintenance' => '0',
                'money_name_singular' => 'point',
                'money_name_plural' => 'points',
                'server_state' => 0,
                'server_cache' => 0,
                'server_secretkey' => '',
                'server_timeout' => 1,
                'condition' => null,
                'skype' => 'http://mineweb.org',
                'youtube' => 'http://mineweb.org',
                'twitter' => 'http://mineweb.org',
                'facebook' => 'http://mineweb.org',
                'banner_server' => serialize([]),
                'email_send_type' => '1',
                'smtpHost' => null,
                'smtpUsername' => null,
                'smtpPort' => null,
                'smtpPassword' => null,
                'google_analytics' => null,
                'end_layout_code' => null,
                'check_uuid' => 0,
                'captcha_type' => 1,
                'captcha_sitekey' => null,
                'captcha_secret' => null,
                'confirm_mail_signup' => 0,
                'confirm_mail_signup_block' => 0,
                'member_page_type' => 0,
                'passwords_hash' => 'blowfish',
                'passwords_salt' => 0,
                'forced_updates' => 1,
                'session_type' => 'php'
            ]);

            $configuration->save();
        } else {
            /*
            Exemple :
            $updateContent = array(
                'configuration' => array(
                    array(
                        'create' => array(
                            array('name' => '1', 'name2' => '2', 'name3' => '3'),
                            array('name' => '1bis', 'name2' => '2bis', 'name3' => '3bis')
                        ),
                        'update' => array(
                            array('id' => '19', 'name' => 'new name')
                        )
                    )
                )
            )
            */
            if (!empty($updateContent)) { // si on nous a transmis du contenu à update dans la table

                foreach ($updateContent as $table => $content) { // on parcours les tables

                    App::uses($table, 'Model');
                    $table = ClassRegistry::init($table); // on charge le model

                    foreach ($content as $action => $data) { // on parcours les actions (update/create)

                        if ($action == "create") { // si on doit créé une entrée

                            foreach ($data as $key => $value) { // on parcours les entrées à créé

                                $table->create();
                                $table->set($value);
                                $table->save();

                            }

                        } else if ($action == "update") { // si on doit update une entrée

                            foreach ($data as $key => $value) { // on parcours les entrées à update

                                $table->read(null, $value['id']);
                                unset($value['id']);
                                $table->set($value);
                                $table->save();

                            }

                        }

                    }

                }

            }
        }
    }

}
