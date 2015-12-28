<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('SETTINGS') ?></h3>
        </div>
        <div class="box-body">

          <form method="post">
            <input type="hidden" id="form_infos" data-ajax="false">

            <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true"><?= $Lang->get('CONFIG__GENERAL_PREFERENCES') ?></a></li>
              <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false"><?= $Lang->get('CONFIG__SOCIAL_PREFERENCES') ?></a></li>
              <li class=""><a href="#tab_3" data-toggle="tab" aria-expanded="false"><?= $Lang->get('CONFIG__OTHER_PREFERENCES') ?></a></li>
              <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">

                <div class="form-group">
                  <label><?= $Lang->get('CONFIG__KEY_NAME') ?></label>
                    <?= $this->Form->input(false, array(
                      'div' => false,
                      'type' => 'text',
                      'name' => 'name',
                      'class' => 'form-control',
                      'value' => $config['name']
                    )); ?>
                </div>

                <div class="form-group">
                  <label><?= $Lang->get('CONFIG__KEY_EMAIL') ?></label>
                    <?= $this->Form->input(false, array(
                      'div' => false,
                      'type' => 'text',
                      'name' => 'email',
                      'class' => 'form-control',
                      'value' => $config['email']
                    )); ?>
                </div>

                <?php if($shopIsInstalled) { ?>

                  <div class="form-group">
                    <label><?= $Lang->get('CONFIG__KEY_MONEY_NAME_SINGULAR') ?></label>
                      <?= $this->Form->input(false, array(
                        'div' => false,
                        'type' => 'text',
                        'name' => 'money_name_singular',
                        'class' => 'form-control',
                        'value' => $config['money_name_singular']
                      )); ?>
                  </div>

                  <div class="form-group">
                    <label><?= $Lang->get('CONFIG__KEY_MONEY_NAME_PLURAL') ?></label>
                      <?= $this->Form->input(false, array(
                        'div' => false,
                        'type' => 'text',
                        'name' => 'money_name_plural',
                        'class' => 'form-control',
                        'value' => $config['money_name_plural']
                      )); ?>
                  </div>

                <?php } ?>

                <div class="form-group">
                  <label><?= $Lang->get('CONFIG__KEY_VERSION') ?></label>
                    <input type="text" value="<?= $config['version'] ?>" class="form-control disabled" disabled>
                </div>

              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_2">

                <div class="form-group">
                  <label><?= $Lang->get('CONFIG__KEY_TWITTER') ?></label>
                    <?= $this->Form->input(false, array(
                      'div' => false,
                      'type' => 'text',
                      'name' => 'twitter',
                      'class' => 'form-control',
                      'value' => $config['twitter']
                    )); ?>
                </div>

                <div class="form-group">
                  <label><?= $Lang->get('CONFIG__KEY_FACEBOOK') ?></label>
                    <?= $this->Form->input(false, array(
                      'div' => false,
                      'type' => 'text',
                      'name' => 'facebook',
                      'class' => 'form-control',
                      'value' => $config['facebook']
                    )); ?>
                </div>

                <div class="form-group">
                  <label><?= $Lang->get('CONFIG__KEY_YOUTUBE') ?></label>
                    <?= $this->Form->input(false, array(
                      'div' => false,
                      'type' => 'text',
                      'name' => 'youtube',
                      'class' => 'form-control',
                      'value' => $config['youtube']
                    )); ?>
                </div>

                <div class="form-group">
                  <label><?= $Lang->get('CONFIG__KEY_SKYPE') ?></label>
                    <?= $this->Form->input(false, array(
                      'div' => false,
                      'type' => 'text',
                      'name' => 'skype',
                      'class' => 'form-control',
                      'value' => $config['skype']
                    )); ?>
                </div>

                <hr>

                <h4><?= $Lang->get('CONFIG__OUR_SOCIAL_BTN') ?></h4>

                <button class="btn btn-success" id="addSocialBtnContainer"><?= $Lang->get('CONFIG__ADD_SOCIAL_BTN') ?></button>
                <?= $this->Html->script('admin/colorpicker') ?>

                <div class="addBtnContainer">
                  <?php
                  $i = 0;
                  if(!empty($social_buttons)) {
                    foreach ($social_buttons as $key => $value) {
                      $i++;

                      echo '<div id="btn-'.$i.'">';

                      echo '<hr>';

                      echo '<input type="hidden" value="'.$value['SocialButton']['id'].'" name="social_btn_edited['.$i.'][id]">';

                      echo '<div class="form-group">';
                      echo '<label>'.$Lang->get('CONFIG__TITLE_SOCIAL_BTN').'</label>';
                      echo '<input type="text" value="'.$value['SocialButton']['title'].'" name="social_btn_edited['.$i.'][title]" class="form-control">';
                      echo '</div>';

                      echo '<div class="form-group">';
                      echo '<label>'.$Lang->get('CONFIG__IMG_SOCIAL_BTN').'</label>';
                      echo '<input type="text" value="'.$value['SocialButton']['img'].'" name="social_btn_edited['.$i.'][img]" class="form-control">';
                      echo '</div>';

                      echo '<div class="form-group">';
                      echo '<label>'.$Lang->get('CONFIG__URL_SOCIAL_BTN').'</label>';
                      echo '<input type="text" value="'.$value['SocialButton']['url'].'" name="social_btn_edited['.$i.'][url]" class="form-control">';
                      echo '</div>';

                      echo '<div class="form-group">';
                      echo '<label>'.$Lang->get('CONFIG__COLOR_SOCIAL_BTN').'</label>';
                      echo '<input type="text" value="'.$value['SocialButton']['color'].'" name="social_btn_edited['.$i.'][color]" class="form-control" id="color_social_btn_'.$i.'">';
                      echo '</div>';
                      echo '<script>$(\'#color_social_btn_'.$i.'\').colorPicker()</script>';

                      echo '<button id="'.$i.'-'.$value['SocialButton']['id'].'" class="btn btn-danger pull-right delete-social-btn-added">'.$Lang->get('DELETE').'</button>';

                      echo '</div>';
                      echo '<div class="clearfix"></div>';
                    }
                  }
                  ?>
                </div>

                <script type="text/javascript">

                  var i = <?= $i ?>;

                  $('#addSocialBtnContainer').click(function(e) {

                    e.preventDefault();

                    var html_content = '<div id="btn-'+i+'">';

                    html_content += '<hr>';

                    html_content += '<div class="form-group">';
                    html_content += '<label><?= $Lang->get('CONFIG__TITLE_SOCIAL_BTN') ?></label>';
                    html_content += '<input type="text" name="social_btn['+i+'][title]" class="form-control">';
                    html_content += '</div>';

                    html_content += '<div class="form-group">';
                    html_content += '<label><?= $Lang->get('CONFIG__IMG_SOCIAL_BTN') ?></label>';
                    html_content += '<input type="text" name="social_btn['+i+'][img]" class="form-control">';
                    html_content += '</div>';

                    html_content += '<div class="form-group">';
                    html_content += '<label><?= $Lang->get('CONFIG__URL_SOCIAL_BTN') ?></label>';
                    html_content += '<input type="text" name="social_btn['+i+'][url]" class="form-control">';
                    html_content += '</div>';

                    html_content += '<div class="form-group">';
                    html_content += '<label><?= $Lang->get('CONFIG__COLOR_SOCIAL_BTN') ?></label>';
                    html_content += '<input type="text" name="social_btn['+i+'][color]" class="form-control" id="color_social_btn_'+i+'">';
                    html_content += '</div>';

                    html_content += '<button id="'+i+'" class="btn btn-danger pull-right delete-social-btn"><?= $Lang->get('DELETE') ?></button>';

                    html_content += '</div>';
                    html_content += '<div class="clearfix"></div>';

                    $('.addBtnContainer').hide().prepend(html_content).fadeIn(250);

                    $('input[id="color_social_btn_'+i+'"]').colorPicker();

                    $('.delete-social-btn').on('click', function(e) {
                      e.preventDefault();
                      deleteSocialBtn($(this).attr('id'), false);
                    });

                    i++;
                  });

                  $('.delete-social-btn').on('click', function(e) {
                    e.preventDefault();
                    deleteSocialBtn($(this).attr('id'), false);
                  });

                  $('.delete-social-btn-added').on('click', function(e) {
                    e.preventDefault();
                    deleteSocialBtn($(this).attr('id'), 'social_btn_added');
                  });

                  function deleteSocialBtn(infos, type) {
                    infos = infos.split('-');
                    var i = infos[0];
                    var id = infos[1];
                    if(type !== false) {
                      $('.addBtnContainer').append('<input type="hidden" name="'+type+'[deleted]['+i+']" value="'+id+'">'); // pour que le php le delete
                    }

                    $('#btn-'+i).slideUp(250, function(e) {
                      $(this).remove();
                    });
                  }
                </script>

              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_3">

                <div class="form-group">
                  <label><?= $Lang->get('CONFIG__KEY_MINEGUARD') ?></label>
                  <div class="radio">
                    <input type="radio" name="mineguard" value="true" <?= ($config['mineguard'] == 'true') ? 'checked=""' : '' ?>>
                    <label><?= $Lang->get('ENABLED') ?></label>
                  </div>
                  <div class="radio">
                    <input type="radio" name="mineguard" value="false" <?= ($config['mineguard'] == 'false') ? 'checked=""': '' ?>>
                    <label><?= $Lang->get('DISABLED') ?></label>
                  </div>
                </div>

                <div class="form-group">
                  <label><?= $Lang->get('CONFIG__KEY_EMAIL_SEND_TYPE') ?></label>
                  <div class="radio">
                    <input type="radio" name="email_send_type" value="1" <?= ($config['email_send_type'] == '1') ? 'checked=""' : '' ?>>
                    <label><?= $Lang->get('NORMAL') ?></label>
                  </div>
                  <div class="radio">
                    <input type="radio" name="email_send_type" value="2" <?= ($config['email_send_type'] == '2') ? 'checked=""': '' ?>>
                    <label><?= $Lang->get('SMTP') ?></label>
                  </div>
                </div>

                <script type="text/javascript">
                  $('input[name="email_send_type"]').on('change', function(e) {
                    if($(this).val() == '2') {
                      $('#smtp-config').slideDown(250);
                    } else {
                      $('#smtp-config').slideUp(250);
                    }
                  });
                </script>

                <div id="smtp-config" style="display:<?= ($config['email_send_type'] == '1') ? 'none' : 'block' ?>;">

                  <div class="form-group">
                    <label><?= $Lang->get('CONFIG__KEY_SMTP_HOST') ?></label>
                      <?= $this->Form->input(false, array(
                        'div' => false,
                        'type' => 'text',
                        'name' => 'smtpHost',
                        'class' => 'form-control',
                        'value' => $config['smtpHost']
                      )); ?>
                  </div>

                  <div class="form-group">
                    <label><?= $Lang->get('CONFIG__KEY_SMTP_USERNAME') ?></label>
                      <?= $this->Form->input(false, array(
                        'div' => false,
                        'type' => 'text',
                        'name' => 'smtpUsername',
                        'class' => 'form-control',
                        'value' => $config['smtpUsername']
                      )); ?>
                  </div>

                  <div class="form-group">
                    <label><?= $Lang->get('CONFIG__KEY_SMTP_PORT') ?></label>
                      <?= $this->Form->input(false, array(
                        'div' => false,
                        'type' => 'text',
                        'name' => 'smtpPort',
                        'class' => 'form-control',
                        'value' => $config['smtpPort']
                      )); ?>
                  </div>

                  <div class="form-group">
                    <label><?= $Lang->get('CONFIG__KEY_SMTP_PASSWORD') ?></label>
                      <?= $this->Form->input(false, array(
                        'div' => false,
                        'type' => 'text',
                        'name' => 'smtpPassword',
                        'class' => 'form-control',
                        'value' => $config['smtpPassword']
                      )); ?>
                  </div>

                </div>

              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>

            <input type="hidden" name="data[_Token][key]" value="<?= $csrfToken ?>">

            <button class="btn btn-primary" type="submit"><?= $Lang->get('SUBMIT') ?></button>
            <a href="<?= $this->Html->url(array('controller' => '', 'action' => '', 'admin' => true)) ?>" type="button" class="btn btn-default"><?= $Lang->get('CANCEL') ?></a>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>
