<?php
Router::connect('/shop/shop/starpass', array('controller' => 'payment', 'action' => 'starpass', 'plugin' => 'shop'));
Router::connect('/shop/shop/starpass/*', array('controller' => 'payment', 'action' => 'starpass', 'plugin' => 'shop'));
Router::connect('/shop/shop/starpass_verif', array('controller' => 'payment', 'action' => 'starpass_verif', 'plugin' => 'shop'));
Router::connect('/shop/starpass', array('controller' => 'payment', 'action' => 'starpass', 'plugin' => 'shop'));
Router::connect('/shop/starpass/*', array('controller' => 'payment', 'action' => 'starpass', 'plugin' => 'shop'));
Router::connect('/shop/starpass_verif', array('controller' => 'payment', 'action' => 'starpass_verif', 'plugin' => 'shop'));

Router::connect('/shop/c/*', array('controller' => 'shop', 'action' => 'index', 'plugin' => 'shop'));
