<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('EDIT_ITEM') ?></h3>
        </div>
        <div class="box-body">
          <form action="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'edit_ajax')) ?>" method="post" data-ajax="true" data-redirect-url="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'index', 'admin' => true)) ?>">

            <div class="ajax-msg"></div>

            <input type="hidden" name="id" value="<?= $item['id'] ?>">

            <div class="form-group">
              <label><?= $Lang->get('GLOBAL__NAME') ?></label>
              <input name="name" class="form-control" value="<?= $item['name'] ?>" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('SHOP__ITEM_DESCRIPTION') ?></label>
              <textarea name="description" class="form-control"><?= $item['description'] ?></textarea>
            </div>

            <div class="form-group">
              <label><?= $Lang->get('CATEGORY') ?></label>
              <select class="form-control" name="category">
                <option value="" selected><?= $item['category'] ?></option>
                <?php foreach ($categories as $key => $value) { ?>
                    <option value="<?= $key ?>"><?= $value ?></option>
                <?php } ?>
              </select>
            </div>
            <input type="hidden" name="category_default" value="<?= $item['category'] ?>">

            <div class="form-group">
              <label><?= $Lang->get('SHOP__ITEM_PRICE') ?></label>
              <input name="price" class="form-control" value="<?= $item['price'] ?>" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('SHOP__ITEM_IMG_URL') ?></label>
              <input name="img_url" class="form-control" value="<?= $item['img_url'] ?>" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('SERVER__TITLE') ?></label>
              <select class="form-control" name="servers" multiple>
                <?php foreach ($servers as $key => $value) { ?>
                    <option value="<?= $key ?>"<?= (in_array($key, $selected_server)) ? ' selected' : '' ?>><?= $value ?></option>
                <?php } ?>
              </select>
            </div>

            <div class="form-group">
              <label><?= $Lang->get('GLOBAL__SERVER_COMMANDS') ?></label>
              <input name="commands" class="form-control" value="<?= $item['commands'] ?>" type="text">
              <small><b>{PLAYER}</b> = Pseudo <br> <b>[{+}]</b> <?= $Lang->get('SERVER__PARSE_NEW_COMMAND') ?> <br><b><?= $Lang->get('GLOBAL__EXAMPLE') ?>:</b> <i>give {PLAYER} 1 1[{+}]broadcast {PLAYER} ...</i></small>
            </div>

            <div class="form-group">
              <label><?= $Lang->get('TIMED_COMMAND') ?></label>
              <div class="radio">
                <label>
                  <input name="timedCommand" type="radio" value="true"<?= ($item['timedCommand']) ? ' checked=""' : ''; ?>> <?= $Lang->get('GLOBAL__ENABLED') ?>
                </label>
                <br>
                <label>
                  <input name="timedCommand" type="radio" value="false"<?= (!$item['timedCommand']) ? ' checked=""' : ''; ?>> <?= $Lang->get('GLOBAL__DISABLED') ?>
                </label>
              </div>
              <small><i><?= $Lang->get('TIMED_COMMAND_EXPLAIN') ?></i></small>
            </div>
            <div id="timedCommands" style="display:<?= ($item['timedCommand']) ? 'block' : 'none' ?>;">
              <div class="form-group">
                <label><?= $Lang->get('GLOBAL__SERVER_COMMANDS') ?></label>
                <input type="text" name="timedCommand_cmd" value="<?= @$item['timedCommand_cmd'] ?>" class="form-control">
                <small><b>{PLAYER}</b> = Pseudo <br> <b>[{+}]</b> <?= $Lang->get('SERVER__PARSE_NEW_COMMAND') ?> <br><b><?= $Lang->get('GLOBAL__EXAMPLE') ?>:</b> <i>give {PLAYER} 1 1[{+}]broadcast {PLAYER} ...</i></small>
              </div>
              <div class="form-group">
                <label><?= $Lang->get('TIME') ?></label>
                  <input type="text" name="timedCommand_time" placeholder="Minutes" value="<?= @$item['timedCommand_time'] ?>" class="form-control">
              </div>
            </div>

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
<script>
  $('input[type="radio"][name="timedCommand"]').change(function(e) {
    if($('input[type="radio"][name="timedCommand"]').serialize() == "timedCommand=true") {
      $('#timedCommands').slideDown(500);
    } else {
      $('#timedCommands').slideUp(500);
    }
  });
</script>
