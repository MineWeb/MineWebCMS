<?php
App::uses('CakeEventManager', 'Event');
App::uses('BuyEventListener', 'Shop.Event');

CakeEventManager::instance()->attach(new BuyEventListener());