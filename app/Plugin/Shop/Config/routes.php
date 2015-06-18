<?php
Router::connect('/shop/c/*', array('controller' => 'shop', 'action' => 'index', 'plugin' => 'shop'));