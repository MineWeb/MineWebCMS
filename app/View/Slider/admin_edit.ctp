<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('SLIDER__EDIT') ?></h3>
        </div>
        <div class="box-body">
          <form action="<?= $this->Html->url(array('controller' => 'slider', 'action' => 'edit_ajax')) ?>" method="post" data-ajax="true" data-upload-image="true" data-redirect-url="<?= $this->Html->url(array('controller' => 'slider', 'action' => 'index', 'admin' => 'true')) ?>">

            <input type="hidden" value="<?= $slider['id'] ?>" name="id">

            <div class="col-md-4">
              <?= $this->element('form.input.upload.img', array('img' => $slider['url_img'], 'filename' => $slider['filename'])) ?>
            </div>

            <div class="col-md-8">

              <div class="form-group">
                <label><?= $Lang->get('GLOBAL__TITLE') ?></label>
                <input name="title" class="form-control" value="<?= $slider['title'] ?>" type="text">
              </div>

              <div class="form-group">
                <label><?= $Lang->get('SLIDER__SUBTITLE') ?></label>
                <input name="subtitle" class="form-control" value="<?= $slider['subtitle'] ?>" type="text">
              </div>

            </div>


            <div class="pull-right">
              <a href="<?= $this->Html->url(array('controller' => 'slider', 'action' => 'index', 'admin' => true)) ?>" class="btn btn-default"><?= $Lang->get('GLOBAL__CANCEL') ?></a>
              <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
