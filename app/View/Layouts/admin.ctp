<?php
$this->EyPlugin = new EyPluginComponent;
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $title_for_layout ?> | Admin</title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <?= $this->Html->css('bootstrap.min.css'); ?>
    <?= $this->Html->css('font-awesome.min.css') ?>
    <?= $this->Html->css('jquery-jvectormap-1.2.2.css'); ?>
    <?= $this->Html->css('AdminLTE.min.css'); ?>
    <?= $this->Html->css('skin-blue.min.css'); ?>

    <?= $this->Html->css('dataTables.bootstrap.css'); ?>

    <!-- jQuery 2.1.4 -->
    <?= $this->Html->script('jQuery-2.1.4.min.js') ?>
    <!-- ChartJS 1.0.1 -->
    <?= $this->Html->script('Chart.min.js') ?>

  <?= $this->Html->script('jquery-ui.min.js') ?>
  </head>
  <body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

      <header class="main-header">
        <a href="<?= $this->Html->url('/') ?>" class="logo">
          <span class="logo-mini">DB</span>
          <span class="logo-lg">Dashboard</span>
        </a>
        <nav class="navbar navbar-static-top" role="navigation">
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>

          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <li class="user user-menu">
                <a href="#">
                  <span class="hidden-xs"><?= $user['pseudo'] ?></span>
                </a>
              </li>
              <li>
                <a href="<?= $this->Html->url(array('controller' => 'user', 'action' => 'logout', 'admin' => false)); ?>"><i class="fa fa-power-off"></i> <?= $Lang->get('LOGOUT') ?></a>
              </li>
            </ul>
          </div>

        </nav>
      </header>
      <aside class="main-sidebar">
        <section class="sidebar">
          <ul class="sidebar-menu">

            <li>
              <a href="<?= $this->Html->url(array('controller' => 'admin', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>">
                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
              </a>
            </li>

            <li class="treeview">
              <a href="#">
                <i class="fa fa-files-o"></i> <span><?= $Lang->get('GENERAL') ?></span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?= $this->Html->url(array('controller' => 'news', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-circle-o"></i> <?= $Lang->get('NEWS') ?></a></li>
                <?php if($this->EyPlugin->isInstalled('eywek.shop.1')) { ?>
                  <li><a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'index', 'admin' => true, 'plugin' => 'shop')) ?>"><i class="fa fa-circle-o"></i> <?= $Lang->get('SHOP') ?></a></li>
                <?php } ?>
                <li><a href="<?= $this->Html->url(array('controller' => 'user', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-circle-o"></i> <?= $Lang->get('MEMBERS') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'slider', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-circle-o"></i> <?= $Lang->get('SLIDER') ?></a></li>
              </ul>
            </li>

            <li class="treeview">
              <a href="#">
                <i class="fa fa-files-o"></i>
                <span><?= $Lang->get('SERVER') ?></span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?= $this->Html->url(array('controller' => 'server', 'action' => 'banlist', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-circle-o"></i> <?= $Lang->get('BANLIST') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'server', 'action' => 'whitelist', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-circle-o"></i> <?= $Lang->get('WHITELIST') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'server', 'action' => 'online', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-circle-o"></i> <?= $Lang->get('ONLINE_PLAYERS') ?></a></li>
              </ul>
            </li>

            <li class="treeview">
              <a href="#">
                <i class="fa fa-files-o"></i>
                <span><?= $Lang->get('CONFIGURATION') ?></span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?= $this->Html->url(array('controller' => 'configuration', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-circle-o"></i> <?= $Lang->get('SETTINGS') ?></a></li>
                <?php if($this->EyPlugin->isInstalled('eywek.vote.2')) { ?>
                  <li><a href="<?= $this->Html->url(array('controller' => 'voter', 'plugin' => 'vote', 'admin' => true, 'plugin' => 'vote')) ?>"><i class="fa fa-circle-o"></i> <?= $Lang->get('VOTE') ?></a></li>
                <?php } ?>
                <li><a href="<?= $this->Html->url(array('controller' => 'server', 'action' => 'link', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-circle-o"></i> <?= $Lang->get('LINK_SERVER') ?></a></li>
              </ul>
            </li>

            <li>
              <a href="<?= $this->Html->url(array('controller' => 'statistics', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>">
                <i class="fa fa-files-o"></i>
                <span><?= $Lang->get('STATISTICS') ?></span>
              </a>
            </li>

            <li>
              <a href="<?= $this->Html->url(array('controller' => 'maintenance', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>">
                <i class="fa fa-files-o"></i>
                <span><?= $Lang->get('MAINTENANCE') ?></span>
              </a>
            </li>

            <li class="treeview">
              <a href="#">
                <i class="fa fa-files-o"></i>
                <span><?= $Lang->get('OTHER') ?></span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-circle-o"></i> <?= $Lang->get('PLUGINS') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-circle-o"></i> <?= $Lang->get('THEMES') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'pages', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-circle-o"></i> <?= $Lang->get('PAGES') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'navbar', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-circle-o"></i> <?= $Lang->get('NAVBAR') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'lang', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-circle-o"></i> <?= $Lang->get('LANG') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'API', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-circle-o"></i> <?= $Lang->get('API') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'permissions', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-circle-o"></i> <?= $Lang->get('PERMISSIONS') ?></a></li>

                <?php if(!empty($plugins_need_admin)) { ?>
                  <li class="treeview">
                    <a href="#">
                      <i class="fa fa-share"></i> <span><?= $Lang->get('PLUGINS_ADMINISTRATION') ?></span>
                      <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                      <?php foreach ($plugins_need_admin as $key => $value) { ?>
                        <li><a href="<?= $this->Html->url(array('controller' => $value['slug'], 'action' => 'index', 'admin' => true, 'plugin' => $value['slug'])) ?>"><i class="fa fa-circle-o"></i> <?= $value['name'] ?></a></li>
                      <?php } ?>
                    </ul>
                  </li>
                <?php } ?>
              </ul>
            </li>

            <li>
              <a href="<?= $this->Html->url(array('controller' => 'update', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>">
                <i class="fa fa-files-o"></i>
                <span><?= $Lang->get('UPDATE') ?></span>
              </a>
            </li>

          </ul>
        </section>
      </aside>

      <div class="content-wrapper">
        <div style="padding: 15px;">
          <?= $Update->available() ?>
          <?php echo $this->Session->flash(); ?>
        </div>

        <?php echo $this->fetch('content'); ?>
      </div>

      <footer class="main-footer text-center">
        <?= $Lang->get('FOOTER_ADMIN') ?>
        Credits <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong>
      </footer>
      <style>
      footer li {
        display:inline;
        padding:0 2px
      }
      </style>

    </div>

    <!-- Bootstrap 3.3.5 -->
    <?= $this->Html->script('bootstrap.min.js') ?>

    <?= $this->Html->script('jquery.dataTables.min.js') ?>
    <?= $this->Html->script('dataTables.bootstrap.min.js') ?>
    <!-- FastClick -->
    <?= $this->Html->script('fastclick.min.js') ?>
    <!-- AdminLTE App -->
    <?= $this->Html->script('app.min.js') ?>
    <!-- Sparkline -->
    <?= $this->Html->script('jquery.sparkline.min.js') ?>
    <!-- jvectormap -->
    <?= $this->Html->script('jquery-jvectormap-1.2.2.min.js') ?>
    <?= $this->Html->script('jquery-jvectormap-world-mill-en.js') ?>
    <!-- SlimScroll 1.3.0 -->
    <?= $this->Html->script('jquery.slimscroll.min.js') ?>

     <?= $this->Html->script('mineweb_admin.js') ?>

    <?= $this->element('mineweb_admin_js'); ?>

  </body>
</html>
