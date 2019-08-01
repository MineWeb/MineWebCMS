<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= $Lang->get('CONFIG__GENERAL_PREFERENCES') ?></h3>
                </div>
                <div class="box-body">

                    <form method="post">

                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab_1" data-toggle="tab"
                                                      aria-expanded="true"><?= $Lang->get('CONFIG__GENERAL_PREFERENCES') ?></a>
                                </li>
                                <li class=""><a href="#tab_2" data-toggle="tab"
                                                aria-expanded="false"><?= $Lang->get('CONFIG__SOCIAL_PREFERENCES') ?></a>
                                </li>
                                <li class=""><a href="#tab_3" data-toggle="tab"
                                                aria-expanded="false"><?= $Lang->get('CONFIG__OTHER_PREFERENCES') ?></a>
                                </li>
                                <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a>
                                </li>
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

                                    <?php if ($shopIsInstalled) { ?>

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

                                    <?= $this->Html->script('admin/bootstrap-select') ?>
                                    <?= $this->Html->css('bootstrap-select.min.css') ?>

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__KEY_LANG') ?></label>
                                        <div class="form-group">
                                            <?= $this->Form->input(false, array(
                                                'div' => false,
                                                'data-live-search' => 'true',
                                                'name' => 'lang',
                                                'class' => 'selectpicker',
                                                'options' => $config['languages_available'],
                                                'selected' => $config['lang']
                                            )); ?>
                                            <a href="<?= $this->Html->url(array('action' => 'editLang')) ?>"
                                               class="btn btn-info"><?= $Lang->get('CONFIG__EDIT_LANG_FILE') ?></a>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__KEY_PASSWORDS_HASH') ?></label>
                                        <div class="form-group">
                                            <?= $this->Form->input(false, array(
                                                'div' => false,
                                                'data-live-search' => 'true',
                                                'name' => 'passwords_hash',
                                                'class' => 'selectpicker',
                                                'options' => array(
                                                    'sha256' => 'sha256',
                                                    'sha1' => 'sha1',
                                                    'sha386' => 'sha386',
                                                    'sha512' => 'sha512'
                                                ),
                                                'selected' => $config['passwords_hash']
                                            )); ?>
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

                                    <hr>
                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__CONDITION_TITLE') ?></label>
                                        <div class="form-group">
                                            <?= $this->Form->input(false, array(
                                                'div' => false,
                                                'type' => 'text',
                                                'name' => 'condition',
                                                'class' => 'form-control',
                                                'value' => $config['condition']
                                            )); ?>
                                        </div>
                                        <small class="text-danger"><?= $Lang->get('CONFIG__CONDITION') ?></small>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__KEY_SESSION_TYPE') ?></label>
                                        <div class="form-group">
                                            <?= $this->Form->input(false, array(
                                                'div' => false,
                                                'data-live-search' => 'true',
                                                'name' => 'session_type',
                                                'class' => 'selectpicker',
                                                'options' => array(
                                                    'php' => $Lang->get('CONFIG__KEY_SESSION_TYPE_PHP'),
                                                    'database' => $Lang->get('CONFIG__KEY_SESSION_TYPE_DB')
                                                ),
                                                'selected' => (!$config['session_type']) ? 'php' : $config['session_type']
                                            )); ?>
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

                                    <button class="btn btn-success"
                                            id="addSocialBtnContainer"><?= $Lang->get('CONFIG__ADD_SOCIAL_BTN') ?></button>
                                    <?= $this->Html->script('admin/colorpicker') ?>

                                    <div class="addBtnContainer">
                                        <?php
                                        $i = 0;
                                        if (!empty($social_buttons)) {
                                            foreach ($social_buttons as $key => $value) {
                                                $i++;

                                                echo '<div id="btn-' . $i . '">';

                                                echo '<hr>';

                                                echo '<input type="hidden" value="' . $value['SocialButton']['id'] . '" name="social_btn_edited[' . $i . '][id]">';

                                                echo '<div class="form-group">';
                                                echo '<label>' . $Lang->get('CONFIG__TITLE_SOCIAL_BTN') . '</label>';
                                                echo '<input type="text" value="' . htmlentities($value['SocialButton']['title']) . '" name="social_btn_edited[' . $i . '][title]" class="form-control">';
                                                echo '</div>';

                                                echo '<div class="form-group">';
                                                echo '<label>' . $Lang->get('CONFIG__IMG_SOCIAL_BTN') . '</label>';
                                                echo '<input type="text" value="' . $value['SocialButton']['img'] . '" name="social_btn_edited[' . $i . '][img]" class="form-control">';
                                                echo '</div>';

                                                echo '<div class="form-group">';
                                                echo '<label>' . $Lang->get('CONFIG__URL_SOCIAL_BTN') . '</label>';
                                                echo '<input type="text" value="' . $value['SocialButton']['url'] . '" name="social_btn_edited[' . $i . '][url]" class="form-control">';
                                                echo '</div>';

                                                echo '<div class="form-group">';
                                                echo '<label>' . $Lang->get('CONFIG__COLOR_SOCIAL_BTN') . '</label>';
                                                echo '<input type="text" value="' . $value['SocialButton']['color'] . '" name="social_btn_edited[' . $i . '][color]" class="form-control" id="color_social_btn_' . $i . '">';
                                                echo '</div>';
                                                echo '<script>$(\'#color_social_btn_' . $i . '\').colorPicker()</script>';

                                                echo '<button id="' . $i . '-' . $value['SocialButton']['id'] . '" class="btn btn-danger pull-right delete-social-btn-added">' . $Lang->get('GLOBAL__DELETE') . '</button>';

                                                echo '</div>';
                                                echo '<div class="clearfix"></div>';
                                            }
                                        }
                                        ?>
                                    </div>

                                    <script type="text/javascript">

                                        var i = <?= $i ?>;

                                        $('#addSocialBtnContainer').click(function (e) {

                                            e.preventDefault();

                                            var html_content = '<div id="btn-' + i + '">';

                                            html_content += '<hr>';

                                            html_content += '<div class="form-group">';
                                            html_content += '<label><?= $Lang->get('CONFIG__TITLE_SOCIAL_BTN') ?></label>';
                                            html_content += '<input type="text" name="social_btn[' + i + '][title]" class="form-control">';
                                            html_content += '</div>';

                                            html_content += '<div class="form-group">';
                                            html_content += '<label><?= $Lang->get('CONFIG__IMG_SOCIAL_BTN') ?></label>';
                                            html_content += '<input type="text" name="social_btn[' + i + '][img]" class="form-control">';
                                            html_content += '</div>';

                                            html_content += '<div class="form-group">';
                                            html_content += '<label><?= $Lang->get('CONFIG__URL_SOCIAL_BTN') ?></label>';
                                            html_content += '<input type="text" name="social_btn[' + i + '][url]" class="form-control">';
                                            html_content += '</div>';

                                            html_content += '<div class="form-group">';
                                            html_content += '<label><?= $Lang->get('CONFIG__COLOR_SOCIAL_BTN') ?></label>';
                                            html_content += '<input type="text" name="social_btn[' + i + '][color]" class="form-control" id="color_social_btn_' + i + '">';
                                            html_content += '</div>';

                                            html_content += '<button id="' + i + '" class="btn btn-danger pull-right delete-social-btn"><?= $Lang->get('GLOBAL__DELETE') ?></button>';

                                            html_content += '</div>';
                                            html_content += '<div class="clearfix"></div>';

                                            $('.addBtnContainer').hide().prepend(html_content).fadeIn(250);

                                            $('input[id="color_social_btn_' + i + '"]').colorPicker();

                                            $('.delete-social-btn').on('click', function (e) {
                                                e.preventDefault();
                                                deleteSocialBtn($(this).attr('id'), false);
                                            });

                                            i++;
                                        });

                                        $('.delete-social-btn').on('click', function (e) {
                                            e.preventDefault();
                                            deleteSocialBtn($(this).attr('id'), false);
                                        });

                                        $('.delete-social-btn-added').on('click', function (e) {
                                            e.preventDefault();
                                            deleteSocialBtn($(this).attr('id'), 'social_btn_added');
                                        });

                                        function deleteSocialBtn(infos, type) {
                                            infos = infos.split('-');
                                            var i = infos[0];
                                            var id = infos[1];
                                            if (type !== false) {
                                                $('.addBtnContainer').append('<input type="hidden" name="' + type + '[deleted][' + i + ']" value="' + id + '">'); // pour que le php le delete
                                            }

                                            $('#btn-' + i).slideUp(250, function (e) {
                                                $(this).remove();
                                            });
                                        }
                                    </script>

                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="tab_3">

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
                                    </div>

                                    <script type="text/javascript">
                                        $('input[name="captcha_type"]').on('change', function (e) {
                                            if ($(this).val() == '2') {
                                                $('#captcha-google').slideDown(250);
                                            } else {
                                                $('#captcha-google').slideUp(250);
                                            }
                                        });
                                    </script>

                                    <div id="captcha-google"
                                         style="display:<?= ($config['captcha_type'] == '2') ? 'block' : 'none' ?>;">
                                        <div class="form-group">
                                            <label><?= $Lang->get('CONFIG__KEY_CAPTCHA_GOOGLE_SITEKEY') ?></label>
                                            <?= $this->Form->input(false, array(
                                                'div' => false,
                                                'type' => 'text',
                                                'name' => 'captcha_google_sitekey',
                                                'class' => 'form-control',
                                                'value' => $config['captcha_google_sitekey'],
                                            )); ?>
                                        </div>

                                        <div class="form-group">
                                            <label><?= $Lang->get('CONFIG__KEY_CAPTCHA_GOOGLE_SECRET') ?></label>
                                            <?= $this->Form->input(false, array(
                                                'div' => false,
                                                'type' => 'text',
                                                'name' => 'captcha_google_secret',
                                                'class' => 'form-control',
                                                'value' => $config['captcha_google_secret'],
                                            )); ?>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__KEY_GOOGLE_ANALYTICS') ?></label>
                                        <?= $this->Form->input(false, array(
                                            'div' => false,
                                            'type' => 'text',
                                            'name' => 'google_analytics',
                                            'class' => 'form-control',
                                            'value' => $config['google_analytics'],
                                            'maxlength' => '15'
                                        )); ?>
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label><?= $Lang->get('CONFIG__KEY_END_LAYOUT_COE') ?></label>
                                        <?= $this->Form->textarea(false, array(
                                            'div' => false,
                                            'rows' => '5',
                                            'type' => 'text',
                                            'name' => 'end_layout_code',
                                            'class' => 'form-control',
                                            'value' => $config['end_layout_code']
                                        )); ?>
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
                                            <?= $this->Form->input(false, array(
                                                'div' => false,
                                                'type' => 'text',
                                                'name' => 'smtpHost',
                                                'class' => 'form-control',
                                                'value' => $config['smtpHost'],
                                                'autocomplete' => 'off'
                                            )); ?>
                                        </div>

                                        <div class="form-group">
                                            <label><?= $Lang->get('CONFIG__KEY_SMTP_USERNAME') ?></label>
                                            <?= $this->Form->input(false, array(
                                                'div' => false,
                                                'type' => 'text',
                                                'name' => 'smtpUsername',
                                                'class' => 'form-control',
                                                'value' => $config['smtpUsername'],
                                                'autocomplete' => 'off'
                                            )); ?>
                                        </div>

                                        <div class="form-group">
                                            <label><?= $Lang->get('CONFIG__KEY_SMTP_PORT') ?></label>
                                            <?= $this->Form->input(false, array(
                                                'div' => false,
                                                'type' => 'text',
                                                'name' => 'smtpPort',
                                                'class' => 'form-control',
                                                'value' => $config['smtpPort'],
                                                'autocomplete' => 'off'
                                            )); ?>
                                        </div>

                                        <div class="form-group">
                                            <label><?= $Lang->get('CONFIG__KEY_SMTP_PASSWORD') ?></label>
                                            <?= $this->Form->input(false, array(
                                                'div' => false,
                                                'type' => 'password',
                                                'name' => 'smtpPassword',
                                                'class' => 'form-control',
                                                'value' => $config['smtpPassword'],
                                                'autocomplete' => 'off'
                                            )); ?>
                                        </div>

                                    </div>

                                </div>
                                <!-- /.tab-pane -->
                            </div>
                            <!-- /.tab-content -->
                        </div>

                        <input type="hidden" name="data[_Token][key]" value="<?= $csrfToken ?>">

                        <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
                        <a href="<?= $this->Html->url(array('controller' => '', 'action' => '', 'admin' => true)) ?>"
                           type="button" class="btn btn-default"><?= $Lang->get('GLOBAL__CANCEL') ?></a>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>
