<?php
function getAdminNav($section, $plugins_need_admin, $context, $Permissions) {

  foreach ($plugins_need_admin[$section] as $menu) {

    if(is_array($menu) && isset($menu['submenu'])) {

      $submenus = '';
      foreach ($menu['submenu'] as $submenu) {

        if((isset($submenu->permission) && $Permissions->can($submenu->permission)) || (!isset($submenu->permission))) {
          $submenus .= '<li><a href="'.$context->Html->url($submenu->url).'"><i class="fa fa-'.$submenu->icon.'"></i> '.$submenu->name.'</a></li>';
        }

      }

      if(!empty($submenus)) {
        echo '<li class="treeview">';
          echo '<a href="#">';
            echo '<i class="fa fa-'.$menu['icon'].'"></i> <span>'.$menu['name'].'</span> <i class="fa fa-angle-left pull-right"></i>';
          echo '</a>';
          echo '<ul class="treeview-menu">';

            echo $submenus;

          echo '</ul>';
        echo '</li>';
      }

    } else {
      if((isset($menu['permission']) && !empty($menu['permission']) && $Permissions->can($menu['permission'])) || (!isset($menu['permission']) && $Permissions->can('MANAGE_PLUGINS'))) {
        echo '<li>';
          echo '<a href="'.$context->Html->url($menu['slug']).'">';
            echo '<i class="fa fa-'.$menu['icon'].'"></i> '.$menu['name'];
          echo '</a>';
        echo '</li>';
      }
    }

  }
}
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

              <li class="dropdown notifications-menu">
                <a href="#" onclick="notification.markAllAsSeen(1)" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true" id="notification-indicator">
                  <i class="fa fa-bell-o"></i>
                </a>
                <ul class="dropdown-menu">
                  <li>
                    <div id="notification-container" class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto;"></div>
                  </li>
                </ul>
              </li>

              <?= $this->Html->script('notification.js') ?>
              <script type="text/javascript">
              // Notifications
                var notification = new $.Notification({
                  'notification_type': 'admin',
                  'limit': 5,
                  'url': {
                    'get': '<?= $this->Html->url(array('plugin' => false, 'admin' => false, 'controller' => 'notifications', 'action' => 'getAll')) ?>',
                    'clear': '<?= $this->Html->url(array('plugin' => false, 'admin' => false, 'controller' => 'notifications', 'action' => 'clear', 'NOTIF_ID')) ?>',
                    'clearAll': '<?= $this->Html->url(array('plugin' => false, 'admin' => false, 'controller' => 'notifications', 'action' => 'clearAll')) ?>',
                    'markAsSeen': '<?= $this->Html->url(array('plugin' => false, 'admin' => false, 'controller' => 'notifications', 'action' => 'markAsSeen', 'NOTIF_ID')) ?>',
                    'markAllAsSeen': '<?= $this->Html->url(array('plugin' => false, 'admin' => false, 'controller' => 'notifications', 'action' => 'markAllAsSeen')) ?>'
                  },
                  'messages': {
                    'markAsSeen': '<?= $Lang->get('NOTIFICATION__MARK_AS_SEEN') ?>',
                    'notifiedBy': '<?= $Lang->get('NOTIFICATION__NOTIFIED_BY') ?>'
                  },
                  'indicator': {
                    'element': '#notification-indicator',
                    'class': 'label label-warning',
                    'style': {},
                    'defaultContent': '<i class="fa fa-bell-o"></i>'
                  },
                  'list': {
                    'element': '#notification-container',
                    'container': {
                      'type': 'ul',
                      'class': 'menu',
                      'style': 'overflow: hidden; width: 100%;'
                    },
                    'notification': {
                      'type': 'li',
                      'class': '',
                      'style': '',
                      'content':'<a href="#">{CONTENT}</a>',
                      'from': {
                        'type': '',
                        'class': '',
                        'style': '',
                        'content': ''
                      },
                      'seen': {
                        'element': {
                          'style': '',
                          'class': ''
                        },
                        'btn': {
                          'element': '.mark-as-seen',
                          'style': '',
                          'class': 'hidden',
                          'attr': [{'onclick': ''}],
                        }
                      }
                    }
                  }
                });
              </script>

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
                <?php if($Permissions->can('MANAGE_USERS')) { ?>
                  <li><a href="<?= $this->Html->url(array('controller' => 'user', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-users"></i> <?= $Lang->get('USER__USERS') ?></a></li>
                <?php } ?>
                <?php if($Permissions->can('MANAGE_PERMISSIONS')) { ?>
                  <li><a href="<?= $this->Html->url(array('controller' => 'permissions', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-user"></i> <?= $Lang->get('PERMISSIONS__LABEL') ?></a></li>
                <?php } ?>
                <?php if($Permissions->can('MANAGE_CONFIGURATION')) { ?>
                  <li><a href="<?= $this->Html->url(array('controller' => 'configuration', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-cog"></i> <?= $Lang->get('CONFIG__GENERAL_PREFERENCES') ?></a></li>
                <?php } ?>
                <?php if($Permissions->can('MANAGE_NAV')) { ?>
                  <li><a href="<?= $this->Html->url(array('controller' => 'navbar', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-bars"></i> <?= $Lang->get('NAVBAR__TITLE') ?></a></li>
                <?php } ?>
                <?php if($Permissions->can('MANAGE_SLIDER')) { ?>
                  <li><a href="<?= $this->Html->url(array('controller' => 'slider', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-picture-o"></i> <?= $Lang->get('SLIDER__TITLE') ?></a></li>
                <?php } ?>
                <?php getAdminNav('general', $plugins_need_admin, $this, $Permissions) ?>
              </ul>
            </li>

            <li class="treeview">
              <a href="#">
                <i class="fa fa-files-o"></i>
                <span><?= $Lang->get('GLOBAL__CUSTOMIZE') ?></span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <?php if($Permissions->can('MANAGE_NEWS')) { ?>
                  <li><a href="<?= $this->Html->url(array('controller' => 'news', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-pencil"></i> <?= $Lang->get('NEWS__TITLE') ?></a></li>
                <?php } ?>
                <?php if($Permissions->can('MANAGE_PAGE')) { ?>
                  <li><a href="<?= $this->Html->url(array('controller' => 'pages', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-file-text-o"></i> <?= $Lang->get('PAGE__TITLE') ?></a></li>
                <?php } ?>

                <?php getAdminNav('customisation', $plugins_need_admin, $this, $Permissions) ?>

              </ul>
            </li>

            <?php if($Permissions->can('MANAGE_SERVERS')) { ?>
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
                  <?php getAdminNav('server', $plugins_need_admin, $this, $Permissions) ?>
                </ul>
              </li>
            <?php } ?>

            <li class="treeview">
              <a href="#">
                <i class="fa fa-folder-o"></i>
                <span><?= $Lang->get('GLOBAL__ADMIN_OTHER_TITLE') ?></span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <?php if($Permissions->can('MANAGE_PLUGINS')) { ?>
                  <li><a href="<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-plus"></i> <?= $Lang->get('PLUGIN__TITLE') ?></a></li>
                <?php } ?>
                <?php if($Permissions->can('MANAGE_THEMES')) { ?>
                  <li><a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-mobile"></i> <?= $Lang->get('THEME__TITLE') ?></a></li>
                <?php } ?>
                <?php if($Permissions->can('MANAGE_API')) { ?>
                  <li><a href="<?= $this->Html->url(array('controller' => 'API', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-sitemap"></i> <?= $Lang->get('API__LABEL') ?></a></li>
                <?php } ?>
                <?php if($Permissions->can('MANAGE_NOTIFICATIONS')) { ?>
                  <li><a href="<?= $this->Html->url(array('controller' => 'notifications', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-flag"></i> <?= $Lang->get('NOTIFICATION__TITLE') ?></a></li>
                <?php } ?>
                <?php if($Permissions->can('VIEW_WEBSITE_HISTORY')) { ?>
                  <li><a href="<?= $this->Html->url(array('controller' => 'history', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="fa fa-table"></i> <?= $Lang->get('HISTORY__VIEW_GLOBAL') ?></a></li>
                <?php } ?>
                <?php getAdminNav('other', $plugins_need_admin, $this, $Permissions) ?>

                <?php if(!empty($plugins_need_admin['default'])) { ?>
                  <li class="treeview">
                    <a href="#">
                      <i class="fa fa-share"></i> <span><?= $Lang->get('PLUGIN__ADMIN_PAGE') ?></span>
                      <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                      <?php getAdminNav('default', $plugins_need_admin, $this, $Permissions) ?>
                    </ul>
                  </li>
                <?php } ?>
              </ul>
            </li>

            <?php if($Permissions->can('VIEW_STATISTICS')) { ?>
              <li>
                <a href="<?= $this->Html->url(array('controller' => 'statistics', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>">
                  <i class="fa fa-bar-chart-o"></i>
                  <span><?= $Lang->get('STATS__TITLE') ?></span>
                </a>
              </li>
            <?php } ?>

            <?php if($Permissions->can('MANAGE_MAINTENANCE')) { ?>
              <li>
                <a href="<?= $this->Html->url(array('controller' => 'maintenance', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>">
                  <i class="fa fa-hand-paper-o"></i>
                  <span><?= $Lang->get('MAINTENANCE__TITLE') ?></span>
                </a>
              </li>
            <?php } ?>

            <?php if($user['isAdmin']) { ?>
              <li>
                <a href="<?= $this->Html->url(array('controller' => 'update', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>">
                  <i class="fa fa-wrench"></i>
                  <span><?= $Lang->get('GLOBAL__UPDATE') ?></span>
                </a>
              </li>
            <?php } ?>

            <?php if($Permissions->can('USE_ADMIN_HELP')) { ?>
              <li>
                <a href="<?= $this->Html->url(array('controller' => 'help', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>">
                  <i class="fa fa-question"></i>
                  <span><?= $Lang->get('HELP__TITLE') ?></span>
                </a>
              </li>
            <?php } ?>

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
