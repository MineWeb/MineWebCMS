<?php
$this->Configuration = new ConfigurationComponent;
?>
<section class="content">
  <div class="alert alert-info">
    <p><b>URL 1 :</b> <?= $this->Html->url(array('controller' => 'shop', 'action' => 'starpass', 'plugin' => 'shop', 'admin' => false), true) ?></p>
    <p><b>URL 2 :</b> <?= $this->Html->url(array('controller' => 'shop', 'action' => 'starpass_verif', 'plugin' => 'shop', 'admin' => false), true) ?></p>
    <p><b>URL 3 :</b> <?= $this->Html->url(array('controller' => 'shop', 'action' => 'starpass', 'plugin' => 'shop', 'admin' => false, 'error'), true) ?></p>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('ADD_OFFER_STARPASS') ?></h3>
        </div>
        <div class="box-body">
          <form action="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'add_starpass_ajax', 'admin' => true)) ?>" method="post" data-ajax="true" data-redirect-url="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'index', 'admin' => true)) ?>">

            <div class="ajax-msg"></div>

            <div class="form-group">
              <label><?= $Lang->get('NAME') ?></label>
              <input name="name" class="form-control" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('IDD') ?></label>
              <input name="idd" class="form-control" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('IDP') ?></label>
              <input name="idp" class="form-control" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('HOW_MONEY_OFFER_PAYPAL') ?> <?= $this->Configuration->get_money_name() ?></label>
              <input name="money" class="form-control" type="text">
            </div>

            <div class="pull-right">
              <a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'index', 'admin' => true)) ?>" class="btn btn-default"><?= $Lang->get('CANCEL') ?></a>
              <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
