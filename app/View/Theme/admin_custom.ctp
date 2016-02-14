<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('THEME__CUSTOMIZATION') ?></h3>
        </div>
        <div class="box-body">

          <form method="post" data-ajax="false">

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
              <label><?= $Lang->get('THEME__FAVICON_URL') ?></label>
              <input type="text" class="form-control" name="favicon_url" value="<?= (isset($config['favicon_url'])) ? $config['favicon_url'] : '' ?>">
            </div>

            <input type="hidden" name="data[_Token][key]" value="<?= $csrfToken ?>">

            <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
            <a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'index', 'admin' => true)) ?>" type="button" class="btn btn-default"><?= $Lang->get('GLOBAL__CANCEL') ?></a>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>
