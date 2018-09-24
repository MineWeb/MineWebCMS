<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('MAINTENANCE__TITLE') ?></h3>
        </div>
        <div class="box-body">

          <form action="" method="post">

            <div class="ajax-msg"></div>

            <div class="form-group">
              <div class="radio">
                <input type="radio" id="activate" class="enabledStatus" name="state" value="enabled"<?= ($Configuration->getKey('maintenance') != '0') ? ' checked=""' : '' ?>>
                <label for="activate">
                  <?= $Lang->get('GLOBAL__ENABLED') ?>
                </label>
              </div>
              <div class="radio">
                <input type="radio" id="disable" class="disabledStatus" name="state" value="disabled"<?= ($Configuration->getKey('maintenance') == '0') ? ' checked=""' : '' ?>>
                <label for="disable">
                  <?= $Lang->get('GLOBAL__DISABLED') ?>
                </label>
              </div>
            </div>

            <div class="form-group reason<?php if($Configuration->getKey('maintenance') == '0') { echo ' hidden'; } ?>">
                <label><?= $Lang->get('MAINTENANCE__REASON') ?></label>
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
                <textarea class="form-control" id="editor" name="reason" cols="30" rows="10"><?= ($Configuration->getKey('maintenance') == '0') ? '' : $Configuration->getKey('maintenance') ?></textarea>
            </div>

            <input type="hidden" name="data[_Token][key]" value="<?= $csrfToken ?>">

            <div class="pull-right">
              <a href="<?= $this->Html->url(array('controller' => 'news', 'action' => 'admin_index', 'admin' => true)) ?>" class="btn btn-default"><?= $Lang->get('GLOBAL__CANCEL') ?></a>
              <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
  $(".enabledStatus").change(function() {
    if($(".enabledStatus").is(':checked')) {
      $(".reason").removeClass('hidden');
    } else {
      $(".reason").addClass('hidden').slideDown(500);
    }
  });
  $(".disabledStatus").change(function() {
    if($(".disabledStatus").is(':checked')) {
      $(".reason").addClass('hidden').slideDown(500);
    } else {
      $(".reason").removeClass('hidden');
    }
  });
</script>
