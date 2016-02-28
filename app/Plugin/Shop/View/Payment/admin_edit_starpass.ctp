<section class="content">
  <div class="alert alert-info">
    <p><b>URL 1 :</b> <?= $this->Html->url('/shop/payment/starpass', true) ?></p>
    <p><b>URL 2 :</b> <?= $this->Html->url('/shop/payment/starpass_verif', true) ?></p>
    <p><b>URL 3 :</b> <?= $this->Html->url('/shop/payment/starpass/error', true) ?></p>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('SHOP__STARPASS_OFFER_EDIT') ?></h3>
        </div>
        <div class="box-body">
          <form action="<?= $this->Html->url(array('controller' => 'payment', 'action' => 'edit_starpass_ajax', 'admin' => true, $starpass['id'])) ?>" method="post" data-ajax="true" data-redirect-url="<?= $this->Html->url(array('controller' => 'payment', 'action' => 'index', 'admin' => true)) ?>">

            <div class="ajax-msg"></div>

            <div class="form-group">
              <label><?= $Lang->get('GLOBAL__NAME') ?></label>
              <input name="name" class="form-control" value="<?= $starpass['name'] ?>" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('IDD') ?></label>
              <input name="idd" class="form-control" value="<?= $starpass['idd'] ?>" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('IDP') ?></label>
              <input name="idp" class="form-control" value="<?= $starpass['idp'] ?>" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('SHOP__OFFER_MONEY_TO_ADD', array('{MONEY_NAME}' => $Configuration->getMoneyName())) ?></label>
              <input name="money" class="form-control" value="<?= $starpass['money'] ?>" type="text">
            </div>

            <div class="pull-right">
              <a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'index', 'admin' => true)) ?>" class="btn btn-default"><?= $Lang->get('GLOBAL__CANCEL') ?></a>
              <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
