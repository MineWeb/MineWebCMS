<?php

class ShopController extends ShopAppController {

	public $components = array('Session', 'Shop.DiscountVoucher', 'History');

	/*
	* ======== Page principale de la boutique ===========
	*/

		function index($category = false) { // Index de la boutique

			$title_for_layout = $this->Lang->get('SHOP__TITLE');
			if($category) {
				$this->set(compact('category'));
			}
			$this->layout = $this->Configuration->getKey('layout'); // On charge le thème configuré
			$this->loadModel('Shop.Item'); // le model des articles
			$this->loadModel('Shop.Category'); // le model des catégories
			$search_items = $this->Item->find('all', array(
				'conditions' => array(
					'OR' => array(
						'display IS NULL',
						'display = 1'
					)
				)
			)); // on cherche tous les items et on envoie à la vue
			$search_categories = $this->Category->find('all'); // on cherche toutes les catégories et on envoie à la vue

			$search_first_category = $this->Category->find('first'); //
			$search_first_category = @$search_first_category['Category']['id']; //

			$this->loadModel('Shop.Paypal');
			$paypal_offers = $this->Paypal->find('all');

			$this->loadModel('Shop.Starpass');
			$starpass_offers = $this->Starpass->find('all');

			$this->loadModel('Shop.DedipassConfig');
			$findDedipassConfig = $this->DedipassConfig->find('first');
			$dedipass = (!empty($findDedipassConfig) && isset($findDedipassConfig['DedipassConfig']['status']) && $findDedipassConfig['DedipassConfig']['status']) ? true : false;

			$this->loadModel('Shop.Paysafecard');
			$paysafecard_enabled = $this->Paysafecard->find('all', array('conditions' => array('amount' => '0', 'code' => 'disable', 'user_id' => 0, 'created' => '1990/00/00 15:00:00')));
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

			$this->set(compact('dedipass', 'paysafecard_enabled', 'money', 'starpass_offers', 'paypal_offers', 'search_first_category', 'search_categories', 'search_items', 'title_for_layout', 'vouchers', 'singular_money', 'plural_money'));
		}




	/*
	* ======== Affichage d'un article dans le modal ===========
	*/

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

				$affich_server = (!empty($search_item[0]['Item']['servers']) && $search_item[0]['Item']['display_server']) ? true : false;


				//On récupére l'element
				if(file_exists(APP.DS.'View'.DS.'Themed'.DS.$this->Configuration->getKey('theme').DS.'Elements'.DS.'modal_buy.ctp')) {
					$element_content = file_get_contents(APP.DS.'View'.DS.'Themed'.DS.$this->Configuration->getKey('theme').DS.'Elements'.DS.'modal_buy.ctp');
				} else {
					$element_content = file_get_contents($this->EyPlugin->pluginsFolder.DS.'Shop'.DS.'View'.DS.'Elements'.DS.'modal_buy.ctp');
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




	/*
	* ======== Achat d'un article depuis le modal ===========
	*/

		public function checkVoucher($code = null, $item_id = null) {
			$this->autoRender = false;
			$this->response->type('json');

			if(!empty($code) && !empty($item_id)) {

				$this->loadModel('Shop.Item');
				$findItem = $this->Item->find('first', array('conditions' => array('id' => $item_id)));

				if(!empty($findItem)) {

					$new_price = $voucher_reduc = $this->DiscountVoucher->get_new_price(
						$findItem['Item']['price'],
						$findItem['Item']['category'],
						$findItem['Item']['id'],
						$code
					);

					echo json_encode(array('price' => $new_price));

				}

			}

			return;

		}



	/*
	* ======== Achat d'un article depuis le modal ===========
	*/

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

					if($search_item['0']['Item']['need_connect']) { //si on oblige le gars a être connecté pour acheter
						$continue = false;
						foreach ($servers_online as $k => $server_id) {

	            $call = $this->Server->call(array('isConnected' => $this->User->getKey('pseudo')), $server_id, true);
							if($call['isConnected'] == 'true') {
                $continue = true;
                break;
              }

						}
						if(!$continue) { // Il est pas connecté (sur aucun des serveurs)
							echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">'.$this->Lang->get('GLOBAL__CLOSE').'</span></button><strong>'.$this->Lang->get('GLOBAL__ERROR').' :</strong> '.$this->Lang->get('SHOP__BUY_ERROR_NO_CONNECTED').'</div>';
							exit;
						}
          }

					$item_price = $search_item['0']['Item']['price'];
					if(!empty($_GET['code'])) {
						$voucher_reduc = $this->DiscountVoucher->get_new_price($item_price, $search_item['0']['Item']['category'], $search_item['0']['Item']['id'], $_GET['code']);
						$item_price = $voucher_reduc; // j'obtient le nouveau prix si une promotion est en cours sur cet article ou sa catégorie
					}
					if($item_price <= $this->User->getKey('money')) {

						$this->getEventManager()->dispatch(new CakeEvent('onBuy', $this));

						// Ajouter au champ used si il a utiliser un voucher
						if(!empty($_GET['code']) && $voucher_reduc != $search_item['0']['Item']['price']) {
							$diff = $search_item['0']['Item']['price'] - $voucher_reduc;
							$this->DiscountVoucher->set_used($this->User->getKey('pseudo'), $_GET['code']);

							$this->loadModel('Shop.VouchersHistory');
							$this->VouchersHistory->create();
							$this->VouchersHistory->set(array(
								'code' => $_GET['code'],
								'user_id' => $this->User->getKey('id'),
								'item_id' => $search_item['0']['Item']['id'],
								'reduction' => $diff
							));
							$this->VouchersHistory->save();

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



	/*
	* ======== Page principale du panel admin ===========
	*/
		public function admin_index() {
			if($this->isConnected AND $this->User->isAdmin()) {

				$this->set('title_for_layout',$this->Lang->get('SHOP__TITLE'));
				$this->layout = 'admin';

				$this->loadModel('Shop.Item');
				$search_items = $this->Item->find('all');

				$this->loadModel('Shop.Category');
				$search_categories = $this->Category->find('all');
				foreach ($search_categories as $v) {
					$categories[$v['Category']['id']]['name'] = $v['Category']['name'];
				}

				$this->loadModel('History');
				$histories_buy = $this->History->find('all', array('conditions' => array('action' => 'BUY_ITEM'), 'order' => 'id DESC'));

				$this->set(compact('categories', 'search_categories', 'search_items', 'histories_buy'));

			} else {
				$this->redirect('/');
			}
		}




	/*
	* ======== Modification d'un article (affichage de la page) ===========
	*/

		public function admin_edit($id = false) {
			if($this->isConnected AND $this->User->isAdmin()) {
				if($id != false) {

					$this->set('title_for_layout', $this->Lang->get('SHOP__ITEM_EDIT'));
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



	/*
	* ======== Modification de l'article (traitement AJAX) ===========
	*/

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
							'timedCommand_time' => $this->request->data['timedCommand_time'],
							'display_server' => $this->request->data['display_server'],
							'need_connect' => $this->request->data['need_connect'],
							'display' => $this->request->data['display']
						));
						$this->Item->save();
						$this->Session->setFlash($this->Lang->get('SHOP__ITEM_EDIT_SUCCESS'), 'default.success');
						echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('SHOP__ITEM_EDIT_SUCCESS')));
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



	/*
	* ======== Ajout d'un article (affichage) ===========
	*/

		public function admin_add_item() {
			if($this->isConnected AND $this->User->isAdmin()) {

				$this->set('title_for_layout', $this->Lang->get('SHOP__ITEM_ADD'));
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



	/*
	* ======== Ajout d'un article (Traitement AJAX) ===========
	*/

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
							'timedCommand_time' => $this->request->data['timedCommand_time'],
							'display_server' => $this->request->data['display_server'],
							'need_connect' => $this->request->data['need_connect'],
							'display' => $this->request->data['display']
							));
						$this->Item->save();
						$this->History->set('ADD_ITEM', 'shop');
						$this->Session->setFlash($this->Lang->get('SHOP__ITEM_ADD_SUCCESS'), 'default.success');
						echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('SHOP__ITEM_ADD_SUCCESS')));
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




	/*
	* ======== Ajout d'une catégorie (affichage & traitement POST) ===========
	*/

		public function admin_add_category() {
			if($this->isConnected AND $this->User->isAdmin()) {

				$this->layout = 'admin';
				$this->set('title_for_layout', $this->Lang->get('SHOP__CATEGORY_ADD'));
				if($this->request->is('post')) {
					if(!empty($this->request->data['name'])) {
						$this->loadModel('Shop.Category');
						$this->Category->read(null, null);
						$this->Category->set(array(
							'name' => $this->request->data['name'],
						));
						$this->History->set('ADD_CATEGORY', 'shop');
						$this->Category->save();
						$this->Session->setFlash($this->Lang->get('SHOP__CATEGORY_ADD_SUCCESS'), 'default.success');
						$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
					} else {
						$this->Session->setFlash($this->Lang->get('ERROR__FILL_ALL_FIELDS'), 'default.error');
					}
				}
			} else {
				$this->redirect('/');
			}
		}

	/*
	* ======== Suppression d'une catégorie/article/paypal/starpass (traitement) ===========
	*/

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
							$this->Session->setFlash($this->Lang->get('SHOP__ITEM_DELETE_SUCCESS'), 'default.success');
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
							$this->Session->setFlash($this->Lang->get('SHOP__CATEGORY_DELETE_SUCCESS'), 'default.success');
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
							$this->Session->setFlash($this->Lang->get('SHOP__PAYPAL_OFFER_DELETE_SUCCESS'), 'default.success');
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
							$this->Session->setFlash($this->Lang->get('SHOP__STARPASS_OFFER_DELETE_SUCCESS'), 'default.success');
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




	/*
	* ======== Page principale pour les promos ===========
	*/


		public function admin_vouchers() {
			if($this->isConnected AND $this->User->isAdmin()) {

				$this->set('title_for_layout',$this->Lang->get('SHOP__VOUCHERS_MANAGE'));
				$this->layout = 'admin';

				$this->loadModel('Shop.Voucher');
				$vouchers = $this->Voucher->find('all');

				$this->loadModel('Shop.VouchersHistory');
				$vouchers_histories = $this->VouchersHistory->find('all', array('order' => 'id DESC'));

				$usersByID = array();

				$findUsers = $this->User->find('all');
				foreach ($findUsers as $key => $value) {
					$usersByID[$value['User']['id']] = $value['User']['pseudo'];
				}

				$itemsByID = array();

				$this->loadModel('Shop.Item');
				$findItems = $this->Item->find('all');
				foreach ($findItems as $key => $value) {
					$itemsByID[$value['Item']['id']] = $value['Item']['name'];
				}

				$this->set(compact('vouchers', 'vouchers_histories', 'usersByID', 'itemsByID'));

			} else {
				throw new ForbiddenException();
			}
		}




	/*
	* ======== Ajout d'un code promotionnel (affichage) ===========
	*/

		public function admin_add_voucher() {
			if($this->isConnected AND $this->User->isAdmin()) {

				$this->set('title_for_layout', $this->Lang->get('SHOP__VOUCHER_ADD'));
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




	/*
	* ======== Ajout d'un code promotionnel (traitement AJAX) ===========
	*/

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
						$this->Session->setFlash($this->Lang->get('SHOP__VOUCHER_ADD_SUCCESS'), 'default.success');
						echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('SHOP__VOUCHER_ADD_SUCCESS')));
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




	/*
	* ======== Suppression d'un code promotionnel (traitement POST) ===========
	*/

		public function admin_delete_voucher($id = false) {
			$this->autoRender = false;
			if($this->isConnected AND $this->User->isAdmin()) {
				if($id != false) {
					$this->loadModel('Shop.Voucher');
					$this->Voucher->delete($id);
					$this->History->set('DELETE_VOUCHER', 'shop');
					$this->Session->setFlash($this->Lang->get('SHOP__VOUCHER_DELETE_SUCCESS'), 'default.success');
					$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
				} else {
					$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
				}
			} else {
				$this->redirect('/');
			}
		}

}
