<?php
App::import('Component', 'ConfigurationComponent');
$this->Configuration = new ConfigurationComponent();
?>
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
            <?php
            $config = $this->Configuration->get_all();
            $config = $config['Configuration'];
            foreach ($config as $key => $value) { ?>
              <?php if(strpos($key, 'maintenance') === false AND strpos($key, 'id') === false AND strpos($key, 'layout') === false AND strpos($key, 'theme') === false AND strpos($key, 'server_') === false) { ?>
                <?php if(strpos($key, 'version') === false AND $key != 'mineguard' AND strpos($key, 'banner_server') === false && strpos($key, 'email_send_type') === false) { ?>
                  <div class="form-group">
                    <label><?= $Lang->get(strtoupper($key)) ?></label>
                      <?= $this->Form->input(false, array(
                        'div' => false,
                          'type' => 'text',
                          'name' => $key,
                          'class' => 'form-control',
                          'value' => $value
                      )); ?>
                      <?php if($key == "lang") { ?>
                        <span class="help-inline"><?= $Lang->get('AVAILABLE') ?> : fr, en. <?= $Lang->get('DEFAULT') ?> : fr.</span>
                      <?php } ?>
                  </div>
                <?php } elseif($key == 'mineguard') { ?>
                  <div class="form-group">
                    <label><?= $Lang->get('MINEGUARD') ?></label>
                    <br>
                    <input type="radio" name="mineguard" value="true" <?php if($value == 'true') { echo 'checked=""'; } ?>>
                    <?= $Lang->get('ENABLED') ?>
                    <input type="radio" name="mineguard" value="false" <?php if($value == 'false') { echo 'checked=""'; } ?>>
                    <?= $Lang->get('DISABLED') ?>
                  </div>
                <?php } elseif($key == 'banner_server') { ?>
                  <div class="form-group">
                    <label><?= $Lang->get('BANNER_SERVER_CHOOSE') ?></label>
                      <?php if(empty($servers)) { ?>
                        <?php
                          echo $this->Form->input('field', array(
                              'multiple' => true,
                                'label' => false,
                                'div' => false,
                                'disabled' => 'disabled',
                                'class' => 'form-control'
                              ));
                        ?>
                      <?php } else { ?>
                        <?php
                          echo $this->Form->input('field', array(
                              'multiple' => true,
                                'label' => false,
                                'div' => false,
                                'name' => 'banner_server',
                                  'options' => $servers,
                                  'selected' => $selected_server,
                                  'class' => 'form-control'
                              ));
                        ?>
                      <?php } ?>
                  </div>
                <?php } elseif($key == 'email_send_type') { ?>
                  <div class="form-group">
                    <label><?= $Lang->get('EMAIL_SEND_TYPE') ?></label>
                    <br>
                    <input type="radio" name="email_send_type" value="1" <?php if($value == '1') { echo 'checked=""'; } ?>>
                    <?= $Lang->get('NORMAL') ?>
                    <input type="radio" name="email_send_type" value="2" <?php if($value == '2') { echo 'checked=""'; } ?>>
                    <?= $Lang->get('SMTP') ?>
                  </div>
                <?php } else { ?>
                  <div class="form-group">
                    <label><?= $Lang->get(strtoupper($key)) ?></label>
                    <?= $this->Form->input(false, array(
                      'div' => false,
                        'type' => 'text',
                        'name' => $key,
                        'disabled' => 'disabled',
                        'class' => 'form-control',
                        'placeholder' => $value
                    )); ?>
                  </div>
                <?php } ?>
              <?php } ?>
            <?php } ?>

            <button class="btn btn-primary" type="submit"><?= $Lang->get('SUBMIT') ?></button>
            <a href="<?= $this->Html->url(array('controller' => '', 'action' => '', 'admin' => true)) ?>" type="button" class="btn"><?= $Lang->get('CANCEL') ?></a>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>
