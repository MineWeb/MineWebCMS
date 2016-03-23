<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('SHOP__CATEGORY_ADD') ?></h3>
        </div>
        <div class="box-body">
          <form action="" method="post" data-ajax="false">

            <div class="ajax-msg"></div>

            <div class="form-group">
              <label><?= $Lang->get('GLOBAL__NAME') ?></label>
              <input name="name" class="form-control"type="text">
            </div>

            <input type="hidden" name="data[_Token][key]" value="<?= $csrfToken ?>">

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
