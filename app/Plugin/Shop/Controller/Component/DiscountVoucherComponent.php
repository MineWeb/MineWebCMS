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
                $langVars['{CATEGORY}'] = '"'.$voucher['effective_on']['value'][0].'"';
              } else {
                $langMSG = 'SHOP__VOUCHER_MSG_MANY_CATEGORIES'; // plusieurs
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

  function get_new_price($price, $category, $item, $code) { // donne le nouveau prix de l'item si il est concerné par une réduction
    $this->Voucher = ClassRegistry::init('Shop.Voucher');
    $search_vouchers = $this->Voucher->find('all', array('conditions' => array('code' => $code)));
    if(!empty($search_vouchers)) { // si il y a une promo en cours

      // SI y'a pas déjà une limite et il a utilisé
      if($search_vouchers[0]['Voucher']['limit_per_user'] == 0) {
        $can_use = true;
      } else {
        $this->User = ClassRegistry::init('User');
        $used = unserialize($search_vouchers[0]['Voucher']['used']);
        if(!empty($used)) {
          $how_used = array_count_values($used);
          if(isset($how_used[$this->User->getKey('id')])) {
            $how_used = $how_used[$this->User->getKey('id')];
          } else {
            $how_used = 0;
          }
        } else {
          $how_used = 0;
        }
        if($how_used < $search_vouchers[0]['Voucher']['limit_per_user']) {
          $can_use = true;
        } else {
          $can_use = false;
        }
      }

      if($can_use) {

        $this->Category = ClassRegistry::init('Shop.Category');
        $search_category = $this->Category->find('all', array('conditions' => array('id' => $category)));
        $category = $search_category['0']['Category']['name'];
        foreach ($search_vouchers as $k) { // une boucle de tout les promos
          $voucher = $k['Voucher'];
          $voucher['effective_on'] = unserialize($voucher['effective_on']); // j'unserilise le effective_on qui est un array
          if($voucher['effective_on']['type'] == 'categories' OR $voucher['effective_on']['type'] == 'items') {
            // si une catégorie/item ou plusieurs sont concernés par la promo
            foreach ($voucher['effective_on']['value'] as $key => $value) { // boule des catégories/items ou la promo est effective
              if($category == $value OR $item == $value) { // si une des catégories/item en promo correspond à la catégorie de l'item ou à l'item même
                if($voucher['type'] == 1) { // si c'est -x%
                  $reduction = 1 - $voucher['reduction'] / 100;
                  $price = $price * $reduction;
                } elseif ($voucher['type'] == 2) { // si c'est -x€
                  $price = $price - $voucher['reduction'];
                }
              }
            }
          } elseif ($voucher['effective_on']['type'] == 'all') {
            // si toute la boutique est concernée
            if($voucher['type'] == 1) { // si c'est -x%
              $reduction = 1 - $voucher['reduction'] / 100;
              $price = $price * $reduction;
            } elseif ($voucher['type'] == 2) { // si c'est -x€
              $price = $price - $voucher['reduction'];
            }
          }
        }
        return $price;
      } else {
        return $price;
      }
    } else {
      return $price;
    }
  }

  function set_used($user_id, $code) {
    $this->Voucher = ClassRegistry::init('Shop.Voucher');
    $search_vouchers = $this->Voucher->find('all', array('conditions' => array('code' => $code)));
    if(!empty($search_vouchers)) {

      $used = unserialize($search_vouchers[0]['Voucher']['used']);
      $used[] = $user_id;
      $used = serialize($used);

      $this->Voucher->read(null, $search_vouchers[0]['Voucher']['id']);
      $this->Voucher->set(array('used' => $used));
      return $this->Voucher->save();
    } else {
      return false;
    }
  }
}
