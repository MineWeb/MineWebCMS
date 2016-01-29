<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('ADD_CATEGORY') ?></h3>
        </div>
        <div class="box-body">
          <form action="" method="post">
            <input type="hidden" id="form_infos" data-ajax="false">

            <div class="ajax-msg"></div>
      
            <div class="form-group">
              <label><?= $Lang->get('NAME') ?></label>
              <input name="name" class="form-control"type="text">
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