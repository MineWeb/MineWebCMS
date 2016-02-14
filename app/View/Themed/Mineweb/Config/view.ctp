<?php
$form_input = array('title' => $Lang->get('THEME__UPLOAD_LOGO'));

if(isset($config['logo']) && $config['logo']) {
  $logo = explode('/', $config['logo']);
  $form_input['img'] = 'uploads/'.end($logo);
  $form_input['filename'] = 'theme_logo';
}
?>
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('THEME__CUSTOMIZATION') ?></h3>
        </div>
        <div class="box-body">

          <form method="post" enctype="multipart/form-data" data-ajax="false">

            <div class="col-md-4">
              <?= $this->element('form.input.upload.img', $form_input) ?>
            </div>

            <div class="col-md-8">
              <div class="form-group">
                <div class="checkbox">
                  <input type="checkbox" name="slider" id="slider"<?= (isset($config['slider']) && $config['slider'] == 'true') ? ' checked' : '' ?>>
                  <label><?= $Lang->get('SLIDER__TITLE') ?></label>
                </div>
              </div>

              <script>
                $('#slider').change(function(){
                  if($('#slider').is(':checked')) {
                    $('#slider').attr('value', 'true');
                  } else {
                    $('#slider').attr('value', 'false');
                  }
                });
                if($('#slider').is(':checked')) {
                  $('#slider').attr('value', 'true');
                } else {
                  $('#slider').attr('value', 'false');
                }
              </script>

              <div class="form-group">
        				<label><?= $Lang->get('NAVBAR__TITLE') ?></label>
        				<div class="radio">
        					<input type="radio" name="navbar" value="navbar2"<?php if($config['navbar'] == "navbar2") { echo ' checked'; } ?>>
                  <label><?= $this->Html->image('/theme/Mineweb/img/navbar2.png', array('width' => '450')) ?></label>
                </div>
                <div class="radio">
        					<input type="radio" name="navbar" value="navbar"<?php if($config['navbar'] == "navbar") { echo ' checked'; } ?>>
                  <label><?= $this->Html->image('/theme/Mineweb/img/navbar.png', array('width' => '450')) ?></label>
        				</div>
        			</div>

              <div class="form-group">
                <label><?= $Lang->get('THEME__FAVICON_URL') ?></label>
                <input type="text" class="form-control" name="favicon_url" value="<?= $config['favicon_url'] ?>">
              </div>

            </div>

            <input name="data[_Token][key]" value="<?= $csrfToken ?>" type="hidden">

            <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
            <a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'index', 'admin' => true)) ?>" type="button" class="btn btn-default"><?= $Lang->get('GLOBAL__CANCEL') ?></a>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>
