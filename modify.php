<?php
// Ajout des tables pour la MAJ
$db = ConnectionManager::getDataSource('default');

// 0.3
$verif = $db->query('SELECT * FROM configurations');
if(!array_key_exists('banner_server', $verif[0]['configurations'])) {
	@$query = $db->query('ALTER TABLE `configurations` ADD `banner_server` text DEFAULT NULL;');
	@$query = $db->query('ALTER TABLE `configurations`
	  DROP `server_host`,
	  DROP `server_port`;
	');
}
@$db->query('CREATE TABLE IF NOT EXISTS `servers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT \'\',
  `ip` varchar(20) NOT NULL DEFAULT \'\',
  `port` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;');

// 0.4
$verif_04 = $db->query('SHOW COLUMNS FROM news;');
$execute_04 = true;
foreach ($verif_04 as $k => $v) {
  if($v['COLUMNS']['Field'] == 'published') {
    $execute_04 = false;
    break;
  }
}
if($execute_04) {
  @$query = $db->query('ALTER TABLE `news` ADD `published` int(1) NOT NULL DEFAULT \'1\';');
}

@$db->query('CREATE TABLE IF NOT EXISTS `ranks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rank_id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL DEFAULT \'\',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;');

// 0.5
// aucune maj sur la bdd

// 0.5.1
// aucune maj sur la bdd

// 0.5.2
// aucune maj bdd

// 0.5.3
// aucune maj bdd

// 1.0.0

function add_column($table, $name, $sql) {

  global $db;

  $verif = $db->query('SHOW COLUMNS FROM '.$table.';');
  $execute = true;
  foreach ($verif as $k => $v) {
    if($v['COLUMNS']['Field'] == $name) {
      $execute = false;
      break;
    }
  }
  if($execute) {
    @$query = $db->query('ALTER TABLE `'.$table.'` ADD `'.$name.'` '.$sql.';');
  }
}
function remove_column($table, $name) {

  global $db;

  $verif = $db->query('SHOW COLUMNS FROM '.$table.';');
  $execute = false;
  foreach ($verif as $k => $v) {
    if($v['COLUMNS']['Field'] == $name) {
      $execute = true;
      break;
    }
  }
  if($execute) {
    @$query = $db->query('ALTER TABLE `'.$table.'` DROP COLUMN `'.$name.'`;');
  }
}
$users = array();
function author_to_userid($table) {

  global $db;
  global $users;
  $verif = $db->query('SHOW COLUMNS FROM '.$table.';');
  $execute = false;
  foreach ($verif as $k => $v) {
    if($v['COLUMNS']['Field'] == 'author') {
      $execute = true;
      break;
    }
  }
  if($execute) {

    $data = $db->query('SELECT * FROM '.$table);
    foreach ($data as $key => $value) {

      $table_author_id = $value[$table]['id'];
      $author_name = $value[$table]['author'];

      if(isset($users[$author_name])) {
        $author_id = $users[$author_name];
      } else {
        // on le cherche
        $search_author = $db->query('SELECT id FROM users WHERE pseudo=\''.$author_name.'\'');
        if(!empty($search_author)) {
          $author_id = $users[$author_name] = $search_author[0]['users']['id'];
        } else {
          $author_id = $users[$author_name] = 0;
        }
      }

      // On leur met l'id
      $db->query('UPDATE '.$table.' SET user_id='.$author_id.' WHERE id='.$table_author_id);

      unset($table_author_id);
      unset($author_name);
      unset($author_id);
      unset($search_author);

    }
    unset($data);

  }
}

  // ApiConfiguration

    add_column('api_configurations', 'skin_width', 'int(11) DEFAULT \'64\'');
    add_column('api_configurations', 'skin_height', 'int(11) DEFAULT \'32\'');
    add_column('api_configurations', 'cape_width', 'int(11) DEFAULT \'64\'');
    add_column('api_configurations', 'cape_height', 'int(11) DEFAULT \'32\'');

  // Comments
    // On créer le champ
    add_column('comments', 'user_id', 'int(20) NOT NULL');
    // On les parcours pour récupérer les users
    author_to_userid('comments');

  // Configurations
    add_column('configurations', 'server_cache', 'int(1) NOT NULL DEFAULT \'0\'');
    add_column('configurations', 'email_send_type', "int(1) DEFAULT '1' COMMENT '1 = default, 2 = smtp'");
    add_column('configurations', 'smtpHost', 'varchar(30) DEFAULT NULL');
    add_column('configurations', 'smtpUsername', 'varchar(150) DEFAULT NULL');
    add_column('configurations', 'smtpPort', 'int(5) DEFAULT NULL');
    add_column('configurations', 'smtpPassword', "varchar(100) DEFAULT NULL");
    add_column('configurations', 'google_analytics', "varchar(15) DEFAULT NULL");
    add_column('configurations', 'end_layout_code', "text");
    add_column('configurations', 'captcha_type', "int(11) DEFAULT '1'");
    add_column('configurations', 'captcha_google_sitekey', "varchar(60) DEFAULT NULL");
    add_column('configurations', 'captcha_google_secret', "varchar(60) DEFAULT NULL");
    add_column('configurations', 'confirm_mail_signup', "int(1) NOT NULL DEFAULT '0'");
    add_column('configurations', 'confirm_mail_signup_block', "int(1) DEFAULT '0'");
    add_column('configurations', 'member_page_type', "int(1) NOT NULL DEFAULT '0'");

  // Histories
    add_column('histories', 'user_id', 'int(20) NOT NULL');
    author_to_userid('histories');

  // Likes
    add_column('likes', 'user_id', 'int(20) NOT NULL');
    author_to_userid('likes');

  // Ajout de login_reties
    @$db->query("CREATE TABLE IF NOT EXISTS `login_retries` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `ip` varchar(16) NOT NULL DEFAULT '',
      `count` int(11) NOT NULL,
      `created` datetime NOT NULL,
      `modified` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;")

  // Navbar
    add_column('navbars', 'open_new_tab', "int(1) DEFAULT '0'");

  // News
    add_column('news', 'user_id', 'int(20) NOT NULL');
    author_to_userid('news');

    remove_column('news', 'like');
    remove_column('news', 'comments');

  // Pages
    add_column('pages', 'user_id', 'int(20) NOT NULL');
    author_to_userid('pages');

  // Plugins
    $verif = $db->query('SHOW COLUMNS FROM plugins;');
    $execute = false;
    foreach ($verif as $k => $v) {
      if($v['COLUMNS']['Field'] == 'plugin_id') {
        $execute = true;
        break;
      }
    }
    if($execute) {
      @$db->query('ALTER TABLE `plugins` CHANGE `plugin_id` `apiID` INT(20)  NOT NULL;');
    }
    unset($verif);
    unset($execute);

  // Servers
    add_column('servers', 'type', "int(1) DEFAULT '0'");

  // Ajout de social_buttons
    @$db->query("CREATE TABLE IF NOT EXISTS `social_buttons` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `title` varchar(50) DEFAULT NULL,
      `img` varchar(120) DEFAULT NULL,
      `color` varchar(30) DEFAULT NULL,
      `url` varchar(120) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");

  // Users
    remove_column('users', 'session');
    add_column('users', 'confirmed', "varchar(25) DEFAULT NULL");


// Général
@clearFolder(ROOT.'/app/tmp/cache/models/');
@clearFolder(ROOT.'/app/tmp/cache/persistent/');
?>
