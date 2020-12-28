<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get('API__LABEL') ?></h3>
                </div>
                <div class="card-body">

                    <form action="#" method="post">

                        <h3><?= $Lang->get('API__SKIN') ?></h3>
                        <br>

                        <div class="ml-5">

                            <div class="form-group">
                                <label><?= $Lang->get('API__SKIN_LABEL') ?></label>
                                <div class="radio">
                                    <input type="radio" name="skins" value="1"<?php if ($config['skins'] == 1) {
                                        echo ' checked="checked"';
                                    } ?>>
                                    <label>
                                        <?= $Lang->get('GLOBAL__ENABLED') ?>
                                    </label>
                                </div>

                                <div class="radio">
                                    <input type="radio" name="skins" value="0"<?php if ($config['skins'] == 0) {
                                        echo ' checked="checked"';
                                    } ?>>
                                    <label>
                                        <?= $Lang->get('GLOBAL__DISABLED') ?>
                                    </label>
                                </div>
                            </div>
                            <hr>


                            <div class="form-group">
                                <label><?= $Lang->get('API__SKIN_PREMIUM_LABEL') ?></label>
                                <em><?= $Lang->get('API__SKIN_PREMIUM_DESC') ?></em>
                                <div class="radio">
                                    <input type="radio" name="get_premium_skins"
                                           value="1"<?php if ($config['get_premium_skins'] == 1) {
                                        echo ' checked="checked"';
                                    } ?>>
                                    <label>
                                        <?= $Lang->get('GLOBAL__ENABLED') ?>
                                    </label>
                                </div>

                                <div class="radio">
                                    <input type="radio" name="get_premium_skins"
                                           value="0"<?php if ($config['get_premium_skins'] == 0) {
                                        echo ' checked="checked"';
                                    } ?>>
                                    <label>
                                        <?= $Lang->get('GLOBAL__DISABLED') ?>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group skins_require">
                                <hr>
                                <label><?= $Lang->get('API__USE_SKIN_RESTORER') ?></label>
                                <div class="radio">
                                    <input type="radio" name="use_skin_restorer"
                                           value="1"<?php if ($config['use_skin_restorer'] == 1) {
                                        echo ' checked="checked"';
                                    } ?>>
                                    <label>
                                        <?= $Lang->get('GLOBAL__ENABLED') ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <input type="radio" name="use_skin_restorer"
                                           value="0"<?php if ($config['use_skin_restorer'] == 0) {
                                        echo ' checked="checked"';
                                    } ?>>
                                    <label>
                                        <?= $Lang->get('GLOBAL__DISABLED') ?>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group skins_require">
                                <label><?= $Lang->get('API__SKIN_RESTORER_SERVER') ?></label>
                                <em><?= $Lang->get('API__SKIN_RESTORER_SERVER_DESC') ?></em>
                                <select class="form-control" name="servers">
                                    <?php foreach ($get_all_servers as $key => $value) { ?>
                                        <option value="<?= $key ?>"<?= (isset($selected_server) && in_array($key, $selected_server)) ? ' selected' : '' ?>><?= $value ?></option>
                                    <?php } ?>
                                </select>
                                <hr>
                            </div>


                            <div class="form-group skins_require">
                                <label><?= $Lang->get('API__SKIN_FREE') ?></label>
                                <div class="radio">
                                    <input type="radio" name="skin_free" value="1"<?php if ($config['skin_free'] == 1) {
                                        echo ' checked="checked"';
                                    } ?>>
                                    <label>
                                        <?= $Lang->get('GLOBAL__ENABLED') ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <input type="radio" name="skin_free" value="0"<?php if ($config['skin_free'] == 0) {
                                        echo ' checked="checked"';
                                    } ?>>
                                    <label>
                                        <?= $Lang->get('GLOBAL__DISABLED') ?>
                                    </label>
                                </div>
                                <hr>
                            </div>


                            <div class="form-group skins_require">
                                <label><?= $Lang->get('API__FILENAME') ?></label>
                                <div class="input-group mb-3">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?= $this->Html->url('/', true) ?></span>
                                    </div>
                                    <input type="text" class="form-control" name="skin_filename"
                                           value="<?= $config['skin_filename'] ?>"
                                           placeholder="<?= $Lang->get('GLOBAL__DEFAULT') ?> : skins/{PLAYER}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">.png</span>
                                    </div>
                                </div>
                                <hr>
                            </div>


                            <div class="form-group skins_require">
                                <label><?= $Lang->get('API__FILE_SIZE') ?></label>
                                <div class="input-group mb-3">

                                    <input type="text" class="form-control" name="skin_width"
                                           value="<?= $config['skin_width'] ?>"
                                           placeholder="<?= $Lang->get('WIDTH') ?>">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">X</span>
                                    </div>
                                    <input type="text" class="form-control" name="skin_height"
                                           value="<?= $config['skin_height'] ?>"
                                           placeholder="<?= $Lang->get('HEIGHT') ?>">
                                </div>
                            </div>

                        </div>

                        <hr>

                        <h3><?= $Lang->get('API__CAPE') ?></h3>
                        <br>
                        <div class="ml-5">

                            <div class="form-group">
                                <label><?= $Lang->get('API__CAPE_LABEL') ?></label>
                                <div class="radio">
                                    <input type="radio" name="capes" value="1"<?php if ($config['capes'] == 1) {
                                        echo ' checked="checked"';
                                    } ?>>
                                    <label>
                                        <?= $Lang->get('GLOBAL__ENABLED') ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <input type="radio" name="capes" value="0"<?php if ($config['capes'] == 0) {
                                        echo ' checked="checked"';
                                    } ?>>
                                    <label>
                                        <?= $Lang->get('GLOBAL__DISABLED') ?>
                                    </label>
                                </div>
                            </div>


                            <div class="form-group capes_require">
                                <hr>
                                <label><?= $Lang->get('API__CAPE_FREE') ?></label>
                                <div class="radio">
                                    <input type="radio" name="cape_free" value="1"<?php if ($config['cape_free'] == 1) {
                                        echo ' checked="checked"';
                                    } ?>>
                                    <label>
                                        <?= $Lang->get('GLOBAL__ENABLED') ?>
                                    </label>
                                </div>
                                <div class="radio">
                                    <input type="radio" name="cape_free" value="0"<?php if ($config['cape_free'] == 0) {
                                        echo ' checked="checked"';
                                    } ?>>
                                    <label>
                                        <?= $Lang->get('GLOBAL__DISABLED') ?>
                                    </label>
                                </div>
                                <hr>
                            </div>


                            <div class="form-group capes_require">
                                <label><?= $Lang->get('API__FILENAME') ?></label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"
                                    ><?= $this->Html->url('/', true) ?></span>
                                    </div>
                                    <input type="text" class="form-control" name="cape_filename"
                                           value="<?= $config['cape_filename'] ?>"
                                           placeholder="<?= $Lang->get('GLOBAL__DEFAULT') ?> : capes/{PLAYER}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">.png</span>
                                    </div>
                                </div>
                                <hr>
                            </div>


                            <div class="input-group capes_require mb-3">
                                <input type="text" class="form-control" name="cape_width"
                                       value="<?= $config['cape_width'] ?>" placeholder="<?= $Lang->get('WIDTH') ?>">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">X</span>
                                </div>
                                <input type="text" class="form-control" name="cape_height"
                                       value="<?= $config['cape_height'] ?>" placeholder="<?= $Lang->get('HEIGHT') ?>">
                            </div>

                        </div>


                        <hr>


                        <script>

                            let skins = $('input[type=radio][name=skins]:checked');
                            if (skins.val() === '0')
                                $('.skins_require').slideUp();
                            $('input[type=radio][name=skins]').change(function () {
                                if (this.value === '0') {
                                    $('.skins_require').slideUp();
                                } else {
                                    $('.skins_require').slideDown();

                                }
                            });


                            let capes = $('input[type=radio][name=capes]:checked');
                            if (capes.val() === '0')
                                $('.capes_require').slideUp();
                            $('input[type=radio][name=capes]').change(function () {
                                if (this.value === '0') {
                                    $('.capes_require').slideUp();
                                } else {
                                    $('.capes_require').slideDown();

                                }
                            });

                        </script>

                        <input type="hidden" name="data[_Token][key]" value="<?= $csrfToken ?>">

                        <div class="float-right">
                            <button class="btn btn-primary"
                                    type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>
