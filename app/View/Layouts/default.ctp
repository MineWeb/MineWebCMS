<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Eywek">

    <title><?= $title_for_layout; ?> - <?= $website_name ?></title>

    <?= $this->Html->css('bootstrap.css') ?>
    <?= $this->Html->css('modern-business.css') ?>
    <?= $this->Html->css('animate.css') ?>
    <?= $this->Html->css('font-awesome.min.css') ?>
    <?= $this->Html->css('timeline.css') ?>
    <?= $this->Html->css('social.css') ?>
	  <?= $this->Html->css('../font-awesome-4.1.0/css/font-awesome.min.css') ?>
	  <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,900' rel='stylesheet' type='text/css'>
	  <?= $this->Html->script('jquery-1.11.0.js') ?>
    <?= $this->Html->script('easy_paginate.js') ?>
    <link rel="icon" type="image/png" href="<?= $theme_config['favicon_url'] ?>" />
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body style="background: url(<?= $theme_config['background_url'] ?>);"><!-- grey.png -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="mini-navbar mini-navbar-dark hidden-xs">
      <div class="container">
        <div class="col-sm-12">
          <?= ($banner_server) ? '<p>'.$banner_server.'</p>' : '<p class="text-center">'.$Lang->get('SERVER_OFF').'</p>' ?>
        </div>
      </div>
    </div>
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?= $this->Html->url('/') ?>"><?= $website_name ?></a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                  <li>
                      <a href="<?= $this->Html->url('/') ?>"><?= $Lang->get('HOME') ?></a>
                  </li>
                  <?php
                    if(!empty($nav)) {
                      $i = 0;
                      foreach ($nav as $key => $value) {
                        if(empty($value['Navbar']['submenu'])) { ?>
                          <li class="li-nav<?= ($this->params['controller'] == $value['Navbar']['name']) ? ' actived' : '' ?>">
                            <a href="<?= $value['Navbar']['url'] ?>"><?= $value['Navbar']['name'] ?></a>
                          </li>
                        <?php } else { ?>
                          <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?= $value['Navbar']['name'] ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                              <?php 
                              $submenu = json_decode($value['Navbar']['submenu']);
                              foreach ($submenu as $k => $v) { ?>
                                <li><a href="<?= rawurldecode($v) ?>"><?= rawurldecode(str_replace('+', ' ', $k)) ?></a></li>
                              <?php } ?>
                            </ul>
                          </li>
                        <?php } ?>
                      <?php 
                        $i++; 
                      }  
                    } 
                    ?>
                    <li>
                      <div class="btn-group">
                        <?php if($isConnected) { ?>
                          <button type="button" class="btn btn-success"><?= $user['pseudo'] ?></button>
                        <?php } else { ?>
                          <button type="button" class="btn btn-success"><i class="fa fa-user"></i></button>
                        <?php } ?>
                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                          <span class="caret"></span>
                          <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                          <?php if($isConnected) { ?>
                            <li><a href="<?= $this->Html->url(array('controller' => 'profile', 'action' => 'index', 'plugin' => false)) ?>"><?= $Lang->get('PROFILE') ?></a></li>
                            <?php if($Permissions->can('ACCESS_DASHBOARD')) { ?>
                              <li class="divider"></li>
                                  <li><a href="<?= $this->Html->url(array('controller' => 'admin', 'action' => 'index', 'plugin' => false, 'admin' => true)) ?>"><?= $Lang->get('ADMIN_PANEL') ?></a></li>
                            <?php } ?>
                            <li class="divider"></li>
                            <li><a href="<?= $this->Html->url(array('controller' => 'user', 'action' => 'logout', 'plugin' => false)) ?>"><?= $Lang->get('LOGOUT') ?></a></li>
                          <?php } else { ?>
                            <li><a href="#" data-toggle="modal" data-target="#login"><?= $Lang->get('LOGIN') ?></a></li>
                            <li><a href="#" data-toggle="modal" data-target="#register"><?= $Lang->get('REGISTER') ?></a></li>
                          <?php } ?>
                        </ul>
                      </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="nav-hop"></div>
    <?= $flash_messages ?>
    <?= $this->fetch('content'); ?>
    <!-- Footer -->

    <div class="container">
      <footer>
        <div class="row">
          <div class="col-lg-12">
            <p><?= $Lang->get('COPYRIGHT') ?></p>
          </div>
        </div>
      </footer>
    </div>
    
    <?= $this->element('modals') ?>

    <?= $this->Html->script('jquery-1.11.0.js') ?>
    <?= $this->Html->script('bootstrap.js') ?>

    <?= $this->Html->script('app.js') ?>
    <script>
    // Config APP.JS

    var LIKE_URL = "<?= $this->Html->url(array('controller' => 'news', 'action' => 'like')) ?>";
    var DISLIKE_URL = "<?= $this->Html->url(array('controller' => 'news', 'action' => 'dislike')) ?>";

    var LOADING_MSG = "<?= $Lang->get('LOADING') ?>";
    var ERROR_MSG = "<?= $Lang->get('ERROR') ?>";
    var INTERNAL_ERROR_MSG = "<?= $Lang->get('ERROR_WHEN_AJAX') ?>";
    var SUCCESS_MSG = "<?= $Lang->get('SUCCESS') ?>";

    var CSRF_TOKEN = "<?= $csrfToken ?>";
    </script>

</body>

</html>