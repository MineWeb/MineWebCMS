<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Eywek">

    <title><?= (isset($title_for_layout)) ? $title_for_layout : 'Error' ?> - <?= (isset($website_name)) ? $website_name : 'MineWeb' ?></title>

    <?= $this->Html->css('bootstrap.css') ?>
    <?= $this->Html->css('modern-business.css') ?>
    <?= $this->Html->css('animate.css') ?>
    <?= $this->Html->css('font-awesome.min.css') ?>
    <?= $this->Html->css('flat.css') ?>
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,900' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,400,300,700' rel='stylesheet' type='text/css'>
    <?= $this->Html->script('jquery-1.11.0.js') ?>
    <?= $this->Html->script('easy_paginate.js') ?>
    <link rel="icon" type="image/png" href="<?= (isset($theme_config) && isset($theme_config['favicon_url'])) ? $theme_config['favicon_url'] : '' ?>" />

    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body><!-- grey.png -->
  <?php if(isset($Lang)) { ?>
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="mini-navbar mini-navbar-default">
      <div class="container">
        <div class="col-sm-12">
          <?= (isset($banner_server) && $banner_server) ? '<p>'.$banner_server.'</p>' : '<p class="text-center">'.$Lang->get('SERVER__STATUS_OFF').'</p>' ?>
        </div>
      </div>
    </div>
        <div class="container nav-content">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?= $this->Html->url('/') ?>"><?= (isset($website_name)) ? $website_name : 'MineWeb' ?></a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="li-nav">
                        <a href="<?= $this->Html->url('/') ?>"><?= $Lang->get('GLOBAL__HOME') ?></a>
                    </li>
                    <?php
                        if(!empty($nav)) {
                          $i = 0;
                          foreach ($nav as $key => $value) { ?>
                            <?php if(empty($value['Navbar']['submenu'])) { ?>
                              <li class="li-nav<?php if($this->params['controller'] == $value['Navbar']['name']) { ?> actived<?php } ?>">
                                  <a href="<?= $value['Navbar']['url'] ?>"<?= ($value['Navbar']['open_new_tab']) ? ' target="_blank"' : '' ?>>
								  <?php if(!empty($value['Navbar']['icon'])): ?> 
									<i class="fa fa-<?= $value['Navbar']['icon'] ?>"></i>
                                  <?php endif; ?>
                                    <?= $value['Navbar']['name'] ?>
                                  </a>
								  
                              </li>
                            <?php } else { ?>
                              <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?= $value['Navbar']['name'] ?> <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                <?php
                                $submenu = json_decode($value['Navbar']['submenu']);
                                foreach ($submenu as $k => $v) {
                                ?>
                                  <li><a href="<?= rawurldecode($v) ?>"<?= ($value['Navbar']['open_new_tab']) ? ' target="_blank"' : '' ?>><?= rawurldecode(str_replace('+', ' ', $k)) ?></a></li>
                                <?php } ?>
                                </ul>
                              </li>
                            <?php } ?>
                    <?php
                          $i++;
                        }
                      } ?>
                    <li class="button">
                        <div class="btn-group">
                          <?php if(isset($isConnected) && $isConnected) { ?>
                            <button type="button" class="btn btn-success"><?= $user['pseudo'] ?></button>
                          <?php } else { ?>
                            <button type="button" class="btn btn-success"><i class="fa fa-user"></i></button>
                          <?php } ?>
                          <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                            <span class="notification-indicator"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                          </button>
                          <ul class="dropdown-menu" role="menu">
                            <?php if($isConnected) { ?>
                              <li><a href="<?= $this->Html->url(array('controller' => 'profile', 'action' => 'index', 'plugin' => false)) ?>"><?= $Lang->get('USER__PROFILE') ?></a></li>
                              <li style="position:relative;">
                                <a href="#notifications_modal" onclick="notification.markAllAsSeen(2)" data-toggle="modal"><?= $Lang->get('NOTIFICATIONS__LIST') ?></a>
                                <span class="notification-indicator"></span>
                              </li>
                              <?php if($Permissions->can('ACCESS_DASHBOARD')) { ?>
                                <li class="divider"></li>
                                    <li><a href="<?= $this->Html->url(array('controller' => 'admin', 'action' => 'index', 'plugin' => false, 'admin' => true)) ?>"><?= $Lang->get('GLOBAL__ADMIN_PANEL') ?></a></li>
                              <?php } ?>
                              <li class="divider"></li>
                              <li><a href="<?= $this->Html->url(array('controller' => 'user', 'action' => 'logout', 'plugin' => false)) ?>"><?= $Lang->get('USER__LOGOUT') ?></a></li>
                            <?php } else { ?>
                              <li><a href="#" data-toggle="modal" data-target="#login"><?= $Lang->get('USER__LOGIN') ?></a></li>
                              <li><a href="#" data-toggle="modal" data-target="#register"><?= $Lang->get('USER__REGISTER') ?></a></li>
                            <?php } ?>
                          </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
  <?php } ?>
    <div class="nav-hop"></div>
    <?php
    $flash_messages = $this->Session->flash();
    if(!empty($flash_messages)) {
      echo '<div class="container">'.$flash_messages.'</div>';
    } ?>
    <?= $this->fetch('content'); ?>
    <!-- Footer -->
  <?php if(isset($Lang)) { ?>
    <footer style="height: 50px;">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <p><?= $Lang->get('GLOBAL__FOOTER') ?></p>
          </div>
        </div>
      </div>
    </footer>



    <?= $this->element('modals') ?>

    <?= $this->Html->script('jquery-1.11.0.js') ?>
    <?= $this->Html->script('bootstrap.js') ?>

    <?= $this->Html->script('app.js') ?>
    <?= $this->Html->script('form.js') ?>
    <?= $this->Html->script('notification.js') ?>
    <script>
    <?php if($isConnected) { ?>
      // Notifications
        var notification = new $.Notification({
          'url': {
            'get': '<?= $this->Html->url(array('plugin' => false, 'controller' => 'notifications', 'action' => 'getAll')) ?>',
            'clear': '<?= $this->Html->url(array('plugin' => false, 'controller' => 'notifications', 'action' => 'clear', 'NOTIF_ID')) ?>',
            'clearAll': '<?= $this->Html->url(array('plugin' => false, 'controller' => 'notifications', 'action' => 'clearAll')) ?>',
            'markAsSeen': '<?= $this->Html->url(array('plugin' => false, 'controller' => 'notifications', 'action' => 'markAsSeen', 'NOTIF_ID')) ?>',
            'markAllAsSeen': '<?= $this->Html->url(array('plugin' => false, 'controller' => 'notifications', 'action' => 'markAllAsSeen')) ?>'
          },
          'messages': {
            'markAsSeen': '<?= $Lang->get('NOTIFICATION__MARK_AS_SEEN') ?>',
            'notifiedBy': '<?= $Lang->get('NOTIFICATION__NOTIFIED_BY') ?>'
          }
        });
    <?php } ?>

    // Config FORM/APP.JS

    var LIKE_URL = "<?= $this->Html->url(array('controller' => 'news', 'action' => 'like')) ?>";
    var DISLIKE_URL = "<?= $this->Html->url(array('controller' => 'news', 'action' => 'dislike')) ?>";

    var LOADING_MSG = "<?= $Lang->get('GLOBAL__LOADING') ?>";
    var ERROR_MSG = "<?= $Lang->get('GLOBAL__ERROR') ?>";
    var INTERNAL_ERROR_MSG = "<?= $Lang->get('ERROR__INTERNAL_ERROR') ?>";
    var FORBIDDEN_ERROR_MSG = "<?= $Lang->get('ERROR__FORBIDDEN') ?>"
    var SUCCESS_MSG = "<?= $Lang->get('GLOBAL__SUCCESS') ?>";

    var CSRF_TOKEN = "<?= $csrfToken ?>";

    $(".navbar-collapse").css({ maxHeight: ($(window).height() - 130) - $(".navbar-header").height() + "px" });
    </script>

    <?php if(isset($google_analytics) && !empty($google_analytics)) { ?>
      <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', '<?= $google_analytics ?>', 'auto');
        ga('send', 'pageview');
      </script>
    <?php } ?>
    <?= (isset($configuration_end_code)) ? $configuration_end_code : '' ?>
  <?php } ?>
</body>

</html>
