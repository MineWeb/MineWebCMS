<?php
App::import('Component', 'ConfigurationComponent');
$this->Configuration = new ConfigurationComponent();
?>
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('MAINTENANCE') ?></h3>
        </div>
        <div class="box-body">

          <form action="" method="post">

            <div class="ajax-msg"></div>

            <div class="form-group">
              <div class="radio">
                <label>
                  <input type="radio" class="enabled" name="state" value="enabled"<?php if($this->Configuration->get('maintenance') != '0') { echo ' checked=""'; } ?>>
                  <?= $Lang->get('ENABLED') ?>
                </label>
              </div>
              <div class="radio">
                <label>
                  <input type="radio" class="disabled" name="state" value="disabled"<?php if($this->Configuration->get('maintenance') == '0') { echo ' checked=""'; } ?>>
                  <?= $Lang->get('DISABLED') ?>
                </label>
              </div>
            </div>

            <div class="form-group reason<?php if($this->Configuration->get('maintenance') == '0') { echo ' hidden'; } ?>">
                <label><?= $Lang->get('REASON') ?></label>
                <?= $this->Html->script('admin/tinymce/tinymce.min.js') ?>
                <script type="text/javascript">
                tinymce.init({
                    selector: "textarea",
                    height : 300,
                    width : '100%',
                    language : 'fr_FR',
                    plugins: "textcolor code image link",
                    toolbar: "fontselect fontsizeselect bold italic underline strikethrough link image forecolor backcolor alignleft aligncenter alignright alignjustify cut copy paste bullist numlist outdent indent blockquote code"
                 });
                </script>
                <textarea class="form-control" id="editor" name="reason" cols="30" rows="10"><?= ($this->Configuration->get('maintenance') == '0') ? '' : $this->Configuration->get('maintenance') ?></textarea>
            </div>

            <div class="pull-right">
              <a href="<?= $this->Html->url(array('controller' => 'news', 'action' => 'admin_index', 'admin' => true)) ?>" class="btn btn-default"><?= $Lang->get('CANCEL') ?></a>
              <button class="btn btn-primary" type="submit"><?= $Lang->get('SUBMIT') ?></button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
  $(".enabled").change(function() {
    if($(".enabled").is(':checked')) {
      $(".reason").removeClass('hidden');
    } else {
      $(".reason").addClass('hidden').slideDown(500);
    }
  });
  $(".disabled").change(function() {
    if($(".disabled").is(':checked')) {
      $(".reason").addClass('hidden').slideDown(500);
    } else {
      $(".reason").removeClass('hidden');
    }
  });
</script>
