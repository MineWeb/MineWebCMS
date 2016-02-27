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
              <li class=""><a href="#tab_hipay" data-toggle="tab" aria-expanded="false">HiPay (Allopass)</a></li>
              <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_starpass">

                <h3><?= $Lang->get('SHOP__STARPASS_OFFERS') ?></h3>

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

                <h3><?= $Lang->get('SHOP__PAYPAL_OFFERS') ?></h3>

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
                    <?php if(!empty($offers['paypal'])) { ?>
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
              </div>

              <div class="tab-pane" id="tab_dedipass">
              </div>

              <div class="tab-pane" id="tab_hipay">
              </div>
            </div>
          </div>


        </div>
      </div>
    </div>
  </div>
</section>
