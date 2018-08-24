<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('NAVBAR__EDIT_TITLE') ?></h3>
        </div>
        <div class="box-body">
          <form method="post" action="<?= $this->Html->url(array('action' => 'edit_ajax', $nav['id'])) ?>" data-ajax="true" data-redirect-url="<?= $this->Html->url(array('action' => 'index')) ?>" data-custom-function="formatteData">

            <div class="form-group">
              <label><?= $Lang->get('GLOBAL__NAME') ?></label>
              <input name="name" class="form-control" type="text" value="<?= $nav['name'] ?>">
            </div>
	    <div class="form-group">
		<label><?= $Lang->get('NAVBAR__ICON') ?></label>
		<p><?= $Lang->get('NAVBAR__ICON__DESC') ?></p>
		<div class="input-group">
			<div class="input-group-addon">fa-</div>
		   <input name="icon" class="form-control" type="text" value="<?= $nav['icon'] ?>">
		</div>
	    </div>

            <div class="form-group">
              <label><?= $Lang->get('GLOBAL__TYPE') ?></label>
              <div class="radio">
                <input type="radio" id="normal" name="type" value="normal"<?= ($nav['type'] == "1") ? ' checked=""' : '' ?>>
                <label><?= $Lang->get('NAVBAR__LINK_TYPE_DEFAULT') ?></label>
              </div>
              <div class="radio">
                <input type="radio" id="dropdown" name="type" value="dropdown"<?= ($nav['type'] == "2") ? ' checked=""' : '' ?>>
                <label><?= $Lang->get('NAVBAR__LINK_TYPE_DROPDOWN') ?></label>
              </div>
            </div>

            <div id="type-normal" class="<?= ($nav['type'] == "1") ? '' : 'hidden' ?>">
              <div class="form-group">
                <label><?= $Lang->get('URL') ?></label>
                <div class="radio">
                  <input type="radio" class="type_plugin" name="url_type" value="plugin"<?= ($nav['url']['type'] == "plugin") ? ' checked=""' : '' ?>>
                  <label><?= $Lang->get('NAVBAR__LINK_TYPE_PLUGIN') ?></label>
                </div>
                  <div class="plugin<?= ($nav['url']['type'] == "plugin") ? '' : ' hidden' ?>">
                      <select class="form-control" name="url_plugin">
                          <?php
                          foreach ($url_plugins as $pluginId => $data) {
                              echo '<option disabled>' . $data->name . '</option>';
                              foreach ($data->routes as $name => $route)
                                  echo '<option value=\'' . json_encode(['id' => $pluginId, 'route' => $route]) . '\' ' . ($nav['url']['type'] === 'plugin' && $nav['url']['id'] === $pluginId && $nav['url']['route'] === $route ? ' selected' : '') . '>' . $name . ' (' . $route . ')</option>';
                          }
                          ?>
                      </select>
                  </div>
                <div class="radio">
                  <input type="radio" class="type_page" name="url_type" value="page"<?= ($nav['url']['type'] == "page") ? ' checked=""' : '' ?>>
                  <label><?= $Lang->get('NAVBAR__LINK_TYPE_PAGE') ?></label>
                </div>
                  <div class="page<?= ($nav['url']['type'] == "page") ? '' : ' hidden' ?>">
                    <select class="form-control" name="url_page">
                        <?php foreach ($url_pages as $key => $value) { ?>
                          <option value="<?= $key ?>"<?= ($nav['url']['id'] == $key) ? ' selected' : '' ?>><?= $value ?></option>
                        <?php } ?>
                    </select>
                  </div>
                <div class="radio">
                  <input type="radio" class="type_custom" name="url_type" value="custom"<?= ($nav['url']['type'] == "custom") ? ' checked=""' : '' ?>>
                  <label><?= $Lang->get('NAVBAR__LINK_TYPE_CUSTOM') ?></label>
                </div>
                  </label>
                  <input type="text" value="<?= ($nav['url']['type'] == "custom") ? $nav['url']['url'] : '' ?>" class="form-control custom<?= ($nav['url']['type'] == "custom") ? '' : ' hidden' ?>" placeholder="<?= $Lang->get('NAVBAR__CUSTOM_URL') ?>" name="url_custom">
                </div>
              </div>

            <div id="type-dropdown" class="<?= ($nav['type'] == "2") ? '' : 'hidden' ?>">
              <div class="form-group">
                <?php
                $i = 0;
                foreach (json_decode($nav['submenu'], true) as $name => $url) {
                  $i++;
                ?>
                  <div class="well" id="nav-<?= $i ?>">
                    <div class="form-group">
                      <label><?= $Lang->get('NAVBAR__LINK_NAME') ?></label>
                      <input type="text" class="form-control name_of_nav" value="<?= urldecode($name) ?>" name="name_of_nav">
                    </div>
                    <div class="form-group">
                      <label><?= $Lang->get('URL') ?></label>
                      <input type="text" class="form-control url_of_nav" value="<?= urldecode($url) ?>" placeholder="<?= $Lang->get('NAVBAR__CUSTOM_URL') ?>" name="url">
                    </div>
                      <a href="#" class="text-danger delete-nav pull-right"><?= $Lang->get('GLOBAL__DELETE') ?></a>
                      <br>
                  </div>
                <?php } ?>
              </div>
              <div id="add-js" data-number="<?= $i ?>"></div>
              <div class="control-group">
                <a href="#" id="add_nav" class="btn btn-success"><?= $Lang->get('NAVBAR__ADD_LINK') ?></a>
              </div>
            </div>

            <div class="form-group">
              <div class="checkbox">
                <input type="checkbox" name="new_tab"<?= ($nav['open_new_tab']) ? ' checked=""' : '' ?>>
                <label><?= $Lang->get('NAVBAR__OPEN_IN_NEW_TAB') ?></label>
              </div>
            </div>

            <div class="pull-right">
              <a href="<?= $this->Html->url(array('controller' => 'navbar', 'action' => 'admin_index', 'admin' => true)) ?>" class="btn btn-default"><?= $Lang->get('GLOBAL__CANCEL') ?></a>
              <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
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
    var add = '<div class="form-group"><div class="well" id="nav-'+how+'"><div class="form-group"><label><?= addslashes($Lang->get('NAVBAR__LINK_NAME')) ?></label><input type="text" class="form-control name_of_nav" name="name_of_nav"></div><div class="form-group"><label><?= $Lang->get('URL') ?></label><input type="text" class="form-control url_of_nav" placeholder="<?= $Lang->get('NAVBAR__CUSTOM_URL') ?>" name="url"></div><a href="#" class="text-danger delete-nav pull-right"><?= $Lang->get('GLOBAL__DELETE') ?></a><br></div></div>';
    $('#add-js').append(add);
    $('#add-js').attr('data-number', how);
    deleteNavEvents();
  });
  deleteNavEvents();
  function deleteNavEvents() {
      $('.delete-nav').unbind('click');
      $('.delete-nav').on('click', function (e) {
          e.preventDefault();
          var div = $(this).parent();
          div.slideUp(150, function () {
              $(this).remove();
          });
      });
  }
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
    function formatteData($form) {
        var name = $form.find("input[name='name']").val();
		var icon = $form.find("input[name='icon']").val();
        var type = $form.find("input[type='radio'][name='type']:checked").val();
        var url;
        if (type === "normal") {
            if ($form.find("input[name='url_type']:checked").val() === "custom")
                url = '{"type":"custom", "url":"' + $form.find("input[name='url_custom']").val() + '"}';
            else if ($form.find("input[name='url_type']:checked").val() === "plugin") {
                var value = $form.find("select[name='url_plugin']").val();
                value = JSON.parse(value);
                url = {
                    type: 'plugin',
                    id: value.id,
                    route: value.route
                };
                url = JSON.stringify(url);
            } else if ($form.find("input[name='url_type']:checked").val() === "page")
                url = '{"type":"page", "id":"' + $form.find("select[name='url_page']").val() + '"}';
            else
                url = "undefined";
        } else {
            var names = $('.name_of_nav').serialize();
            names = names.split('&');
            var urls = $('.url_of_nav').serialize();
            urls = urls.split('&');
            url = {};
            for (var key in test = names) {
                var l = test[key].split('=');
                l = l[1];
                var p = urls[key].split('=');
                p = p[1];
                url[l] = p;
            }
        }

        var inputs = {};
        inputs['name'] = name;
		inputs['icon'] = icon;
        inputs['type'] = type;
        inputs['url'] = url;
        inputs['open_new_tab'] = $('input[name="new_tab"]').is(':checked');
        inputs['data[_Token][key]'] = '<?= $csrfToken ?>';

        return inputs;
    }
</script>
