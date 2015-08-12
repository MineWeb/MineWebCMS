<?php 
  
$this->Connect = new ConnectComponent;
$this->EyPlugin = new EyPluginComponent;
?>
<!DOCTYPE html>
<html>
<head>
	<title><?= $title_for_layout ?> | Admin</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Eywek">


    <?php echo $this->Html->css('admin/cake.generic.css'); ?>
    <?php echo $this->Html->css('admin/chosen'); ?>
    <?php echo $this->Html->css('admin/bootstrap.min.css'); ?>
    <?php echo $this->Html->css('admin/theme/avocado'); ?>

    <?php echo $this->Html->css('font-awesome.min.css'); ?>
    <?php echo $this->fetch('css'); ?>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,400,600,300' rel='stylesheet' type='text/css'> 
	<style type="text/css">
		body { padding-top: 102px; }
	</style>
	<?php echo $this->Html->css('bootstrap-responsive.css'); ?>

	<?php echo $this->Html->script('admin/jquery.1.9.1.min.js'); ?>

	<?php echo $this->Html->script('admin/jquery.jpanelmenu.min.js'); ?>
	<?php echo $this->Html->script('admin/avocado-custom-predom.js'); ?>
	<?php echo $this->Html->script('admin/chart.js'); ?>

</head>
<body>
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a href="#">
					<button type="button" class="btn btn-navbar mobile-menu">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</a>
				<a class="brand" href="#"><?= $Lang->get('ADMIN_PANEL') ?></a>
				<ul class="nav pull-right">
					<li class="dropdown">
						<a href="<?= $this->Html->url(array('controller' => 'user', 'action' => 'profile', 'admin' => false)); ?>" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-user icon-white"></i> 
							<span class="hidden-phone"><?= $this->Connect->get_pseudo() ?></span>
						</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="breadcrumb clearfix">
			<div class="container">
				<ul class="pull-left">
					<li><a href="<?= $this->Html->url(array('controller' => '/', 'action' => '', 'admin' => false)) ?>"><i class="icon-home"></i> <?= $Lang->get('HOME') ?></a> <span class="divider">/</span></li>
					<li class="active"><a href="<?= $this->Html->url(array('controller' => '', 'action' => 'index', 'admin' => true)) ?>"><i class="icon-align-justify"></i> <?= $Lang->get('ADMIN_PANEL') ?></a><span class="divider">/</span></li>

				</ul>
				<ul class="pull-right">
					<li><a href="<?= $this->Html->url(array('controller' => 'user', 'action' => 'logout', 'admin' => false)); ?>"><i class="icon-off"></i><?= $Lang->get('LOGOUT') ?></a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="navbar navbar-inverse" id="nav">
			<div class="navbar-inner">
				<ul class="nav">
					<li class="active"><a href="<?= $this->Html->url(array('controller' => '', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="icon-home"></i> <?= $Lang->get('HOME') ?></a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-th"></i> <?= $Lang->get('GENERAL') ?> <b class="caret"></b>
						</a>
						<ul class="dropdown-menu">
							<li><a href="<?= $this->Html->url(array('controller' => 'news', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="icon-book"></i> <?= $Lang->get('NEWS') ?></a></li>
							<?php if($this->EyPlugin->is_installed('Shop')) { ?>
								<li><a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'index', 'admin' => true, 'plugin' => 'shop')) ?>"><i class="icon-shopping-cart"></i> <?= $Lang->get('SHOP') ?></a></li>
							<?php } ?>
							<li><a href="<?= $this->Html->url(array('controller' => 'user', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="icon-user"></i> <?= $Lang->get('MEMBERS') ?></a></li>
							<li><a href="<?= $this->Html->url(array('controller' => 'slider', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="icon-resize-horizontal"></i> <?= $Lang->get('SLIDER') ?></a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-terminal"></i> <?= $Lang->get('SERVER') ?> <b class="caret"></b>
						</a>
						<ul class="dropdown-menu">
							<li><a href="<?= $this->Html->url(array('controller' => 'server', 'action' => 'banlist', 'admin' => true, 'plugin' => false)) ?>"><i class="icon-th-list"></i> <?= $Lang->get('BANLIST') ?></a></li>
							<li><a href="<?= $this->Html->url(array('controller' => 'server', 'action' => 'whitelist', 'admin' => true, 'plugin' => false)) ?>"><i class="icon-th-list"></i> <?= $Lang->get('WHITELIST') ?></a></li>
							<li><a href="<?= $this->Html->url(array('controller' => 'server', 'action' => 'online', 'admin' => true, 'plugin' => false)) ?>"><i class="icon-share-alt"></i> <?= $Lang->get('ONLINE_PLAYERS') ?></a></li>
						</ul>
					</li>
					<!-- Main Navigation: UI Elements -->
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-align-justify">
							</i> <?= $Lang->get('CONFIGURATION') ?> <b class="caret"></b>
						</a>
						<ul class="dropdown-menu">
							<li><a href="<?= $this->Html->url(array('controller' => 'configuration', 'admin' => true, 'plugin' => false)) ?>"><i class="icon-info-sign"></i> <?= $Lang->get('SETTINGS') ?></a></li>
							<?php if($this->EyPlugin->is_installed('Vote')) { ?>
								<li><a href="<?= $this->Html->url(array('controller' => 'voter', 'plugin' => 'vote', 'admin' => true, 'plugin' => 'vote')) ?>"><i class="icon-share"></i> <?= $Lang->get('VOTE') ?></a></li>
							<?php } ?>
							<li><a href="<?= $this->Html->url(array('controller' => 'server', 'action' => 'link', 'admin' => true, 'plugin' => false)) ?>"><i class="icon-th-large"></i> <?= $Lang->get('LINK_SERVER') ?></a></li>
						</ul>
					</li>
					<li><a href="<?= $this->Html->url(array('controller' => 'statistics', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="icon-bar-chart"></i> <?= $Lang->get('STATISTICS') ?></a></li>
					<!-- / Main Navigation: UI Elements -->
					<li><a href="<?= $this->Html->url(array('controller' => 'maintenance', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="icon-shield"></i> <?= $Lang->get('MAINTENANCE') ?></a></li>
					<!-- Main Navigation: Components -->
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-reorder"></i> <?= $Lang->get('OTHER') ?> <b class="caret"></b>
						</a>
						<ul class="dropdown-menu">
							<li><a href="<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><?= $Lang->get('PLUGINS') ?></a></li>
							<li><a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><?= $Lang->get('THEMES') ?></a></li>
							<li><a href="<?= $this->Html->url(array('controller' => 'pages', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><?= $Lang->get('PAGES') ?></a></li>
							<li><a href="<?= $this->Html->url(array('controller' => 'navbar', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><?= $Lang->get('NAVBAR') ?></a></li>
							<li><a href="<?= $this->Html->url(array('controller' => 'lang', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="icon-bold"></i> <?= $Lang->get('LANG') ?></a></li>
							<li><a href="<?= $this->Html->url(array('controller' => 'API', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="icon-wrench"></i> <?= $Lang->get('API') ?></a></li>
							<li><a href="<?= $this->Html->url(array('controller' => 'permissions', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="icon-bold"></i> <?= $Lang->get('PERMISSIONS') ?></a></li>
							<?php if(!empty($plugins_need_admin)) { ?>
								<li class="dropdown-submenu">
									<a href="#"><i class="icon-signin"></i> <?= $Lang->get('PLUGINS_ADMINISTRATION') ?></a>
									<ul class="dropdown-menu">
										<?php foreach ($plugins_need_admin as $key => $value) { ?>
											<li><a href="<?= $this->Html->url(array('controller' => $value['slug'], 'action' => 'index', 'admin' => true, 'plugin' => $value['slug'])) ?>"><?= $value['name'] ?></a></li>
										<?php } ?>
									</ul>
								</li>
							<?php } ?>
						</ul>
					</li>
					<li><a href="<?= $this->Html->url(array('controller' => 'update', 'action' => 'index', 'admin' => true, 'plugin' => false)) ?>"><i class="icon-sitemap"></i> <?= $Lang->get('UPDATE') ?></a></li>
				</ul>
			</div>
		</div>
		<?php if($this->params['controller'] == 'admin') { ?>
			<div class="row-fluid">
			<div class="span3 well infobox">
				<i class="icon-6x icon-user"></i>
				<div class="pull-right text-right">
					<?= $Lang->get('REGISTERED_USER') ?><br>
					<b class="huge"><?= $registered_users ?></b><br>
					<span class="caps muted">+ <?= $registered_users_today ?> <?= $Lang->get('TODAY') ?></span>
				</div>
			</div>
			<!-- / Information Boxes: Users Registered -->
			<!-- Information Boxes: Active Users -->
			<div class="span3 well infobox">
				<i class="icon-6x icon-rss"></i>
				<div class="pull-right text-right">
					<?= $Lang->get('NBR_OF_VISITS') ?><br>
					<b class="huge"><?= $count_visits ?></b><br>
					<span class="caps muted">+ <?= $count_visits_today ?> <?= $Lang->get('TODAY') ?></span>
				</div>
			</div>
			<!-- / Information Boxes: Active Users -->
			<?php if($this->EyPlugin->is_installed('Shop')) { ?>
			<!-- Information Boxes: Images -->
			<div class="span3 well infobox">
				<i class="icon-6x icon-shopping-cart"></i>
				<div class="pull-right text-right">
					<?= $Lang->get('PURCHASE') ?><br>
					<b class="huge"><?= $purchase ?></b><br>
					<span class="caps muted">+ <?= $purchase_today ?> <?= $Lang->get('TODAY') ?></span>
				</div>
			</div>
			<!-- / Information Boxes: Images -->
			<?php } ?>
			<!-- Information Boxes: Applications -->
			<div class="span3 well infobox">
				<i class="icon-6x icon-pencil"></i>
				<div class="pull-right text-right">
					<?= $Lang->get('NEWS_WRITTEN') ?><br>
					<b class="huge"><?= $nbr_news ?></b><br>
					<span class="caps muted">
					<?php 
					if($nbr_comments_type == "today") {
						echo '+ ';
					}
					echo $nbr_comments;
					?>
					 <?= $Lang->get('COMMENTS') ?></span>
				</div>
			</div>
		</div>
		<?php } ?>
		<?php if($this->params['controller'] == "update") { ?>
			<?= $Update->available() ?>
		<?php } ?>
		<div id="content">

			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
	</div>
	<footer class="footer">
      <div class="container">
        <?= $Lang->get('FOOTER_ADMIN') ?>
      </div>
	</footer>
	<?= $this->Html->script('admin/all.js') ?>
	<?php echo $this->Html->script('admin/bootstrap/bootstrap.min.js'); ?>
	<?php echo $this->fetch('script'); ?>
</body>
</html>
<?php 
$timestamp_fin = microtime(true);
$difference_ms = $timestamp_fin - TIMESTAMP_DEBUT;
echo '<!-- Exécution du script : ' . $difference_ms . ' secondes. -->'; ?>