<?php

class ShopController extends ShopAppController {

	public $components = array('Session', 'Shop.DiscountVoucher', 'History');

	function index($category = false) { // Index de la boutique

		$title_for_layout = $this->Lang->get('SHOP__TITLE');
		if($category) {
			$this->set(compact('category'));
		}
		$this->layout = $this->Configuration->getKey('layout'); // On charge le thème configuré
		$this->loadModel('Shop.Item'); // le model des articles
		$this->loadModel('Shop.Category'); // le model des catégories
		$search_items = $this->Item->find('all'); // on cherche tous les items et on envoie à la vue
		$search_categories = $this->Category->find('all'); // on cherche toutes les catégories et on envoie à la vue

		$search_first_category = $this->Category->find('first'); //
		$search_first_category = @$search_first_category['Category']['id']; //

		$this->loadModel('Shop.Paypal');
		$paypal_offers = $this->Paypal->find('all');

		$this->loadModel('Shop.Starpass');;
		$starpass_offers = $this->Starpass->find('all');

		$this->loadModel('Shop.Paysafecard');
		$paysafecard_enabled = $this->Paysafecard->find('all', array('conditions' => array('amount' => '0', 'code' => 'disable', 'author' => 'website', 'created' => '1990/00/00 15:00:00')));
		if(!empty($paysafecard_enabled)) {
			$paysafecard_enabled = false;
		} else {
			$paysafecard_enabled = true;
		}

		$money = 0;
		if($this->isConnected) {
			$money = $this->User->getKey('money') . ' ';
        	$money += ($this->User->getKey('money') == 1 OR $this->User->getKey('money') == 0) ? $this->Configuration->getMoneyName(false) : $this->Configuration->getMoneyName();
        }

        $vouchers = $this->DiscountVoucher;

        $singular_money = $this->Configuration->getMoneyName(false);
        $plural_money = $this->Configuration->getMoneyName();

		$this->set(compact('paysafecard_enabled', 'money', 'starpass_offers', 'paypal_offers', 'search_first_category', 'search_categories', 'search_items', 'title_for_layout', 'vouchers', 'singular_money', 'plural_money'));
	}

	function ajax_get($id) { // Permet d'afficher le contenu du modal avant l'achat (ajax)

		$this->autoRender = false;
		if($this->isConnected AND $this->Permissions->can('CAN_BUY')) { // si l'utilisateur est connecté
			$this->loadModel('Shop.Item'); // je charge le model des articles
			$search_item = $this->Item->find('all', array('conditions' => array('id' => $id))); // je cherche l'article selon l'id
			$money = ($search_item['0']['Item']['price'] == 1) ?  $this->Configuration->getMoneyName(false) : $this->Configuration->getMoneyName();// je dis que la variable $money = le nom de la money au pluriel ou singulier selon le prix
			if(!empty($search_item[0]['Item']['servers'])) {
				$this->loadModel('Server');
				$search_servers_list = $this->Server->find('all');
				foreach ($search_servers_list as $key => $value) {
					$servers_list[$value['Server']['id']] = $value['Server']['name'];
				}
				$search_item[0]['Item']['servers'] = unserialize($search_item[0]['Item']['servers']);
				$servers = '';
				$i = 0;
				foreach ($search_item[0]['Item']['servers'] as $key => $value) {
					$i++;
					$servers = $servers.$servers_list[$value];
					if($i < count($search_item[0]['Item']['servers'])) {
						$servers = $servers.', ';
					}
				}
			}

			$affich_server = (!empty($search_item[0]['Item']['servers'])) ? true : false;


			//On récupére l'element
			if(file_exists(APP.DS.'View'.DS.'Themed'.DS.$this->Configuration->getKey('theme').DS.'Element'.DS.'modal_buy.ctp')) {
				$element_content = file_get_contents(APP.DS.'View'.DS.'Themed'.DS.$this->Configuration->getKey('theme').DS.'Element'.DS.'modal_buy.ctp');
			} else {
				$element_content = file_get_contents($this->EyPlugin->pluginsFolder.DS.'Shop'.DS.'View'.DS.'Element'.DS.'modal_buy.ctp');
			}

			// On remplace les messages de langues

			$i = 0;
			$count = substr_count($element_content, '{LANG-');
			while ($i < $count) {
				$i++;

				$element_explode_for_lang = explode('{LANG-', $element_content);
				$element_explode_for_lang = explode('}', $element_explode_for_lang[1])[0];

				$element_content = str_replace('{LANG-'.$element_explode_for_lang.'}', $this->Lang->get($element_explode_for_lang), $element_content);

			}

			// On remplace les variables
			$servers = (!isset($servers)) ? null : $servers;

			$vars = array(
				'{ITEM_NAME}' => $search_item['0']['Item']['name'],
				'{ITEM_DESCRIPTION}' => nl2br($search_item['0']['Item']['description']),
				'{ITEM_SERVERS}' => $servers,
				'{ITEM_PRICE}' => $search_item['0']['Item']['price'],
				'{SITE_MONEY}' => $money,
				'{ITEM_ID}' => $search_item['0']['Item']['id']
			);
			$element_content = strtr($element_content, $vars);

			// La condition d'affichage de serveur
			$element_explode_for_server = explode('[IF AFFICH_SERVER]', $element_content);
			$element_explode_for_server = explode('[/IF AFFICH_SERVER]', $element_explode_for_server[1])[0];

			$search_server = '[IF AFFICH_SERVER]'.$element_explode_for_server.'[/IF AFFICH_SERVER]';
			$element_content = ($affich_server) ? str_replace($search_server, $element_explode_for_server, $element_content) : str_replace($search_server, '', $element_content);

			echo $element_content;

		} else {
			echo $this->Lang->get('USER__ERROR_MUST_BE_LOGGED'); // si il n'est pas connecté
		}
	}

	function buy_ajax($id) {
		$this->autoRender = false;

		if($this->isConnected AND $this->Permissions->can('CAN_BUY')) {
			$this->loadModel('Shop.Item');
			$search_item = $this->Item->find('all', array('conditions' => array('id' => $id)));
			$search_item['0']['Item']['servers'] = unserialize($search_item['0']['Item']['servers']);
			if(!empty($search_item['0']['Item']['servers'])) {
				foreach ($search_item['0']['Item']['servers'] as $key => $value) {
					$servers_online[] = $this->Server->online($value);
				}
			} else {
				$servers_online = array($this->Server->online());
			}
			if(!in_array(false, $servers_online)) {
				$item_price = $search_item['0']['Item']['price'];
				if(!empty($_GET['code'])) {
					$voucher_reduc = $this->DiscountVoucher->get_new_price($item_price, $search_item['0']['Item']['category'], $search_item['0']['Item']['id'], $_GET['code']);
					$item_price = $voucher_reduc; // j'obtient le nouveau prix si une promotion est en cours sur cet article ou sa catégorie
				}
				if($item_price <= $this->User->getKey('money')) {

					$this->getEventManager()->dispatch(new CakeEvent('onBuy', $this));

					// Ajouter au champ used si il a utiliser un voucher
					if(!empty($_GET['code']) && $voucher_reduc != $search_item['0']['Item']['price']) {
						$this->DiscountVoucher->set_used($this->User->getKey('pseudo'), $_GET['code']);
					}
					//

					$new_sold = $this->User->getKey('money') - $item_price;
					$this->loadModel('User');
					$this->User->read(null, $this->User->getKey('id'));
					$this->User->set(array('money' => $new_sold));
					$this->User->save();
					$this->History->set('BUY_ITEM', 'shop', $search_item['0']['Item']['name']);
					$commands = $search_item['0']['Item']['commands'];
					// executer les commandes
					if(empty($search_item['0']['Item']['servers'])) {

						$this->Server->commands($commands);

					} else {

						foreach ($search_item['0']['Item']['servers'] as $key => $value) {
							$this->Server->commands($commands, $value);
						}

					}

					// si y'a une timed command à faire
					if($search_item[0]['Item']['timedCommand']) {

						// Get le timestamp du server
						$serverTimestamp = $this->Server->call('getServerTimestamp')['getServerTimestamp'];

						$time = ($search_item[0]['Item']['timedCommand_time'] * 60000) + $serverTimestamp; // minutes*60000 = miliseconds + timestamp de base

						$commands = str_replace('{PLAYER}', $this->User->getKey('pseudo'), $search_item[0]['Item']['timedCommand_cmd']);
					    $commands = explode('[{+}]', $commands);
					    $performCommands = array();
					    foreach ($commands as $key => $value) {
					    	$result[] = $this->Server->call(array('performTimedCommand' => $time.':!:'.$value), true);
					    }
					}

					echo '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">'.$this->Lang->get('GLOBAL__CLOSE').'</span></button><strong>'.$this->Lang->get('GLOBAL__SUCCESS').' :</strong> '.$this->Lang->get('SHOP__BUY_SUCCESS').'</div>';
				} else {
					echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">'.$this->Lang->get('GLOBAL__CLOSE').'</span></button><strong>'.$this->Lang->get('GLOBAL__ERROR').' :</strong> '.$this->Lang->get('SHOP__BUY_ERROR_NO_ENOUGH_MONEY').'</div>';
				}
			} else {
					echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">'.$this->Lang->get('GLOBAL__CLOSE').'</span></button><strong>'.$this->Lang->get('GLOBAL__ERROR').' :</strong> '.$this->Lang->get('SERVER__MUST_BE_ON').'</div>';
			}
		} else {
			echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">'.$this->Lang->get('GLOBAL__CLOSE').'</span></button><strong>'.$this->Lang->get('GLOBAL__ERROR').' :</strong> '.$this->Lang->get('USER__ERROR_MUST_BE_LOGGED').'</div>';
		}
	}

	public function admin_index() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->set('title_for_layout',$this->Lang->get('SHOP__TITLE'));
			$this->layout = 'admin';
			$this->loadModel('Shop.Item');
			$search_items = $this->Item->find('all');
			$this->set(compact('search_items'));
			$this->loadModel('Shop.Category');
			$search_categories = $this->Category->find('all');
			foreach ($search_categories as $v) {
				$categories[$v['Category']['id']]['name'] = $v['Category']['name'];
			}
			$this->set(compact('categories'));
			$this->set(compact('search_categories'));

			$this->loadModel('Shop.Paysafecard');
			$psc = $this->Paysafecard->find('all', array('conditions' => array('amount !=' => '0', 'code !=' => 'disable', 'author !=' => 'website', 'created !=' => '1990/00/00 15:00:00')));
			$this->set(compact('psc'));

			$paysafecard_enabled = $this->Paysafecard->find('all', array('conditions' => array('amount' => '0', 'code' => 'disable', 'author' => 'website', 'created' => '1990/00/00 15:00:00')));
			if(!empty($paysafecard_enabled)) {
				$paysafecard_enabled = false;
			} else {
				$paysafecard_enabled = true;
			}
			$this->set(compact('paysafecard_enabled'));

			$this->loadModel('Shop.Voucher');
			$vouchers = $this->Voucher->find('all');
			$this->set(compact('vouchers'));

			$this->loadModel('Shop.Paypal');
			$paypal_offers = $this->Paypal->find('all');
			$this->set(compact('paypal_offers'));

			$this->loadModel('Shop.Starpass');
			$starpass_offers = $this->Starpass->find('all');
			$this->set(compact('starpass_offers'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_edit($id = false) {
		if($this->isConnected AND $this->User->isAdmin()) {
			if($id != false) {

				$this->set('title_for_layout', $this->Lang->get('EDIT_ITEM'));
				$this->layout = 'admin';
				$this->loadModel('Shop.Item');
				$item = $this->Item->find('all', array('conditions' => array('id' => $id)));
				if(!empty($item)) {
					$item = $item[0]['Item'];
					$this->loadModel('Shop.Category');
					$item['category'] = $this->Category->find('all', array('conditions' => array('id' => $item['category'])));
					$item['category'] = $item['category'][0]['Category']['name'];
					$this->set(compact('item'));

					$search_categories = $this->Category->find('all', array('fields' => 'name'));
					foreach ($search_categories as $v) {
						if($v['Category']['name'] != $item['category']) {
							$categories[$v['Category']['name']] = $v['Category']['name'];
						}
					}
					$this->set(compact('categories'));

					$this->loadModel('Server');

					$servers = $this->Server->findSelectableServers(true);
					$this->set(compact('servers'));

					if(!empty($item['servers'])) {
						$item['servers'] = unserialize($item['servers']);
						foreach ($item['servers'] as $key => $value) {
							if(isset($servers[$value])) {
								$selected_server[] = $value;
							}
						}
					} else {
						$selected_server = array();
					}
					$this->set(compact('selected_server'));

				} else {
					$this->Session->setFlash($this->Lang->get('UNKNONW_ID'), 'default.error');
					$this->redirect(array('controller' => 'news', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'news', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_edit_ajax() {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {
			if($this->request->is('post')) {
				if(empty($this->request->data['category'])) {
					$this->request->data['category'] = $this->request->data['category_default'];
				}
				if(!empty($this->request->data['id']) AND !empty($this->request->data['name']) AND !empty($this->request->data['description']) AND !empty($this->request->data['category']) AND !empty($this->request->data['price']) AND !empty($this->request->data['servers']) AND !empty($this->request->data['commands']) AND !empty($this->request->data['timedCommand'])) {
					$this->loadModel('Shop.Category');
					$this->request->data['category'] = $this->Category->find('all', array('conditions' => array('name' => $this->request->data['category'])));
					$this->request->data['category'] = $this->request->data['category'][0]['Category']['id'];
					$this->request->data['timedCommand'] = ($this->request->data['timedCommand'] == 'true') ? 1 : 0;
					if(!$this->request->data['timedCommand']) {
						$this->request->data['timedCommand_cmd'] = NULL;
						$this->request->data['timedCommand_time'] = NULL;
					}
					$this->loadModel('Shop.Item');
					$this->Item->read(null, $this->request->data['id']);
					$this->Item->set(array(
						'name' => $this->request->data['name'],
						'description' => $this->request->data['description'],
						'category' => $this->request->data['category'],
						'price' => $this->request->data['price'],
						'servers' => serialize($this->request->data['servers']),
						'commands' => $this->request->data['commands'],
						'img_url' => $this->request->data['img_url'],
						'timedCommand' => $this->request->data['timedCommand'],
						'timedCommand_cmd' => $this->request->data['timedCommand_cmd'],
						'timedCommand_time' => $this->request->data['timedCommand_time']
						));
					$this->Item->save();
					$this->Session->setFlash($this->Lang->get('ITEM_SUCCESS_EDIT'), 'default.success');
					echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('ITEM_SUCCESS_EDIT')));
				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')));
				}
			} else {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST')));
			}
		} else {
			throw new ForbiddenException();
		}
	}

	public function admin_add_item() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->set('title_for_layout', $this->Lang->get('ADD_ITEM'));
			$this->layout = 'admin';
			$this->loadModel('Shop.Category');
			$search_categories = $this->Category->find('all', array('fields' => 'name'));
			foreach ($search_categories as $v) {
				$categories[$v['Category']['name']] = $v['Category']['name'];
			}
			$this->set(compact('categories'));

			$this->loadModel('Server');
			$servers = $this->Server->findSelectableServers(true);
			$this->set(compact('servers'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add_item_ajax() {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {
			if($this->request->is('post')) {
				if(!empty($this->request->data['name']) AND !empty($this->request->data['description']) AND !empty($this->request->data['category']) AND !empty($this->request->data['price']) AND !empty($this->request->data['servers']) AND !empty($this->request->data['commands']) AND !empty($this->request->data['timedCommand'])) {
					$this->loadModel('Shop.Category');
					$this->request->data['category'] = $this->Category->find('all', array('conditions' => array('name' => $this->request->data['category'])));
					$this->request->data['category'] = $this->request->data['category'][0]['Category']['id'];
					$this->request->data['timedCommand'] = ($this->request->data['timedCommand'] == 'true') ? 1 : 0;
					if(!$this->request->data['timedCommand']) {
						$this->request->data['timedCommand_cmd'] = NULL;
						$this->request->data['timedCommand_time'] = NULL;
					}
					$this->loadModel('Shop.Item');
					$this->Item->read(null, null);
					$this->Item->set(array(
						'name' => $this->request->data['name'],
						'description' => $this->request->data['description'],
						'category' => $this->request->data['category'],
						'price' => $this->request->data['price'],
						'servers' => serialize($this->request->data['servers']),
						'commands' => $this->request->data['commands'],
						'img_url' => $this->request->data['img_url'],
						'timedCommand' => $this->request->data['timedCommand'],
						'timedCommand_cmd' => $this->request->data['timedCommand_cmd'],
						'timedCommand_time' => $this->request->data['timedCommand_time']
						));
					$this->Item->save();
					$this->History->set('ADD_ITEM', 'shop');
					$this->Session->setFlash($this->Lang->get('ITEM_SUCCESS_ADD'), 'default.success');
					echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('ITEM_SUCCESS_ADD')));
				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')));
				}
			} else {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST')));
			}
		} else {
			throw new ForbiddenException();
		}
	}

	public function admin_add_category() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->layout = 'admin';
			$this->set('title_for_layout', $this->Lang->get('ADD_CATEGORY'));
			if($this->request->is('post')) {
				if(!empty($this->request->data['name'])) {
					$this->loadModel('Shop.Category');
					$this->Category->read(null, null);
					$this->Category->set(array(
						'name' => $this->request->data['name'],
					));
					$this->History->set('ADD_CATEGORY', 'shop');
					$this->Category->save();
					$this->Session->setFlash($this->Lang->get('CATEGORY_SUCCESS_ADD'), 'default.success');
					$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
				} else {
					$this->Session->setFlash($this->Lang->get('ERROR__FILL_ALL_FIELDS'), 'default.error');
				}
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_delete($type = false, $id = false) {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {
			if($type != false AND $id != false) {
				if($type == "item") {
					$this->loadModel('Shop.Item');
					$find = $this->Item->find('all', array('conditions' => array('id' => $id)));
					if(!empty($find)) {
						$this->Item->delete($id);
						$this->History->set('DELETE_ITEM', 'shop');
						$this->Session->setFlash($this->Lang->get('DELETE_ITEM_SUCCESS'), 'default.success');
						$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
					} else {
						$this->Session->setFlash($this->Lang->get('UNKNONW_ID'), 'default.error');
						$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
					}
				} elseif($type == "category") {
					$this->loadModel('Shop.Category');
					$find = $this->Category->find('all', array('conditions' => array('id' => $id)));
					if(!empty($find)) {
						$this->Category->delete($id);
						$this->History->set('DELETE_CATEGORY', 'shop');
						$this->Session->setFlash($this->Lang->get('DELETE_CATEGORY_SUCCESS'), 'default.success');
						$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
					} else {
						$this->Session->setFlash($this->Lang->get('UNKNONW_ID'), 'default.error');
						$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
					}
				} elseif($type == "paypal") {
					$this->loadModel('Shop.Paypal');
					$find = $this->Paypal->find('all', array('conditions' => array('id' => $id)));
					if(!empty($find)) {
						$this->Paypal->delete($id);
						$this->History->set('DELETE_PAYPAL_OFFER', 'shop');
						$this->Session->setFlash($this->Lang->get('DELETE_PAYPAL_OFFER_SUCCESS'), 'default.success');
						$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
					} else {
						$this->Session->setFlash($this->Lang->get('UNKNONW_ID'), 'default.error');
						$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
					}
				} elseif($type == "starpass") {
					$this->loadModel('Shop.Starpass');
					$find = $this->Starpass->find('all', array('conditions' => array('id' => $id)));
					if(!empty($find)) {
						$this->Starpass->delete($id);
						$this->History->set('DELETE_STARPASS_OFFER', 'shop');
						$this->Session->setFlash($this->Lang->get('DELETE_STARPASS_OFFER_SUCCESS'), 'default.success');
						$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
					} else {
						$this->Session->setFlash($this->Lang->get('UNKNONW_ID'), 'default.error');
						$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
					}
				}
			} else {
				$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_toggle_paysafecard() {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {
			$this->loadModel('Shop.Paysafecard');

			$paysafecard_enabled = $this->Paysafecard->find('all', array('conditions' => array('amount' => '0', 'code' => 'disable', 'author' => 'website', 'created' => '1990/00/00 15:00:00')));
			if(!empty($paysafecard_enabled)) {
				$this->Paysafecard->delete($paysafecard_enabled[0]['Paysafecard']['id']);

				$this->History->set('ENABLE_PAYSAFECARD', 'shop');

				$this->Session->setFlash($this->Lang->get('SHOP__PAYSAFECARD_ENABLE_SUCCESS'), 'default.success');
				$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
			} else {
				$this->Paysafecard->read(null, $paysafecard_enabled[0]['Paysafecard']['id']);
				$this->Paysafecard->set(array('amount' => '0', 'code' => 'disable', 'author' => 'website', 'created' => '1990/00/00 15:00:00'));
				$this->Paysafecard->save();

				$this->History->set('DISABLE_PAYSAFECARD', 'shop');

				$this->Session->setFlash($this->Lang->get('SHOP__PAYSAFECARD_DISABLE_SUCCESS'), 'default.success');
				$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
			}

		} else {
			$this->redirect('/');
		}
	}

	public function admin_paysafecard_valid($id = false, $money = false) {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {
			if($id != false AND $money != false) {
				$this->loadModel('Shop.Paysafecard');
				$search = $this->Paysafecard->find('all', array('conditions' => array('id' => $id)));
				if(!empty($search)) {
					$this->History->set('BUY_MONEY', 'shop', 'paysafecard|'.$money.'|'.$search['0']['Paysafecard']['amount']);
					$this->Paysafecard->delete($id);
					$this->loadModel('User');
					$user = $this->User->find('all', array('conditions' => array('pseudo' => $search['0']['Paysafecard']['author'])));
					$user_id = $user[0]['User']['id'];
					$new_money = $user[0]['User']['money'] + intval($money);
					$this->User->read(null, $user_id);
					$this->User->set(array(
						'money' => $new_money
					));
					$this->User->save();
					$this->loadModel('Shop.PaysafecardMessage');
					$this->PaysafecardMessage->read(null, null);
					$this->PaysafecardMessage->set(array(
						'to' => $search['0']['Paysafecard']['author'],
						'type' => 1,
						'amount' => $search['0']['Paysafecard']['amount'],
						'added_points' => intval($money)
					));
					$this->PaysafecardMessage->save();

					$this->History->set('VALID_PAYSAFECARD', 'shop');

					$this->Session->setFlash($this->Lang->get('SHOP__PAYSAFECARD_VALID_SUCCESS'), 'default.success');
					$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
				} else {
					$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_paysafecard_invalid($id = false) {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {
			if($id != false) {
				$this->loadModel('Shop.Paysafecard');
				$search = $this->Paysafecard->find('all', array('conditions' => array('id' => $id)));
				if(!empty($search)) {
					$this->Paysafecard->delete($id);
					$this->loadModel('Shop.PaysafecardMessage');
					$this->PaysafecardMessage->read(null, null);
					$this->PaysafecardMessage->set(array(
						'to' => $search['0']['Paysafecard']['author'],
						'type' => 0,
						'amount' => $search['0']['Paysafecard']['amount'],
						'added_points' => 0
					));
					$this->PaysafecardMessage->save();

					$this->History->set('INVALID_PAYSAFECARD', 'shop');

					$this->Session->setFlash($this->Lang->get('SHOP__PAYSAFECARD_INVALID_SUCCESS'), 'default.success');
					$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
				} else {
					$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	public function paysafecard() {
		 $this->autoRender = false;
		if($this->isConnected AND $this->Permissions->can('CREDIT_ACCOUNT')) {
			if($this->request->is('post')) {
				if(!empty($this->request->data['amount']) AND !empty($this->request->data['code1']) AND !empty($this->request->data['code2']) AND !empty($this->request->data['code3']) AND !empty($this->request->data['code4'])) {
					$this->request->data['amount'] = intval($this->request->data['amount']);
					if($this->request->data['amount'] > 0) {
						if(strlen($this->request->data['code1']) == 4 AND strlen($this->request->data['code2']) == 4 AND strlen($this->request->data['code3']) == 4 AND strlen($this->request->data['code4']) == 4) {
							// faire des vérifications (interdiction d'avoir entré plus de 2 PSC)
							$codes = $this->request->data['code1'].' '.$this->request->data['code2'].' '.$this->request->data['code3'].' '.$this->request->data['code4'];

							$this->loadModel('Shop.Paysafecard');
							$search = $this->Paysafecard->find('first', array('conditions' => array('code' => $codes)));
							if(empty($search)) {
								$search2 = $this->Paysafecard->find('count', array('conditions' => array('author' => $this->User->getKey('pseudo'))));
								if($search2 < 2) {
									$this->Paysafecard->read(null, null);
									$this->Paysafecard->set(array(
										'amount' => $this->request->data['amount'],
										'code' => $codes,
										'author' => $this->User->getKey('pseudo')
									));
									$this->Paysafecard->save();
									$this->History->set('ADD_PAYSAFECARD', 'credit_shop');
									echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('SHOP__PAYSAFECARD_ADD_SUCCESS')));
								} else {
									echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('SHOP__PAYSAFECARD_ERROR_ALREADY_TOO_PSC_IN_DB')));
								}
							} else {
								echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('SHOP__PAYSAFECARD_ERROR_ALREADY_IN_DB')));
							}
						}  else {
							echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('NOT_4_CHARACTER')));
						}
					}  else {
						echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('NOT_NUMBER')));
					}
				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')));
				}
			} else {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST')));
			}
		} else {
			throw new InternalErrorException();
		}
	}

	public function admin_add_paypal() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->set('title_for_layout', $this->Lang->get('ADD_OFFER_PAYPAL'));
			$this->layout = 'admin';
		} else {
			$this->redirect('/');
		}
	}

	public function admin_edit_paypal($id = false) {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->set('title_for_layout', $this->Lang->get('EDIT_OFFER_PAYPAL'));
			$this->layout = 'admin';
			if($id != false) {
				$this->loadModel('Shop.Paypal');
				$search = $this->Paypal->find('all', array('conditions' => array('id' => $id)));
				if(!empty($search)) {
					$this->set(compact('id'));
					$this->set('paypal', $search[0]['Paypal']);
				} else {
					$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add_starpass() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->set('title_for_layout', $this->Lang->get('ADD_OFFER_STARPASS'));
			$this->layout = 'admin';
		} else {
			$this->redirect('/');
		}
	}

	public function admin_edit_starpass($id = false) {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->set('title_for_layout', $this->Lang->get('EDIT_OFFER_STARPASS'));
			$this->layout = 'admin';
			if($id != false) {
				$this->loadModel('Shop.Starpass');
				$search = $this->Starpass->find('all', array('conditions' => array('id' => $id)));
				if(!empty($search)) {
					$this->set(compact('id'));
					$this->set('starpass', $search[0]['Starpass']);
				} else {
					$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
			}
		}	 else {
			$this->redirect('/');
		}
	}

	public function admin_add_paypal_ajax() {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {
			if($this->request->is('ajax')) {
				if(!empty($this->request->data['name']) AND !empty($this->request->data['email']) AND !empty($this->request->data['price']) AND !empty($this->request->data['money'])) {
					$this->request->data['price'] = number_format($this->request->data['price'], 2, '.', '');
					$this->request->data['money'] = number_format($this->request->data['money'], 2, '.', '');
					if(filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL)) {
						$this->loadModel('Shop.Paypal');
						$this->Paypal->read(null, null);
						$this->Paypal->set($this->request->data);
						$this->Paypal->save();
						$this->History->set('ADD_PAYPAL_OFFER', 'shop');
						$this->Session->setFlash($this->Lang->get('ADD_PAYPAL_OFFER_SUCCESS'), 'default.success');
						echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ADD_PAYPAL_OFFER_SUCCESS')));
					} else {
						echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER__ERROR_EMAIL_NOT_VALID')));
					}
				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')));
				}
			} else {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST')));
			}
		} else {
			throw new ForbiddenException();
		}
	}

	public function admin_edit_paypal_ajax($id = false) {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {
			if($id != false) {
				$this->loadModel('Shop.Paypal');
				$search = $this->Paypal->find('all', array('conditions' => array('id' => $id)));
				if(!empty($search)) {
					if($this->request->is('ajax')) {
						if(!empty($this->request->data['name']) AND !empty($this->request->data['email']) AND !empty($this->request->data['price']) AND !empty($this->request->data['money'])) {
							$this->request->data['price'] = number_format($this->request->data['price'], 2, '.', '');
							$this->request->data['money'] = number_format($this->request->data['money'], 2, '.', '');
							if(filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL)) {
								$this->loadModel('Shop.Paypal');
								$this->Paypal->read(null, $id);
								$this->Paypal->set($this->request->data);
								$this->Paypal->save();
								$this->History->set('EDIT_PAYPAL_OFFER', 'shop');
								$this->Session->setFlash($this->Lang->get('EDIT_PAYPAL_OFFER_SUCCESS'), 'default.success');
								echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('EDIT_PAYPAL_OFFER_SUCCESS')));
							} else {
								echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('USER__ERROR_EMAIL_NOT_VALID')));
							}
						} else {
							echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')));
						}
					} else {
						echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST')));
					}
				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('UNKNONW_ID')));
				}
			} else {
				throw new NotFoundException();
			}
		} else {
			throw new ForbiddenException();
		}
	}

	public function admin_add_starpass_ajax() {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {
			if($this->request->is('ajax')) {
				if(!empty($this->request->data['name']) AND !empty($this->request->data['idd']) AND !empty($this->request->data['idp']) AND !empty($this->request->data['money'])) {
					$this->request->data['money'] = intval($this->request->data['money']);
					$this->request->data['idd'] = intval($this->request->data['idd']);
					$this->request->data['idp'] = intval($this->request->data['idp']);
					$this->loadModel('Shop.Starpass');
					$this->Starpass->read(null, null);
					$this->Starpass->set($this->request->data);
					$this->Starpass->save();
					$this->History->set('ADD_STARPASS_OFFER', 'shop');
					$this->Session->setFlash($this->Lang->get('ADD_STARPASS_OFFER_SUCCESS'), 'default.success');
					echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('ADD_STARPASS_OFFER_SUCCESS')));
				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')));
				}
			} else {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST')));
			}
		} else {
			throw new ForbiddenException();
		}
	}

	public function admin_edit_starpass_ajax($id = false) {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {
			if($id != false) {
				if($this->request->is('ajax')) {
					if(!empty($this->request->data['name']) AND !empty($this->request->data['idd']) AND !empty($this->request->data['idp']) AND !empty($this->request->data['money'])) {
						$this->request->data['money'] = intval($this->request->data['money']);
						$this->request->data['idd'] = intval($this->request->data['idd']);
						$this->request->data['idp'] = intval($this->request->data['idp']);
						$this->loadModel('Shop.Starpass');
						$this->Starpass->read(null, $id);
						$this->Starpass->set($this->request->data);
						$this->Starpass->save();
						$this->History->set('EDIT_STARPASS_OFFER', 'shop');
						$this->Session->setFlash($this->Lang->get('EDIT_STARPASS_OFFER_SUCCESS'), 'default.success');
						echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('EDIT_STARPASS_OFFER_SUCCESS')));
					} else {
						echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')));
					}
				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST')));
				}
			} else {
				throw new NotFoundException();
			}
		}	 else {
			throw new ForbiddenException();
		}
	}

	public function admin_add_voucher() {
		if($this->isConnected AND $this->User->isAdmin()) {

			$this->set('title_for_layout', $this->Lang->get('ADD_VOUCHER'));
			$this->layout = 'admin';

			$this->loadModel('Shop.Category');
			$search_categories = $this->Category->find('all', array('fields' => array('name', 'id')));
			foreach ($search_categories as $v) {
				$categories[$v['Category']['name']] = $v['Category']['name'];
			}
			$this->set(compact('categories'));
			$this->loadModel('Shop.Item');
			$search_items = $this->Item->find('all', array('fields' => array('name', 'id')));
			foreach ($search_items as $v) {
				$items[$v['Item']['id']] = $v['Item']['name'];
			}
			$this->set(compact('items'));

		} else {
			$this->redirect('/');
		}
	}

	public function admin_add_voucher_ajax() {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {
			if($this->request->is('post')) {
				if(!empty($this->request->data['code']) AND !empty($this->request->data['effective_on']) AND !empty($this->request->data['type']) AND !empty($this->request->data['reduction']) AND !empty($this->request->data['end_date'])) {
					if($this->request->data['effective_on'] == "categories") {
						$effective_on_value = array('type' => 'categories', 'value' => $this->request->data['effective_on_categorie']);
					}
					if($this->request->data['effective_on'] == "items") {
						$effective_on_value = array('type' => 'items', 'value' => $this->request->data['effective_on_item']);
					}
					if($this->request->data['effective_on'] == "all") {
						$effective_on_value = array('type' => 'all');
					}
					$this->loadModel('Shop.Voucher');
					$this->Voucher->read(null, null);
					$this->Voucher->set(array(
						'code' => $this->request->data['code'],
						'effective_on' => serialize($effective_on_value),
						'type' => intval($this->request->data['type']),
						'reduction' => $this->request->data['reduction'],
						'limit_per_user' => $this->request->data['limit_per_user'],
						'end_date' => $this->request->data['end_date'],
						'affich' => $this->request->data['affich'],
					));
					$this->Voucher->save();
					$this->History->set('ADD_VOUCHER', 'shop');
					$this->Session->setFlash($this->Lang->get('VOUCHER_SUCCESS_ADD'), 'default.success');
					echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('VOUCHER_SUCCESS_ADD')));
				} else {
					echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')));
				}
			} else {
				echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__BAD_REQUEST')));
			}
		} else {
			throw new ForbiddenException();
		}
	}

	public function admin_delete_voucher($id = false) {
		$this->autoRender = false;
		if($this->isConnected AND $this->User->isAdmin()) {
			if($id != false) {
				$this->loadModel('Shop.Voucher');
				$this->Voucher->delete($id);
				$this->History->set('DELETE_VOUCHER', 'shop');
				$this->Session->setFlash($this->Lang->get('DELETE_SUCCESS_ADD'), 'default.success');
				$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
			} else {
				$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	public function starpass($id = false) {
		if($this->isConnected AND $this->Permissions->can('CREDIT_ACCOUNT')) {
			if(($this->request->is('post') AND !empty($this->request->data['offer'])) || $id) {
				$this->loadModel('Shop.Starpass');
				if($this->request->is('post')) {
					$id = $this->request->data['offer'];
				}
				$search = $this->Starpass->find('all', array('conditions' => array('id' => $id)));
				if(!empty($search)) {
					$this->set('id', $search[0]['Starpass']['id']);
					$this->set('idd', $search[0]['Starpass']['idd']);
					$this->set('idp', $search[0]['Starpass']['idp']);
					$this->set('money', $search[0]['Starpass']['money']);
					$this->set('title_for_layout', $this->Lang->get('CREDIT_STARPASS'));
					$this->layout = $this->Configuration->getKey('layout');
				} else {
					throw new NotFoundException();
				}
			} else {
				throw new NotFoundException();
			}
		} else {
			throw new ForbiddenException();
		}
	}

	public function starpass_verif() {
		$this->autoRender = false;
		if($this->isConnected AND $this->Permissions->can('CREDIT_ACCOUNT')) {
			$offer_id = $_POST['DATAS'];
			$this->loadModel('Shop.Starpass');
			$search_starpass = $this->Starpass->find('all', array('conditions' => array('id' => $offer_id)));
			if(!empty($search_starpass)) {
				// Déclaration des variables
				$ident=$idp=$ids=$idd=$codes=$code1=$code2=$code3=$code4=$code5=$datas='';

				$idd = $search_starpass[0]['Starpass']['idd'];
				$idp = $search_starpass[0]['Starpass']['idp'];
				$ident=$idp.";".$ids.";".$idd;
				// On récupère le(s) code(s) sous la forme 'xxxxxxxx;xxxxxxxx'
				if(isset($_POST['code1'])) $code1 = $_POST['code1'];
				if(isset($_POST['code2'])) $code2 = ";".$_POST['code2'];
				if(isset($_POST['code3'])) $code3 = ";".$_POST['code3'];
				if(isset($_POST['code4'])) $code4 = ";".$_POST['code4'];
				if(isset($_POST['code5'])) $code5 = ";".$_POST['code5'];
				$codes=$code1.$code2.$code3.$code4.$code5;
				// On récupère le champ DATAS
				if(isset($_POST['DATAS'])) $datas = $_POST['DATAS'];
				// On encode les trois chaines en URL
				$ident=urlencode($ident);
				$codes=urlencode($codes);
				$datas=urlencode($datas);

				/* Envoi de la requête vers le serveur StarPass
				Dans la variable tab[0] on récupère la réponse du serveur
				Dans la variable tab[1] on récupère l'URL d'accès ou d'erreur suivant la réponse du serveur */
				$get_f=@file("http://script.starpass.fr/check_php.php?ident=$ident&codes=$codes&DATAS=$datas");
				if(!$get_f)
				{
				exit( "Votre serveur n'a pas accès au serveur de StarPass, merci de contacter votre hébergeur. " );
				}
				$tab = explode("|",$get_f[0]);
				if(!$tab[1]) $url = "http://script.starpass.fr/error.php";
				else $url = $tab[1];

				// dans $pays on a le pays de l'offre. exemple "fr"
				$pays = $tab[2];
				// dans $palier on a le palier de l'offre. exemple "Plus A"
				$palier = urldecode($tab[3]);
				// dans $id_palier on a l'identifiant de l'offre
				$id_palier = urldecode($tab[4]);
				// dans $type on a le type de l'offre. exemple "sms", "audiotel, "cb", etc.
				$type = urldecode($tab[5]);
				// vous pouvez à tout moment consulter la liste des paliers à l'adresse : http://script.starpass.fr/palier.php

				// Si $tab[0] ne répond pas "OUI" l'accès est refusé
				// On redirige sur l'URL d'erreur
				if( substr($tab[0],0,3) != "OUI" )
				{
			       /* erreur */
			       $this->Session->setFlash($this->Lang->get('ERROR__INTERNAL_ERROR'), 'default.error');
			       $this->redirect(array('controller' => 'shop', 'action' => 'index'));
				}
				else
				{
			       /* Le serveur a répondu "OUI" */
					$user_money = $this->User->getKey('money');
					$new_money = intval($user_money) + intval($search_starpass[0]['Starpass']['money']);

					$this->User->setKey('money', $new_money);

					$this->History->set('BUY_MONEY', 'shop', 'starpass|'.$search_starpass[0]['Starpass']['money']);

					$this->Session->setFlash($this->Lang->get('SUCCESS_STARPASS'), 'default.success');
					$this->redirect(array('controller' => 'shop', 'action' => 'index'));
				}
			} else {
				$this->redirect(array('controller' => 'shop', 'action' => 'index'));
			}
		} else {
			$this->redirect(array('controller' => 'shop', 'action' => 'index'));
		}
	}

	public function ipn() { // cf. https://developer.paypal.com/docs/classic/ipn/gs_IPN/
		$this->autoRender = false;

		if($this->request->is('post')) { //On vérifie l'état de la requête

			// On assigne les variables
				$item_name        = $this->request->data['item_name'];
	 			$item_number      = $this->request->data['item_number'];
	 			$payment_status   = $this->request->data['payment_status'];
	 			$payment_amount   = $this->request->data['mc_gross'];
	 			$payment_currency = $this->request->data['mc_currency'];
	 			$txn_id           = $this->request->data['txn_id'];
	 			$receiver_email   = $this->request->data['receiver_email'];
	 			$payer_email      = $this->request->data['payer_email'];
				$user_id      		= $this->request->data['custom'];

			// On vérifie que l'utilisateur contenu dans le champ custom existe bien

				$this->loadModel('User');
				if(!$this->User->exist($user_id)) {
					throw new InternalErrorException('PayPal : Unknown user');
				}

			// On prépare la requête de vérification
				$IPN = $this->request->data;
				$IPN['cmd'] = '_notify-validate';

			// On fais la requête

				$cURL = curl_init();
				curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($cURL, CURLOPT_URL, "https://www.paypal.com/cgi-bin/webscr");
				curl_setopt($cURL, CURLOPT_ENCODING, 'gzip');
				curl_setopt($cURL, CURLOPT_BINARYTRANSFER, true);
				curl_setopt($cURL, CURLOPT_POST, true); // POST back
				curl_setopt($cURL, CURLOPT_POSTFIELDS, $IPN); // the $IPN
				curl_setopt($cURL, CURLOPT_HEADER, false);
				curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($cURL, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
				curl_setopt($cURL, CURLOPT_FORBID_REUSE, true);
				curl_setopt($cURL, CURLOPT_FRESH_CONNECT, true);
				curl_setopt($cURL, CURLOPT_CONNECTTIMEOUT, 30);
				curl_setopt($cURL, CURLOPT_TIMEOUT, 60);
				curl_setopt($cURL, CURLINFO_HEADER_OUT, true);
				curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
						'Connection: close',
						'Expect: ',
				));
				$Response = curl_exec($cURL);
				$Status = (int)curl_getinfo($cURL, CURLINFO_HTTP_CODE);
				curl_close($cURL);

			// On traite la réponse

				// On vérifie que il y ai pas eu d'erreur

					if(empty($Response) || $Status != 200 || !$Status){
						throw new InternalErrorException('PayPal : Error with PayPal Response');
					}

				// On vérifie que la paiement est vérifié

					if(!preg_match('~^(VERIFIED)$~i', trim($Response))) {
						throw new InternalErrorException('PayPal : Paiement not verified');
					}

				// On effectue les autres vérifications

					if($payment_status == "Completed") { //Le paiment est complété

						if($payment_currency == "EUR") { //Le paiement est bien en euros

							// On cherche l'offre avec ce montant là
							$this->loadModel('Shop.Paypal');
							$findOffer = $this->Paypal->find('first', array('conditions' => array('price' => $payment_amount)));
							if(!empty($findOffer)) {

								// On vérifie que ce soit le bon mail
								if($receiver_email == $findOffer['Paypal']['email']) {

									// On vérifie que le paiement pas déjà en base de données
									$this->loadModel('Shop.PaypalHistory');
									$findPayment = $this->PaypalHistory->find('first', array('conditions' => array('payment_id' => $txn_id)));

									if(empty($findPayment)) {

										// On récupére le solde de l'utilisateur et on ajoute ses nouveaux crédits
										$sold = $this->User->getFromUser('money', $user_id);
										$new_sold = $sold + $findOffer['Paypal']['money'];

										// On ajoute l'argent à l'utilisateur
										$this->User->setToUser('money', $newSold, $user_id);

										// On l'ajoute dans l'historique global
										$this->HistoryC = $this->Components->load('History');
										$this->HistoryC->set('BUY_MONEY', 'shop');

										// On l'ajoute dans l'historique des paiements
										$this->PaypalHistory->create();
										$this->PaypalHistory->set(array(
											'payment_id' => $txn_id,
											'user_id' => $user_id,
											'offer_id' => $findOffer['Paypal']['id'],
											'payment_amount' => $findOffer['Paypal']['money']
										));
										$this->PaypalHistory->save();

										$this->response->statusCode(200);

									} else {
										throw new InternalErrorException('PayPal : Payment already credited');
									}

								}

							} else {
								throw new InternalErrorException('PayPal : Unknown offer');
							}

						} else {
							throw new InternalErrorException('PayPal : Bad currency');
						}

					} else {
						throw new InternalErrorException('PayPal : Paiement not completed');
					}

		} else {
			throw new InternalErrorException('PayPal : Not post');
		}
	}

}
