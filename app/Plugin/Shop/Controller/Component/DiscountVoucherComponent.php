<?php

/**
* Composant qui gère les coupons de réduction
* Composant utilisé seulement pour la boutique
**/

/**
* Structure de la table Voucher
*
* id -> id
* code -> le code a rentrer
* type -> 1 ou 2 (pourcentage ou money) [Par défaut -> 1]
* reduction -> la réduction (ex: 10 pour -10€ ou -10%)
* effective_on -> effectif sur une(plusieurs) catégorie(s) ou un(plusieurs) article(s)
* limit_per_user -> limite d'utilisation de la réduction par user, si 0 = infini
* end_date -> date de fin, quand on met date infini on met 2050
* created -> date de création*
* affich -> 1 ou 2 [Par défaut 1], si 1 = un message public est affiché sur la boutique, si 2 aucun message n'est affiché
**/

class DiscountVoucherComponent extends Object {

  static private $items;
  static private $categories;

  private $controller;

  function shutdown(&$controller) {
  }

  function beforeRender(&$controller) {
  }

  function beforeRedirect() {
  }

  function initialize(&$controller) {
    // Verification des réductions actuelles
    // suppression si la date de fin est passé

    $this->controller = $controller;

    $this->Voucher = ClassRegistry::init('Shop.Voucher');

    $get_vouchers = $this->Voucher->find('all');
    foreach ($get_vouchers as $k) {
      $end = strtotime($k['Voucher']['end_date']);
      if($end < strtotime('now')) {
        $this->Voucher->delete($k['Voucher']['id']);
      }
    }
  }

  function startup(&$controller) {
  }

  function getItemNameById($id) {
    if(empty(self::$items)) {
      $this->Item = ClassRegistry::init('Shop.Item');
      $items = $this->Item->find('all');
      foreach ($items as $key => $value) {
        self::$items[$value['Item']['id']] = $value['Item']['name'];
      }
    }
    return self::$items[$id];
  }

  function getCategoryNameById($id) {
    if(empty(self::$categories)) {
      $this->Category = ClassRegistry::init('Shop.Category');
      $categories = $this->Category->find('all');
      foreach ($categories as $key => $value) {
        self::$categories[$value['Category']['id']] = $value['Category']['name'];
      }
    }
    return self::$categories[$id];
  }

  function get_vouchers() { // affiche dans une alert info les promotions en cours si elle doivent être affichées
    $this->Lang = $this->controller->Lang;
    $this->Configuration = $this->controller->Configuration;
      // le fichier de langue
    $this->Voucher = ClassRegistry::init('Shop.Voucher'); // le model principal
    $search_vouchers = $this->Voucher->find('all'); // le cherche les promos
    if(!empty($search_vouchers)) { // si il y a une promo en cours
      foreach ($search_vouchers as $k) { // un foreach si il y en a plusieurs
        $voucher = $k['Voucher'];
        if($voucher['affich'] == 1) { // si on doit l'afficher, sinon je retourne rien
          $voucher['effective_on'] = unserialize($voucher['effective_on']); // j'unserilise le effective_on qui est un array

          $return = '<div class="alert alert-info"><i class="fa fa-shopping-cart"></i> '; // début du message

            $langVars = array();

            if($voucher['effective_on']['type'] == 'categories') { // si cela concerne une catégorie

              if(count($voucher['effective_on']['value']) == 1) { // combien de catégories concernée ?
                $langMSG = 'SHOP__VOUCHER_MSG_ONE_CATEGORY'; // plusieurs
                $langVars['{CATEGORY}'] = '"'.$this->getCategoryNameById($voucher['effective_on']['value'][0]).'"';
              } else {
                $langMSG = 'SHOP__VOUCHER_MSG_MANY_CATEGORIES'; // plusieurs

                foreach ($voucher['effective_on']['value'] as $key => $value) {
                  $voucher['effective_on']['value'][$key] = $this->getCategoryNameById($value);
                }

                $langVars['{CATEGORIES}'] = '"'.implode('", "', $voucher['effective_on']['value']).'"';
              }

            } elseif ($voucher['effective_on']['type'] == 'items') { // si cela concerne un article

              if(count($voucher['effective_on']['value']) == 1) { // combien de catégories concernée ?
                $langMSG = 'SHOP__VOUCHER_MSG_ONE_ITEM'; // plusieurs
                $langVars['{ITEM}'] = '"'.$this->getItemNameById($voucher['effective_on']['value'][0]).'"';
              } else {
                $langMSG = 'SHOP__VOUCHER_MSG_MANY_ITEMS'; // plusieurs

                foreach ($voucher['effective_on']['value'] as $key => $value) {
                  $voucher['effective_on']['value'][$key] = $this->getItemNameById($value);
                }

                $langVars['{ITEMS}'] = implode('", "', $voucher['effective_on']['value']);
              }

            } elseif ($voucher['effective_on']['type'] == 'all') { // si cela concerne toute la boutique
              $langMSG = 'SHOP__VOUCHER_MSG_ALL';
            }

            $langVars['{CODE}'] = $voucher['code'];

            $langVars['{REDUCTION}'] = ' -'.$voucher['reduction'];
            if($voucher['type'] == 1) {
              $langVars['{REDUCTION}'] .= '%';
            } elseif ($voucher['type'] == 2) {
              $langVars['{REDUCTION}'] .= ' '.$this->Configuration->getMoneyName();
            }

            $return .= $this->Lang->get($langMSG, $langVars);

          $return .= '</div>';

          echo $return;

        }
      }
    }
  }

  public $vouchersUsed = array();

  private function checkIfAlreadyUsed($voucherID, $limit, $usedBy) {

    if($limit == 0) {
      return array(true, 0); //Pas de limite
    } else {

      $this->User = ClassRegistry::init('User');
      $user_id = $this->User->getKey('id');

      if(!empty($usedBy) || isset($this->vouchersUsed[$voucherID])) { // Déjà utilisé ou déjà check

        if(!isset($this->vouchersUsed[$voucherID][$user_id])) {
          $usedBy = unserialize($usedBy);
          $how_used = array_count_values($usedBy);
        } else {
          $how_used[$user_id] = intval($this->vouchersUsed[$voucherID][$user_id]) + 1;// On rajoute 1 à chaque fois (si plusieurs check pour la quantité)
        }

        if(isset($how_used[$user_id])) { // Déjà utilisé

          $how_used = $how_used[$user_id];
          if($how_used < $limit) {

            $this->vouchersUsed[$voucherID][$user_id] = $how_used;
            return array(true, $how_used);

          } else {
            return array(false, $limit);
          }

        } else { // Jamais utilisé
          $this->vouchersUsed[$voucherID][$user_id] = 0;
          return array(true, 0);
        }

      } else {  //Personne l'a utilisé
        $this->vouchersUsed[$voucherID][$user_id] = 0;
        return array(true, 0);
      }

    }

  }

  function getNewPrice($item_id, $code) { // donne le nouveau prix de l'item si il est concerné par une réduction
    $this->Voucher = ClassRegistry::init('Shop.Voucher');
    $findVoucher = $this->Voucher->find('first', array('conditions' => array('code' => $code)));
    if(!empty($findVoucher)) { // si il y a une promo en cours

      $findVoucher['Voucher']['effective_on'] = unserialize($findVoucher['Voucher']['effective_on']);

      // SI y'a pas déjà une limite et il a utilisé
      list($can_use, $how_used) = $this->checkIfAlreadyUsed($findVoucher['Voucher']['id'], $findVoucher['Voucher']['limit_per_user'], $findVoucher['Voucher']['used']);

      if($can_use) { // On peux l'utiliser

        // On cherche les infos de l'article
          $this->Item = ClassRegistry::init('Shop.Item');
          $findItem = $this->Item->find('first', array('conditions' => array('id' => $item_id)));

          if(empty($findItem)) {
            return array('status' => false, 'error' => 3); // on trouve pas l'article
          }

          $itemPrice = $findItem['Item']['price'];
          $itemCategoryID = $findItem['Item']['category'];

        // On prépare le prix si pas de modifications
          $price = $itemPrice;

        // On cherche si la catégorie est concerné par la promo
          if($findVoucher['Voucher']['effective_on']['type'] == 'categories') { // Si y'a des catégories concernées
            if(in_array($itemCategoryID, $findVoucher['Voucher']['effective_on']['value'])) { // Si celle de l'article l'est

              // On change le prix
                if($findVoucher['Voucher']['type'] == 1) { // si c'est -x%
                  $reduction = 1 - $findVoucher['Voucher']['reduction'] / 100;
                  $price = $itemPrice * $reduction;
                } elseif ($findVoucher['Voucher']['type'] == 2) { // si c'est -x€
                  $price = $itemPrice - $findVoucher['Voucher']['reduction'];
                }


            }
          }

        // On cherche si l'article est concerné par la promo
          if($findVoucher['Voucher']['effective_on']['type'] == 'items') { // Si y'a des articles concernés
            if(in_array($item_id, $findVoucher['Voucher']['effective_on']['value'])) { // Si l'article l'est

              // On change le prix
                if($findVoucher['Voucher']['type'] == 1) { // si c'est -x%
                  $reduction = 1 - $findVoucher['Voucher']['reduction'] / 100;
                  $price = $itemPrice * $reduction;
                } elseif ($findVoucher['Voucher']['type'] == 2) { // si c'est -x€
                  $price = $itemPrice - $findVoucher['Voucher']['reduction'];
                }


            }
          }

        // On vérifie que la promo ne concerne pas toute la boutique
          if($findVoucher['Voucher']['effective_on']['type'] == 'all') { // Si y'a des catégories concernées
            // On change le prix
              if($findVoucher['Voucher']['type'] == 1) { // si c'est -x%
                $reduction = 1 - $findVoucher['Voucher']['reduction'] / 100;
                $price = $itemPrice * $reduction;
              } elseif ($findVoucher['Voucher']['type'] == 2) { // si c'est -x€
                $price = $itemPrice - $findVoucher['Voucher']['reduction'];
              }
          }

        // On retourne le prix
          return array('status' => true, 'price' => $price);

      } else {
        return array('status' => false, 'error' => 2); // on a pas le droit
      }
    } else {
      return array('status' => false, 'error' => 1);  //on trouve pas la promo
    }
  }

  function set_used($user_id, $code, $voucher_used_count = 1) {
    $this->Voucher = ClassRegistry::init('Shop.Voucher');
    $search_vouchers = $this->Voucher->find('all', array('conditions' => array('code' => $code)));
    if(!empty($search_vouchers)) {

      $used = unserialize($search_vouchers[0]['Voucher']['used']);
      $i = 0;
      while ($i < $voucher_used_count) {
        $used[] = $user_id;
        $i++;
      }
      $used = serialize($used);

      $this->Voucher->read(null, $search_vouchers[0]['Voucher']['id']);
      $this->Voucher->set(array('used' => $used));
      return $this->Voucher->save();
    } else {
      return false;
    }
  }
}
