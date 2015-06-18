<?php
Router::connect('/vote', array('controller' => 'voter', 'action' => 'index', 'plugin' => 'vote'));