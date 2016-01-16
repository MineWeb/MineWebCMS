<!-- Modal (connexion ...) -->
<div class="modal modal-medium fade" id="login" tabindex="-1" role="dialog" aria-labelledby="loginLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= $Lang->get('CLOSE') ?></span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $Lang->get('LOGIN_ACTION') ?></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" data-ajax="true" action="<?= $this->Html->url(array('plugin' => null, 'controller' => 'user', 'action' => 'ajax_login')) ?>" data-redirect-url="?">
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label"><?= $Lang->get('PSEUDO') ?></label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="pseudo" id="inputEmail3" placeholder="<?= $Lang->get('ENTER_PSEUDO') ?>">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?= $Lang->get('PASSWORD') ?></label>
            <div class="col-sm-10">
              <input type="password" class="form-control" name="password" placeholder="<?= $Lang->get('ENTER_PASSWORD') ?>">
            </div>
          </div>
          <center><a data-dismiss="modal" href="#" data-toggle="modal" data-target="#lostpasswd"><?= $Lang->get('FORGOT_PASSWORD') ?></a></center>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= $Lang->get('CLOSE') ?></button>
        <button type="submit" class="btn btn-primary"><?= $Lang->get('LOGIN') ?></button>
      </form>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-medium fade" id="lostpasswd" tabindex="-1" role="dialog" aria-labelledby="lostpasswdLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= $Lang->get('CLOSE') ?></span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $Lang->get('FORGOT_PASSWORD') ?></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" data-ajax="true" action="<?= $this->Html->url(array('plugin' => null, 'controller' => 'user', 'action' => 'ajax_lostpasswd')) ?>">
          <div class="form-group">
            <label class="col-sm-2 control-label"><?= $Lang->get('EMAIL') ?></label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="email" placeholder="<?= $Lang->get('ENTER_EMAIL') ?>">
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= $Lang->get('CLOSE') ?></button>
        <button type="submit" class="btn btn-primary"><?= $Lang->get('SEND_EMAIL') ?></button>
      </form>
      </div>
    </div>
  </div>
</div>

<?php if(!empty($resetpsswd)) { ?>
  <div class="modal modal-medium fade" id="lostpasswd2" tabindex="-1" role="dialog" aria-labelledby="lostpasswd2Label" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= $Lang->get('CLOSE') ?></span></button>
          <h4 class="modal-title" id="myModalLabel"><?= $Lang->get('FORGOT_PASSWORD') ?></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" method="POST" data-ajax="true" action="<?= $this->Html->url(array('plugin' => null, 'controller' => 'user', 'action' => 'ajax_resetpasswd')) ?>" data-redirect-url="?">
            <input type="hidden" name="pseudo" value="<?= $resetpsswd['pseudo'] ?>">
            <input type="hidden" name="email" value="<?= $resetpsswd['email'] ?>">
            <div class="form-group">
              <label  class="col-sm-2 control-label"><?= $Lang->get('PASSWORD') ?></label>
              <div class="col-sm-10">
                <input type="password" class="form-control" name="password" placeholder="<?= $Lang->get('ENTER_PASSWORD') ?>">
              </div>
            </div>
            <div class="form-group">
              <label  class="col-sm-2 control-label"><?= $Lang->get('PASSWORD_CONFIRMATION') ?></label>
              <div class="col-sm-10">
                <input type="password" class="form-control" name="password2" placeholder="<?= $Lang->get('ENTER_PASSWORD_CONFIRMATION') ?>">
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><?= $Lang->get('CLOSE') ?></button>
          <button type="submit" class="btn btn-success"><?= $Lang->get('SAVE') ?></button>
        </form>
        </div>
      </div>
    </div>
  </div>
<?php } ?>

<div class="modal fade" id="register" tabindex="-1" role="dialog" aria-labelledby="registerLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= $Lang->get('CLOSE') ?></span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $Lang->get('REGISTER_ACTION') ?></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" data-ajax="true" action="<?= $this->Html->url(array('plugin' => null, 'controller' => 'user', 'action' => 'ajax_register')) ?>" data-redirect-url="?">
          <div class="form-group">
            <label  class="col-sm-2 control-label"><?= $Lang->get('PSEUDO') ?></label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="pseudo" placeholder="<?= $Lang->get('ENTER_PSEUDO') ?>">
            </div>
          </div>
          <div class="form-group">
            <label  class="col-sm-2 control-label"><?= $Lang->get('PASSWORD') ?></label>
            <div class="col-sm-10">
              <input type="password" class="form-control" name="password" placeholder="<?= $Lang->get('ENTER_PASSWORD') ?>">
            </div>
          </div>
          <div class="form-group">
            <label  class="col-sm-2 control-label"><?= $Lang->get('PASSWORD_CONFIRMATION') ?></label>
            <div class="col-sm-10">
              <input type="password" class="form-control" name="password_confirmation" placeholder="<?= $Lang->get('ENTER_PASSWORD_CONFIRMATION') ?>">
            </div>
          </div>
          <div class="form-group">
            <label  class="col-sm-2 control-label"><?= $Lang->get('EMAIL') ?></label>
            <div class="col-sm-10">
              <input type="email" class="form-control" name="email" placeholder="<?= $Lang->get('ENTER_EMAIL') ?>">
            </div>
          </div>
          <?php if($reCaptcha['type'] == "google") { ?>
            <script src='https://www.google.com/recaptcha/api.js'></script>
            <div class="form-group">
              <label class="col-sm-2 control-label"><?= $Lang->get('CAPTCHA') ?></label>
              <div class="col-sm-10">
                <div class="g-recaptcha" data-sitekey="<?= $reCaptcha['siteKey'] ?>"></div>
              </div>
            </div>
          <?php } else { ?>
            <div class="form-group">
              <label for="inputPassword3" class="col-sm-2 control-label"><?= $Lang->get('CAPTCHA') ?></label>
              <div class="col-sm-10">
                <?php
                  echo $this->Html->image(array('controller' => 'user', 'action' => 'get_captcha', 'plugin' => false), array('plugin' => false, 'id' => 'captcha_image'));
                  echo $this->Html->link($Lang->get('RELOAD_CAPTCHA'), 'javascript:void(0);',array('id' => 'reload'));
                ?>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label"></label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="captcha" id="inputPassword3" placeholder="<?= $Lang->get('ENTER_CAPTCHA') ?>">
              </div>
            </div>
          <?php } ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= $Lang->get('CLOSE') ?></button>
        <button type="submit" class="btn btn-primary"><?= $Lang->get('REGISTER') ?></button>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    <?php if(!empty($resetpsswd)) { ?>
      $('#lostpasswd2').modal('show');
    <?php } ?>
  });
</script>
