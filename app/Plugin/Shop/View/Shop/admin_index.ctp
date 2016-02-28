<?php
App::import('Component', 'Shop.DiscountVoucherComponent');
$this->DiscountVoucher = new DiscountVoucherComponent;
?>
<section class="content">
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
    </div>
  </div>
</section>
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
