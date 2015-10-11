<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('CUSTOMIZATION') ?></h3>
        </div>
        <div class="box-body">
        
          <form method="post">
            <input type="hidden" id="form_infos" data-ajax="false">

            <div class="form-group">
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="slider" id="slider"<?= (isset($config['slider']) && $config['slider'] == 'true') ? ' checked' : '' ?>>
                  <?= $Lang->get('SLIDER') ?>
                </label>
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
            </script>

            <div class="form-group">
              <label><?= $Lang->get('BACKGROUND_URL') ?></label>
              <input type="text" class="form-control" name="background_url" value="<?= $config['background_url'] ?>" placeholder="<?= $Lang->get('DEFAULT').' : http://subtlepatterns2015.subtlepatterns.netdna-cdn.com/patterns/geometry2.png' ?>">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('FAVICON_URL') ?></label>
              <input type="text" class="form-control" name="favicon_url" value="<?= $config['favicon_url'] ?>">
            </div>

            <button class="btn btn-primary" type="submit"><?= $Lang->get('SUBMIT') ?></button>
            <a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'index', 'admin' => true)) ?>" type="button" class="btn btn-default"><?= $Lang->get('CANCEL') ?></a> 
          </form>   

        </div>
      </div>
    </div>
  </div>
</section>