<?php

class PaymentController extends ShopAppController {

  public function beforeFilter() {
    parent::beforeFilter();
    $this->Security->unlockedActions = array('starpass', 'starpass_verif', 'ipn', 'dedipass_ipn');
  }

  /*
	* ======== Affichage de la gestion admin ===========
	*/


    public function admin_index() {
  		if($this->isConnected AND $this->User->isAdmin()) {

        $this->layout = 'admin';

        $this->set('title_for_layout', $this->Lang->get('SHOP__ADMIN_MANAGE_PAYMENT'));

        // On récupére toutes les offres

          $offers = array();
          $offersByID = array();

          $this->loadModel('Shop.Starpass');
          $offers['starpass'] = $this->Starpass->find('all');

          foreach ($offers['starpass'] as $key => $value) {
            $offersByID['starpass'][$value['Starpass']['id']] = $value['Starpass']['name'];
          }

          $this->loadModel('Shop.Paypal');
          $offers['paypal'] = $this->Paypal->find('all');

          foreach ($offers['paypal'] as $key => $value) {
            $offersByID['paypal'][$value['Paypal']['id']] = $value['Paypal']['name'];
          }

        // On récupére tous les historiques

          $histories = array();
          $usersToFind = array();

          $this->loadModel('Shop.StarpassHistory', array('order' => 'id DESC'));
          $histories['starpass'] = $this->StarpassHistory->find('all');
          foreach ($histories['starpass'] as $key => $value) {
            $usersToFind[] = $value['StarpassHistory']['user_id'];
          }

          $this->loadModel('Shop.PaypalHistory', array('order' => 'id DESC'));
          $histories['paypal'] = $this->PaypalHistory->find('all');
          foreach ($histories['paypal'] as $key => $value) {
            $usersToFind[] = $value['PaypalHistory']['user_id'];
          }

          $this->loadModel('Shop.PaysafecardHistory', array('order' => 'id DESC'));
          $histories['paysafecard'] = $this->PaysafecardHistory->find('all');
          foreach ($histories['paysafecard'] as $key => $value) {
            $usersToFind[] = $value['PaysafecardHistory']['author_id'];
          }

          $this->loadModel('Shop.DedipassHistory', array('order' => 'id DESC'));
          $histories['dedipass'] = $this->DedipassHistory->find('all');
          foreach ($histories['dedipass'] as $key => $value) {
            $usersToFind[] = $value['DedipassHistory']['user_id'];
          }


        // Les PaySafeCards c'est différents

          $this->loadModel('Shop.Paysafecard');
          $findPaysafecardsStatus = $this->Paysafecard->find('first', array('conditions' => array('amount' => '0', 'code' => 'disable', 'user_id' => 0, 'created' => '1990/00/00 15:00:00')));
    			$paysafecardsStatus = (empty($findPaysafecardsStatus)) ? true : false;

          $paysafecards = $this->Paysafecard->find('all');
          foreach ($paysafecards as $key => $value) {
            $usersToFind[] = $value['Paysafecard']['user_id'];
          }

        // On récupère tous les utilisateurs pour afficher leur pseudo

          $usersByID = array();

          $findUsers = $this->User->find('all', array('conditions' => array('id' => $usersToFind)));
          foreach ($findUsers as $key => $value) {
            $usersByID[$value['User']['id']] = $value['User']['pseudo'];
          }

        // Config de dédipass

          $this->loadModel('Shop.DedipassConfig');
          $dedipassConfig = $this->DedipassConfig->find('first');


        // On set toutes les variables
          $this->set(compact(
            'offers',
            'offersByID',
            'histories',
            'usersByID',
            'paysafecards',
            'paysafecardsStatus',
            'dedipassConfig'
          ));

      } else {
        throw new ForbiddenException();
      }
    }


  /*
	* ======== Switch du mode de paiement PaySafeCard (traitement POST) ===========
	*/

  	public function admin_toggle_paysafecard() {
  		$this->autoRender = false;
  		if($this->isConnected AND $this->User->isAdmin()) {
  			$this->loadModel('Shop.Paysafecard');

  			$paysafecard_enabled = $this->Paysafecard->find('all', array('conditions' => array('amount' => '0', 'code' => 'disable', 'user_id' => 0, 'created' => '1990/00/00 15:00:00')));
  			if(!empty($paysafecard_enabled)) {
  				$this->Paysafecard->delete($paysafecard_enabled[0]['Paysafecard']['id']);

  				$this->History->set('ENABLE_PAYSAFECARD', 'shop');

  				$this->Session->setFlash($this->Lang->get('SHOP__PAYSAFECARD_ENABLE_SUCCESS'), 'default.success');
  				$this->redirect(array('action' => 'index'));
  			} else {
  				$this->Paysafecard->read(null, $paysafecard_enabled[0]['Paysafecard']['id']);
  				$this->Paysafecard->set(array('amount' => '0', 'code' => 'disable', 'user_id' => 0, 'created' => '1990/00/00 15:00:00'));
  				$this->Paysafecard->save();

  				$this->History->set('DISABLE_PAYSAFECARD', 'shop');

  				$this->Session->setFlash($this->Lang->get('SHOP__PAYSAFECARD_DISABLE_SUCCESS'), 'default.success');
  				$this->redirect(array('action' => 'index'));
  			}

  		} else {
  			$this->redirect('/');
  		}
  	}





  /*
	* ======== Validation d'une PaySafeCard (traitement POST) ===========
	*/

  	public function admin_paysafecard_valid($id = false, $money = false) {
  		$this->autoRender = false;
  		if($this->isConnected AND $this->User->isAdmin()) {
  			if($id != false AND $money != false) {

  				$this->loadModel('Shop.Paysafecard');
  				$search = $this->Paysafecard->find('first', array('conditions' => array('id' => $id)));

  				if(!empty($search)) {

            $findPaysafecard = $search['Paysafecard'];

  					$this->History->set('BUY_MONEY', 'shop', null, $findPaysafecard['user_id']);

  					$user_money = $this->User->getFromUser('money', $findPaysafecard['user_id']);
  					$new_money = intval($user_money) + intval($money);
            $this->User->setToUser('money', $new_money, $findPaysafecard['user_id']);

  					$this->loadModel('Shop.PaysafecardMessage');
  					$this->PaysafecardMessage->create();
  					$this->PaysafecardMessage->set(array(
  						'user_id' => $findPaysafecard['user_id'],
  						'type' => 1,
  						'amount' => $findPaysafecard['amount'],
  						'added_points' => intval($money)
  					));
  					$this->PaysafecardMessage->save();

            $this->loadModel('Shop.PaysafecardHistory');
            $this->PaysafecardHistory->create();
            $this->PaysafecardHistory->set(array(
              'code' => $findPaysafecard['code'],
              'amount' => $findPaysafecard['amount'],
              'credits_gived' => intval($money),
              'user_id' => $findPaysafecard['user_id'],
              'author_id' => $this->User->getKey('id')
            ));
            $this->PaysafecardHistory->save();

            $this->Paysafecard->delete($id);
  					$this->History->set('VALID_PAYSAFECARD', 'shop');

  					$this->Session->setFlash($this->Lang->get('SHOP__PAYSAFECARD_VALID_SUCCESS'), 'default.success');
  					$this->redirect(array('action' => 'index', 'admin' => true));
  				} else {
  					$this->redirect(array('action' => 'index', 'admin' => true));
  				}
  			} else {
  				$this->redirect(array('action' => 'index', 'admin' => true));
  			}
  		} else {
  			$this->redirect('/');
  		}
  	}





  /*
	* ======== Invalidation d'une PaySafeCard (traitement POST) ===========
	*/

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
  						'to' => $search['0']['Paysafecard']['user_id'],
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





  /*
	* ======== Ajout d'une PaySafeCard (traitement AJAX) ===========
	*/

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
  								$search2 = $this->Paysafecard->find('count', array('conditions' => array('user_id' => $this->User->getKey('id'))));
  								if($search2 < 2) {
  									$this->Paysafecard->read(null, null);
  									$this->Paysafecard->set(array(
  										'amount' => $this->request->data['amount'],
  										'code' => $codes,
  										'user_id' => $this->User->getKey('id')
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



  /*
	* ======== Ajout d'une offre PayPal (affichage) ===========
	*/

  	public function admin_add_paypal() {
  		if($this->isConnected AND $this->User->isAdmin()) {

  			$this->set('title_for_layout', $this->Lang->get('SHOP__PAYPAL_OFFER_ADD'));
  			$this->layout = 'admin';
  		} else {
  			$this->redirect('/');
  		}
  	}


  /*
  * ======== Ajout d'une offre PayPal (Traitement AJAX) ===========
  */

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
              $this->Session->setFlash($this->Lang->get('SHOP__PAYPAL_OFFER_ADD_SUCCESS'), 'default.success');
              echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('SHOP__PAYPAL_OFFER_ADD_SUCCESS')));
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



  /*
	* ======== Modification d'une offre PayPal (affichage) ===========
	*/

  	public function admin_edit_paypal($id = false) {
  		if($this->isConnected AND $this->User->isAdmin()) {

  			$this->set('title_for_layout', $this->Lang->get('SHOP__PAYPAL_OFFER_EDIT'));
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



  /*
	* ======== Modification d'une offre PayPal (traitement AJAX) ===========
	*/

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
  								$this->Session->setFlash($this->Lang->get('SHOP__PAYPAL_OFFER_EDIT_SUCCESS'), 'default.success');
  								echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('SHOP__PAYPAL_OFFER_EDIT_SUCCESS')));
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



  /*
  * ======== Ajout d'une offre StarPass (affichage) ===========
  */

    public function admin_add_starpass() {
      if($this->isConnected AND $this->User->isAdmin()) {

        $this->set('title_for_layout', $this->Lang->get('SHOP__STARPASS_OFFER_ADD'));
        $this->layout = 'admin';
      } else {
        $this->redirect('/');
      }
    }



  /*
	* ======== Ajout d'une offre StarPass (traitement AJAX) ===========
	*/

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
  					$this->Session->setFlash($this->Lang->get('SHOP__STARPASS_OFFER_ADD_SUCCESS'), 'default.success');
  					echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('SHOP__STARPASS_OFFER_ADD_SUCCESS')));
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
	* ======== Modification d'une offre StarPass (affichage) ===========
	*/

  	public function admin_edit_starpass($id = false) {
  		if($this->isConnected AND $this->User->isAdmin()) {

  			$this->set('title_for_layout', $this->Lang->get('SHOP__STARPASS_OFFER_EDIT'));
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



  /*
	* ======== Modification d'une offre StarPass (traitement AJAX) ===========
	*/

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
  						$this->Session->setFlash($this->Lang->get('SHOP__STARPASS_OFFER_EDIT_SUCCESS'), 'default.success');
  						echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('SHOP__STARPASS_OFFER_EDIT_SUCCESS')));
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



  /*
	* ======== Affichage de la page avec StarPass ===========
	*/

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
  					$this->set('title_for_layout', $this->Lang->get('SHOP__STARPASS_PAYMENT'));
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



  /*
	* ======== Vérification d'une transaction StarPass ===========
	*/

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
            $this->Session->setFlash($this->Lang->get('ERROR__INTERNAL_ERROR'), 'default.error');
            $this->redirect(array('controller' => 'shop', 'action' => 'starpass', $search_starpass[0]['Starpass']['id']));
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
  			       $this->Session->setFlash($this->Lang->get('SHOP__STARPASS_PAYMENT_ERROR'), 'default.error');
  			       $this->redirect(array('controller' => 'shop', 'action' => 'starpass', $search_starpass[0]['Starpass']['id']));
  				}
  				else
  				{
  			       /* Le serveur a répondu "OUI" */
  					$user_money = $this->User->getKey('money');
  					$new_money = intval($user_money) + intval($search_starpass[0]['Starpass']['money']);

  					$this->User->setKey('money', $new_money);

  					$this->History->set('BUY_MONEY', 'shop'); //L'historique global

            // On l'ajoute dans l'historique des paiements
            $this->loadModel('Shop.StarpassHistory');

            $this->StarpassHistory->create();
            $this->StarpassHistory->set(array(
              'code' => $codes,
              'user_id' => $this->User->getKey('id'),
              'offer_id' => $search_starpass[0]['Starpass']['id'],
              'credits_gived' => intval($search_starpass[0]['Starpass']['money'])
            ));
            $this->StarpassHistory->save();

  					$this->Session->setFlash($this->Lang->get('SHOP__STARPASS_PAYMENT_SUCCESS'), 'default.success');
  					$this->redirect(array('controller' => 'shop', 'action' => 'index'));
  				}
  			} else {
  				$this->redirect(array('controller' => 'shop', 'action' => 'index'));
  			}
  		} else {
  			$this->redirect(array('controller' => 'shop', 'action' => 'index'));
  		}
  	}




  /*
	* ======== Vérification d'une transaction PayPal ===========
	*/

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
  											'payment_amount' => $payment_amount,
                        'credits_gived' => $findOffer['Paypal']['money']
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




  /*
	* ======== Affichage Dédipass ===========
	*/

    public function dedipass() {
      if($this->isConnected AND $this->Permissions->can('CREDIT_ACCOUNT')) {
  			$this->loadModel('Shop.DedipassConfig');
  			$search = $this->DedipassConfig->find('first');
				if(!empty($search)) {
					$this->set('dedipassPublicKey', $search['DedipassConfig']['public_key']);
					$this->set('title_for_layout', $this->Lang->get('SHOP__DEDIPASS_PAYMENT'));
					$this->layout = $this->Configuration->getKey('layout');
				} else {
					throw new NotFoundException();
				}
  		} else {
  			throw new ForbiddenException();
  		}
    }



  /*
	* ======== Affichage Dédipass ===========
	*/



    public function admin_toggle_dedipass() {
      $this->autoRender = false;
      if($this->isConnected AND $this->User->isAdmin()) {

        $this->loadModel('Shop.DedipassConfig');
        $findConfig = $this->DedipassConfig->find('first');

        if(empty($findConfig['DedipassConfig'])) {
          $this->Session->setFlash($this->Lang->get('SHOP__DEDIPASS_TOGGLE_ERROR_NO_CONFIG'), 'default.error');
          $this->redirect(array('action' => 'index'));
        }

        $dedipassStatus = (isset($findConfig['DedipassConfig']['status']) && $findConfig['DedipassConfig']['status']) ? 0 : 1;

        $this->DedipassConfig->read(null, 1);
        $this->DedipassConfig->set(array('status' => $dedipassStatus));
        $this->DedipassConfig->save();

        if($dedipassStatus) {
          $this->Session->setFlash($this->Lang->get('SHOP__DEDIPASS_TOGGLE_ENABLE_SUCCESS'), 'default.success');
        } else {
          $this->Session->setFlash($this->Lang->get('SHOP__DEDIPASS_TOGGLE_DISABLE_SUCCESS'), 'default.success');
        }
        $this->redirect(array('action' => 'index'));

      }
      throw new ForbiddenException();
    }




  /*
	* ======== Configuration Dédipass ===========
	*/

    public function admin_dedipass_config() {
      $this->autoRender = false;
      if($this->isConnected AND $this->User->isAdmin()) {
        if($this->request->is('ajax')) {
          if(!empty($this->request->data['publicKey'])) {

            $this->loadModel('Shop.DedipassConfig');

            $dedipassConfig = $this->DedipassConfig->find('first');
            if(empty($dedipassConfig)) {
              $this->DedipassConfig->create();
            } else {
              $this->DedipassConfig->read(null, 1);
            }
            $this->DedipassConfig->set(array(
              'public_key' => $this->request->data['publicKey']
            ));
            $this->DedipassConfig->save();

            $this->History->set('EDIT_DEDIPASS_CONFIG', 'shop');

            echo json_encode(array('statut' => true, 'msg' => $this->Lang->get('SHOP__DEDIPASS_EDIT_CONFIG_SUCCESS')));
            return;

          } else {
            echo json_encode(array('statut' => false, 'msg' => $this->Lang->get('ERROR__FILL_ALL_FIELDS')));
            return;
          }
        }
      }
      throw new ForbiddenException();
    }



  /*
	* ======== Vérification d'une transaction Dédipass ===========
	*/

    public function dedipass_ipn() {
      $this->autoRender = false;
  		if($this->request->is('post') && $this->Permissions->can('CREDIT_ACCOUNT')) {
  			//$public_key  = isset($this->request->data['key']) ? preg_replace('/[^a-zA-Z0-9]+/', '', $this->request->data['key']) : '';
        $this->loadModel('Shop.DedipassConfig');
  			$search = $this->DedipassConfig->find('first');
        $public_key = $search['DedipassConfig']['public_key'];
  			$code = isset($this->request->data['code']) ? preg_replace('/[^a-zA-Z0-9]+/', '', $this->request->data['code']) : '';
  			$rate = isset($this->request->data['rate']) ? preg_replace('/[^a-zA-Z0-9\-]+/', '', $this->request->data['rate']) : '';
  			// Validation des champs
  			if(empty($code)) {
          $this->Session->setFlash($this->Lang->get('SHOP__DEDIPASS_PAYMENT_ERROR_EMPTY_CODE'), 'default.error');
          $this->redirect(array('action' => 'dedipass'));
  			} elseif (empty($rate)) {
          $this->Session->setFlash($this->Lang->get('SHOP__DEDIPASS_PAYMENT_ERROR_EMPTY_RATE'), 'default.error');
          $this->redirect(array('action' => 'dedipass'));
  			} else {

  				if($this->isConnected) {

  				  	$dedipass = file_get_contents('http://api.dedipass.com/v1/pay/?key='.$public_key.'&rate='.$rate.'&code='.$code);
  				  	$dedipass = json_decode($dedipass);

  				  	$code = $dedipass->code; // Le code
  				  	$rate = $dedipass->rate; // Le palier

  				  	if($dedipass->status == 'success') {
  				    	// Le code est valide
  				    	$virtual_currency = $dedipass->virtual_currency; // Nombre de points à créditer à l'utilisateur

  				    	$user_money = $this->User->getKey('money');
  				    	$new_money = $user_money + floatval($virtual_currency);
                $this->User->setKey('money', $new_money);

  						  $this->History->set('BUY_MONEY_DEDIPASS', 'buy');

                $this->loadModel('Shop.DedipassHistory');
                $this->DedipassHistory->create();
                $this->DedipassHistory->set(array(
                  'user_id' => $this->User->getKey('id'),
                  'code' => $code,
                  'rate' => $rate,
                  'credits_gived' => $virtual_currency
                ));
                $this->DedipassHistory->save();

  				    	$this->Session->setFlash($this->Lang->get('SHOP__DEDIPASS_PAYMENT_SUCCESS', array('{MONEY}' => $virtual_currency, '{MONEY_NAME}' => $this->Configuration->getMoneyName())), 'default.success');
  						  $this->redirect(array('controller' => 'shop', 'action' => 'index'));

  				  	} else {
                $this->Session->setFlash($this->Lang->get('SHOP__DEDIPASS_PAYMENT_ERROR_INVAID_CODE'), 'default.error');
                $this->redirect(array('action' => 'dedipass'));
  				  	}

  				} else {
            $this->Session->setFlash($this->Lang->get('SHOP__DEDIPASS_PAYMENT_ERROR_NOT_CONNECTED', array('{CODE}' => $code)), 'default.error');
            $this->redirect(array('controller' => 'shop', 'action' => 'index'));
  				}
  			}
  		}
  		throw new NotFoundException();
    }


}
