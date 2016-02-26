<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('ADD_OFFER_PAYPAL') ?></h3>
        </div>
        <div class="box-body">
          <form action="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'add_paypal_ajax', 'admin' => true)) ?>" method="post" data-ajax="true" data-redirect-url="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'index', 'admin' => true)) ?>">

            <div class="ajax-msg"></div>

            <div class="form-group">
              <label><?= $Lang->get('GLOBAL__NAME') ?></label>
              <input name="name" class="form-control" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('USER__EMAIL') ?></label>
              <input name="email" class="form-control" type="email">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('SHOP__ITEM_PRICE') ?></label>
              <input name="price" class="form-control" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('HOW_MONEY_OFFER_PAYPAL') ?> <?= $Configuration->getMoneyName() ?></label>
              <input name="money" class="form-control" type="text">
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
