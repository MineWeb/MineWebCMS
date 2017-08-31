<?php

	if(file_exists(ROOT.'/config/installed.txt') AND file_exists(ROOT.'/config/install.txt')) {

		Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));

		Router::connect('/robots.txt', array('controller' => 'pages', 'action' => 'robots'));

		Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

		Router::connect('/blog', array('controller' => 'news', 'action' => 'blog'));
		Router::connect('/blog/*', array('controller' => 'news', 'action' => 'index'));
		Router::connect('/blog/', array('controller' => 'news', 'action' => 'blog'));

		Router::connect('/p/*', array('controller' => 'pages', 'action' => 'index'));

		Router::connect('/profile', array('controller' => 'user', 'action' => 'profile'));

		Router::connect('/profile/modify', array('controller' => 'user', 'action' => 'modify_profile'));

        Router::connect('/api/:action', array('controller' => 'API'));

		// Admin
		Configure::write('Routing.prefixes', array('admin'));
		Router::connect(
			'/admin',
			array('controller' => 'admin', 'action' => 'index', 'prefix' => 'admin')
		);
		Configure::write('Routing.admin', 'admin');

	} else {

		// if not install
    	Router::connect('/', array('controller' => 'install', 'action' => 'index'));

	}

	// End
	CakePlugin::routes();

	require CAKE . 'Config' . DS . 'routes.php';
