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

    <?= $this->Html->css('admin.css'); ?>

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
                <a href="<?= $this->Html->url(array('controller' => 'user', 'action' => 'logout', 'admin' => false, 'plugin' => false)); ?>"><i class="fa fa-power-off"></i> <?= $Lang->get('USER__LOGOUT') ?></a>
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
                <i class="fa fa-cogs"></i> <span><?= $Lang->get('GLOBAL__ADMIN_GENERAL') ?></span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?= $this->Html->url(array('controller' => 'user', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-users"></i> <?= $Lang->get('USER__USERS') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'permissions', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-user"></i> <?= $Lang->get('PERMISSIONS__LABEL') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'configuration', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-cog"></i> <?= $Lang->get('CONFIG__GENERAL_PREFERENCES') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'navbar', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-bars"></i> <?= $Lang->get('NAVBAR__TITLE') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'slider', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-picture-o"></i> <?= $Lang->get('SLIDER__TITLE') ?></a></li>
                <?php foreach ($plugins_need_admin['general'] as $key => $value) { ?>
                  <li><a href="<?= $this->Html->url($value['slug']) ?>"><i class="fa fa-circle-o"></i> <?= $value['name'] ?></a></li>
                <?php } ?>
              </ul>
            </li>

            <li class="treeview">
              <a href="#">
                <i class="fa fa-files-o"></i>
                <span><?= $Lang->get('GLOBAL__CUSTOMIZE') ?></span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?= $this->Html->url(array('controller' => 'news', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-pencil"></i> <?= $Lang->get('NEWS__TITLE') ?></a></li>
                <?php if($EyPlugin->isInstalled('eywek.shop.1')) { ?>
                  <li class="treeview">
                    <a href="#">
                      <i class="fa fa-shopping-cart"></i> <span><?= $Lang->get('SHOP__TITLE') ?></span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                      <li><a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'index', 'admin' => true, 'plugin' => 'shop')) ?>"><i class="fa fa-shopping-basket"></i> <?= $Lang->get('SHOP__ADMIN_MANAGE_ITEMS') ?></a></li>
                      <li><a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'vouchers', 'admin' => true, 'plugin' => 'shop')) ?>"><i class="fa fa-percent"></i> <?= $Lang->get('SHOP__ADMIN_MANAGE_VOUCHERS') ?></a></li>
                      <li><a href="<?= $this->Html->url(array('controller' => 'payment', 'action' => 'index', 'admin' => true, 'plugin' => 'shop')) ?>"><i class="fa fa-credit-card"></i> <?= $Lang->get('SHOP__ADMIN_MANAGE_PAYMENT') ?></a></li>
                    </ul>
                  </li>
                <?php } ?>
                <?php if($EyPlugin->isInstalled('eywek.vote.3')) { ?>
                  <li><a href="<?= $this->Html->url(array('controller' => 'voter', 'plugin' => 'vote', 'admin' => true, 'plugin' => 'vote')) ?>"><i class="fa fa-external-link"></i> <?= $Lang->get('VOTE__TITLE_ACTION') ?></a></li>
                <?php } ?>
                <li><a href="<?= $this->Html->url(array('controller' => 'pages', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-file-text-o"></i> <?= $Lang->get('PAGE__TITLE') ?></a></li>
                <?php foreach ($plugins_need_admin['customisation'] as $key => $value) { ?>
                  <li><a href="<?= $this->Html->url($value['slug']) ?>"><i class="fa fa-circle-o"></i> <?= $value['name'] ?></a></li>
                <?php } ?>
              </ul>
            </li>

            <li class="treeview">
              <a href="#">
                <i class="fa fa-server"></i>
                <span><?= $Lang->get('SERVER__TITLE') ?></span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?= $this->Html->url(array('controller' => 'server', 'action' => 'link', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-arrows-h"></i> <?= $Lang->get('SERVER__LINK') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'server', 'action' => 'banlist', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-ban"></i> <?= $Lang->get('SERVER__BANLIST') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'server', 'action' => 'whitelist', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-list"></i> <?= $Lang->get('SERVER__WHITELIST') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'server', 'action' => 'online', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-list-ul"></i> <?= $Lang->get('SERVER__ONLINE_PLAYERS') ?></a></li>
                <?php foreach ($plugins_need_admin['server'] as $key => $value) { ?>
                  <li><a href="<?= $this->Html->url($value['slug']) ?>"><i class="fa fa-circle-o"></i> <?= $value['name'] ?></a></li>
                <?php } ?>
              </ul>
            </li>

            <li class="treeview">
              <a href="#">
                <i class="fa fa-folder-o"></i>
                <span><?= $Lang->get('GLOBAL__ADMIN_OTHER_TITLE') ?></span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-plus"></i> <?= $Lang->get('PLUGIN__TITLE') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-mobile"></i> <?= $Lang->get('THEME__TITLE') ?></a></li>
                <li><a href="<?= $this->Html->url(array('controller' => 'API', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-sitemap"></i> <?= $Lang->get('API__LABEL') ?></a></li>
                <?php foreach ($plugins_need_admin['other'] as $key => $value) { ?>
                  <li><a href="<?= $this->Html->url($value['slug']) ?>"><i class="fa fa-circle-o"></i> <?= $value['name'] ?></a></li>
                <?php } ?>

                <?php if(!empty($plugins_need_admin['default'])) { ?>
                  <li class="treeview">
                    <a href="#">
                      <i class="fa fa-share"></i> <span><?= $Lang->get('PLUGIN__ADMIN_PAGE') ?></span>
                      <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                      <?php foreach ($plugins_need_admin['default'] as $key => $value) { ?>
                        <li><a href="<?= $this->Html->url($value['slug']) ?>"><i class="fa fa-circle-o"></i> <?= $value['name'] ?></a></li>
                      <?php } ?>
                    </ul>
                  </li>
                <?php } ?>
              </ul>
            </li>

            <li>
              <a href="<?= $this->Html->url(array('controller' => 'statistics', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>">
                <i class="fa fa-bar-chart-o"></i>
                <span><?= $Lang->get('STATS__TITLE') ?></span>
              </a>
            </li>

            <li>
              <a href="<?= $this->Html->url(array('controller' => 'maintenance', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>">
                <i class="fa fa-hand-paper-o"></i>
                <span><?= $Lang->get('MAINTENANCE__TITLE') ?></span>
              </a>
            </li>

            <li>
              <a href="<?= $this->Html->url(array('controller' => 'update', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>">
                <i class="fa fa-wrench"></i>
                <span><?= $Lang->get('GLOBAL__UPDATE') ?></span>
              </a>
            </li>

            <li>
              <a href="<?= $this->Html->url(array('controller' => 'help', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>">
                <i class="fa fa-question"></i>
                <span><?= $Lang->get('HELP__TITLE') ?></span>
              </a>
            </li>

          </ul>
        </section>
      </aside>

      <div class="content-wrapper">
        <div style="padding: 15px;">
          <?= $Update->available() ?>
          <?= (isset($admin_custom_message['messageHTML'])) ? $admin_custom_message['messageHTML'] : '' ?>
          <?php echo $this->Session->flash(); ?>
        </div>

        <?php echo $this->fetch('content'); ?>
      </div>

      <footer class="main-footer text-center">
        <?= $Lang->get('GLOBAL__FOOTER_ADMIN') ?>
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

     <?= $this->Html->script('form.js') ?>
     <script type="text/javascript">
       var LOADING_MSG = "<?= $Lang->get('GLOBAL__LOADING') ?>";
       var ERROR_MSG = "<?= $Lang->get('GLOBAL__ERROR') ?>";
       var INTERNAL_ERROR_MSG = "<?= $Lang->get('ERROR__INTERNAL_ERROR') ?>";
       var FORBIDDEN_ERROR_MSG = "<?= $Lang->get('ERROR__FORBIDDEN') ?>"
       var SUCCESS_MSG = "<?= $Lang->get('GLOBAL__SUCCESS') ?>";

       var CSRF_TOKEN = "<?= $csrfToken ?>";
     </script>

    <?= $this->element('mineweb_admin_js'); ?>

  </body>
</html>
