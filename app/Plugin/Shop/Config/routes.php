<?php
Router::connect('/shop/starpass/*', array('controller' => 'shop', 'action' => 'starpass', 'plugin' => 'shop', 'admin' => false));
Router::connect('/shop/c/*', array('controller' => 'shop', 'action' => 'index', 'plugin' => 'shop'));