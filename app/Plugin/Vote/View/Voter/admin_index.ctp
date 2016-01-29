<section class="content">
  <a href="<?= $this->Html->url(array('controller' => 'voter', 'action' => 'reset', 'admin' => true)) ?>" class="btn btn-app btn-block"><i class="fa fa-repeat"></i><?= $Lang->get('RESET') ?></a>
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('VOTE_TITLE') ?></h3>
        </div>
        <div class="box-body">

          <form action="" method="post"  data-ajax="false">

            <div class="ajax-msg"></div>

            <div class="form-group">
              <label><?= $Lang->get('REWARDS_TYPE') ?></label>
              <?php
                if(@$vote['rewards_type'] == 0) {
                  $options = array('0' => $Lang->get('RANDOM'), '1' => $Lang->get('ALL'));
                } else {
                  $options = array('1' => $Lang->get('ALL'), '0' => $Lang->get('RANDOM'));
                }
              ?>
              <select class="form-control" name="rewards_type" id="rewards_type">
                <?php foreach ($options as $key => $value) { ?>
                  <option value="<?= $key ?>"><?= $value ?></option>
                <?php } ?>
              </select>
            </div>

            <div class="form-group">
              <label><?= $Lang->get('SERVER') ?></label>
              <select class="form-control" name="servers" multiple>
                <?php foreach ($servers as $key => $value) { ?>
                    <option value="<?= $key ?>"<?= (in_array($key, $selected_server)) ? ' selected' : '' ?>><?= $value ?></option>
                <?php } ?>
              </select>
            </div>

            <?php if(!empty($vote['rewards'])) { ?>
              <?php $i = 0; foreach ($vote['rewards'] as $k => $v) { $i++; ?>
                <div class="box box-info reward_list" id="reward-<?= $i ?>">
                  <div class="box-body">
                    <div class="form-group">
                      <label><?= $Lang->get('REWARD_TYPE') ?></label>
                      <select name="type_reward" class="form-control reward_type">
                        <?php if($v['type'] == "money") { ?>
                          <option value="money"><?= $Lang->get('MONEY') ?></option>
                          <option value="server"><?= $Lang->get('SERVER') ?></option>
                        <?php } else { ?>
                          <option value="server"><?= $Lang->get('SERVER') ?></option>
                          <option value="money"><?= $Lang->get('MONEY') ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label><?= $Lang->get('NAME') ?></label>
                      <input type="text" class="form-control reward_name" name="reward_name" value="<?= $v['name'] ?>">
                    </div>
                    <div class="form-group">
                      <label><?= $Lang->get('REWARD_VALUE') ?></label>
                      <?php
                      if($v['type'] == "money") {
                        $reward_value = $v['how'];
                      } else {
                        $reward_value = $v['command'];
                      }
                      ?>
                      <input type="text" name="reward_value" class="form-control reward_value" placeholder="<?= $Lang->get('CMD_OR_MONEY') ?>" value="<?= $reward_value ?>">
                    </div>
                    <div class="form-group reward_proba_container" style="display:<?= (@$vote['rewards_type'] == 0) ? 'block' : 'none' ?>;">
                      <label><?= $Lang->get('REWARD_PROBABILITY') ?></label>
                      <input type="text" name="reward_proba" class="form-control reward_proba" value="<?= $v['proba'] ?>" placeholder="<?= $Lang->get('REWARD_PERCENTAGE') ?>">
                    </div>
                  </div>
                  <div class="box-footer">
                    <button id="<?= $i ?>" class="btn btn-danger pull-right delete"><?= $Lang->get('DELETE') ?></button><br>
                  </div>
                </div>
              <?php } ?>
            <?php } else { $i = 1; ?>
              <div class="box box-info reward_list" id="reward-1">
                <div class="box-body">
                  <div class="form-group">
                    <label><?= $Lang->get('REWARD_TYPE') ?></label>
                    <select name="type_reward" class="form-control reward_type">
                      <option value="money"><?= $Lang->get('MONEY') ?></option>
                      <option value="server"><?= $Lang->get('SERVER') ?></option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label><?= $Lang->get('NAME') ?></label>
                    <input type="text" class="form-control reward_name" name="reward_name">
                  </div>
                  <div class="form-group">
                    <label><?= $Lang->get('REWARD_VALUE') ?></label>
                    <input type="text" name="reward_value" class="form-control reward_value" placeholder="<?= $Lang->get('CMD_OR_MONEY') ?>">
                  </div>
                  <div class="form-group reward_proba_container" style="display:<?= (@$vote['rewards_type'] == 0) ? 'block' : 'none' ?>;">
                    <label><?= $Lang->get('REWARD_PROBABILITY') ?></label>
                    <input type="text" name="reward_proba" class="form-control reward_proba" value="<?= $v['proba'] ?>" placeholder="<?= $Lang->get('REWARD_PERCENTAGE') ?>">
                  </div>
                </div>
            </div>
            <?php } ?>
            <div id="add-js" data-number="<?= $i ?>"></div>
            <div class="form-group">
              <a href="#" id="add_reward" class="btn btn-info"><?= $Lang->get('ADD_REWARD') ?></a>
            </div>

            <hr>

            <?php if(!empty($vote['websites'])) { ?>
              <?php $i = 0; foreach ($vote['websites'] as $k => $v) { $i++; ?>
                <div class="box box-success websites" id="website-<?= $i ?>">
                  <div class="box-body">
                    <div class="form-group">
                      <label><?= $Lang->get('TIME_VOTE') ?></label>
                      <input name="time_vote" class="form-control" value="<?= $v['time_vote'] ?>" placeholder="minutes" type="text">
                    </div>

                    <div class="form-group">
                      <label><?= $Lang->get('PAGE_VOTE') ?></label>
                      <input name="page_vote" class="form-control" value="<?= $v['page_vote'] ?>" placeholder="Ex: http://google.fr" type="text">
                    </div>

                    <div class="form-group">
                      <label><?= $Lang->get('WEBSITE_TYPE') ?></label>
                      <select name="website_type" data-id="<?= $i ?>" class="form-control website_type">
                        <option value="rpg"<?= ($v['website_type'] == "rpg") ? ' selected' : '' ?>><?= $Lang->get('WEBSITE_TYPE_RPG') ?></option>
                        <option value="other"<?= ($v['website_type'] == "other") ? ' selected' : '' ?>><?= $Lang->get('WEBSITE_TYPE_OTHER') ?></option>
                      </select>
                    </div>

                    <div id="rpg-<?= $i ?>" style="display:<?= ($v['website_type'] == "rpg") ? 'block' : 'none' ?>;">
                      <div class="form-group">
                      <label><?= $Lang->get('ID_VOTE') ?></label>
                        <input name="rpg_id" class="form-control" placeholder="Ex: 44835 (http://www.rpg-paradize.com/site-[...]-44835)" type="text" value="<?= @$v['rpg_id'] ?>">
                      </div>
                    </div>
                  </div>
                  <div class="box-footer">
                    <button id="<?= $i ?>" class="btn btn-danger pull-right delete_website"><?= $Lang->get('DELETE') ?></button><br>
                  </div>
                </div>
              <?php } ?>
            <?php } else { $i=1; ?>
              <div class="box box-success websites">
                <div class="box-body">
                  <div class="form-group">
                    <label><?= $Lang->get('TIME_VOTE') ?></label>
                    <input name="time_vote" class="form-control" placeholder="minutes" type="text">
                  </div>

                  <div class="form-group">
                    <label><?= $Lang->get('PAGE_VOTE') ?></label>
                    <input name="page_vote" class="form-control" placeholder="Ex: http://google.fr" type="text">
                  </div>

                  <div class="form-group">
                    <label><?= $Lang->get('WEBSITE_TYPE') ?></label>
                    <select name="website_type" data-id="1" class="form-control website_type">
                      <option value="rpg"><?= $Lang->get('WEBSITE_TYPE_RPG') ?></option>
                      <option value="other"><?= $Lang->get('WEBSITE_TYPE_OTHER') ?></option>
                    </select>
                  </div>

                  <div id="rpg-1" style="display:block;">
                    <div class="form-group">
                    <label><?= $Lang->get('ID_VOTE') ?></label>
                      <input name="rpg_id" class="form-control" placeholder="Ex: 44835 (http://www.rpg-paradize.com/site-[...]-44835)" type="text">
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>

            <div id="website_content"></div>

            <div class="form-group">
              <div class="btn btn-success btn-block" data-count="<?= $i ?>" id="add_website"><?= $Lang->get('ADD_WEBSITE') ?></div>
            </div>

            <div class="pull-right">
              <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>
<script>
  $('.website_type').change(function(e) {
    if($(this).val() == "other") {
      $('#rpg-'+$(this).attr('data-id')).hide(500);
    } else {
      $('#rpg-'+$(this).attr('data-id')).show(500);
    }
  });

  $('#rewards_type').change(function(e) {
    if($(this).val() == "0") {
      $.each($('.reward_proba_container'), function(index, value) {
        $(value).show();
      });
    } else {
      $.each($('.reward_proba_container'), function(index, value) {
        $(value).hide();
      });
    }
  });

  $('#add_website').click(function(e) {
    e.preventDefault();
    var how = parseInt($(this).attr('data-count')) + 1;
    var add = '<div class="box box-success websites"><div class="box-body"><div class="form-group"><label><?= addslashes($Lang->get('TIME_VOTE')) ?></label><input name="time_vote" class="form-control" placeholder="minutes" type="text"></div><div class="form-group"><label><?= addslashes($Lang->get('PAGE_VOTE')) ?></label><input name="page_vote" class="form-control" placeholder="Ex: http://google.fr" type="text"></div><div class="form-group"><label><?= addslashes($Lang->get('WEBSITE_TYPE')) ?></label><select name="website_type" data-id="'+how+'" class="form-control website_type"><option value="rpg"><?= addslashes($Lang->get('WEBSITE_TYPE_RPG')) ?></option><option value="other"><?= addslashes($Lang->get('WEBSITE_TYPE_OTHER')) ?></option></select></div><div id="rpg-'+how+'"><div class="form-group"><label><?= addslashes($Lang->get('ID_VOTE')) ?></label><input name="rpg_id" class="form-control" placeholder="Ex: 44835 (http://www.rpg-paradize.com/site-[...]-44835)" type="text"></div></div></div></div>';
    $('#website_content').append(add);
    $(this).attr('data-count', how);

    $('.website_type').change(function(e) {
      if($(this).val() == "other") {
        $('#rpg-'+$(this).attr('data-id')).hide(500);
      } else {
        $('#rpg-'+$(this).attr('data-id')).show(500);
      }
    });
  });

  $('.delete').click(function(e) {
    e.preventDefault();
    var id = $(this).attr('id');
    $('#reward-'+id).slideUp(500).empty();
  });

  $('.delete_website').click(function(e) {
    e.preventDefault();
    var id = $(this).attr('id');
    $('#website-'+id).slideUp(500).empty();
  });


  $('#add_reward').click(function(e) {
    e.preventDefault();
    var how = $('#add-js').attr('data-number');
    how = parseInt(how) + 1;

    if($('#rewards_type').val() == "0") {
      var display_proba = 'block';
    } else {
      var display_proba = 'none';
    }

    var add = '<div class="box box-info reward_list" id="reward-'+how+'"><div class="box-body"><div class="form-group"><label><?= $Lang->get('REWARD_TYPE') ?></label><select name="type_reward" class="form-control reward_type"><option value="money"><?= $Lang->get('MONEY') ?></option><option value="server"><?= $Lang->get('SERVER') ?></option></select></div><div class="form-group"><label><?= $Lang->get('NAME') ?></label><input type="text" class="form-control reward_name" name="reward_name"></div><div class="form-group"><label><?= $Lang->get('REWARD_VALUE') ?></label><input type="text" name="reward_value" class="form-control reward_value" placeholder="<?= $Lang->get('CMD_OR_MONEY') ?>"></div><div class="form-group reward_proba_container" style="display:'+display_proba+';"><label><?= $Lang->get('REWARD_PROBABILITY') ?></label><input type="text" name="reward_proba" class="form-control reward_proba" placeholder="<?= addslashes($Lang->get('REWARD_PERCENTAGE')) ?>"></div></div></div>';
    $('#add-js').append(add);
    $('#add-js').attr('data-number', how);
  });
</script>
<script>
  $("form").submit(function( event ) {
    event.preventDefault();

    var $form = $( this );

    var submit = $form.find("input[type='submit']");

    $form.find('.ajax-msg').empty().html('<div class="alert alert-info"><a class="close" data-dismiss="alert">×</a><?= $Lang->get('LOADING') ?> ...</div>').fadeIn(500);
    var submit_btn_content = $form.find('button[type=submit]').html();
    $form.find('button[type=submit]').html('<?= $Lang->get('LOADING') ?>...').attr('disabled', 'disabled').fadeIn(500);

    var servers = $form.find("select[name='servers']").val();
    var rewards_type = $form.find("select[name='rewards_type']").val();

    // récompenses
    var rewards = {};
    var i = 0;
    $.each($('.reward_list'), function(index, value) {
      var reward_infos = $(value);

      if(reward_infos.find('select[name="type_reward"]').val() == "server") {
        rewards[i] = {
          type : reward_infos.find('select[name="type_reward"]').val(),
          name : reward_infos.find('input[name="reward_name"]').val(),
          command : reward_infos.find('input[name="reward_value"]').val(),
          proba : reward_infos.find('input[name="reward_proba"]').val(),
        }
      } else {
        rewards[i] = {
          type : reward_infos.find('select[name="type_reward"]').val(),
          name : reward_infos.find('input[name="reward_name"]').val(),
          how : reward_infos.find('input[name="reward_value"]').val(),
          proba : reward_infos.find('input[name="reward_proba"]').val()
        }
      }
      i++;
    });
    //

    // sites
    var website = {};
    var i = 0;
    $.each($('.websites'), function(index, value) {
      var website_infos = $(value);
      website[i] = {
        time_vote : website_infos.find('input[name="time_vote"]').val(),
        page_vote : website_infos.find('input[name="page_vote"]').val(),
        website_type : website_infos.find('select[name="website_type"]').val(),
        rpg_id : website_infos.find('input[name="rpg_id"]').val()
      }
      i++;
    });
    //

   $.post("<?= $this->Html->url(array('controller' => 'voter', 'action' => 'add_ajax', 'admin' => true)) ?>", { servers : servers, rewards_type : rewards_type, rewards : rewards, website : website }, function(data) {
        data2 = data.split("|");
    if(data.indexOf('true') != -1) {
          $('.ajax-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
          $form.find('button[type="submit"]').html(submit_btn_content).attr('disabled', false).fadeIn(500);
           document.location.href="<?= $this->Html->url(array('controller' => 'voter', 'action' => 'admin_index', 'admin' => 'true')) ?>";
        } else if(data.indexOf('false') != -1) {
          $('.ajax-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
          $form.find('button[type="submit"]').html(submit_btn_content).attr('disabled', false).fadeIn(500);
      } else {
      $('.ajax-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
      $form.find('button[type="submit"]').html(submit_btn_content).attr('disabled', false).fadeIn(500);
    }
    });
  });
</script>
