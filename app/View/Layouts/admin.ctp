<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $title_for_layout ?> | Admin</title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" type="image/png" href="<?= (isset($theme_config) && isset($theme_config['favicon_url'])) ? $theme_config['favicon_url'] : '' ?>" />

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
            <span class="logo-mini">PA</span>
            <span class="logo-lg"><?= $Lang->get('GLOBAL__ADMINISTRATION'); ?></span>
        </a>
        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">

                    <li class="dropdown notifications-menu">
                        <a href="#" onclick="notification.markAllAsSeen(1)" class="dropdown-toggle"
                           data-toggle="dropdown" aria-expanded="true" id="notification-indicator">
                            <i class="fa fa-bell-o"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <div id="notification-container" class="slimScrollDiv"
                                     style="position: relative; overflow: hidden; width: auto;"></div>
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

                    <li class="user user-menu">
                        <a href="#">
                            <span class="hidden-xs"><?= $user['pseudo'] ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $this->Html->url(array('controller' => 'user', 'action' => 'logout', 'admin' => false, 'plugin' => false)); ?>"><i
                                    class="fa fa-power-off"></i> <?= $Lang->get('USER__LOGOUT') ?></a>
                    </li>
                </ul>
            </div>

        </nav>
    </header>
    <aside class="main-sidebar">
        <section class="sidebar">
            <ul class="sidebar-menu">
                <?php
                function displayNav($nav, $context) {
                    foreach ($nav as $name => $value) {
                        if (!isset($value['menu']) && !isset($value['route']))
                            continue;
                        if (!isset($value['menu']) && isset($value['permission']) && !$context->Permissions->can($value['permission'])) // Check perms
                            continue;
                        if (isset($value['menu']))
                            echo '<li class="treeview">';
                        else
                            echo '<li>';
                        // Link
                        echo '<a href="' . (isset($value['route']) ? $context->Html->url($value['route']) : '#') . '">';
                        echo '<i class="fa fa-' . $value['icon'] . '"></i><span>' . $context->Lang->get($name) . '</span>';
                        if (isset($value['menu']))
                            echo '<i class="fa fa-angle-left pull-right"></i>';
                        echo '</a>';
                        if (isset($value['menu'])) {
                            echo '<ul class="treeview-menu">';
                                displayNav($value['menu'], $context);
                            echo '</ul>';
                        }
                        echo '</li>';
                    }
                }
                displayNav($adminNavbar, (object)['Lang' => $Lang, 'Permissions' => $Permissions, 'Html' => $this->Html]);
                ?>
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
            display: inline;
            padding: 0 2px
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
