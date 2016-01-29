<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('ADD_ITEM') ?></h3>
        </div>
        <div class="box-body">
          <form action="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'add_item_ajax')) ?>" method="post">
            <input type="hidden" id="form_infos" data-ajax="true" data-redirect-url="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'index', 'admin' => true)) ?>">

            <div class="ajax-msg"></div>
      
            <div class="form-group">
              <label><?= $Lang->get('NAME') ?></label>
              <input name="name" class="form-control"type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('DESCRIPTION') ?></label>
              <textarea name="description" class="form-control"></textarea>
            </div>

            <div class="form-group">
              <label><?= $Lang->get('CATEGORY') ?></label>
              <select class="form-control" name="category">
                <?php foreach ($categories as $key => $value) { ?>
                    <option value="<?= $key ?>"><?= $value ?></option>
                <?php } ?>
              </select>
            </div>
            <input type="hidden" name="category_default">

            <div class="form-group">
              <label><?= $Lang->get('PRICE') ?></label>
              <input name="price" class="form-control" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('IMG_URL') ?></label>
              <input name="img_url" class="form-control" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('SERVER') ?></label>
              <select class="form-control" name="servers" multiple>
                <?php foreach ($servers as $key => $value) { ?>
                    <option value="<?= $key ?>"><?= $value ?></option>
                <?php } ?>
              </select>
            </div>

            <div class="form-group">
              <label><?= $Lang->get('COMMANDS') ?></label>
              <input name="commands" class="form-control" type="text">
              <small><b>{PLAYER}</b> = Pseudo <br> <b>[{+}]</b> <?= $Lang->get('FOR_NEW_COMMAND') ?> <br><b><?= $Lang->get('EXAMPLE') ?>:</b> <i>give {PLAYER} 1 1[{+}]broadcast {PLAYER} ...</i></small>
            </div>

            <div class="form-group">
              <label><?= $Lang->get('TIMED_COMMAND') ?></label>
              <div class="radio">
                <label>
                  <input name="timedCommand" type="radio" value="true"> <?= $Lang->get('ENABLED') ?>
                </label>
                <br>
                <label>
                  <input name="timedCommand" type="radio" value="false" checked> <?= $Lang->get('DISABLED') ?>
                </label>
              </div>
              <small><i><?= $Lang->get('TIMED_COMMAND_EXPLAIN') ?></i></small>
            </div>
            <div id="timedCommands" style="display:none;">
              <div class="form-group">
                <label><?= $Lang->get('COMMANDS') ?></label>
                <input type="text" name="timedCommand_cmd" class="form-control">
                <small><b>{PLAYER}</b> = Pseudo <br> <b>[{+}]</b> <?= $Lang->get('FOR_NEW_COMMAND') ?> <br><b><?= $Lang->get('EXAMPLE') ?>:</b> <i>give {PLAYER} 1 1[{+}]broadcast {PLAYER} ...</i></small>
              </div>
              <div class="form-group">
                <label><?= $Lang->get('TIME') ?></label>
                  <input type="text" name="timedCommand_time" placeholder="Minutes" class="form-control">
              </div>
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
<script>
  $('input[type="radio"][name="timedCommand"]').change(function(e) {
    if($('input[type="radio"][name="timedCommand"]').serialize() == "timedCommand=true") {
      $('#timedCommands').slideDown(500);
    } else {
      $('#timedCommands').slideUp(500);
    }
  });
</script>