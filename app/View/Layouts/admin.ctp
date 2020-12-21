<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $title_for_layout ?> | Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png"
          href="<?= (isset($theme_config) && isset($theme_config['favicon_url'])) ? $theme_config['favicon_url'] : '' ?>"/>
    <!-- Font Awesome 5 -->
    <?= $this->Html->css('fontawesome-5/css/all.css'); ?>
    <!-- Tempusdominus Bbootstrap 4 -->
    <?= $this->Html->css('bootstrap-4/plugins/tempusdominus/tempusdominus-bootstrap-4.min.css'); ?>
    <!-- iCheck -->
    <?= $this->Html->css('bootstrap-4/plugins/icheck/icheck-bootstrap.min.css'); ?>
    <!-- Theme style -->
    <?= $this->Html->css('adminlte-3/adminlte.min.css'); ?>
    <?= $this->Html->css('adminlte-3/plugins/datatables-bs4/dataTables.bootstrap4.min.css') ?>

    <?= $this->Html->css('admin.css'); ?>

    <!-- overlayScrollbars -->
    <?= $this->Html->css('adminlte-3/plugins/overlayScrollbars/OverlayScrollbars.min.css'); ?>
    <!-- Daterange picker -->
    <?= $this->Html->css('adminlte-3/plugins/daterangepicker/daterangepicker.css'); ?>
    <!-- jQuery -->
    <?= $this->Html->script('adminlte-3/plugins/jquery/jquery.min.js') ?>
    <!-- ChartJS -->
    <?= $this->Html->script('chart.js/Chart.min.js') ?>
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-dark navbar-lightblue">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" onclick="notification.markAllAsSeen(1)" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                </a>
                <div id="notification-container" class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#">
                    <span class="hidden-xs"><?= $user['pseudo'] ?></span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   href="<?= $this->Html->url(array('controller' => 'user', 'action' => 'logout', 'admin' => false, 'plugin' => false)); ?>"><i
                            class="fa fa-power-off"></i> <?= $Lang->get('USER__LOGOUT') ?></a>
            </li>
        </ul>

        <?= $this->Html->script('notification.js') ?>
        <script type="text/javascript">
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
                        'content': '<a href="#">{CONTENT}</a>',
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
    </nav>

    <aside class="main-sidebar sidebar-dark-lightblue elevation-4">
        <a href="<?= $this->Html->url('/') ?>" class="brand-link navbar-lightblue text-center text-white">
            <span class="brand-text font-weight-light"><?= $Lang->get('GLOBAL__ADMINISTRATION'); ?></span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column nav-flat nav-child-indent" data-widget="treeview"
                    role="menu"
                    data-accordion="false">


                    <?php

                    function checkCurrent($nav, $context)
                    {
                        foreach ($nav as $name => $value) {
                            if (isset($value['menu']))
                                return checkCurrent($value['menu'], $context);
                            $route = (isset($value['route']) ? $context->Html->url($value['route']) : '#');
                            $current = $route == $context->Html->url(null, false);
                            if ($current == $route)
                                return true;
                        }
                        return false;
                    }

                    function displayNav($nav, $context)
                    {
                        foreach ($nav as $name => $value) {
                            if (!isset($value['menu']) && !isset($value['route']))
                                continue;
                            if (!isset($value['menu']) && isset($value['permission']) && !$context->Permissions->can($value['permission'])) // Check perms
                                continue;
                            $currentMenu = checkCurrent($value['menu'], $context) ? "menu-open" : "";
                            if (isset($value['menu'])) {
                                echo '<li class="nav-item has-treeview ' . ($currentMenu ? "menu-open" : "") . '">';
                            }
                            else
                                echo '<li class="nav-item">';
                            // Link
                            $route = (isset($value['route']) ? $context->Html->url($value['route']) : '#');
                            $current = $route == $context->Html->url(null, false);
                            echo '<a class="nav-link  ' . ($current || $currentMenu  ? " active" : "") . '" href="' . $route . '">';
                            echo '<i class=" ' . (strpos($value['icon'], "fa-") ? $value['icon'] : "fa fa-" . $value['icon']) . ' nav-icon"></i>  <p>' . $context->Lang->get($name);
                            if (isset($value['menu']))
                                echo '<i class="fas fa-angle-left right"></i></p>';
                            else echo '</p>';
                            echo '</a>';
                            if (isset($value['menu'])) {
                                echo '<ul class="nav nav-treeview">';
                                displayNav($value['menu'], $context);
                                echo '</ul>';
                            }
                            echo '</li>';
                        }
                    }

                    displayNav($adminNavbar, (object)['Lang' => $Lang, 'Permissions' => $Permissions, 'Html' => $this->Html]);
                    ?>
                </ul>
            </nav>
        </div>
    </aside>
    <div class="content-wrapper">
        <section class="content-header">
            <?= $Update->available() ?>
            <?= (isset($admin_custom_message['messageHTML'])) ? $admin_custom_message['messageHTML'] : '' ?>
            <?php echo $this->Session->flash(); ?>
        </section>

        <?php echo $this->fetch('content'); ?>
    </div>

    <footer class="main-footer text-center">
        <?= $Lang->get('GLOBAL__FOOTER_ADMIN') ?>
        <p>CakePhP version : <a href="https://cakephp.org/"><?= Configure::version(); ?></a></p>
        Credits <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong>
    </footer>

    <style>
        footer li {
            display: inline;
            padding: 0 2px
        }
    </style>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<?= $this->Html->script('adminlte-3/plugins/jquery/jquery.min.js') ?>
<!-- jQuery UI 1.11.4 -->
<?= $this->Html->script('adminlte-3/plugins/jquery-ui/jquery-ui.min.js') ?>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<?= $this->Html->script('bootstrap-4/bootstrap.bundle.min.js') ?>

<?= $this->Html->script('adminlte-3/plugins/datatables/jquery.dataTables.min.js') ?>
<?= $this->Html->script('adminlte-3/plugins/datatables-bs4/dataTables.bootstrap4.min.js') ?>
<!-- Sparkline -->
<?= $this->Html->script('adminlte-3/plugins/sparklines/sparkline.js') ?>
<!-- jQuery Knob Chart -->
<?= $this->Html->script('adminlte-3/plugins/jquery-knob/jquery.knob.min.js') ?>
<!-- daterangepicker -->
<?= $this->Html->script('bootstrap-4/plugins/moment/moment.min.js') ?>
<!-- Tempusdominus Bootstrap 4 -->
<?= $this->Html->script('bootstrap-4/plugins/tempusdominus/tempusdominus-bootstrap-4.min.js') ?>
<?= $this->Html->script('adminlte-3/plugins/daterangepicker/daterangepicker.js'); ?>

<!-- overlayScrollbars -->
<?= $this->Html->script('adminlte-3/plugins/overlayScrollbars/jquery.overlayScrollbars.min.js'); ?>
<!-- AdminLTE App -->
<?= $this->Html->script('adminlte-3/adminlte.js') ?>

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
