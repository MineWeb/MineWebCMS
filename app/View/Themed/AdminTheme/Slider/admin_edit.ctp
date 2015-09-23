<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('EDIT_SLIDER') ?></h3>
        </div>
        <div class="box-body">
          <form action="<?= $this->Html->url(array('controller' => 'slider', 'action' => 'edit_ajax')) ?>" method="post">
            <input type="hidden" id="form_infos" data-ajax="true" data-redirect-url="<?= $this->Html->url(array('controller' => 'slider', 'action' => 'index', 'admin' => 'true')) ?>">

            <div class="ajax-msg"></div>

            <input type="hidden" value="<?= $slider['id'] ?>" name="id">
      
            <div class="form-group">
              <label><?= $Lang->get('TITLE') ?></label>
              <input name="title" class="form-control" value="<?= $slider['title'] ?>" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('SUBTITLE') ?></label>
              <input name="subtitle" class="form-control" value="<?= $slider['subtitle'] ?>" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('URL_IMG') ?></label>
              <input name="url_img" class="form-control" value="<?= $slider['url_img'] ?>" type="text">
            </div>


            <div class="pull-right">
              <a href="<?= $this->Html->url(array('controller' => 'user', 'action' => 'index', 'admin' => true)) ?>" class="btn btn-default"><?= $Lang->get('CANCEL') ?></a>  
              <button class="btn btn-primary" type="submit"><?= $Lang->get('SUBMIT') ?></button>
            </div>
          </form>      
        </div>
      </div>
    </div>
  </div>
</section>