<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('SHOP__ITEM_EDIT') ?></h3>
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
              <textarea id="editor" name="description" class="form-control"><?= $item['description'] ?></textarea>
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
            </div>

            <div class="form-group">
              <label><?= $Lang->get('SHOP__CATEGORY') ?></label>
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

            <hr>

            <div class="form-group">
              <div class="checkbox">
                <input name="multiple_buy" type="checkbox"<?= (isset($item['multiple_buy']) && $item['multiple_buy']) ? ' checked=""' : '' ?>>
                <label><?= $Lang->get('SHOP__ITEM_MULTIPLE_BUY') ?></label>
              </div>
            </div>

            <hr>

            <div class="form-group">
              <div class="checkbox">
                <input name="cart" type="checkbox"<?= (isset($item['cart']) && $item['cart']) ? ' checked=""' : '' ?>>
                <label><?= $Lang->get('SHOP__ITEM_CART') ?></label>
              </div>
            </div>

            <hr>

            <div class="form-group">
              <label><?= $Lang->get('SHOP__ITEM_IMG_URL') ?></label>
              <input name="img_url" class="form-control" value="<?= $item['img_url'] ?>" type="text">
            </div>

            <hr>

            <div class="form-group">
              <label><?= $Lang->get('SERVER__TITLE') ?></label>
              <select class="form-control" name="servers" multiple>
                <?php foreach ($servers as $key => $value) { ?>
                    <option value="<?= $key ?>"<?= (in_array($key, $selected_server)) ? ' selected' : '' ?>><?= $value ?></option>
                <?php } ?>
              </select>
            </div>

            <div class="form-group">
              <div class="checkbox">
                <input name="display_server" type="checkbox"<?= (isset($item['display_server']) && $item['display_server']) ? ' checked=""' : '' ?>>
                <label><?= $Lang->get('SHOP__ITEM_DISPLAY_SERVER') ?></label>
              </div>
            </div>

            <div class="form-group">
              <label><?= $Lang->get('GLOBAL__SERVER_COMMANDS') ?></label>
              <div class="input-group">
                <input name="commands[0]" class="form-control" value="<?= htmlentities($item['commands'][0]) ?>" type="text">
                <div class="input-group-btn">
                  <button data-i="1" type="button" id="addCommand" class="btn btn-success"><?= $Lang->get('SHOP__ITEM_ADD_COMMAND') ?></button>
                </div>
              </div>
              <div class="addCommand">
                <?php
                  $i = 0;
                  unset($item['commands'][0]);
                  foreach ($item['commands'] as $key => $value) {
                    $i++;
                    echo '<div class="input-group" style="margin-top:5px;" id="'.$i.'">';
                      echo '<input name="commands['.$i.']" class="form-control" value="'.htmlentities($value).'" type="text">';
                      echo '<span class="input-group-btn">';
                        echo '<button class="btn btn-danger delete-cmd" data-id="'.$i.'" type="button"><span class="fa fa-close"></span></button>';
                      echo '</span>';
                    echo '</div>';
                  }
                ?>
              </div>
              <small><b>{PLAYER}</b> = Pseudo <br> <b>[{+}]</b> <?= $Lang->get('SERVER__PARSE_NEW_COMMAND') ?> <br><b><?= $Lang->get('GLOBAL__EXAMPLE') ?>:</b> <i>give {PLAYER} 1 1[{+}]broadcast {PLAYER} ...</i></small>
            </div>

            <div class="form-group">
              <div class="checkbox">
                <input name="broadcast_global" type="checkbox"<?= (!isset($item['broadcast_global']) || $item['broadcast_global']) ? ' checked=""' : '' ?>>
                <label><?= $Lang->get('SHOP__ITEM_BROADCAST_GLOBAL') ?></label>
              </div>
            </div>

            <div class="form-group">
              <div class="checkbox">
                <input name="need_connect" type="checkbox"<?= (isset($item['need_connect']) && $item['need_connect']) ? ' checked=""' : '' ?>>
                <label><?= $Lang->get('SHOP__ITEM_CHECKBOX_CONNECT') ?></label>
              </div>
            </div>

            <hr>

            <div class="form-group">
              <div class="checkbox">
                <input name="give_skin" type="checkbox"<?= (isset($item['give_skin']) && $item['give_skin']) ? ' checked=""' : '' ?>>
                <label><?= $Lang->get('SHOP__ITEM_GIVE_SKIN') ?></label>
              </div>
            </div>

            <div class="form-group">
              <div class="checkbox">
                <input name="give_cape" type="checkbox"<?= (isset($item['give_cape']) && $item['give_cape']) ? ' checked=""' : '' ?>>
                <label><?= $Lang->get('SHOP__ITEM_GIVE_CAPE') ?></label>
              </div>
            </div>

            <hr>

            <div class="form-group">
              <label><?= $Lang->get('SHOP__ITEM_TIMED_COMMAND') ?></label>
              <div class="radio">
                <input name="timedCommand" type="radio" value="true"<?= ($item['timedCommand']) ? ' checked=""' : ''; ?>>
                <label>
                  <?= $Lang->get('GLOBAL__ENABLED') ?>
                </label>
              </div>
              <div class="radio">
                <input name="timedCommand" type="radio" value="false"<?= (!$item['timedCommand']) ? ' checked=""' : ''; ?>>
                <label>
                  <?= $Lang->get('GLOBAL__DISABLED') ?>
                </label>
              </div>
              <small><i><?= $Lang->get('SHOP__ITEM_TIMED_COMMAND_DESC') ?></i></small>
            </div>
            <div id="timedCommands" style="display:<?= ($item['timedCommand']) ? 'block' : 'none' ?>;">
              <div class="form-group">
                <label><?= $Lang->get('GLOBAL__SERVER_COMMANDS') ?></label>
                <input type="text" name="timedCommand_cmd" value="<?= (isset($item['timedCommand_cmd'])) ? htmlentities($item['timedCommand_cmd']) : '' ?>" class="form-control">
                <small><b>{PLAYER}</b> = Pseudo <br> <b>[{+}]</b> <?= $Lang->get('SERVER__PARSE_NEW_COMMAND') ?> <br><b><?= $Lang->get('GLOBAL__EXAMPLE') ?>:</b> <i>give {PLAYER} 1 1[{+}]broadcast {PLAYER} ...</i></small>
              </div>
              <div class="form-group">
                <label><?= $Lang->get('SHOP__ITEM_TIMED_COMMAND_TIME') ?></label>
                  <input type="text" name="timedCommand_time" placeholder="Minutes" value="<?= @$item['timedCommand_time'] ?>" class="form-control">
              </div>
            </div>

            <hr>

            <div class="form-group">
              <div class="checkbox">
                <input name="display" type="checkbox"<?= ((isset($item['display']) && $item['display'])) ? ' checked=""' : '' ?>>
                <label><?= $Lang->get('SHOP__ITEM_CHECKBOX_DISPLAY') ?></label>
              </div>
            </div>

            <hr>

            <div class="form-group">
              <label><?= $Lang->get('SHOP__ITEM_PREREQUISITES') ?></label>
              <select class="form-control" name="prerequisites_type">
                <option value="0"<?= (!isset($item['prerequisites_type']) || empty($item['prerequisites_type']) || $item['prerequisites_type'] == 0) ? ' selected' : '' ?>><?= $Lang->get('SHOP__ITEM_PREREQUISITES_TYPE_0') ?></option>
                <option value="1"<?= ($item['prerequisites_type'] == 1) ? ' selected' : '' ?>><?= $Lang->get('SHOP__ITEM_PREREQUISITES_TYPE_1') ?></option>
                <option value="2"<?= ($item['prerequisites_type'] == 2) ? ' selected' : '' ?>><?= $Lang->get('SHOP__ITEM_PREREQUISITES_TYPE_2') ?></option>
              </select>
            </div>

            <script type="text/javascript">
              $('select[name="prerequisites_type"]').on('change', function(e) {
                if($(this).val() == '1' || $(this).val() == '2') {
                  $('#prerequisites').slideDown();
                } else {
                  $('#prerequisites').slideUp();
                }
              });
            </script>

            <div class="form-group" style="display:<?= ($item['prerequisites_type'] == '2' || $item['prerequisites_type'] == '1') ? 'block' : 'none' ?>;" id="prerequisites">
              <label><?= $Lang->get('SHOP__ITEM_PREREQUISITES_ITEMS') ?></label>
              <select class="form-control" name="prerequisites" multiple>
                <?php foreach ($items_available as $id => $name) { ?>
                    <option value="<?= $id ?>"<?= (in_array($id, $item['prerequisites'])) ? ' selected' : '' ?>><?= $name ?></option>
                <?php } ?>
              </select>
            </div>

            <hr>

            <div class="form-group">
              <div class="checkbox">
                <input id="reductional_items_checkbox" name="reductional_items_checkbox" type="checkbox"<?= ((isset($item['reductional_items']) && !empty($item['reductional_items']))) ? ' checked=""' : '' ?>>
                <label><?= $Lang->get('SHOP__ITEM_CHECKBOX_REDUCTIONAL_ITEMS') ?></label>
              </div>
            </div>

            <script type="text/javascript">
              $('#reductional_items_checkbox').on('change', function(e) {
                if($('#reductional_items_checkbox:checked').length > 0) {
                  $('#reductional_items').slideDown();
                } else {
                  $('#reductional_items').slideUp();
                }
              });
            </script>

            <div class="form-group" style="display:<?= (isset($item['reductional_items']) && !empty($item['reductional_items'])) ? 'block' : 'none' ?>;" id="reductional_items">
              <label><?= $Lang->get('SHOP__ITEM_PREREQUISITES_ITEMS') ?></label>
              <select class="form-control" name="reductional_items" multiple>
                <?php foreach ($items_available as $id => $name) { ?>
                    <option value="<?= $id ?>"<?= (in_array($id, $item['reductional_items'])) ? ' selected' : '' ?>><?= $name ?></option>
                <?php } ?>
              </select>
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

  $('#addCommand').on('click', function(e) {

    e.preventDefault();

    var i = parseInt($(this).attr('data-i'));

    var input = '';
    input += '<div style="margin-top:5px;" class="input-group" id="'+i+'">';
      input += '<input name="commands['+i+']" class="form-control" type="text">';
      input += '<span class="input-group-btn">';
        input += '<button class="btn btn-danger delete-cmd" data-id="'+i+'" type="button"><span class="fa fa-close"></span></button>';
      input += '</span>';
    input + '</div>';

    i++;

    $(this).attr('data-i', i);

    $('.addCommand').append(input);

    $('.delete-cmd').unbind('click');
    $('.delete-cmd').on('click', function(e) {

      var id = $(this).attr('data-id');

      $('#'+id).slideUp(150, function() {
        $('#'+id).remove();
      });
    });

  });

  $('.delete-cmd').on('click', function(e) {

    var id = $(this).attr('data-id');

    $('#'+id).slideUp(150, function() {
      $('#'+id).remove();
    });
  });
</script>
