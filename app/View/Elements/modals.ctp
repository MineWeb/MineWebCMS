<!-- Modal (connexion ...) -->
<div class="modal modal-medium fade" id="login" tabindex="-1" role="dialog" aria-labelledby="loginLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= $Lang->get('GLOBAL__CLOSE') ?></span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $Lang->get('USER__LOGIN') ?></h4>
      </div>
        <form id="login-before-two-factor-auth" method="POST" data-ajax="true" action="<?= $this->Html->url(array('plugin' => false, 'admin' => false, 'controller' => 'user', 'action' => 'ajax_login')) ?>" data-callback-function="afterLogin">
            <div class="modal-body">
              <div class="ajax-msg"></div>
              <div class="form-group">
                  <h5><?= $Lang->get('USER__USERNAME') ?></h5>
                  <input type="text" class="form-control" name="pseudo" id="inputEmail3" placeholder="<?= $Lang->get('USER__USERNAME_LABEL') ?>">
              </div>

              <div class="form-group">
                  <h5><?= $Lang->get('USER__PASSWORD') ?></h5>
                  <input type="password" class="form-control" name="password" placeholder="<?= $Lang->get('USER__PASSWORD_LABEL') ?>">
              </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember_me">
                                <?= $Lang->get('USER__REMEMBER_ME') ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center"><h5><a data-dismiss="modal" href="#" data-toggle="modal"
                                                    data-target="#lostpasswd"><?= $Lang->
                        get('USER__PASSWORD_FORGOT_LABEL') ?></a></h5></div>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-block"><?= $Lang->get('USER__LOGIN') ?></button>
          </div>
        </form>
        <form id="login-two-factor-auth" style="display:none;" method="POST" data-ajax="true" action="<?= $this->Html->url(array('plugin' => null, 'admin' => false, 'controller' => 'Authentification', 'action' => 'validLogin')) ?>" data-redirect-url="?">
            <div class="modal-body">
                <div class="ajax-msg"></div>
                <input type="checkbox" style="display: none;" name="remember_me">
                <div class="form-group">
                    <h5><?= $Lang->get('USER__LOGIN_CODE') ?></h5>
                    <input type="text" class="form-control" name="code" placeholder="<?= $Lang->get('USER__LOGIN_CODE') ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-block"><?= $Lang->get('USER__LOGIN') ?></button>
            </div>
        </form>
    </div>
  </div>
</div>
<script type="text/javascript">
    function afterLogin(req, res) {
        if (res['two-factor-auth'] === undefined)
            return window.location = '?t_' + Date.now()
        $('#login-two-factor-auth input[name="remember_me"]').attr('checked', $('#login-before-two-factor-auth input[name="remember_me"]').is(':checked'))
        $('#login-before-two-factor-auth').slideUp(150)
        $('#login-two-factor-auth').slideDown(150)
    }
</script>
<div class="modal modal-medium fade" id="lostpasswd" tabindex="-1" role="dialog" aria-labelledby="lostpasswdLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= $Lang->get('GLOBAL__CLOSE') ?></span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $Lang->get('USER__PASSWORD_FORGOT_LABEL') ?></h4>
      </div>
      <form method="POST" data-ajax="true" action="<?= $this->Html->url(array('plugin' => null, 'admin' => false, 'controller' => 'user', 'action' => 'ajax_lostpasswd')) ?>">
          <div class="modal-body">
              <div class="ajax-msg"></div>
              <div class="form-group">
                  <h5><?= $Lang->get('USER__EMAIL') ?> </h5>
                  <input type="text" class="form-control" name="email" placeholder="<?= $Lang->get('USER__EMAIL_LABEL') ?>">
              </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-block"><?= $Lang->get('USER__PASSWORD_FORGOT_SEND_MAIL') ?></button>
          </div>
      </form>
    </div>
  </div>
</div>

<?php if(!empty($resetpsswd)) { ?>
  <div class="modal modal-medium fade" id="lostpasswd2" tabindex="-1" role="dialog" aria-labelledby="lostpasswd2Label" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= $Lang->get('GLOBAL__CLOSE') ?></span></button>
          <h4 class="modal-title" id="myModalLabel"><?= $Lang->get('USER__PASSWORD_FORGOT_LABEL') ?></h4>
        </div>
        <form method="POST" data-ajax="true" action="<?= $this->Html->url(array('plugin' => null, 'admin' => false, 'controller' => 'user', 'action' => 'ajax_resetpasswd')) ?>" data-redirect-url="?">
            <div class="modal-body">
                <div class="ajax-msg"></div>
                <input type="hidden" name="key" value="<?= $resetpsswd['key'] ?>">
                <input type="hidden" name="email" value="<?= $resetpsswd['email'] ?>">
                <div class="form-group">
                    <h5><?= $Lang->get('USER__PASSWORD') ?></h5>
                    <input type="password" class="form-control" name="password" placeholder="<?= $Lang->get('USER__PASSWORD_LABEL') ?>">
                </div>
                <div class="form-group">
                    <h5><?= $Lang->get('USER__PASSWORD_CONFIRM') ?></h5>
                    <input type="password" class="form-control" name="password2" placeholder="<?= $Lang->get('USER__PASSWORD_CONFIRM_LABEL') ?>">
                </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success btn-block"><?= $Lang->get('GLOBAL__SAVE') ?></button>
            </div>
        </form>
      </div>
    </div>
  </div>
<?php } ?>

<div class="modal fade" id="register" tabindex="-1" role="dialog" aria-labelledby="registerLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= $Lang->get('GLOBAL__CLOSE') ?></span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $Lang->get('USER__REGISTER') ?></h4>
      </div>
      <form method="POST" data-ajax="true" action="<?= $this->Html->url(array('plugin' => null, 'admin' => false, 'controller' => 'user', 'action' => 'ajax_register')) ?>" data-redirect-url="?">
          <div class="modal-body">
              <div class="ajax-msg"></div>
              <div class="form-group">
                  <h5><?= $Lang->get('USER__USERNAME') ?></h5>
                  <input type="text" class="form-control" name="pseudo" placeholder="<?= $Lang->get('USER__USERNAME_LABEL') ?>">
              </div>
              <div class="form-group">
                  <h5><?= $Lang->get('USER__PASSWORD') ?></h5>
                  <input type="password" class="form-control" name="password" placeholder="<?= $Lang->get('USER__PASSWORD_LABEL') ?>">
              </div>
              <div class="form-group">
                  <h5><?= $Lang->get('USER__PASSWORD_CONFIRM') ?></h5>
                  <input type="password" class="form-control" name="password_confirmation" placeholder="<?= $Lang->get('USER__PASSWORD_CONFIRM_LABEL') ?>">
              </div>
              <div class="form-group">
                  <h5><?= $Lang->get('USER__EMAIL') ?> </h5>
                  <input type="email" class="form-control" name="email" placeholder="<?= $Lang->get('USER__EMAIL_LABEL') ?>">
              </div>
              
              <?php if($reCaptcha['type'] == "google") { ?>
                <script src='https://www.google.com/recaptcha/api.js'></script>
                <div class="form-group">
                    <h5><?= $Lang->get('FORM__CAPTCHA') ?></h5>
                    <div class="g-recaptcha" data-sitekey="<?= $reCaptcha['siteKey'] ?>"></div>
                </div>
              <?php } else { ?>
              
                <div class="form-group">
                    <h5><?= $Lang->get('FORM__CAPTCHA') ?></h5>
                    <?php
                      echo $this->Html->image(array('controller' => 'user', 'action' => 'get_captcha', 'plugin' => false, 'admin' => false), array('plugin' => false, 'admin' => false, 'id' => 'captcha_image'));
                      echo $this->Html->link($Lang->get('FORM__RELOAD_CAPTCHA'), 'javascript:void(0);',array('id' => 'reload'));
                    ?>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="captcha" id="inputPassword3" placeholder="<?= $Lang->get('FORM__CAPTCHA_LABEL') ?>">
                </div>
              <?php } ?>
              <?php if (!empty($condition)) { ?>
              <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="condition">
                        <?=$Lang->get('USER__CONDITION_1')?> <a href="<?= $condition ?>"> <?= $Lang->get('USER__CONDITION_2')?></a>
                    </label>
                </div>
              </div>
             <?php } ?>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-block"><?= $Lang->get('USER__REGISTER') ?></button>
          </div>
      </form>
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

<?php if(isset($isConnected) && $isConnected) { ?>
  <div class="modal modal-medium fade" id="notifications_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= $Lang->get('GLOBAL__CLOSE') ?></span></button>
          <h4 class="modal-title"><?= $Lang->get('NOTIFICATIONS__LIST') ?></h4>
        </div>
        <div class="modal-body" style="padding:0;">

          <div class="notifications-list"></div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-block" onclick="notification.markAllAsSeen()" data-dismiss="modal"><?= $Lang->get('NOTIFICATIONS__MARK_ALL_AS_SEEN') ?></button>
          <button type="submit" class="btn btn-danger btn-block" onclick="notification.clearAll()" data-dismiss="modal"><?= $Lang->get('NOTIFICATIONS__CLEAR_ALL') ?></button>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
