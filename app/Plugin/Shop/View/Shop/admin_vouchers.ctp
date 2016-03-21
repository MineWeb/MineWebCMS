<?php
App::import('Component', 'Shop.DiscountVoucherComponent');
$this->DiscountVoucher = new DiscountVoucherComponent;
?>
<section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><?= $Lang->get('SHOP__VOUCHERS_MANAGE') ?> &nbsp;&nbsp;<a class="btn btn-success" href="<?= $this->Html->url(array('action' => 'add_voucher')) ?>"><?= $Lang->get('GLOBAL__ADD') ?></a></h3>
          </div>
          <div class="box-body">

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
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><?= $Lang->get('SHOP__VOUCHERS_HISTORIES') ?></h3>
          </div>
          <div class="box-body">

            <table class="table table-bordered dataTable">
              <thead>
                <tr>
                  <th><?= $Lang->get('SHOP__VOUCHER_CODE') ?></th>
                  <th>Pseudo</th>
                  <th><?= $Lang->get('SHOP__VOUCHER_REDUCTION', array('{MONEY_NAME}' => $Configuration->getMoneyName())) ?></th>
                  <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($vouchers_histories as $key => $value) { ?>
                  <tr>
                    <td><?= $value['VouchersHistory']['code'] ?></td>
                    <td><?= (isset($usersByID[$value['VouchersHistory']['user_id']])) ? $usersByID[$value['VouchersHistory']['user_id']] : $value['VouchersHistory']['user_id'] ?></td>
                    <td><?= $value['VouchersHistory']['reduction'] ?> <?= $Configuration->getMoneyName() ?></td>
                    <td><?= $Lang->date($value['VouchersHistory']['created']) ?></td>
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
