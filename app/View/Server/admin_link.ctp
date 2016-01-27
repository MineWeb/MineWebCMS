<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('CONFIG_SERVER') ?></h3>
        </div>
        <div class="box-body">

          <form action="<?= $this->Html->url(array('controller' => 'server', 'action' => 'config', 'admin' => true)) ?>" method="post">

            <input type="hidden" id="form_infos" data-ajax="true">

            <div class="ajax-msg"></div>

            <div class="form-group">
              <label><?= $Lang->get('TIMEOUT') ?></label>
              <input type="text" class="form-control" name="timeout" value="<?= $timeout ?>">
            </div>

            <button type="submit" class="btn btn-primary"><?= $Lang->get('SUBMIT') ?></button>
            <a href="<?= $this->Html->url(array('action' => 'switchState')) ?>" class="btn btn-<?= ($isEnabled) ? 'danger' : 'success' ?>"><?= ($isEnabled) ? $Lang->get('DISABLE') : $Lang->get('ENABLE') ?></a>
            <a href="<?= $this->Html->url(array('action' => 'switchCacheState')) ?>" class="btn btn-<?= ($isCacheEnabled) ? 'danger' : 'success' ?>"><?= ($isCacheEnabled) ? $Lang->get('SERVER__DISABLE_CACHE') : $Lang->get('SERVER__ENABLE_CACHE') ?></a>

          </form>

        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('SERVER__CONFIG_BANNER_MSG') ?></h3>
        </div>
        <div class="box-body">

          <form action="<?= $this->Html->url(array('controller' => 'server', 'action' => 'editBannerMsg', 'admin' => true)) ?>" method="post">

            <input type="hidden" id="form_infos" data-ajax="true">

            <div class="ajax-msg"></div>

            <div class="form-group">
              <input type="text" class="form-control" name="msg" value="<?= $bannerMsg ?>">
              <small><?= $Lang->get('AVAILABLE_VARIABLES') ?> : {MOTD}, {VERSION}, {ONLINE}, {ONLINE_LIMIT}</small>
            </div>

            <button type="submit" class="btn btn-primary"><?= $Lang->get('SUBMIT') ?></button>

          </form>

        </div>
      </div>
    </div>
  </div>
  <?php if(!empty($servers)) { ?>
    <?php foreach ($servers as $key => $value) { ?>
      <div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title"><?= $Lang->get('LINK_SERVER') ?></h3>
            </div>
            <div class="box-body">

              <form action="<?= $this->Html->url(array('controller' => 'server', 'action' => 'link_ajax', 'admin' => true)) ?>" method="post">

                <input type="hidden" id="form_infos" data-ajax="true">

                <div class="ajax-msg"></div>

                <input type="hidden" name="id" value="<?= $value['Server']['id'] ?>">

                <div class="form-group">
                  <label><?= $Lang->get('SERVER__TYPE') ?></label>
                  <select class="form-control" name="type">
                    <option value="0"<?= ($value['Server']['type'] == '0') ? ' selected' : '' ?>><?= $Lang->get('SERVER__TYPE_DEFAULT') ?></option>
                    <option value="1"<?= ($value['Server']['type'] == '1') ? ' selected' : '' ?>><?= $Lang->get('SERVER__TYPE_BUNGEE') ?></option>
                    <option value="2"<?= ($value['Server']['type'] == '2') ? ' selected' : '' ?>><?= $Lang->get('SERVER__TYPE_QUERY') ?></option>
                  </select>
                </div>

                <div class="form-group">
                  <label><?= $Lang->get('NAME') ?></label>
                  <input type="text" class="form-control" name="name" value="<?= $value['Server']['name'] ?>" placeholder="Ex: MineWeb">
                </div>

                <div class="form-group">
                  <label><?= $Lang->get('SERVER_HOST') ?></label>
                  <input type="text" class="form-control" name="host" value="<?= $value['Server']['ip'] ?>" placeholder="Ex: 127.0.0.1">
                </div>

                <div class="form-group">
                  <label><?= $Lang->get('PORT') ?></label>
                  <input type="text" class="form-control" name="port" value="<?= $value['Server']['port'] ?>" placeholder="Ex: 8080">
                </div>

                <button type="submit" class="btn btn-success"><?= $Lang->get('SUBMIT') ?></button>
                <a href="<?= $this->Html->url(array('controller' => 'server', 'action' => 'delete', 'admin' => true, $value['Server']['id'])) ?>" type="submit" class="btn btn-danger"><?= $Lang->get('DELETE') ?></a>

                <button class="btn btn-info switchBanner pull-right<?= ($value['Server']['activeInBanner']) ? ' active' : '' ?>" id="<?= $value['Server']['id'] ?>"><?= $Lang->get('SERVER__AFFICH_BANNER') ?></button>

              </form>

            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  <?php } ?>


  <div id="add_server_content"></div>
  <div class="btn btn-success btn-block" id="add_server"><?= $Lang->get('ADD_SERVER') ?></div>
</section>
<script>

  $('.switchBanner').click(function(e) {
    e.preventDefault();

    var btn = $(this);

    var id = btn.attr('id');

    if(btn.hasClass('active')) {
      btn.removeClass('active');
    } else {
      btn.addClass('active');
    }

    $.get('<?= $this->Html->url(array('action' => 'switchBanner')) ?>/'+id);

    return false;
  });


  var i = 0;
  $("#add_server").click(function() {
    i++;
    var new_server = '<div class="row">';
      new_server += '<div class="col-md-12">';
        new_server += '<div class="box">';
          new_server += '<div class="box-header with-border">';
            new_server += '<h3 class="box-title"><?= $Lang->get('LINK_SERVER') ?></h3>';
          new_server += '</div>';
          new_server += '<div class="box-body">';
            new_server += '<form id="'+i+'" action="<?= $this->Html->url(array('controller' => 'server', 'action' => 'link_ajax', 'admin' => true)) ?>" method="post">';
              new_server += '<input type="hidden" id="form_infos" data-ajax="true">';
              new_server += '<div class="ajax-msg"></div>';
              new_server += '<div class="form-group">';
                new_server += '<label><?= $Lang->get('SERVER__TYPE') ?></label>';
                new_server += '<select class="form-control" name="type">';
                  new_server += '<option value="0"><?= $Lang->get('SERVER__TYPE_DEFAULT') ?></option>';
                  new_server += '<option value="1"><?= $Lang->get('SERVER__TYPE_BUNGEE') ?></option>';
                  new_server += '<option value="2"><?= $Lang->get('SERVER__TYPE_QUERY') ?></option>';
                new_server +='</select>';
              new_server += '</div>';
              new_server += '<div class="form-group">';
                new_server += '<label><?= $Lang->get('NAME') ?></label>';
                new_server += '<input type="text" class="form-control" name="name" placeholder="Ex: MineWeb">';
              new_server += '</div>';
              new_server += '<div class="form-group">';
                new_server += '<label><?= $Lang->get('SERVER_HOST') ?></label>';
                new_server += '<input type="text" class="form-control" name="host" placeholder="Ex: 127.0.0.1">';
              new_server += '</div>';
              new_server += '<div class="form-group">';
                new_server += '<label><?= $Lang->get('PORT') ?></label>';
                new_server += '<input type="text" class="form-control" name="port" placeholder="Ex: 8080">';
              new_server += '</div>';
              new_server += '<button type="submit" class="btn btn-success"><?= $Lang->get('SUBMIT') ?></button>';
            new_server += '</form>';
          new_server += '</div>';
        new_server += '</div>';
      new_server +='</div>';
    new_server +='</div>'+"\n";



      $('#add_server_content').append(new_server);

      $("form").unbind("submit");

      $("form").on("submit", function(e) {
        form = $(this);

        form_infos = form.find('input[type="hidden"][data-ajax="true"]');
        if(form_infos.length <= 0) {
          form_infos = form.find('input[type="hidden"][data-ajax="false"]');
        }

        if(form_infos.attr('data-ajax') == "false") {
          return;
        }

        e.preventDefault();

        var submit = form.find("input[type='submit']");

        form.find('.ajax-msg').empty().html('<div class="alert alert-info"><a class="close" data-dismiss="alert">×</a><?= $Lang->get('LOADING') ?> ...</div>').fadeIn(500);

          var submit_btn_content = form.find('button[type=submit]').html();
          form.find('button[type=submit]').html('<?= $Lang->get('LOADING') ?>...').attr('disabled', 'disabled').fadeIn(500);

          // Data

          var array = form.serialize();
          array = array.split('&');

          form.find('input[type="checkbox"]').each(function(){
            if(!$(this).is(':checked')) {
              array.push($(this).attr('name')+'=off');
            }
            });

          var inputs = {};

          var i = 0;
          for (var key in args = array)
          {
            input = args[i];
            input = input.split('=');
            input_name = input[0];

            if(form.find('input[name="'+input_name+'"]').attr('type') == "text" || form.find('input[name="'+input_name+'"]').attr('type') == "hidden" || form.find('input[name="'+input_name+'"]').attr('type') == "textarea" || form.find('input[name="'+input_name+'"]').attr('type') == "email" || form.find('input[name="'+input_name+'"]').attr('type') == "password") {
              inputs[input_name] = form.find('input[name="'+input_name+'"]').val(); // je récup la valeur comme ça pour éviter la sérialization
            } else if(form.find('input[name="'+input_name+'"]').attr('type') == "radio") {
              inputs[input_name] = form.find('input[name="'+input_name+'"][type="radio"]:checked').val();
            } else if(form.find('input[name="'+input_name+'"]').attr('type') == "checkbox") {
              if(form.find('input[name="'+input_name+'"]:checked').val() !== undefined) {
                inputs[input_name] = 1;
              } else {
                inputs[input_name] = 0;
              }
            } else if(form.find('textarea[name="'+input_name+'"]').attr('id') == "editor") {
                  inputs[input_name] = tinymce.get('editor').getContent();
            } else if(form.find('select[name="'+input_name+'"]').val() !== undefined) {
              inputs[input_name] = form.find('select[name="'+input_name+'"]').val();
            }

            i++;
          }

          inputs["data[_Token][key]"] = '<?= $csrfToken ?>';

          //

        $.post(form.attr('action'), inputs, function(data) {
                data2 = data.split("|");
            if(data.indexOf('true') != -1) {
                  form.find('.ajax-msg').html('<div class="alert alert-success" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
                  if(form_infos.attr('data-redirect-url') !== undefined) {
                    document.location.href=form_infos.attr('data-redirect-url');
                  }
                  form.find('button[type="submit"]').html(submit_btn_content).attr('disabled', false).fadeIn(500);
                } else if(data.indexOf('false') != -1) {
                  form.find('.ajax-msg').html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
                  form.find('button[type="submit"]').html(submit_btn_content).attr('disabled', false).fadeIn(500);
              } else {
              form.find('.ajax-msg').html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
              form.find('button[type="submit"]').html(submit_btn_content).attr('disabled', false).fadeIn(500);
            }
            });
      });
  });
</script>
