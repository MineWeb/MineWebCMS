<style media="screen">
table tr td:last-child {
  white-space: nowrap;
  width: 1px;
}
table tr td:last-child > div.btn-group {
  width: 170px;
}
</style>
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('SHOP__ADMIN_MANAGE_PAYMENT') ?></h3>
        </div>
        <div class="box-body">

          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_starpass" data-toggle="tab" aria-expanded="true">StarPass</a></li>
              <li class=""><a href="#tab_paypal" data-toggle="tab" aria-expanded="false">PayPal</a></li>
              <li class=""><a href="#tab_psc" data-toggle="tab" aria-expanded="false">PaySafeCard</a></li>
              <li class=""><a href="#tab_dedipass" data-toggle="tab" aria-expanded="false">Dédipass</a></li>
              <li class=""><a href="#tab_points_transfer" data-toggle="tab" aria-expanded="false"><?= $Lang->get('SHOP__USER_POINTS_TRANSFER_ADMIN') ?></a></li>
              <?= $Module->loadModules('shop_payments_modal_admin_tab') ?>
              <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
            </ul>
            <div class="tab-content">
              <?= $Module->loadModules('shop_payments_modal_admin_tab_content') ?>
              <div class="tab-pane active" id="tab_starpass">

                <h3><?= $Lang->get('SHOP__STARPASS_OFFERS') ?> <a href="<?= $this->Html->url(array('controller' => 'payment', 'action' => 'add_starpass', 'admin' => true)) ?>" class="btn btn-success pull-right"><?= $Lang->get('GLOBAL__ADD') ?></a></h3>

                <br><br>

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
                    <?php if(isset($offers['starpass'])) { ?>
                      <?php foreach ($offers['starpass'] as $key => $value) { ?>
                        <tr>
                          <td><?= $value['Starpass']['name'] ?></td>
                          <td><?= $value['Starpass']['money'] ?></td>
                          <td><?= $Lang->date($value['Starpass']['created']) ?></td>
                          <td>
                            <div class="btn-group" role="group">
                              <a href="<?= $this->Html->url(array('controller' => 'payment', 'action' => 'edit_starpass/'.$value["Starpass"]["id"], 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('GLOBAL__EDIT') ?></a>
                              <a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'shop', 'action' => 'delete/starpass/'.$value["Starpass"]["id"], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('GLOBAL__DELETE') ?></a>
                            </div>
                          </td>
                        </tr>
                      <?php } ?>
                    <?php } ?>
                  </tbody>
                </table>

                <hr>

                <h3><?= $Lang->get('SHOP__STARPASS_HISTORIES') ?></h3>

                <table class="table table-bordered dataTable">
                  <thead>
                    <tr>
                      <th><?= $Lang->get('SHOP__STARPASS_CODE') ?></th>
                      <th><?= $Lang->get('USER__USERNAME') ?></th>
                      <th><?= $Lang->get('SHOP__STARPASS_OFFER') ?></th>
                      <th><?= ucfirst($Configuration->getMoneyName()) ?></th>
                      <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(isset($histories['starpass'])) { ?>
                      <?php foreach ($histories['starpass'] as $key => $value) { ?>
                        <tr>
                          <td><?= $value['StarpassHistory']['code'] ?></td>
                          <td><?= (isset($usersByID[$value['StarpassHistory']['user_id']])) ? $usersByID[$value['StarpassHistory']['user_id']] : $value['StarpassHistory']['user_id'] ?></td>
                          <td><?= (isset($offersByID['starpass'][$value['StarpassHistory']['offer_id']])) ? $offersByID['starpass'][$value['StarpassHistory']['offer_id']] : $value['StarpassHistory']['offer_id'] ?></td>
                          <td><?= $value['StarpassHistory']['credits_gived'] ?></td>
                          <td><?= $Lang->date($value['StarpassHistory']['created']) ?></td>
                        </tr>
                      <?php } ?>
                    <?php } ?>
                  </tbody>
                </table>

              </div>

              <div class="tab-pane" id="tab_paypal">

                <h3><?= $Lang->get('SHOP__PAYPAL_OFFERS') ?> <a href="<?= $this->Html->url(array('controller' => 'payment', 'action' => 'add_paypal', 'admin' => true)) ?>" class="btn btn-success pull-right"><?= $Lang->get('GLOBAL__ADD') ?></a></h3>

                <br><br>

                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th><?= $Lang->get('GLOBAL__NAME') ?></th>
                      <th><?= $Lang->get('SHOP__PAYPAL_MAIL') ?></th>
                      <th><?= $Lang->get('SHOP__ITEM_PRICE') ?></th>
                      <th><?= ucfirst($Configuration->getMoneyName()) ?></th>
                      <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                      <th><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(isset($offers['paypal'])) { ?>
                      <?php foreach ($offers['paypal'] as $key => $value) { ?>
                        <tr>
                          <td><?= $value['Paypal']['name'] ?></td>
                          <td><?= $value['Paypal']['email'] ?></td>
                          <td><?= $value['Paypal']['price'] ?></td>
                          <td><?= $value['Paypal']['money'] ?></td>
                          <td><?= $Lang->date($value['Paypal']['created']) ?></td>
                          <td>
                            <div class="btn-group" role="group">
                              <a href="<?= $this->Html->url(array('controller' => 'payment', 'action' => 'edit_paypal/'.$value["Paypal"]["id"], 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('GLOBAL__EDIT') ?></a>
                              <a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'payment', 'action' => 'delete/paypal/'.$value["Paypal"]["id"], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('GLOBAL__DELETE') ?></a>
                            </div>
                          </td>
                        </tr>
                      <?php } ?>
                    <?php } ?>
                  </tbody>
                </table>

                <hr>

                <h3><?= $Lang->get('SHOP__PAYPAL_HISTORIES') ?></h3>

                <table class="table table-bordered dataTable">
                  <thead>
                    <tr>
                      <th><?= $Lang->get('SHOP__PAYPAL_PAYMENT_ID') ?></th>
                      <th><?= $Lang->get('USER__USERNAME') ?></th>
                      <th><?= $Lang->get('SHOP__PAYPAL_OFFER') ?></th>
                      <th><?= $Lang->get('SHOP__GLOBAL_AMOUNT') ?></th>
                      <th><?= ucfirst($Configuration->getMoneyName()) ?></th>
                      <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(isset($histories['paypal'])) { ?>
                      <?php foreach ($histories['paypal'] as $key => $value) { ?>
                        <tr>
                          <td><?= $value['PaypalHistory']['payment_id'] ?></td>
                          <td><?= (isset($usersByID[$value['PaypalHistory']['user_id']])) ? $usersByID[$value['PaypalHistory']['user_id']] : $value['PaypalHistory']['user_id'] ?></td>
                          <td><?= (isset($offersByID['starpass'][$value['PaypalHistory']['offer_id']])) ? $offersByID['starpass'][$value['PaypalHistory']['offer_id']] : $value['PaypalHistory']['offer_id'] ?></td>
                          <td><?= $value['PaypalHistory']['payment_amount'] ?></td>
                          <td><?= $value['PaypalHistory']['credits_gived'] ?></td>
                          <td><?= $Lang->date($value['PaypalHistory']['created']) ?></td>
                        </tr>
                      <?php } ?>
                    <?php } ?>
                  </tbody>
                </table>

              </div>

              <div class="tab-pane" id="tab_psc">

                <h3><?= $Lang->get('SHOP__PAYSAFECARD_ADMIN_TITLE') ?> <a href="<?= $this->Html->url(array('action' => 'toggle_paysafecard')) ?>" class="btn btn-<?= ($paysafecardsStatus) ? 'danger' : 'success' ?> pull-right"><?= ($paysafecardsStatus) ? $Lang->get('GLOBAL__DISABLE') : $Lang->get('GLOBAL__ENABLE') ?></a></h3>

                <br><br>

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
                    <?php if(isset($paysafecards)) { ?>
                      <?php foreach ($paysafecards as $key => $value) { ?>
                        <?php if($value['Paysafecard']['user_id'] != "0") { ?>
                          <tr>
                            <td><?= $usersByID[$value['Paysafecard']['user_id']] ?></td>
                            <td><?= $value['Paysafecard']['amount'] ?></td>
                            <td><?= $value['Paysafecard']['code'] ?></td>
                            <td><?= $Lang->date($value['Paysafecard']['created']) ?></td>
                            <td>
                              <a href="#" onClick="howmuch(<?= $value['Paysafecard']['id'] ?>)" class="btn btn-success"><?= $Lang->get('SHOP__PAYSAFECARD_ACCEPT') ?></a>
                              <a href="<?= $this->Html->url(array('action' => 'paysafecard_invalid/'.$value['Paysafecard']['id'])) ?>" class="btn btn-danger"><?= $Lang->get('SHOP__PAYSAFECARD_REFUSE') ?></a>
                            </td>
                          </tr>
                        <?php } ?>
                      <?php } ?>
                    <?php } ?>
                  </tbody>
                </table>

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


                <hr>

                <h3><?= $Lang->get('SHOP__PAYSAFECARD_HISTORIES') ?></h3>

                <table class="table table-bordered dataTable">
                  <thead>
                    <tr>
                      <th><?= $Lang->get('SHOP__PAYSAFECARD_CODE') ?></th>
                      <th><?= $Lang->get('USER__USERNAME') ?></th>
                      <th><?= $Lang->get('SHOP__PAYSAFECARD_VALID_USER') ?></th>
                      <th><?= $Lang->get('SHOP__GLOBAL_AMOUNT') ?></th>
                      <th><?= ucfirst($Configuration->getMoneyName()) ?></th>
                      <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(isset($histories['paysafecard'])) { ?>
                      <?php foreach ($histories['paysafecard'] as $key => $value) { ?>
                        <tr>
                          <td><?= $value['PaysafecardHistory']['code'] ?></td>
                          <td><?= (isset($usersByID[$value['PaysafecardHistory']['user_id']])) ? $usersByID[$value['PaysafecardHistory']['user_id']] : $value['PaysafecardHistory']['user_id'] ?></td>
                          <td><?= (isset($usersByID[$value['PaysafecardHistory']['author_id']])) ? $usersByID[$value['PaysafecardHistory']['author_id']] : $value['PaysafecardHistory']['author_id'] ?></td>
                          <td><?= $value['PaysafecardHistory']['amount'] ?></td>
                          <td><?= $value['PaysafecardHistory']['credits_gived'] ?></td>
                          <td><?= $Lang->date($value['PaysafecardHistory']['created']) ?></td>
                        </tr>
                      <?php } ?>
                    <?php } ?>
                  </tbody>
                </table>


              </div>

              <div class="tab-pane" id="tab_dedipass">

                <h3><?= $Lang->get('SHOP__DEDIPASS_CONFIGURATION') ?> <a href="<?= $this->Html->url(array('action' => 'toggle_dedipass')) ?>" class="btn btn-<?= (isset($dedipassConfig['DedipassConfig']['status']) && $dedipassConfig['DedipassConfig']['status']) ? 'danger' : 'success' ?> pull-right"><?= (isset($dedipassConfig['DedipassConfig']['status']) && $dedipassConfig['DedipassConfig']['status']) ? $Lang->get('GLOBAL__DISABLE') : $Lang->get('GLOBAL__ENABLE') ?></a></h3>

                <form action="<?= $this->Html->url(array('action' => 'dedipass_config')) ?>" data-ajax="true">

                  <div class="form-group">
                    <label><?= $Lang->get('SHOP__DEDIPASS_PUBLICKEY') ?></label>
                    <input type="text" class="form-control" name="publicKey" placeholder="Ex: 4e2009e88d5c5587302e996de5fe1f47"<?= (isset($dedipassConfig['DedipassConfig']['public_key'])) ? ' value="'.$dedipassConfig['DedipassConfig']['public_key'].'"' : '' ?>>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
                  </div>

                </form>

                <hr>

                <h3><?= $Lang->get('SHOP__DEDIPASS_HISTORIES') ?></h3>

                <table class="table table-bordered dataTable">
                  <thead>
                    <tr>
                      <th><?= $Lang->get('SHOP__DEDIPASS_CODE') ?></th>
                      <th><?= $Lang->get('SHOP__DEDIPASS_RATE') ?></th>
                      <th><?= $Lang->get('USER__USERNAME') ?></th>
                      <th><?= ucfirst($Configuration->getMoneyName()) ?></th>
                      <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(isset($histories['dedipass'])) { ?>
                      <?php foreach ($histories['dedipass'] as $key => $value) { ?>
                        <tr>
                          <td><?= $value['DedipassHistory']['code'] ?></td>
                          <td><?= $value['DedipassHistory']['rate'] ?></td>
                          <td><?= (isset($usersByID[$value['DedipassHistory']['user_id']])) ? $usersByID[$value['DedipassHistory']['user_id']] : $value['DedipassHistory']['user_id'] ?></td>
                          <td><?= $value['DedipassHistory']['credits_gived'] ?></td>
                          <td><?= $Lang->date($value['DedipassHistory']['created']) ?></td>
                        </tr>
                      <?php } ?>
                    <?php } ?>
                  </tbody>
                </table>

              </div>

              <div class="tab-pane" id="tab_points_transfer">

                <h3><?= $Lang->get('SHOP__USER_POINTS_TRANSFER_HISTORIES', array('{MONEY_NAME}' => $Configuration->getMoneyName())) ?></h3>

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


        </div>
      </div>
    </div>
  </div>
</section>
