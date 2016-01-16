<?php
$this->EyPlugin = new EyPluginComponent;
$this->Configuration = new ConfigurationComponent;
?>
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('EDIT_USER') ?></h3>
        </div>
        <div class="box-body">
          <form action="<?= $this->Html->url(array('controller' => 'user', 'action' => 'edit_ajax')) ?>" method="post">
            <input type="hidden" id="form_infos" data-ajax="true" data-redirect-url="<?= $this->Html->url(array('controller' => 'user', 'action' => 'index', 'admin' => 'true')) ?>">

            <div class="ajax-msg"></div>

            <input type="hidden" value="<?= $user['pseudo'] ?>" name="pseudo">

            <div class="form-group">
              <label><?= $Lang->get('PSEUDO') ?></label>
              <input class="form-control" value="<?= $user['pseudo'] ?>" type="text" disabled="">
           	</div>

            <?php if(!$this->Configuration->get('confirm_mail_signup')) { ?>
              <div class="form-group">
                <label><?= $Lang->get('EMAIL') ?></label>
                <input name="email" class="form-control" value="<?= $user['email'] ?>" type="email">
              </div>
            <?php } else { ?>
              <div class="form-group">
                <label><?= $Lang->get('EMAIL') ?></label>
                <div class="input-group">
                  <input value="<?= $user['email'] ?>" type="email" name="email" class="form-control">
                  <span class="input-group-btn">
                    <a class="btn btn-success<?= ($user['confirmed']) ? ' disabled' : '' ?>" href="<?= ($user['confirmed']) ? '#' : $this->Html->url(array('action' => 'confirm', $user['id'])) ?>"><?= ($user['confirmed']) ? $Lang->get('USER__EMAIL_CONFIRMED') : $Lang->get('USER__CONFIRM_EMAIL') ?></a>
                  </span>
                </div>
              </div>
            <?php } ?>

            <div class="form-group">
              <label><?= $Lang->get('PASSWORD') ?></label>
              <input name="password" class="form-control" type="password">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('RANK') ?></label>
              <select class="form-control" name="rank">
                <option value="" selected><?= ($user['rank'] == 4) ? $Lang->get('ADMINISTRATOR') : $user['rank'] ?></option>
                <?php foreach ($options_ranks as $key => $value) { ?>
                    <option value="<?= $key ?>"><?= $value ?></option>
                <?php } ?>
              </select>
            </div>

            <?php if($this->EyPlugin->isInstalled('Shop')) { ?>
              <div class="form-group">
                <label><?= $Lang->get('MONEY') ?></label>
                <input name="money" class="form-control" value="<?= $user['money'] ?>" type="text">
              </div>
            <?php } ?>

            <?php if($this->EyPlugin->isInstalled('Vote')) { ?>
              <div class="form-group">
                <label><?= $Lang->get('VOTE') ?></label>
               	<input name="vote" class="form-control" value="<?= $user['vote'] ?>" type="text">
              </div>
            <?php } ?>


            <div class="form-group">
              <label>IP</label>
              <input class="form-control" value="<?= $user['ip'] ?>" type="text" disabled="">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('CREATED_SIGNIN') ?></label>
              <input class="form-control" value="<?= $user['created'] ?>" type="text" disabled="">
            </div>

            <div class="pull-right">
              <a href="<?= $this->Html->url(array('controller' => 'user', 'action' => 'index', 'admin' => true)) ?>" class="btn btn-default"><?= $Lang->get('CANCEL') ?></a>
              <button class="btn btn-primary" type="submit"><?= $Lang->get('SUBMIT') ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('USER__HIS_HISTORIES') ?></h3>
        </div>
        <div class="box-body">

          <table class="table table-bordered dataTable">
            <thead>
              <tr>
                <th>ID</th>
                <th><?= $Lang->get('ACTION') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($user['History'] as $key => $v) { ?>
                <tr>
                  <td><?= $key ?></td>
                  <td><?= $v ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>

        </div>
      </div>
    </div>
  </div>
  <?= $Module->loadModules('admin_user_edit') ?>
</section>
