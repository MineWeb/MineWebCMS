<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get('CONFIG__GENERAL_PREFERENCES') ?></h3>
                </div>
                <div class="card-body">

                    <form method="post">

                        <div class="nav-tabs-custom">

                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link text-dark active" href="#tab_1" data-toggle="tab" aria-expanded="true"><?= $Lang->get('CONFIG__GENERAL_PREFERENCES') ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-dark" href="#tab_2" data-toggle="tab" aria-expanded="false"><?= $Lang->get('CONFIG__OTHER_PREFERENCES') ?></a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tab_1">

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__KEY_NAME') ?></label>
                                        <?= $this->Form->input(false, [
                                            'div' => false,
                                            'type' => 'text',
                                            'name' => 'name',
                                            'class' => 'form-control',
                                            'value' => $config['name']
                                        ]); ?>
                                    </div>

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__KEY_EMAIL') ?></label>
                                        <?= $this->Form->input(false, [
                                            'div' => false,
                                            'type' => 'text',
                                            'name' => 'email',
                                            'class' => 'form-control',
                                            'value' => $config['email']
                                        ]); ?>
                                    </div>

                                    <?php if ($shopIsInstalled) { ?>

                                        <div class="form-group">
                                            <label><?= $Lang->get('CONFIG__KEY_MONEY_NAME_SINGULAR') ?></label>
                                            <?= $this->Form->input(false, [
                                                'div' => false,
                                                'type' => 'text',
                                                'name' => 'money_name_singular',
                                                'class' => 'form-control',
                                                'value' => $config['money_name_singular']
                                            ]); ?>
                                        </div>

                                        <div class="form-group">
                                            <label><?= $Lang->get('CONFIG__KEY_MONEY_NAME_PLURAL') ?></label>
                                            <?= $this->Form->input(false, [
                                                'div' => false,
                                                'type' => 'text',
                                                'name' => 'money_name_plural',
                                                'class' => 'form-control',
                                                'value' => $config['money_name_plural']
                                            ]); ?>
                                        </div>

                                    <?php } ?>

                                    <?= $this->Html->script('bootstrap-4/plugins/bootstrap-select/bootstrap-select.min.js') ?>
                                    <?= $this->Html->css('bootstrap-4/plugins/bootstrap-select/bootstrap-select.min.css') ?>

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__KEY_LANG') ?></label>
                                        <div class="form-group">
                                            <?= $this->Form->input(false, [
                                                'div' => false,
                                                'data-live-search' => 'true',
                                                'name' => 'lang',
                                                'class' => 'selectpicker',
                                                'options' => $config['languages_available'],
                                                'selected' => $config['lang']
                                            ]); ?>
                                            <a href="<?= $this->Html->url(['action' => 'editLang']) ?>"
                                               class="btn btn-info"><?= $Lang->get('CONFIG__EDIT_LANG_FILE') ?></a>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__KEY_PASSWORDS_HASH') ?></label>
                                        <div class="form-group">
                                            <?= $this->Form->input(false, [
                                                'div' => false,
                                                'data-live-search' => 'true',
                                                'name' => 'passwords_hash',
                                                'class' => 'selectpicker',
                                                'options' => [
                                                    'sha256' => 'sha256',
                                                    'sha1' => 'sha1',
                                                    'sha384' => 'sha384',
                                                    'sha512' => 'sha512',
                                                    'blowfish' => 'bcrypt'
                                                ],
                                                'selected' => $config['passwords_hash']
                                            ]); ?>
                                        </div>
                                        <div class="form-group">
                                            <input type="hidden" name="passwords_salt"
                                                   value="<?= $config['passwords_salt'] ?>">
                                            <div class="checkbox">
                                                <input name="passwords_salt_checkbox"
                                                       type="checkbox" <?= $config['passwords_salt'] == '1' ? 'checked' : '' ?>>
                                                <label><?= $Lang->get('CONFIG__KEY_PASSWORDS_SALT') ?></label>
                                            </div>
                                        </div>
                                        <script type="text/javascript">
                                            $('input[name="passwords_salt_checkbox"]').on('change', function (e) {
                                                $('input[name="passwords_salt').val($('input[name="passwords_salt_checkbox"]:checked').length > 0 ? '1' : '0')
                                            })
                                        </script>
                                        <small class="text-danger"><?= $Lang->get('CONFIG__KEY_PASSWORDS_ADVERTISSEMENT') ?></small>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__CHECK_UUID') ?></label>
                                        <div class="form-group">
                                            <input type="hidden" name="check_uuid"
                                                   value="<?= $config['check_uuid'] ?>">
                                            <div class="checkbox">
                                                <input name="check_uuid_checkbox"
                                                       type="checkbox" <?= $config['check_uuid'] == '1' ? 'checked' : '' ?>>
                                                <label><?= $Lang->get('CONFIG__CHECK_UUID_CHANGE') ?></label>
                                            </div>
                                        </div>
                                        <script type="text/javascript">
                                            $('input[name="check_uuid_checkbox"]').on('change', function (e) {
                                                $('input[name="check_uuid').val($('input[name="check_uuid_checkbox"]:checked').length > 0 ? '1' : '0')
                                            })
                                        </script>
                                    </div>

                                    <hr>
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label><?= $Lang->get('CONFIG__CONDITION_TITLE') ?></label>
                                            <?= $this->Form->input(false, [
                                                'div' => false,
                                                'type' => 'text',
                                                'name' => 'condition',
                                                'class' => 'form-control',
                                                'value' => $config['condition']
                                            ]); ?>
                                        </div>
                                        <small class="text-danger"><?= $Lang->get('CONFIG__CONDITION') ?></small>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__KEY_SESSION_TYPE') ?></label>
                                        <div class="form-group">
                                            <?= $this->Form->input(false, [
                                                'div' => false,
                                                'data-live-search' => 'true',
                                                'name' => 'session_type',
                                                'class' => 'selectpicker',
                                                'options' => [
                                                    'php' => $Lang->get('CONFIG__KEY_SESSION_TYPE_PHP'),
                                                    'database' => $Lang->get('CONFIG__KEY_SESSION_TYPE_DB')
                                                ],
                                                'selected' => (!$config['session_type']) ? 'php' : $config['session_type']
                                            ]); ?>
                                        </div>
                                        <small class="text-info"><?= $Lang->get('CONFIG__KEY_SESSION_TYPE_INFO') ?></small>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__KEY_VERSION') ?></label>
                                        <input type="text" value="<?= file_get_contents(ROOT . DS . 'VERSION') ?>"
                                               class="form-control disabled" disabled>
                                    </div>

                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane fade" id="tab_2">

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__KEY_MEMBER_PAGE_TYPE') ?></label>
                                        <br>
                                        <small><?= $Lang->get('CONFIG__KEY_MEMBER_PAGE_TYPE_EXPLAIN') ?></small>
                                        <div class="radio">
                                            <input type="radio" name="member_page_type"
                                                   value="0" <?= ($config['member_page_type'] == '0') ? 'checked=""' : '' ?>>
                                            <label><?= $Lang->get('CONFIG__KEY_MEMBER_PAGE_TYPE_DEFAULT') ?></label>
                                        </div>
                                        <div class="radio">
                                            <input type="radio" name="member_page_type"
                                                   value="1" <?= ($config['member_page_type'] == '1') ? 'checked=""' : '' ?>>
                                            <label><?= $Lang->get('CONFIG__KEY_MEMBER_PAGE_TYPE_SEARCH') ?></label>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__KEY_CONFIRM_MAIL_SIGNUP') ?></label>
                                        <br>
                                        <small><?= $Lang->get('CONFIG__CONFIRM_MAIL_SIGNUP_EXPLAIN') ?></small>
                                        <div class="radio">
                                            <input type="radio" name="confirm_mail_signup"
                                                   value="1" <?= ($config['confirm_mail_signup'] == '1') ? 'checked=""' : '' ?>>
                                            <label><?= $Lang->get('GLOBAL__ENABLE') ?></label>
                                        </div>
                                        <div class="radio">
                                            <input type="radio" name="confirm_mail_signup"
                                                   value="0" <?= ($config['confirm_mail_signup'] == '0') ? 'checked=""' : '' ?>>
                                            <label><?= $Lang->get('GLOBAL__DISABLE') ?></label>
                                        </div>
                                    </div>

                                    <script type="text/javascript">
                                        $('input[name="confirm_mail_signup"]').on('change', function (e) {
                                            if ($(this).val() == '1') {
                                                $('#confirm_mail_signup').slideDown(250);
                                            } else {
                                                $('#confirm_mail_signup').slideUp(250);
                                            }
                                        });
                                    </script>

                                    <div id="confirm_mail_signup"
                                         style="display:<?= ($config['confirm_mail_signup'] == '1') ? 'block' : 'none' ?>;">
                                        <div class="form-group">
                                            <label><?= $Lang->get('CONFIG__KEY_CONFIRM_MAIL_SIGNUP_BLOCK') ?></label>
                                            <div class="radio">
                                                <input type="radio" name="confirm_mail_signup_block"
                                                       value="1" <?= ($config['confirm_mail_signup_block'] == '1') ? 'checked=""' : '' ?>>
                                                <label><?= $Lang->get('GLOBAL__ENABLE') ?></label>
                                            </div>
                                            <div class="radio">
                                                <input type="radio" name="confirm_mail_signup_block"
                                                       value="0" <?= ($config['confirm_mail_signup_block'] == '0') ? 'checked=""' : '' ?>>
                                                <label><?= $Lang->get('GLOBAL__DISABLE') ?></label>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__KEY_CAPTCHA_TYPE') ?></label>
                                        <div class="radio">
                                            <input type="radio" name="captcha_type"
                                                   value="1" <?= ($config['captcha_type'] == '1') ? 'checked=""' : '' ?>>
                                            <label><?= $Lang->get('GLOBAL__TYPE_NORMAL') ?></label>
                                        </div>
                                        <div class="radio">
                                            <input type="radio" name="captcha_type"
                                                   value="2" <?= ($config['captcha_type'] == '2') ? 'checked=""' : '' ?>>
                                            <label><?= $Lang->get('CONFIG__TYPE_CAPTCHA_GOOGLE') ?></label>
                                        </div>

                                        <div class="radio">
                                            <input type="radio" name="captcha_type"
                                                   value="3" <?= ($config['captcha_type'] == '3') ? 'checked=""' : '' ?>>
                                            <label><?= $Lang->get('CONFIG__TYPE_CAPTCHA_HCAPTCHA') ?></label>
                                        </div>
                                    </div>

                                    <script type="text/javascript">
                                        $('input[name="captcha_type"]').on('change', function (e) {
                                            if ($(this).val() == '2' || $(this).val() == '3') {
                                                $('#captcha').slideDown(250);
                                            } else {
                                                $('#captcha').slideUp(250);
                                            }
                                        });
                                    </script>

                                    <div id="captcha"
                                         style="display:<?= ($config['captcha_type'] == '2' || $config['captcha_type'] == '3') ? 'block' : 'none' ?>;">
                                        <div class="form-group">
                                            <label><?= $Lang->get('CONFIG__KEY_CAPTCHA_SITEKEY') ?></label>
                                            <?= $this->Form->input(false, [
                                                'div' => false,
                                                'type' => 'text',
                                                'name' => 'captcha_sitekey',
                                                'class' => 'form-control',
                                                'value' => $config['captcha_sitekey'],
                                            ]); ?>
                                        </div>

                                        <div class="form-group">
                                            <label><?= $Lang->get('CONFIG__KEY_CAPTCHA_SECRET') ?></label>
                                            <?= $this->Form->input(false, [
                                                'div' => false,
                                                'type' => 'text',
                                                'name' => 'captcha_secret',
                                                'class' => 'form-control',
                                                'value' => $config['captcha_secret'],
                                            ]); ?>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__KEY_GOOGLE_ANALYTICS') ?></label>
                                        <?= $this->Form->input(false, [
                                            'div' => false,
                                            'type' => 'text',
                                            'name' => 'google_analytics',
                                            'class' => 'form-control',
                                            'value' => $config['google_analytics'],
                                            'maxlength' => '15'
                                        ]); ?>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__KEY_END_LAYOUT_COE') ?></label>
                                        <?= $this->Form->textarea(false, [
                                            'div' => false,
                                            'rows' => '5',
                                            'type' => 'text',
                                            'name' => 'end_layout_code',
                                            'class' => 'form-control',
                                            'value' => $config['end_layout_code']
                                        ]); ?>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__KEY_EMAIL_SEND_TYPE') ?></label>
                                        <div class="radio">
                                            <input type="radio" name="email_send_type"
                                                   value="1" <?= ($config['email_send_type'] == '1') ? 'checked=""' : '' ?>>
                                            <label><?= $Lang->get('GLOBAL__TYPE_NORMAL') ?></label>
                                        </div>
                                        <div class="radio">
                                            <input type="radio" name="email_send_type"
                                                   value="2" <?= ($config['email_send_type'] == '2') ? 'checked=""' : '' ?>>
                                            <label><?= $Lang->get('SMTP') ?></label>
                                        </div>
                                    </div>

                                    <script type="text/javascript">
                                        $('input[name="email_send_type"]').on('change', function (e) {
                                            if ($(this).val() == '2') {
                                                $('#smtp-config').slideDown(250);
                                            } else {
                                                $('#smtp-config').slideUp(250);
                                            }
                                        });
                                    </script>

                                    <div id="smtp-config"
                                         style="display:<?= ($config['email_send_type'] == '1') ? 'none' : 'block' ?>;">

                                        <div class="form-group">
                                            <label><?= $Lang->get('CONFIG__KEY_SMTP_HOST') ?></label>
                                            <?= $this->Form->input(false, [
                                                'div' => false,
                                                'type' => 'text',
                                                'name' => 'smtpHost',
                                                'class' => 'form-control',
                                                'value' => $config['smtpHost'],
                                                'autocomplete' => 'off'
                                            ]); ?>
                                        </div>

                                        <div class="form-group">
                                            <label><?= $Lang->get('CONFIG__KEY_SMTP_USERNAME') ?></label>
                                            <?= $this->Form->input(false, [
                                                'div' => false,
                                                'type' => 'text',
                                                'name' => 'smtpUsername',
                                                'class' => 'form-control',
                                                'value' => $config['smtpUsername'],
                                                'autocomplete' => 'off'
                                            ]); ?>
                                        </div>

                                        <div class="form-group">
                                            <label><?= $Lang->get('CONFIG__KEY_SMTP_PORT') ?></label>
                                            <?= $this->Form->input(false, [
                                                'div' => false,
                                                'type' => 'text',
                                                'name' => 'smtpPort',
                                                'class' => 'form-control',
                                                'value' => $config['smtpPort'],
                                                'autocomplete' => 'off'
                                            ]); ?>
                                        </div>

                                        <div class="form-group">
                                            <label><?= $Lang->get('CONFIG__KEY_SMTP_PASSWORD') ?></label>
                                            <?= $this->Form->input(false, [
                                                'div' => false,
                                                'type' => 'password',
                                                'name' => 'smtpPassword',
                                                'class' => 'form-control',
                                                'value' => $config['smtpPassword'],
                                                'autocomplete' => 'off'
                                            ]); ?>
                                        </div>

                                    </div>

                                </div>
                                <!-- /.tab-pane -->
                            </div>
                            <!-- /.tab-content -->
                        </div>

                        <input type="hidden" name="data[_Token][key]" value="<?= $csrfToken ?>">

                        <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
                        <a href="<?= $this->Html->url(['controller' => '', 'action' => '', 'admin' => true]) ?>"
                           type="button" class="btn btn-default"><?= $Lang->get('GLOBAL__CANCEL') ?></a>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>
