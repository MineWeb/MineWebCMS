<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get('SOCIAL__HOME') ?></h3>
                </div>
                <div class="card-body">
                    <form method="post" data-ajax="true" data-upload-image="true" data-redirect-url="<?= $this->Html->url(['controller' => 'social', 'action' => 'index', 'admin' => 'true']) ?>" id="add-social-button">
                        <div class="form-group">
                            <label><?= $Lang->get('SOCIAL__BUTTON_SELECT_DEFAULT') ?></label>    
                            <select class="form-control" aria-label="><?= $Lang->get('SOCIAL__BUTTON_SELECT_DEFAULT') ?>" id="select-social-default">
                                <?php $haveSelected = false;foreach($social_default as $value) { ?>
                                    <option value="<?= strtolower($value['title']) ?>" <?php if(strtolower($social_button['title']) == strtolower($value['title'])) { echo "selected"; $haveSelected = true; } ?>><?= $value['title'] ?></option>
                                <?php } ?>
                                <option value="custom" <?php if(!$haveSelected) { echo "selected"; } ?>>Custom</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><?= $Lang->get('SOCIAL__BUTTON_TITLE') ?></label>
                            <input type="text" name="title" class="form-control global-reset-input" id="social-title" value="<?= $social_button['title'] ?>">
                        </div>

                        <div class="form-group">
                            <label><?= $Lang->get('SOCIAL__CHOOSE_TYPE') ?></label>
                            <div class="form-check">    
                                <input class="form-check-input"  type="radio" id="choose-is-img" name="type" value="img" <?php if($social_button_type == 'img') { echo "checked"; } ?>>
                                <label class="form-check-label" for="choose-is-img"><?= $Lang->get('SOCIAL__CHOOSE_TYPE_IMG') ?></label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="choose-is-icon" name="type" value="icon" <?php if($social_button_type == 'fa') { echo "checked"; } ?>>
                                <label class="form-check-label" for="choose-is-icon"><?= $Lang->get('SOCIAL__CHOOSE_TYPE_ICON') ?></label>
                            </div>
                        </div>


                            <div id="type-is-img" <?php if($social_button_type != 'img') { ?>class="d-none"<?php } ?>>
                                <div class="form-group mx-5">
                                    <label><?= $Lang->get('SOCIAL__BUTTON_IMG') ?></label><em> <?= $Lang->get('SOCIAL__BUTTON_IMG_SIZE') ?></em>
                                    <input type="text" name="img" class="form-control img-or-icon-input global-reset-input" placeholder="https://images.google.com" value="<?= $social_button['extra'] ?>">
                                </div>
                                <div class="text-right mx-5">
                                    <a class="btn btn-default type-cancel"><?= $Lang->get('SOCIAL__CHOOSE_TYPE_CANCEL') ?></a>
                                </div>
                            </div>

                            <div id="type-is-icon" <?php if($social_button_type != 'fa') { ?>class="d-none"<?php } ?>>
                                <div class="form-group mx-5">
                                    <label><?= $Lang->get('SOCIAL__BUTTON_ICON') ?></label>
                                    <p><?= $Lang->get('SOCIAL_ICON_DESC') ?><a target="_blank" href="https://fontawesome.com/" title="Lien vers fontawesome">https://fontawesome.com/</a>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">FA</span>
                                        </div>
                                        <input type="text" name="icon" class="form-control img-or-icon-input global-reset-input" placeholder="fab fa-teamspeak"  value="<?= $social_button['extra'] ?>">
                                    </div>
                                </div>
                                <div class="text-right mx-5">
                                    <a class="btn btn-default type-cancel"><?= $Lang->get('SOCIAL__CHOOSE_TYPE_CANCEL') ?></a>
                                </div>
                            </div> 


                        <div class="form-group">
                            <label><?= $Lang->get('SOCIAL__BUTTON_URL') ?></label>
                            <input type="text" name="url" class="form-control" value="<?= $social_button['url'] ?>">
                        </div>

                        <div class="form-group">
                            <label><?= $Lang->get('SOCIAL__BUTTON_COLOR') ?></label>
                            <input type="color" name="color" class="form-control global-reset-input" id="social-color" value="<?= $social_button['color'] ?>">
                        </div>

                        <div class="float-right">
                            <a href="<?= $this->Html->url(['controller' => 'social', 'action' => 'index', 'admin' => true]) ?>" class="btn btn-default"><?= $Lang->get('GLOBAL__CANCEL') ?></a>
                            <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    // Default select //
   
        // event
        $('#select-social-default').change(function() {
            getSelectValue($(this).val());
        });

        function getSelectValue(value) {
            switch (value) {
                <?php foreach($social_default as $value) { ?>
                case '<?= strtolower($value['title']) ?>':
                    dispatchData('<?= $value['title'] ?>', '<?= $value['extra'] ?>', '<?= $value['color'] ?>');
                    break;
                <?php } ?>
                default:
                    globalReset();
                    break;
            }

        }

        //Function for default
        function dispatchData(title, extra, color) {
            $("#social-title").val(title);

            if(extra.length > 0 ) {
                if(extra.includes('fa-')) {
                    $('#choose-is-icon').prop('checked', true);
                    $("#type-is-img").addClass("d-none");
                    $("#type-is-icon").removeClass("d-none");
                    $("#type-is-icon input").val(extra);
                } else {
                    $('#choose-is-img').prop('checked', true);
                    $("#type-is-img").removeClass("d-none");
                    $("#type-is-icon").addClass("d-none");
                    $("#type-is-img input").val(extra);
                }
            }
            
            $("#social-color").val(color);
        }

        //Function for custom
        function globalReset() {
            $('input:radio[name="type"]').prop('checked', false);
            $("#type-is-img").addClass("d-none");
            $("#type-is-icon").addClass("d-none");
            $(".global-reset-input").val("");
        }
    
    // Type of illustration //
    $('input:radio[name="type"]').change(function() {
        if ($(this).is(':checked')) {
            if($(this).val() == 'img') {
                $("#type-is-img").removeClass("d-none");
                $("#type-is-icon").addClass("d-none");
            }
            if($(this).val() == 'icon') {
                $("#type-is-img").addClass("d-none");
                $("#type-is-icon").removeClass("d-none");
            }
        }
    });

    //reset type part
    $('.type-cancel').click(function() {
        $('input:radio[name="type"]').prop('checked', false);
        $("#type-is-img").addClass("d-none");
        $("#type-is-icon").addClass("d-none");
        $(".img-or-icon-input").val("");
    });
</script>
