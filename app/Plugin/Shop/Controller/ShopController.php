<?php 

class ShopController extends AppController {

	public $components = array('Session', 'Shop.DiscountVoucher', 'History');

	function index($category = false) { // Index de la boutique
		  
		$title_for_layout = $this->Lang->get('SHOP'); $this->set(compact('title_for_layout'));
		if($category) {
			$this->set(compact('category'));
		}
		$this->layout = $this->Configuration->get_layout(); // On charge le thème configuré
		$this->loadModel('Item'); // le model des articles
		$this->loadModel('Category'); // le model des catégories
		$search_items = $this->Item->find('all'); $this->set(compact('search_items')); // on cherche tous les items et on envoie à la vue
		$search_categories = $this->Category->find('all'); $this->set(compact('search_categories')); // on cherche toutes les catégories et on envoie à la vue
		
		$search_first_category = $this->Category->find('first'); //
		$search_first_category = $search_first_category['Category']['id']; //
		$this->set(compact('search_first_category')); // on cherche la première catégorie et on envoie à la vue

		$this->loadModel('Paypal');
		$paypal_offers = $this->Paypal->find('all');
		$this->set(compact('paypal_offers'));

		$this->loadModel('Starpass');
		$starpass_offers = $this->Starpass->find('all');
		$this->set(compact('starpass_offers'));

		$this->loadModel('Paysafecard');
		$paysafecard_enabled = $this->Paysafecard->find('all', array('conditions' => array('amount' => '0', 'code' => 'disable', 'author' => 'website', 'created' => '1990/00/00 15:00:00')));
		if(!empty($paysafecard_enabled)) {
			$paysafecard_enabled = false;
		} else {
			$paysafecard_enabled = true;
		}
		$this->set(compact('paysafecard_enabled'));
	}

	function ajax_get($id) { // Permet d'afficher le contenu du modal avant l'achat (ajax)
		  
		$this->layout = null;
		if($this->Connect->connect() AND $this->Permissions->can('CAN_BUY')) { // si l'utilisateur est connecté 
			$this->loadModel('Item'); // je charge le model des articles
			$search_item = $this->Item->find('all', array('conditions' => array('id' => $id))); // je cherche l'article selon l'id
			if($search_item['0']['Item']['price'] == 1) { $money = $this->Configuration->get_money_name(false, true); } else { $money = $this->Configuration->get_money_name(); } // je dis que la variable $money = le nom de la money au pluriel ou singulier selon le prix
			echo '
		<div class="modal-body">
			<div id="msg_buy"></div>
			<p><b>'.$this->Lang->get('NAME_OF_ITEM').' :</b> '.$search_item['0']['Item']['name'].'</p>
			<p><b>'.$this->Lang->get('DESCRIPTION').' :</b> '.$search_item['0']['Item']['description'].'</p>
			<p><b>'.$this->Lang->get('PRICE').' :</b> '.$search_item['0']['Item']['price'] . ' ' . $money .'</p>
			<p><input name="code" type="text" class="form-control" id="code-voucher" style="width:245px;" placeholder="'.$this->Lang->get('HAVE_YOU_VOUCHER').'"></p>
		</div>
      	<div class="modal-footer">
        	<button type="button" class="btn btn-default" data-dismiss="modal">'.$this->Lang->get('CLOSE').'</button>
        	<button type="button" class="btn btn-primary'; // j'affiche le contenu du modal
        	//if($search_item['0']['Item']['price'] > $this->Connect->get('money')) { // si il a pas assez de money 
        	//	echo ' disabled" title="'.$this->Lang->get('NO_ENOUGH_MONEY'); // je met le bouton en disable
        	//} else {
        		echo '" onClick="buy(\''.$search_item['0']['Item']['id'].'\')'; // sinon, il a assez de money donc j'ajoute la fonction js pour acheter 
        	//}
        	echo '">'.$this->Lang->get('BUY').'</button>
     	</div>'; // puis je fini l'affichage du modal
		} else {
			echo $this->Lang->get('NEED_CONNECT'); // si il n'est pas connecté
		}
	}

	function buy_ajax($id) {
		$this->layout = null;
		  
		if($this->Connect->connect() AND $this->Permissions->can('CAN_BUY')) {
			if(Configure::read('server.online')) {
				$this->loadModel('Item');
				$search_item = $this->Item->find('all', array('conditions' => array('id' => $id)));
				$item_price = $search_item['0']['Item']['price'];
				if(!empty($_GET['code'])) {
					$item_price = $this->DiscountVoucher->get_new_price($item_price, $search_item['0']['Item']['category'], $search_item['0']['Item']['name'], $_GET['code']); // j'obtient le nouveau prix si une promotion est en cours sur cet article ou sa catégorie
				}
				if($item_price <= $this->Connect->get('money')) {
					$new_sold = $this->Connect->get('money') - $item_price;
					$this->loadModel('User');
					$this->User->read(null, $this->Connect->get_id());
					$this->User->set(array('money' => $new_sold));
					$this->User->save();
					$this->History->set('BUY_ITEM', 'shop', $search_item['0']['Item']['name']);
					$commands = $search_item['0']['Item']['commands'];
					// executer les commandes
					$this->Server->commands($commands);
					echo '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">'.$this->Lang->get('CLOSE').'</span></button><strong>'.$this->Lang->get('SUCCESS').' :</strong> '.$this->Lang->get('BUY_SUCCESS').'</div>';
				} else {
					echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">'.$this->Lang->get('CLOSE').'</span></button><strong>'.$this->Lang->get('ERROR').' :</strong> '.$this->Lang->get('NO_ENOUGH_MONEY').'</div>';
				}
			} else {
					echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">'.$this->Lang->get('CLOSE').'</span></button><strong>'.$this->Lang->get('ERROR').' :</strong> '.$this->Lang->get('NEED_SERVER_ON').'</div>';
			}
		} else {
			echo '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">'.$this->Lang->get('CLOSE').'</span></button><strong>'.$this->Lang->get('ERROR').' :</strong> '.$this->Lang->get('NEED_CONNECT').'</div>';
		}
	}

	public function admin_index() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout',$this->Lang->get('SHOP'));
			$this->layout = 'admin';
			$this->loadModel('Item');
			$search_items = $this->Item->find('all');
			$this->set(compact('search_items'));
			$this->loadModel('Category');
			$search_categories = $this->Category->find('all');
			foreach ($search_categories as $v) {
				$categories[$v['Category']['id']]['name'] = $v['Category']['name'];
			}
			$this->set(compact('categories'));
			$this->set(compact('search_categories'));

			$this->loadModel('Paysafecard');
			$psc = $this->Paysafecard->find('all', array('conditions' => array('amount !=' => '0', 'code !=' => 'disable', 'author !=' => 'website', 'created !=' => '1990/00/00 15:00:00')));
			$this->set(compact('psc'));

			$paysafecard_enabled = $this->Paysafecard->find('all', array('conditions' => array('amount' => '0', 'code' => 'disable', 'author' => 'website', 'created' => '1990/00/00 15:00:00')));
			if(!empty($paysafecard_enabled)) {
				$paysafecard_enabled = false;
			} else {
				$paysafecard_enabled = true;
			}
			$this->set(compact('paysafecard_enabled'));

			$this->loadModel('Voucher');
			$vouchers = $this->Voucher->find('all');
			$this->set(compact('vouchers'));

			$this->loadModel('Paypal');
			$paypal_offers = $this->Paypal->find('all');
			$this->set(compact('paypal_offers'));

			$this->loadModel('Starpass');
			$starpass_offers = $this->Starpass->find('all');
			$this->set(compact('starpass_offers'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_edit($id = false) {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			if($id != false) {
				 
				$this->set('title_for_layout', $this->Lang->get('EDIT_ITEM'));
				$this->layout = 'admin';
				$this->loadModel('Item');
				$item = $this->Item->find('all', array('conditions' => array('id' => $id)));
				if(!empty($item)) {
					$item = $item[0]['Item'];
					$this->loadModel('Category');
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
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->layout = null;
			if($this->request->is('post')) {
				if(empty($this->request->data['category'])) {
					$this->request->data['category'] = $this->request->data['category_default'];
				}
				if(!empty($this->request->data['id']) AND !empty($this->request->data['name']) AND !empty($this->request->data['description']) AND !empty($this->request->data['category']) AND !empty($this->request->data['price']) AND !empty($this->request->data['commands'])) {
					$this->loadModel('Category');
					$this->request->data['category'] = $this->Category->find('all', array('conditions' => array('name' => $this->request->data['category'])));
					$this->request->data['category'] = $this->request->data['category'][0]['Category']['id'];
					$this->loadModel('Item');
					$this->Item->read(null, $this->request->data['id']);
					$this->Item->set(array(
						'name' => $this->request->data['name'],
						'description' => $this->request->data['description'],
						'category' => $this->request->data['category'],
						'price' => $this->request->data['price'],
						'commands' => $this->request->data['commands'],
						'img_url' => $this->request->data['img_url']
						));
					$this->Item->save();
					$this->Session->setFlash($this->Lang->get('ITEM_SUCCESS_EDIT'), 'default.success');
					echo $this->Lang->get('ITEM_SUCCESS_EDIT').'|true';
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST' ,$language).'|false';
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add_item() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout', $this->Lang->get('ADD_ITEM'));
			$this->layout = 'admin';
			$this->loadModel('Category');
			$search_categories = $this->Category->find('all', array('fields' => 'name'));
			foreach ($search_categories as $v) {
				$categories[$v['Category']['name']] = $v['Category']['name'];
			}
			$this->set(compact('categories'));
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add_item_ajax() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->layout = null;
			if($this->request->is('post')) {
				if(!empty($this->request->data['name']) AND !empty($this->request->data['description']) AND !empty($this->request->data['category']) AND !empty($this->request->data['price']) AND !empty($this->request->data['commands'])) {
					$this->loadModel('Category');
					$this->request->data['category'] = $this->Category->find('all', array('conditions' => array('name' => $this->request->data['category'])));
					$this->request->data['category'] = $this->request->data['category'][0]['Category']['id'];
					$this->loadModel('Item');
					$this->Item->read(null, null);
					$this->Item->set(array(
						'name' => $this->request->data['name'],
						'description' => $this->request->data['description'],
						'category' => $this->request->data['category'],
						'price' => $this->request->data['price'],
						'commands' => $this->request->data['commands'],
						'img_url' => $this->request->data['img_url']
						));
					$this->Item->save();
					$this->History->set('ADD_ITEM', 'shop');
					$this->Session->setFlash($this->Lang->get('ITEM_SUCCESS_ADD'), 'default.success');
					echo $this->Lang->get('ITEM_SUCCESS_ADD').'|true';
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST' ,$language).'|false';
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add_category() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->layout = 'admin';
			$this->set('title_for_layout', $this->Lang->get('ADD_CATEGORY'));
			if($this->request->is('post')) {
				if(!empty($this->request->data['name'])) {
					$this->loadModel('Category');
					$this->Category->read(null, null);
					$this->Category->set(array(
						'name' => $this->request->data['name'],
					));
					$this->History->set('ADD_CATEGORY', 'shop');
					$this->Category->save();
					$this->Session->setFlash($this->Lang->get('CATEGORY_SUCCESS_ADD'), 'default.success');
					$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
				} else {
					$this->Session->setFlash($this->Lang->get('COMPLETE_ALL_FIELDS'), 'default.error');
				}
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_delete($type = false, $id = false) {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			if($type != false AND $id != false) {
				 
				$this->set('title_for_layout', $this->Lang->get('EDIT_ITEM'));
				$this->layout = null;
				if($type == "item") {
					$this->loadModel('Item');
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
					$this->loadModel('Category');
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
					$this->loadModel('Paypal');
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
					$this->loadModel('Starpass');
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
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			$this->loadModel('Paysafecard');
			$paysafecard_enabled = $this->Paysafecard->find('all', array('conditions' => array('amount' => '0', 'code' => 'disable', 'author' => 'website', 'created' => '1990/00/00 15:00:00')));
			if(!empty($paysafecard_enabled)) {
				$this->Paysafecard->delete($paysafecard_enabled[0]['Paysafecard']['id']);

				$this->History->set('ENABLE_PAYSAFECARD', 'shop');
					 
				$this->Session->setFlash($this->Lang->get('PAYSAFECARD_ENABLE_SUCCESS'), 'default.success');
				$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
			} else {
				$this->Paysafecard->read(null, $paysafecard_enabled[0]['Paysafecard']['id']);
				$this->Paysafecard->set(array('amount' => '0', 'code' => 'disable', 'author' => 'website', 'created' => '1990/00/00 15:00:00'));
				$this->Paysafecard->save();

				$this->History->set('DISABLE_PAYSAFECARD', 'shop');
					 
				$this->Session->setFlash($this->Lang->get('PAYSAFECARD_DISABLE_SUCCESS'), 'default.success');
				$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
			}

		} else {
			$this->redirect('/');
		}
	}

	public function admin_paysafecard_valid($id = false, $money = false) {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			if($id != false AND $money != false) {
				$this->loadModel('Paysafecard');
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
					$this->loadModel('PaysafecardMessage');
					$this->PaysafecardMessage->read(null, null);
					$this->PaysafecardMessage->set(array(
						'to' => $search['0']['Paysafecard']['author'],
						'type' => 1,
						'amount' => $search['0']['Paysafecard']['amount'],
						'added_points' => intval($money)
					));
					$this->PaysafecardMessage->save();

					$this->History->set('VALID_PAYSAFECARD', 'shop');
					 
					$this->Session->setFlash($this->Lang->get('PAYSAFECARD_VALID_SUCCESS'), 'default.success');
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
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			if($id != false) {
				$this->loadModel('Paysafecard');
				$search = $this->Paysafecard->find('all', array('conditions' => array('id' => $id)));
				if(!empty($search)) {
					$this->Paysafecard->delete($id);
					$this->loadModel('PaysafecardMessage');
					$this->PaysafecardMessage->read(null, null);
					$this->PaysafecardMessage->set(array(
						'to' => $search['0']['Paysafecard']['author'],
						'type' => 0,
						'amount' => $search['0']['Paysafecard']['amount'],
						'added_points' => 0
					));
					$this->PaysafecardMessage->save();

					$this->History->set('INVALID_PAYSAFECARD', 'shop');
					 
					$this->Session->setFlash($this->Lang->get('PAYSAFECARD_INVALID_SUCCESS'), 'default.success');
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
		 
		$this->layout = null;
		if($this->Connect->connect() AND $this->Permissions->can('CREDIT_ACCOUNT')) {
			if($this->request->is('post')) {
				if(!empty($this->request->data['amount']) AND !empty($this->request->data['code1']) AND !empty($this->request->data['code2']) AND !empty($this->request->data['code3']) AND !empty($this->request->data['code4'])) {
					$this->request->data['amount'] = intval($this->request->data['amount']);
					if($this->request->data['amount'] > 0) {
						if(strlen($this->request->data['code1']) == 4 AND strlen($this->request->data['code2']) == 4 AND strlen($this->request->data['code3']) == 4 AND strlen($this->request->data['code4']) == 4) {
							// faire des vérifications (interdiction d'avoir entré plus de 2 PSC)
							$this->loadModel('Paysafecard');
							$search = $this->Paysafecard->find('count', array('conditions' => array('author' => $this->Connect->get_pseudo())));
							if($search < 2) {
								$this->Paysafecard->read(null, null);
								$this->Paysafecard->set(array(
									'amount' => $this->request->data['amount'],
									'code' => $this->request->data['code1'].' '.$this->request->data['code2'].' '.$this->request->data['code3'].' '.$this->request->data['code4'],
									'author' => $this->Connect->get_pseudo()
								));
								$this->Paysafecard->save();
								$this->History->set('ADD_PAYSAFECARD', 'credit_shop');
								echo $this->Lang->get('SUCCESS_ADD_PSC').'|true';
							} else {
								echo $this->Lang->get('ALREADY_2_PSC_IN_DB').'|false';
							}
						}  else {
							echo $this->Lang->get('NOT_4_CHARACTER').'|false';
						}	
					}  else {
						echo $this->Lang->get('NOT_NUMBER').'|false';
					}	
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST' ,$language).'|false';
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add_paypal() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout', $this->Lang->get('ADD_OFFER_PAYPAL'));
			$this->layout = 'admin';
		} else {
			$this->redirect('/');
		}
	}

	public function admin_edit_paypal($id = false) {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout', $this->Lang->get('EDIT_OFFER_PAYPAL'));
			$this->layout = 'admin';
			if($id != false) {
				$this->loadModel('Paypal');
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
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout', $this->Lang->get('ADD_OFFER_STARPASS'));
			$this->layout = 'admin';
		} else {
			$this->redirect('/');
		}
	}

	public function admin_edit_starpass($id = false) {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout', $this->Lang->get('EDIT_OFFER_STARPASS'));
			$this->layout = 'admin';
			if($id != false) {
				$this->loadModel('Starpass');
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
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout', $this->Lang->get(''));
			$this->layout = null;
			if($this->request->is('ajax')) {
				if(!empty($this->request->data['name']) AND !empty($this->request->data['email']) AND !empty($this->request->data['price']) AND !empty($this->request->data['money'])) {
					$this->request->data['price'] = intval($this->request->data['price']);
					$this->request->data['money'] = intval($this->request->data['money']);
					if(filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL)) {
						$this->loadModel('Paypal');
						$this->Paypal->read(null, null);
						$this->Paypal->set($this->request->data);
						$this->Paypal->save();
						$this->History->set('ADD_PAYPAL_OFFER', 'shop');
						$this->Session->setFlash($this->Lang->get('ADD_PAYPAL_OFFER_SUCCESS'), 'default.success');
						echo $this->Lang->get('ADD_PAYPAL_OFFER_SUCCESS').'|true';
					} else {
						echo $this->Lang->get('EMAIL_NOT_VALIDATE').'|false';
					}
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST').'|false';
			}
			$this->render('ajax_get');
		} else {
			$this->redirect('/');
		}
	}

	public function admin_edit_paypal_ajax($id = false) {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout', $this->Lang->get(''));
			$this->layout = null;
			if($id != false) {
				$this->loadModel('Paypal');
				$search = $this->Paypal->find('all', array('conditions' => array('id' => $id)));
				if(!empty($search)) {
					if($this->request->is('ajax')) {
						if(!empty($this->request->data['name']) AND !empty($this->request->data['email']) AND !empty($this->request->data['price']) AND !empty($this->request->data['money'])) {
							$this->request->data['price'] = intval($this->request->data['price']);
							$this->request->data['money'] = intval($this->request->data['money']);
							if(filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL)) {
								$this->loadModel('Paypal');
								$this->Paypal->read(null, $id);
								$this->Paypal->set($this->request->data);
								$this->Paypal->save();
								$this->History->set('EDIT_PAYPAL_OFFER', 'shop');
								$this->Session->setFlash($this->Lang->get('EDIT_PAYPAL_OFFER_SUCCESS'), 'default.success');
								echo $this->Lang->get('EDIT_PAYPAL_OFFER_SUCCESS').'|true';
							} else {
								echo $this->Lang->get('EMAIL_NOT_VALIDATE').'|false';
							}
						} else {
							echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
						}
					} else {
						echo $this->Lang->get('NOT_POST').'|false';
					}
				} else {
					echo $this->Lang->get('UNKNONW_ID').'|false';
				}
				$this->render('ajax_get');
			} else {
				$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_add_starpass_ajax() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout', $this->Lang->get(''));
			$this->layout = null;
			if($this->request->is('ajax')) {
				if(!empty($this->request->data['name']) AND !empty($this->request->data['idd']) AND !empty($this->request->data['idp']) AND !empty($this->request->data['money'])) {
					$this->request->data['money'] = intval($this->request->data['money']);
					$this->request->data['idd'] = intval($this->request->data['idd']);
					$this->request->data['idp'] = intval($this->request->data['idp']);
					$this->loadModel('Starpass');
					$this->Starpass->read(null, null);
					$this->Starpass->set($this->request->data);
					$this->Starpass->save();
					$this->History->set('ADD_STARPASS_OFFER', 'shop');
					$this->Session->setFlash($this->Lang->get('ADD_STARPASS_OFFER_SUCCESS'), 'default.success');
					echo $this->Lang->get('ADD_STARPASS_OFFER_SUCCESS').'|true';
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST').'|false';
			}
			$this->render('ajax_get');
		} else {
			$this->redirect('/');
		}
	}

	public function admin_edit_starpass_ajax($id = false) {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout', $this->Lang->get(''));
			$this->layout = null;
			if($id != false) {
				if($this->request->is('ajax')) {
					if(!empty($this->request->data['name']) AND !empty($this->request->data['idd']) AND !empty($this->request->data['idp']) AND !empty($this->request->data['money'])) {
						$this->request->data['money'] = intval($this->request->data['money']);
						$this->request->data['idd'] = intval($this->request->data['idd']);
						$this->request->data['idp'] = intval($this->request->data['idp']);
						$this->loadModel('Starpass');
						$this->Starpass->read(null, $id);
						$this->Starpass->set($this->request->data);
						$this->Starpass->save();
						$this->History->set('EDIT_STARPASS_OFFER', 'shop');
						$this->Session->setFlash($this->Lang->get('EDIT_STARPASS_OFFER_SUCCESS'), 'default.success');
						echo $this->Lang->get('EDIT_STARPASS_OFFER_SUCCESS').'|true';
					} else {
						echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
					}
				} else {
					echo $this->Lang->get('NOT_POST').'|false';
				}
				$this->render('ajax_get');
			} else {
				$this->redirect(array('controller' => 'shop', 'action' => 'index', 'admin' => true));
			}
		}	 else {
			$this->redirect('/');
		}
	}

	public function admin_add_voucher() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->set('title_for_layout', $this->Lang->get('ADD_VOUCHER'));
			$this->layout = 'admin';

			$this->loadModel('Category');
			$search_categories = $this->Category->find('all', array('fields' => array('name', 'id')));
			foreach ($search_categories as $v) {
				$categories[$v['Category']['name']] = $v['Category']['name'];
			}
			$this->set(compact('categories'));
			$this->loadModel('Item');
			$search_items = $this->Item->find('all', array('fields' => array('name', 'id')));
			foreach ($search_items as $v) {
				$items[$v['Item']['name']] = $v['Item']['name'];
			}
			$this->set(compact('items'));

		} else {
			$this->redirect('/');
		}
	}

	public function admin_add_voucher_ajax() {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->layout = null;
			if($this->request->is('post')) {
				if(!empty($this->request->data['code']) AND !empty($this->request->data['effective_on']) AND !empty($this->request->data['type']) AND !empty($this->request->data['reduction'])) {
					if($this->request->data['effective_on'] == "categories") {
						$effective_on_value = array('type' => 'categories', 'value' => $this->request->data['effective_on_categorie']);
					}
					if($this->request->data['effective_on'] == "items") {
						$effective_on_value = array('type' => 'items', 'value' => $this->request->data['effective_on_item']);
					}
					if($this->request->data['effective_on'] == "all") {
						$effective_on_value = array('type' => 'all');
					}
					$this->loadModel('Voucher');
					$this->Voucher->read(null, null);
					$this->Voucher->set(array(
						'code' => $this->request->data['code'],
						'effective_on' => serialize($effective_on_value),
						'type' => intval($this->request->data['type']),
						'reduction' => $this->request->data['reduction'],
						//'limit_per_user' => $this->request->data['limit_per_ip'],
						'affich' => $this->request->data['affich'],
					));
					$this->Voucher->save();
					$this->History->set('ADD_VOUCHER', 'shop');
					$this->Session->setFlash($this->Lang->get('VOUCHER_SUCCESS_ADD'), 'default.success');
					echo $this->Lang->get('VOUCHER_SUCCESS_ADD').'|true';
				} else {
					echo $this->Lang->get('COMPLETE_ALL_FIELDS').'|false';
				}
			} else {
				echo $this->Lang->get('NOT_POST' ,$language).'|false';
			}
		} else {
			$this->redirect('/');
		}
	}

	public function admin_delete_voucher($id = false) {
		if($this->Connect->connect() AND $this->Connect->if_admin()) {
			 
			$this->layout = null;
			if($id != false) {
				$this->loadModel('Voucher');
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

	public function starpass() {
		 
		if($this->Connect->connect() AND $this->Permissions->can('CREDIT_ACCOUNT')) {
			if($this->request->is('post') AND !empty($this->request->data['offer'])) {
				$this->loadModel('Starpass');
				$search = $this->Starpass->find('all', array('conditions' => array('id' => $this->request->data['offer'])));
				if(!empty($search)) {
					$this->set('id', $search[0]['Starpass']['id']);
					$this->set('idd', $search[0]['Starpass']['idd']);
					$this->set('idp', $search[0]['Starpass']['idp']);
					$this->set('money', $search[0]['Starpass']['money']);
					$this->set('title_for_layout', $this->Lang->get('CREDIT_STARPASS'));
					$this->layout = $this->Configuration->get_layout();
				} else {
					$this->redirect(array('controller' => 'shop', 'action' => 'index'));
				}
			} else {
				$this->redirect(array('controller' => 'shop', 'action' => 'index'));
			}
		} else {
			$this->redirect('/');
		}
	}

		/* TESTER */
	public function starpass_verif() {
		$this->layout = null;
		 
		if($this->Connect->connect() AND $this->Permissions->can('CREDIT_ACCOUNT')) {
			$offer_id = $_POST['DATAS'];
			$this->loadModel('Starpass');
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
				$get_f=@file( "http://script.starpass.fr/check_php.php?ident=$ident&codes=$codes&DATAS=$datas" ); 
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
			       $this->Session->setFlash($this->Lang->get('INTERNAL_ERROR'), 'default.error');
			       $this->redirect(array('controller' => 'shop', 'action' => 'index'));
				} 
				else 
				{ 
			       /* Le serveur a répondu "OUI" */
					$user_money = $this->Connect->get('money');
					$new_money = intval($user_money) + intval($search_starpass[0]['Starpass']['money']);

					$this->Connect->set('money', $new_money);

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

	public function ipn() {
		$this->layout = null;
		if($this->Connect->connect() AND $_POST AND $this->Permissions->can('CREDIT_ACCOUNT')) {
		    if(empty($IPN)){
		        $IPN = $_POST;
		    }
		    if(empty($IPN['verify_sign'])){
		        echo 'null';
		    }
		    $IPN['cmd'] = '_notify-validate';
		    //$PaypalHost = (empty($IPN['test_ipn']) ? 'www' : 'www.sandbox').'.paypal.com';
		    $PaypalHost = 'www.paypal.com';
		    $cURL = curl_init();
		    curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
		    curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, false);
		    curl_setopt($cURL, CURLOPT_URL, "https://{$PaypalHost}/cgi-bin/webscr");
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
		    if(empty($Response) or !preg_match('~^(VERIFIED|INVALID)$~i', $Response = trim($Response)) or !$Status){
		        echo 'null';
		    }
		    if(intval($Status / 100) != 2){
		        echo 'false';
		    }
		    $item_name = $_POST['item_name'];
			$item_number = $_POST['item_number'];
			$payment_status = $_POST['payment_status'];
			$payment_amount = $_POST['mc_gross'];
			$payment_currency = $_POST['mc_currency'];
			$txn_id = $_POST['txn_id'];
			$receiver_email = $_POST['receiver_email'];
			$payer_email = $_POST['payer_email'];
			$custom = $_POST['custom'];

		    if($Response == "VERIFIED") {
		    	// vérifier que payment_status a la valeur Completed
				if ( $payment_status == "Completed") {
					$this->loadModel('Paypal');
					$search_offer = $this->Paypal->find('all', array('conditions' => array('price' => $payment_amount))); // je cherche une offre avec ce montant là
					if(!empty($search_offer)) {
						$search_offer = $search_offer[0]['Paypal'];
						$email_account = $search_offer['email'];
						if ( $email_account == $receiver_email) {
							if ($payment_currency=="EUR") {
								// il a bien payé
								$user_money = $this->Connect->get('money');
								$new_money = intval($user_money) + intval($search_offer['money']);

								$this->Connect->set('money', $new_money);

								$this->History->set('BUY_MONEY', 'shop', 'paypal|'.$search_offer['money'].'|'.$txn_id);

								$this->Session->setFlash($this->Lang->get('SUCCESS_PAYPAL'), 'default.success');
								$this->redirect(array('controller' => 'shop', 'action' => 'index'));
							}
						}
					} else {
						// erreur pendant le traitement
						$this->Session->setFlash($this->Lang->get('INTERNAL_ERROR'), 'default.error');
		       			$this->redirect(array('controller' => 'shop', 'action' => 'index'));
					}
				} else {
					// erreur pendant le traitement
					$this->Session->setFlash($this->Lang->get('INTERNAL_ERROR'), 'default.error');
		       		$this->redirect(array('controller' => 'shop', 'action' => 'index'));
				}
			} else {
				// idem
				$this->Session->setFlash($this->Lang->get('INTERNAL_ERROR'), 'default.error');
		       	$this->redirect(array('controller' => 'shop', 'action' => 'index'));
			}
		} else {
			// redirection
			$this->redirect('/');
		}
	}
	/* -- */

}