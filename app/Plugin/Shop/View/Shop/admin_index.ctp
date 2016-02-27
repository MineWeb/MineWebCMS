<?php
App::import('Component', 'Shop.DiscountVoucherComponent');
$this->DiscountVoucher = new DiscountVoucherComponent;
?>
<section class="content">
  <div class="row">
    <div class="col-md-6">
      <a class="btn btn-app btn-block" href="#" data-toggle="modal" data-target="#manage_vouchers">
          <i class="fa fa-shopping-cart"></i> <?= $Lang->get('SHOP__VOUCHERS_MANAGE') ?>
      </a>
    </div>
    <div class="col-md-6">
      <a class="btn btn-app btn-block" id="show" href="#">
          <i class="fa fa-money"></i> <?= $Lang->get('SHOP__ADMIN_PART_2') ?>
      </a>
      <a class="btn btn-app btn-block" id="hide" href="#" style="display:none;">
          <i class="fa fa-money"></i> <?= $Lang->get('SHOP__ADMIN_PART_1') ?>
      </a>
    </div>
  </div>
  <hr>
  <div id="1">
    <div class="row">
      <div class="col-md-6">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><?= $Lang->get('SHOP__ITEMS_AVAILABLE') ?> &nbsp;&nbsp;<a href="<?php if(!empty($search_categories)) { ?><?= $this->Html->url(array('controller' => 'shop', 'action' => 'add_item', 'admin' => true)) ?><?php } ?>" class="btn btn-success<?php if(empty($search_categories)) { echo ' disabled'; } ?>"><?= $Lang->get('GLOBAL__ADD') ?></a></h3>
          </div>
          <div class="box-body">

            <table class="table table-bordered dataTable">
              <thead>
                <tr>
                  <th><?= $Lang->get('GLOBAL__NAME') ?></th>
                  <th><?= $Lang->get('SHOP__ITEM_PRICE') ?></th>
                  <th><?= $Lang->get('SHOP__CATEGORY') ?></th>
                  <th class="right"><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($search_items as $value => $v) { ?>
                  <tr>
                    <td><?= $v["Item"]["name"] ?></td>
                    <td><?= $v["Item"]["price"] ?> <?= $Configuration->getMoneyName() ?></td>
                    <td><?= $categories[$v["Item"]["category"]]['name'] ?></td>
                    <td class="right">
                      <a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'edit/'.$v["Item"]["id"], 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('GLOBAL__EDIT') ?></a>
                      <a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'shop', 'action' => 'delete/item/'.$v["Item"]["id"], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('GLOBAL__DELETE') ?></a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>

          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><?= $Lang->get('SHOP__CATEGORIES') ?> &nbsp;&nbsp;<a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'add_category', 'admin' => true)) ?>" class="btn btn-success"><?= $Lang->get('GLOBAL__ADD') ?></a></h3>
          </div>
          <div class="box-body">

            <table class="table table-bordered dataTable">
              <thead>
                <tr>
                  <th><?= $Lang->get('GLOBAL__NAME') ?></th>
                  <th class="right"><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($search_categories as $value => $v) { ?>
                  <tr>
                    <td><?= $v["Category"]["name"] ?></td>
                    <td class="right">
                      <a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'shop', 'action' => 'delete/category/'.$v["Category"]["id"], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('GLOBAL__DELETE') ?></button>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>

          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><?= $Lang->get('SHOP__HISTORY_PURCHASES') ?></h3>
          </div>
          <div class="box-body">

            <table class="table table-bordered dataTable">
              <thead>
                <tr>
                  <th><?= $Lang->get('SHOP__ITEM') ?></th>
                  <th>Pseudo</th>
                  <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($History->get('SHOP', false, false, 'BUY_ITEM') as $value => $v) { ?>
                  <tr>
                    <td><?= $v['History']['other'] ?></td>
                    <td><?= $v['History']['author'] ?></td>
                    <td><?= $Lang->date($v['History']['created']) ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>

          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><?= $Lang->get('SHOP__HISTORY_PURCHASES_MONEY', array('{MONEY_NAME}' => $Configuration->getMoneyName())) ?></h3>
          </div>
          <div class="box-body">

            <table class="table table-bordered dataTable">
              <thead>
                <tr>
                  <th>Pseudo</th>
                  <th><?= $Lang->get('GLOBAL__TYPE') ?></th>
                  <th><?= ucfirst($Configuration->getMoneyName()) ?></th>
                  <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                  <th><?= $Lang->get('SHOP__PAYPAL_PAYMENT_ID') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($History->get('SHOP', false, false, 'BUY_MONEY') as $value => $v) { ?>
                <?php
                $other = explode('|', $v['History']['other']);
                $type = $other[0];
                $money = $other[1];
                if($type == "paypal") {
                  $id = $other[2];
                } else {
                  $id = $Lang->get('GLOBAL__UNDEFINED');
                }
                ?>
                  <tr>
                    <td><?= $v['History']['author'] ?></td>
                    <td><?= ucfirst($type) ?></td>
                    <td><?= $money ?></td>
                    <td><?= $Lang->date($v['History']['created']) ?></td>
                    <td><?= $id ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>

          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="2" style="display:none;">
    <div class="row">
      <div class="col-md-6">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><?= $Lang->get('SHOP__PAYSAFECARD') ?>&nbsp;&nbsp;<a href="<?= $this->Html->url(array('controller' => 'payment', 'action' => 'toggle_paysafecard', 'admin' => true)) ?>" class="btn btn-success"><?php if($paysafecard_enabled) { echo $Lang->get('GLOBAL__DISABLE'); } else { echo $Lang->get('GLOBAL__ENABLE'); } ?></a></h3>
          </div>
          <div class="box-body">

            <table class="table table-bordered dataTable">
              <thead>
                <tr>
                  <th><?= $Lang->get('USER__USERNAME') ?></th>
                  <th><?= $Lang->get('SHOP__GLOBAL_AMOUNT') ?></th>
                  <th><?= $Lang->get('SHOP__VOUCHER_CODE') ?></th>
                  <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                  <th class="right"><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php if(!empty($psc)) { ?>
                  <?php foreach ($psc as $key => $value) { ?>
                    <tr>
                      <td><?= $value['Paysafecard']['author'] ?></td>
                      <td><?= $value['Paysafecard']['amount'] ?></td>
                      <td><?= $value['Paysafecard']['code'] ?></td>
                      <td><?= $Lang->date($value['Paysafecard']['created']) ?></td>
                      <td>
                        <a href="#" onClick="howmuch(<?= $value['Paysafecard']['id'] ?>)" class="btn btn-success"><?= $Lang->get('SHOP__PAYSAFECARD_ACCEPT') ?></a>
                        <a href="<?= $this->Html->url(array('controller' => 'payment', 'action' => 'paysafecard_invalid/'.$value['Paysafecard']['id'])) ?>" class="btn btn-danger"><?= $Lang->get('SHOP__PAYSAFECARD_REFUSE') ?></a>
                      </td>
                    </tr>
                  <?php } ?>
                <?php } ?>
              </tbody>
            </table>

          </div>
        </div>
      </div>
      <script>
      function howmuch(id) {
          var money = prompt("<?= $Lang->get('SHOP__PAYSAFECARD_VALID_CONFIRM') ?>");

          if (money != null) {
              document.location = '<?= $this->Html->url(array('controller' => 'payment', 'action' => 'paysafecard_valid/')) ?>/'+id+'/'+money;
          } else {
            return false;
          }
      }
      </script>
      <div class="col-md-6">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><?= $Lang->get('SHOP__PAYPAL_OFFERS') ?> &nbsp;&nbsp;<a href="<?= $this->Html->url(array('controller' => 'payment', 'action' => 'add_paypal', 'admin' => true)) ?>" class="btn btn-success"><?= $Lang->get('GLOBAL__ADD') ?></a></h3>
          </div>
          <div class="box-body">

            <table class="table table-bordered">
              <thead>
                <tr>
                  <th><?= $Lang->get('GLOBAL__NAME') ?></th>
                  <th><?= $Lang->get('MAIL') ?></th>
                  <th><?= $Lang->get('SHOP__ITEM_PRICE') ?></th>
                  <th><?= ucfirst($Configuration->getMoneyName()) ?></th>
                  <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                  <th class="right"><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php if(!empty($paypal_offers)) { ?>
                  <?php foreach ($paypal_offers as $key => $value) { ?>
                    <tr>
                      <td><?= $value['Paypal']['name'] ?></td>
                      <td><?= $value['Paypal']['email'] ?></td>
                      <td><?= $value['Paypal']['price'] ?></td>
                      <td><?= $value['Paypal']['money'] ?></td>
                      <td><?= $Lang->date($value['Paypal']['created']) ?></td>
                      <td class="right">
                        <a href="<?= $this->Html->url(array('controller' => 'payment', 'action' => 'edit_paypal/'.$value["Paypal"]["id"], 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('GLOBAL__EDIT') ?></a>
                        <a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'payment', 'action' => 'delete/paypal/'.$value["Paypal"]["id"], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('GLOBAL__DELETE') ?></a>
                      </td>
                    </tr>
                  <?php } ?>
                <?php } ?>
              </tbody>
            </table>

          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><?= $Lang->get('SHOP__HISTORY_TRANSFER_MONEY') ?> <?= $Lang->get('GLOBAL__OF') ?> <?= $Configuration->getMoneyName() ?></h3>
          </div>
          <div class="box-body">

            <table class="table table-bordered dataTable">
              <thead>
              <tr>
                <th>Pseudo</th>
                <th><?= ucfirst($Configuration->getMoneyName()) ?></th>
                <th><?= $Lang->get('SHOP__USER_POINTS_TRANSFER_WHO') ?></th>
                <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($History->get('SHOP', false, false, 'SEND_MONEY') as $value => $v) { ?>
              <?php
              $other = explode('|', $v['History']['other']);
              ?>
                <tr>
                  <td><?= $v['History']['author'] ?></td>
                  <td><?= $other[1] ?></td>
                  <td><?= $other[0] ?></td>
                  <td><?= $Lang->date($v['History']['created']) ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>

          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><?= $Lang->get('SHOP__STARPASS_OFFERS') ?> &nbsp;&nbsp;<a href="<?= $this->Html->url(array('controller' => 'payment', 'action' => 'add_starpass', 'admin' => true)) ?>" class="btn btn-success"><?= $Lang->get('GLOBAL__ADD') ?></a></h3>
          </div>
          <div class="box-body">

            <table class="table table-bordered">
              <thead>
                <tr>
                  <th><?= $Lang->get('GLOBAL__NAME') ?></th>
                  <th><?= ucfirst($Configuration->getMoneyName()) ?></th>
                  <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                  <th><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php if(!empty($starpass_offers)) { ?>
                  <?php foreach ($starpass_offers as $key => $value) { ?>
                    <tr>
                      <td><?= $value['Starpass']['name'] ?></td>
                      <td><?= $value['Starpass']['money'] ?></td>
                      <td><?= $Lang->date($value['Starpass']['created']) ?></td>
                      <td class="right">
                        <a href="<?= $this->Html->url(array('controller' => 'payment', 'action' => 'edit_starpass/'.$value["Starpass"]["id"], 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('GLOBAL__EDIT') ?></button>
                        <a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'shop', 'action' => 'delete/starpass/'.$value["Starpass"]["id"], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('GLOBAL__DELETE') ?></button>
                      </td>
                    </tr>
                  <?php } ?>
                <?php } ?>
              </tbody>
            </table>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script>
$( "#show" ).click(function() {
  $('#1').fadeOut(250);
  $('#2').fadeIn(500).show();
  $('#show').hide();
  $('#hide').show();
});
$( "#hide" ).click(function() {
  $('#2').fadeOut(250);
  $('#1').fadeIn(500);
  $('#hide').hide();
  $('#show').show();
});
function confirmDel(url) {
  if (confirm("<?= $Lang->get('GLOBAL__CONFIRM_DELETE') ?>"))
    window.location.href=''+url+'';
  else
    return false;
}
</script>
<div class="modal fade" id="manage_vouchers" tabindex="-1" role="dialog" aria-labelledby="manage_vouchersLabel" aria-hidden="true" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?= $Lang->get('SHOP__VOUCHERS_MANAGE') ?></h4>
      </div>
      <div class="modal-body">
        <a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'add_voucher', 'admin' => true)) ?>" class="btn btn-success btn-block"><?= $Lang->get('SHOP__VOUCHER_ADD') ?></a><br><br>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th><?= $Lang->get('SHOP__VOUCHER_CODE') ?></th>
              <th><?= $Lang->get('SHOP__VOUCHER_END_DATE') ?></th>
              <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
              <th><?= $Lang->get('SHOP__VOUCHER_LIMIT_SHORT') ?></th>
              <th><?= $Lang->get('SHOP__VOUCHER_DISPLAYED') ?></th>
              <th><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($vouchers as $key => $value) { ?>
              <tr>
                <td><?= $value['Voucher']['code'] ?></td>
                <td><?= $Lang->date($value['Voucher']['end_date']) ?></td>
                <td><?= $Lang->date($value['Voucher']['created']) ?></td>
                <td><?= $value['Voucher']['limit_per_user'] ?></td>
                <td>
                  <?php
                    if($value['Voucher']['affich'] == 1) {
                      echo $Lang->get('GLOBAL__YES');
                    } else {
                      echo $Lang->get('GLOBAL__NO');
                    }
                  ?>
                </td>
                <td>
                  <a href="<?= $this->Html->url(array('controller' => 'Shop', 'admin' => true, 'action' => 'delete_voucher/'.$value['Voucher']['id'])) ?>" class="btn btn-danger"><?= $Lang->get('GLOBAL__DELETE') ?></a>
                </td>
              </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= $Lang->get('GLOBAL__CANCEL') ?></button>
      </div>
    </div>
  </div>
</div>
