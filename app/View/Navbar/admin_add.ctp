<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('ADD_NEWS') ?></h3>
        </div>
        <div class="box-body">
          <form method="post" id="nav_add">

            <div class="ajax-msg"></div>

            <div class="form-group">
              <label><?= $Lang->get('NAME') ?></label>
              <input name="name" class="form-control" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('TYPE') ?></label>
              <div class="radio">
                <input type="radio" id="normal" name="type" value="normal">
                <label><?= $Lang->get('NORMAL') ?></label>
              </div>
              <div class="radio">
                <input type="radio" id="dropdown" name="type" value="dropdown">
                <label><?= $Lang->get('DROPDOWN') ?></label>
              </div>
            </div>

            <div id="type-normal" class="hidden">
              <div class="form-group">
                <label><?= $Lang->get('URL') ?></label>
                <div class="radio">
                  <input type="radio" class="type_plugin" name="url_type" value="plugin">
                  <label><?= $Lang->get('PLUGIN') ?></label>
                </div>
                  <div class="hidden plugin">
                    <select class="form-control" name="url_plugin">
                      <?php foreach ($url_plugins as $key => $value) { ?>
                          <option value="<?= $key ?>"><?= $value ?></option>
                        <?php } ?>
                    </select>
                  </div>
                <div class="radio">
                  <input type="radio" class="type_page" name="url_type" value="page">
                  <label><?= $Lang->get('PAGE') ?></label>
                </div>
                  <div class="hidden page">
                    <select class="form-control" name="url_page">
                        <?php foreach ($url_pages as $key => $value) { ?>
                          <option value="<?= $key ?>"><?= $value ?></option>
                        <?php } ?>
                    </select>
                  </div>
                <div class="radio">
                  <input type="radio" class="type_custom" name="url_type" value="custom">
                  <label><?= $Lang->get('CUSTOM') ?></label>
                </div>
                  </label>
                  <input type="text" class="form-control hidden custom" placeholder="<?= $Lang->get('YOUR_URL') ?>" name="url_custom">
                </div>
              </div>

            <div id="type-dropdown" class="hidden">
              <div class="form-group">
                <div class="well" id="nav-1">
                  <div class="form-group">
                    <label><?= $Lang->get('NAME_OF_NAV') ?></label>
                    <input type="text" class="form-control name_of_nav" name="name_of_nav">
                  </div>
                  <div class="form-group">
                    <label><?= $Lang->get('URL') ?></label>
                    <input type="text" class="form-control url_of_nav" placeholder="<?= $Lang->get('YOUR_URL') ?>" name="url">
                  </div>
                </div>
              </div>
              <div id="add-js" data-number="1"></div>
              <div class="control-group">
                <a href="#" id="add_nav" class="btn btn-success"><?= $Lang->get('ADD_NAV') ?></a>
              </div>
            </div>

            <div class="form-group">
              <div class="checkbox">
                <input type="checkbox" name="new_tab">
                <label><?= $Lang->get('NAV__OPEN_IN_NEW_TAB') ?></label>
              </div>
            </div>

            <div class="pull-right">
              <a href="<?= $this->Html->url(array('controller' => 'navbar', 'action' => 'admin_index', 'admin' => true)) ?>" class="btn btn-default"><?= $Lang->get('CANCEL') ?></a>
              <button class="btn btn-primary" type="submit"><?= $Lang->get('SUBMIT') ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
  $('#add_nav').click(function(e) {
    e.preventDefault();
    var how = $('#add-js').attr('data-number');
    how = parseInt(how) + 1;
    var add = '<div class="form-group"><div class="well" id="nav-'+how+'"><div class="form-group"><label><?= addslashes($Lang->get('NAME_OF_NAV')) ?></label><input type="text" class="form-control name_of_nav" name="name_of_nav"></div><div class="form-group"><label><?= $Lang->get('URL') ?></label><input type="text" class="form-control url_of_nav" placeholder="<?= $Lang->get('YOUR_URL') ?>" name="url"></div></div></div>'
    $('#add-js').append(add);
    $('#add-js').attr('data-number', how);
  });
</script>
<script type="text/javascript">
  $("#normal").change(function() {
    if($("#normal").is(':checked')) {
      $("#type-normal").removeClass('hidden');
      $("#type-dropdown").addClass('hidden');
    } else {
      $("#type-normal").addClass('hidden');
      $("#type-dropdown").removeClass('hidden');
    }
  });
  $("#dropdown").change(function() {
    if($("dropdown").is(':checked')) {
      $("#type-dropdown").addClass('hidden');
      $("#type-normal").removeClass('hidden');
    } else {
      $("#type-dropdown").removeClass('hidden');
      $("#type-normal").addClass('hidden');
    }
  });

  $(".type_plugin").change(function() {
    if($(".type_plugin").is(':checked')) {
      $(".page").addClass('hidden');
      $(".custom").addClass('hidden');
      $(".plugin").removeClass('hidden');
    } else {
      $(".plugin").addClass('hidden');
    }
  });

  $(".type_page").change(function() {
    if($(".type_page").is(':checked')) {
      $(".page").removeClass('hidden');
      $(".custom").addClass('hidden');
      $(".plugin").addClass('hidden');
    } else {
      $(".page").addClass('hidden');
    }
  });

  $(".type_custom").change(function() {
    if($(".type_custom").is(':checked')) {
      $(".page").addClass('hidden');
      $(".custom").removeClass('hidden');
      $(".plugin").addClass('hidden');
    } else {
      $(".custom").addClass('hidden');
    }
  });
</script>
<script type="text/javascript">
  $("#nav_add").submit(function( event ) {

    $('.ajax-msg').html('<div class="alert alert-info"><a class="close" data-dismiss="alert">×</a><?= $Lang->get('LOADING') ?> ...</div>').fadeIn(500);

    var $form = $( this );

    var submit_btn_content = $form.find('button[type=submit]').html();
    $form.find('button[type=submit]').html('<?= $Lang->get('LOADING') ?>...').attr('disabled', 'disabled').fadeIn(500);
    var name = $form.find("input[name='name']").val();
    var type = $form.find("input[type='radio'][name='type']:checked").val();
    if(type == "normal") {
      if($form.find("input[name='url_type']:checked").val() == "custom") {

        var url = '{"type":"custom", "url":"'+$form.find("select[name='url_custom']").val()+'"}';

      } else if($form.find("input[name='url_type']:checked").val() == "plugin") {

        var url = '{"type":"plugin", "id":"'+$form.find("select[name='url_plugin']").val()+'"}';

      } else if($form.find("input[name='url_type']:checked").val() == "page") {

        var url = '{"type":"page", "id":"'+$form.find("select[name='url_page']").val()+'"}';

      } else {

        var url = "undefined";

      }
    } else {
      var names = $('.name_of_nav').serialize();
      names = names.split('&');
      var urls = $('.url_of_nav').serialize();
      urls = urls.split('&');
      var url = {};
      var test = "success"
      for (var key in test = names)
      {
        var l = test[key].split('=');
        l = l[1];
        console.log(l);
        var p = urls[key].split('=');
        p = p[1];
        url[l] = p;
      }
      console.log(url);
    }

    var inputs = {};
    inputs['name'] = name;
    inputs['type'] = type;
    inputs['url'] = url;
    inputs['open_new_tab'] = $('input[name="new_tab"]').is(':checked');
    inputs['data[_Token][key]'] = '<?= $csrfToken ?>';

    console.log(inputs);

    $.post("<?= $this->Html->url(array('controller' => 'navbar', 'action' => 'add_ajax', 'admin' => true)) ?>", inputs, function(data) {
        data2 = data.split("|");
    if(data.indexOf('true') != -1) {
          $('.ajax-msg').empty().html('<div class="alert alert-success" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
           document.location.href="<?= $this->Html->url(array('controller' => 'navbar', 'action' => 'admin_index', 'admin' => 'true')) ?>";
           $form.find('button[type="submit"]').html(submit_btn_content).attr('disabled', false).fadeIn(500);
        } else if(data.indexOf('false') != -1) {
          $('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
          $form.find('button[type="submit"]').html(submit_btn_content).attr('disabled', false).fadeIn(500);
      } else {
        $('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
        $form.find('button[type="submit"]').html(submit_btn_content).attr('disabled', false).fadeIn(500);
    }
    });
    return false;
  });
</script>
