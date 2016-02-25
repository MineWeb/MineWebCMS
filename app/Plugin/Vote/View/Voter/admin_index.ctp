<section class="content">
  <a href="<?= $this->Html->url(array('controller' => 'voter', 'action' => 'reset', 'admin' => true)) ?>" class="btn btn-app btn-block"><i class="fa fa-repeat"></i><?= $Lang->get('GLOBAL__RESET') ?></a>
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('VOTE__TITLE') ?></h3>
        </div>
        <div class="box-body">

          <form action="<?= $this->Html->url(array('action' => 'add_ajax')) ?>" method="post"  data-ajax="true" data-custom-function="formatteData" data-redirect-url="">

            <div class="ajax-msg"></div>

            <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true"><?= $Lang->get('VOTE__CONFIG_WEBSITES') ?></a></li>
              <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false"><?= $Lang->get('VOTE__CONFIG_REWARDS') ?></a></li>
              <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">

                <?php if(!empty($vote['websites'])) { ?>
                  <?php $i = 0; foreach ($vote['websites'] as $k => $v) { $i++; ?>
                    <div class="box box-success websites" id="website-<?= $i ?>">
                      <div class="box-body">
                        <div class="form-group">
                          <label><?= $Lang->get('VOTE__CONFIG_TIME_VOTE') ?></label>
                          <input name="time_vote" class="form-control" value="<?= $v['time_vote'] ?>" placeholder="minutes" type="text">
                        </div>

                        <div class="form-group">
                          <label><?= $Lang->get('VOTE__CONFIG_PAGE_VOTE') ?></label>
                          <input name="page_vote" class="form-control" value="<?= $v['page_vote'] ?>" placeholder="Ex: http://google.fr" type="text">
                        </div>

                        <div class="form-group">
                          <label><?= $Lang->get('VOTE__CONFIG_WEBSITE_TYPE') ?></label>
                          <select name="website_type" data-id="<?= $i ?>" class="form-control website_type">
                            <option value="rpg"<?= ($v['website_type'] == "rpg") ? ' selected' : '' ?>><?= $Lang->get('VOTE__CONFIG_WEBSITE_TYPE_RPG') ?></option>
                            <option value="other"<?= ($v['website_type'] == "other") ? ' selected' : '' ?>><?= $Lang->get('VOTE__CONFIG_WEBSITE_TYPE_OTHER') ?></option>
                          </select>
                        </div>

                        <div id="rpg-<?= $i ?>" style="display:<?= ($v['website_type'] == "rpg") ? 'block' : 'none' ?>;">
                          <div class="form-group">
                          <label><?= $Lang->get('VOTE__CONFIG_ID_RPG') ?></label>
                            <input name="rpg_id" class="form-control" placeholder="Ex: 44835 (http://www.rpg-paradize.com/site-[...]-44835)" type="text" value="<?= @$v['rpg_id'] ?>">
                          </div>
                        </div>
                      </div>
                      <div class="box-footer">
                        <button id="<?= $i ?>" class="btn btn-danger pull-right delete_website"><?= $Lang->get('GLOBAL__DELETE') ?></button><br>
                      </div>
                    </div>
                  <?php } ?>
                <?php } else { $i=1; ?>
                  <div class="box box-success websites">
                    <div class="box-body">
                      <div class="form-group">
                        <label><?= $Lang->get('VOTE__CONFIG_TIME_VOTE') ?></label>
                        <input name="time_vote" class="form-control" placeholder="minutes" type="text">
                      </div>

                      <div class="form-group">
                        <label><?= $Lang->get('VOTE__CONFIG_PAGE_VOTE') ?></label>
                        <input name="page_vote" class="form-control" placeholder="Ex: http://google.fr" type="text">
                      </div>

                      <div class="form-group">
                        <label><?= $Lang->get('VOTE__CONFIG_WEBSITE_TYPE') ?></label>
                        <select name="website_type" data-id="1" class="form-control website_type">
                          <option value="rpg"><?= $Lang->get('VOTE__CONFIG_WEBSITE_TYPE_RPG') ?></option>
                          <option value="other"><?= $Lang->get('VOTE__CONFIG_WEBSITE_TYPE_OTHER') ?></option>
                        </select>
                      </div>

                      <div id="rpg-1" style="display:block;">
                        <div class="form-group">
                        <label><?= $Lang->get('VOTE__CONFIG_ID_RPG') ?></label>
                          <input name="rpg_id" class="form-control" placeholder="Ex: 44835 (http://www.rpg-paradize.com/site-[...]-44835)" type="text">
                        </div>
                      </div>
                    </div>
                  </div>
                <?php } ?>

                <div id="website_content"></div>

                <div class="form-group">
                  <div class="btn btn-success btn-block" data-count="<?= $i ?>" id="add_website"><?= $Lang->get('VOTE__CONFIG_ADD_WEBSITE') ?></div>
                </div>

              </div>
              <div class="tab-pane" id="tab_2">

                <div class="form-group">
                  <label><?= $Lang->get('VOTE__CONFIG_REWARDS_TYPE') ?></label>
                  <?php
                    if(@$vote['rewards_type'] == 0) {
                      $options = array('0' => $Lang->get('GLOBAL__RANDOM'), '1' => $Lang->get('GLOBAL__ALL'));
                    } else {
                      $options = array('1' => $Lang->get('GLOBAL__ALL'), '0' => $Lang->get('GLOBAL__RANDOM'));
                    }
                  ?>
                  <select class="form-control" name="rewards_type" id="rewards_type">
                    <?php foreach ($options as $key => $value) { ?>
                      <option value="<?= $key ?>"><?= $value ?></option>
                    <?php } ?>
                  </select>
                </div>

                <div class="form-group">
                  <label><?= $Lang->get('SERVER__TITLE') ?></label>
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
                          <label><?= $Lang->get('VOTE__CONFIG_REWARD_TYPE') ?></label>
                          <select name="type_reward" class="form-control reward_type">
                            <?php if($v['type'] == "money") { ?>
                              <option value="money"><?= $Lang->get('USER__MONEY') ?></option>
                              <option value="server"><?= $Lang->get('SERVER__TITLE') ?></option>
                            <?php } else { ?>
                              <option value="server"><?= $Lang->get('SERVER__TITLE') ?></option>
                              <option value="money"><?= $Lang->get('USER__MONEY') ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="form-group">
                          <label><?= $Lang->get('GLOBAL__NAME') ?></label>
                          <input type="text" class="form-control reward_name" name="reward_name" value="<?= $v['name'] ?>">
                        </div>
                        <div class="form-group">
                          <label><?= $Lang->get('VOTE__CONFIG_REWARD_VALUE') ?></label>
                          <?php
                          if($v['type'] == "money") {
                            $reward_value = $v['how'];
                          } else {
                            $reward_value = $v['command'];
                          }
                          ?>
                          <input type="text" name="reward_value" class="form-control reward_value" placeholder="<?= $Lang->get('VOTE__CONFIG_COMMAND_OR_MONEY') ?>" value="<?= $reward_value ?>">
                        </div>
                        <div class="form-group reward_proba_container" style="display:<?= (@$vote['rewards_type'] == 0) ? 'block' : 'none' ?>;">
                          <label><?= $Lang->get('VOTE__CONFIG_REWARD_PROBABILITY') ?></label>
                          <input type="text" name="reward_proba" class="form-control reward_proba" value="<?= $v['proba'] ?>" placeholder="<?= $Lang->get('VOTE__CONFIG_REWARD_PERCENTAGE') ?>">
                        </div>
                      </div>
                      <div class="box-footer">
                        <button id="<?= $i ?>" class="btn btn-danger pull-right delete"><?= $Lang->get('GLOBAL__DELETE') ?></button><br>
                      </div>
                    </div>
                  <?php } ?>
                <?php } else { $i = 1; ?>
                  <div class="box box-info reward_list" id="reward-1">
                    <div class="box-body">
                      <div class="form-group">
                        <label><?= $Lang->get('VOTE__CONFIG_REWARD_TYPE') ?></label>
                        <select name="type_reward" class="form-control reward_type">
                          <option value="money"><?= $Lang->get('USER__MONEY') ?></option>
                          <option value="server"><?= $Lang->get('SERVER__TITLE') ?></option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label><?= $Lang->get('GLOBAL__NAME') ?></label>
                        <input type="text" class="form-control reward_name" name="reward_name">
                      </div>
                      <div class="form-group">
                        <label><?= $Lang->get('VOTE__CONFIG_REWARD_VALUE') ?></label>
                        <input type="text" name="reward_value" class="form-control reward_value" placeholder="<?= $Lang->get('VOTE__CONFIG_COMMAND_OR_MONEY') ?>">
                      </div>
                      <div class="form-group reward_proba_container" style="display:<?= (@$vote['rewards_type'] == 0) ? 'block' : 'none' ?>;">
                        <label><?= $Lang->get('VOTE__CONFIG_REWARD_PROBABILITY') ?></label>
                        <input type="text" name="reward_proba" class="form-control reward_proba" value="<?= $v['proba'] ?>" placeholder="<?= $Lang->get('VOTE__CONFIG_REWARD_PERCENTAGE') ?>">
                      </div>
                    </div>
                  </div>
                <?php } ?>

                <div id="add-js" data-number="<?= $i ?>"></div>
                <div class="form-group">
                  <a href="#" id="add_reward" class="btn btn-info"><?= $Lang->get('VOTE__CONFIG_ADD_REWARD') ?></a>
                </div>

              </div>

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
    var add = '';
    add +='<div class="box box-success websites">';
      add +='<div class="box-body">';
        add +='<div class="form-group">';
          add +='<label><?= addslashes($Lang->get('VOTE__CONFIG_TIME_VOTE')) ?></label>';
          add +='<input name="time_vote" class="form-control" placeholder="minutes" type="text">';
        add +='</div>';
        add +='<div class="form-group">';
          add +='<label><?= addslashes($Lang->get('VOTE__CONFIG_PAGE_VOTE')) ?></label>';
          add +='<input name="page_vote" class="form-control" placeholder="Ex: http://google.fr" type="text">';
        add +='</div>';
        add +='<div class="form-group">';
          add +='<label><?= addslashes($Lang->get('VOTE__CONFIG_WEBSITE_TYPE')) ?></label>';
          add +='<select name="website_type" data-id="'+how+'" class="form-control website_type">';
            add +='<option value="rpg"><?= addslashes($Lang->get('VOTE__CONFIG_WEBSITE_TYPE_RPG')) ?></option>';
            add +='<option value="other"><?= addslashes($Lang->get('VOTE__CONFIG_WEBSITE_TYPE_OTHER')) ?></option>';
            add +='</select>';
          add +='</div>';
        add +='<div id="rpg-'+how+'">';
          add +='<div class="form-group">';
            add +='<label><?= addslashes($Lang->get('VOTE__CONFIG_ID_RPG')) ?></label>';
            add +='<input name="rpg_id" class="form-control" placeholder="Ex: 44835 (http://www.rpg-paradize.com/site-[...]-44835)" type="text">';
          add +='</div>';
        add +='</div>';
      add +='</div>';
    add +='</div>';
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

    var add = '';
    add +='<div class="box box-info reward_list" id="reward-'+how+'">';
      add +='<div class="box-body">';
        add +='<div class="form-group">';
          add +='<label><?= $Lang->get('VOTE__CONFIG_REWARD_TYPE') ?></label>';
            add +='<select name="type_reward" class="form-control reward_type">';
              add +='<option value="money"><?= $Lang->get('USER__MONEY') ?></option>';
              add +='<option value="server"><?= $Lang->get('SERVER__TITLE') ?></option>';
            add +='</select>';
          add +='</div>';
        add +='<div class="form-group">';
          add +='<label><?= $Lang->get('GLOBAL__NAME') ?></label>';
          add +='<input type="text" class="form-control reward_name" name="reward_name">';
        add +='</div>';
        add +='<div class="form-group">';
          add +='<label><?= $Lang->get('VOTE__CONFIG_REWARD_VALUE') ?></label>';
          add +='<input type="text" name="reward_value" class="form-control reward_value" placeholder="<?= $Lang->get('VOTE__CONFIG_COMMAND_OR_MONEY') ?>">';
        add +='</div>';
        add +='<div class="form-group reward_proba_container" style="display:'+display_proba+';">';
          add +='<label><?= $Lang->get('VOTE__CONFIG_REWARD_PROBABILITY') ?></label>';
          add +='<input type="text" name="reward_proba" class="form-control reward_proba" placeholder="<?= addslashes($Lang->get('VOTE__CONFIG_REWARD_PERCENTAGE')) ?>">';
        add +='</div>';
      add +='</div>';
    add +='</div>';
    $('#add-js').append(add);
    $('#add-js').attr('data-number', how);
  });
</script>
<script>
  function formatteData($form) {

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

   return {servers : servers, rewards_type : rewards_type, rewards : rewards, website : website};
  }
</script>
