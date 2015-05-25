<?php   ?>
    <!-- Modal (connexion ...) -->
    <div class="modal modal-medium fade" id="login" tabindex="-1" role="dialog" aria-labelledby="loginLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= $Lang->get('CLOSE') ?></span></button>
            <h4 class="modal-title" id="myModalLabel"><?= $Lang->get('LOGIN_ACTION') ?></h4>
          </div>
          <div class="modal-body">
            <div id="msg-on-login"></div>
            <form class="form-horizontal" role="form" method="POST" id="login_form">
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?= $Lang->get('PSEUDO') ?></label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="pseudo" id="inputEmail3" placeholder="<?= $Lang->get('ENTER_PSEUDO') ?>">
                </div>
              </div>
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label"><?= $Lang->get('PASSWORD') ?></label>
                <div class="col-sm-10">
                  <input type="password" class="form-control" name="password" id="inputPassword3" placeholder="<?= $Lang->get('ENTER_PASSWORD') ?>">
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
            <div id="msg-lostpasswd"></div>
            <form class="form-horizontal" role="form" method="POST" id="lostpasswd_form">
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?= $Lang->get('EMAIL') ?></label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="email" id="inputEmail3" placeholder="<?= $Lang->get('ENTER_EMAIL') ?>">
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
              <div id="msg-resetpasswd"></div>
              <form class="form-horizontal" role="form" method="POST" id="resetpassword_form">
                <input type="hidden" name="pseudo" value="<?= $resetpsswd['pseudo'] ?>">
                <input type="hidden" name="email" value="<?= $resetpsswd['email'] ?>">
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label"><?= $Lang->get('PASSWORD') ?></label>
                  <div class="col-sm-10">
                    <input type="password" class="form-control" name="password" id="inputEmail3" placeholder="<?= $Lang->get('ENTER_PASSWORD') ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label"><?= $Lang->get('PASSWORD_CONFIRMATION') ?></label>
                  <div class="col-sm-10">
                    <input type="password" class="form-control" name="password2" id="inputEmail3" placeholder="<?= $Lang->get('ENTER_PASSWORD_CONFIRMATION') ?>">
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
            <div id="msg-on-register"></div>
            <form class="form-horizontal" role="form" method="POST" id="register_form">
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label"><?= $Lang->get('PSEUDO') ?></label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="pseudo" id="inputEmail3" placeholder="<?= $Lang->get('ENTER_PSEUDO') ?>">
                </div>
              </div>
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label"><?= $Lang->get('PASSWORD') ?></label>
                <div class="col-sm-10">
                  <input type="password" class="form-control" name="password" id="inputPassword3" placeholder="<?= $Lang->get('ENTER_PASSWORD') ?>">
                </div>
              </div>
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label"><?= $Lang->get('PASSWORD_CONFIRMATION') ?></label>
                <div class="col-sm-10">
                  <input type="password" class="form-control" name="password-confirmation" id="inputPassword3" placeholder="<?= $Lang->get('ENTER_PASSWORD_CONFIRMATION') ?>">
                </div>
              </div>
              <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label"><?= $Lang->get('EMAIL') ?></label>
                <div class="col-sm-10">
                  <input type="email" class="form-control" name="email" id="inputPassword3" placeholder="<?= $Lang->get('ENTER_EMAIL') ?>">
                </div>
              </div>
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
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= $Lang->get('CLOSE') ?></button>
            <button type="submit" class="btn btn-primary"><?= $Lang->get('REGISTER') ?></button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- ### --> 
    <!-- JS -->


    <script type="text/javascript">

        $("#register_form").submit(function( event ) {
            $('#msg-on-register').hide().html('<div class="alert alert-success" role="alert"><p><?= $Lang->get('ON_REGISTER') ?></p></div>').fadeIn(1500);
            var $form = $( this );
            var pseudo = $form.find("input[name='pseudo']").val();
            var password = $form.find("input[name='password']").val();
            var password_confirmation = $form.find("input[name='password-confirmation']").val();
            var email = $form.find("input[name='email']").val();
            var captcha = $form.find("input[name='captcha']").val();
            $.post("<?= $this->Html->url(array('plugin' => null, 'controller' => 'user', 'action' => 'ajax_register')) ?>", { pseudo : pseudo, password : password, password_confirmation : password_confirmation, email : email, captcha : captcha }, function(data) {
              if(data == 'true') {
                $('#msg-on-register').hide().html('<div class="alert alert-success" role="alert"><p><b><?= $Lang->get('SUCCESS') ?> : </b><?= $Lang->get('SUCCESS_REGISTER') ?></p></div>').fadeIn(1500);
                window.setTimeout(location.reload(), 2000);
              } else {
                $('#msg-on-register').hide().html('<div class="alert alert-danger" role="alert"><p><b><?= $Lang->get('ERROR') ?> : </b>'+data+'</p></div>').fadeIn(1500);
              }
            });
            return false;
        });

          $("#login_form").submit(function( event ) {
            $('#msg-on-login').hide().html('<div class="alert alert-success" role="alert"><p><?= $Lang->get('ON_LOGIN') ?></p></div>').fadeIn(1500);
            var $form = $( this );
            var pseudo = $form.find("input[name='pseudo']").val();
            var password = $form.find("input[name='password']").val();
            $.post("<?= $this->Html->url(array('plugin' => null, 'controller' => 'user', 'action' => 'ajax_login')) ?>", { pseudo : pseudo, password : password }, function(data) {
              if(data == 'true') {
                $('#msg-on-login').hide().html('<div class="alert alert-success" role="alert"><p><b><?= $Lang->get('SUCCESS') ?> : </b><?= $Lang->get('SUCCESS_LOGIN') ?></p></div>').fadeIn(1500);
                window.setTimeout(location.reload(), 2000);
              } else {
                $('#msg-on-login').hide().html('<div class="alert alert-danger" role="alert"><p><b><?= $Lang->get('ERROR') ?> : </b>'+data+'</p></div>').fadeIn(1500);
              }
            });
            return false;
        });

        $("#lostpasswd_form").submit(function( event ) {
            $('#msg-lostpasswd').hide().html('<div class="alert alert-info" role="alert"><p><?= $Lang->get('LOADING') ?></p></div>').fadeIn(1500);
            var $form = $( this );
            var email = $form.find("input[name='email']").val();
            $.post("<?= $this->Html->url(array('plugin' => null, 'controller' => 'user', 'action' => 'ajax_lostpasswd')) ?>", { email : email }, function(data) {
              if(data == 'true') {
                $('#msg-lostpasswd').hide().html('<div class="alert alert-success" role="alert"><p><b><?= $Lang->get('SUCCESS') ?> : </b><?= $Lang->get('SUCCESS_SEND_RESET_MAIL') ?></p></div>').fadeIn(1500);
              } else {
                $('#msg-lostpasswd').hide().html('<div class="alert alert-danger" role="alert"><p><b><?= $Lang->get('ERROR') ?> : </b>'+data+'</p></div>').fadeIn(1500);
              }
            });
            return false;
        });

        $("#resetpassword_form").submit(function( event ) {
            $('#msg-resetpasswd').hide().html('<div class="alert alert-info" role="alert"><p><?= $Lang->get('LOADING') ?></p></div>').fadeIn(1500);
            var $form = $( this );
            var email = $form.find("input[name='email']").val();
            var password = $form.find("input[name='password']").val();
            var password2 = $form.find("input[name='password2']").val();
            $.post("<?= $this->Html->url(array('plugin' => null, 'controller' => 'user', 'action' => 'ajax_resetpasswd')) ?>", { email : email, password : password, password2 : password2 }, function(data) {
              if(data == 'true') {
                $('#msg-resetpasswd').hide().html('<div class="alert alert-success" role="alert"><p><b><?= $Lang->get('SUCCESS') ?> : </b><?= $Lang->get('SUCCESS_RESET_PASSWORD') ?></p></div>').fadeIn(1500);
                window.setTimeout(location.reload('/'), 5000);
              } else {
                $('#msg-resetpasswd').hide().html('<div class="alert alert-danger" role="alert"><p><b><?= $Lang->get('ERROR') ?> : </b>'+data+'</p></div>').fadeIn(1500);
              }
            });
            return false;
        });

      $(document).ready(function(){

        <?php if(!empty($resetpsswd)) { ?>
          $('#lostpasswd2').modal('show');
        <?php } ?>
        
         $('#reload').click(function() {
                var captcha = $("#captcha_image");
                 captcha.attr('src', captcha.attr('src')+'?'+Math.random());
                return false;
            });
        
      });
    </script>