<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get("SOCIAL__HOME") ?></h3>
                </div>
                <div class="card-body">
                    <a class="btn btn-large btn-block btn-primary" href="<?= $this->Html->url(['controller' => 'social', 'action' => 'add', 'admin' => true]) ?>"><?= $Lang->get('SOCIAL__ADD') ?></a>
                    <hr>
                    <table class="table table-responsive-sm table-bordered">
                        <thead>
                            <tr>
                                <th><?= $Lang->get("SOCIAL__BUTTON_TITLE") ?></th>
                                <th><?= $Lang->get("SOCIAL__BUTTON_TYPE") ?></th>
                                <th><?= $Lang->get("SOCIAL__BUTTON_URL") ?></th>
                                <th><?= $Lang->get("SOCIAL__BUTTON_COLOR") ?></th>
                                <th class="right"><?= $Lang->get("GLOBAL__ACTIONS") ?></th>
                            </tr>
                        </thead>
                        
                        <tbody id="sortable">
                            <?php $i = 0;foreach ($social_buttons as $key => $value) { $i++ ?>
                                <tr class="item" style="cursor:move;" id="<?= $value["SocialButton"]["id"] ?>-<?= $i ?>">
                                    <td><?= $value['SocialButton']['title'] ?></td>
                                    <td>
                                        <?php if(!empty($value['SocialButton']['extra'])) { ?>
                                            <a href="#" class="m-auto text-dark" title="<?= $value['SocialButton']['extra'] ?>">
                                                <?php if(strpos($value['SocialButton']['extra'], 'fa-')) { ?>
                                                    <i class="<?= $value['SocialButton']['extra'] ?> fa-3x m-auto"></i>
                                                <?php } else { ?>
                                                    <img src="<?= $value['SocialButton']['extra'] ?>" class="m-auto" alt="<?= $Lang->get("SOCIAL__BUTTON_IMG_ALT") . $value['SocialButton']['title'] ?>" style="height: 3em;">
                                                <?php } ?>
                                            </a>
                                        <?php } else {
                                            echo $Lang->get("SOCIAL__EMPTY_TYPE");
                                        } ?>
                                    </td>
                                    <td><a href="<?= $value['SocialButton']['url'] ?>"><?= $value['SocialButton']['url'] ?></a></td>
                                    <td><div class="socialbutton-color p-2 text-center" style="background-color: <?= $value['SocialButton']['color'] ?>"><?= $value['SocialButton']['color'] ?><div></td>
                                    <td>
                                        <a href="<?= $this->Html->url(['controller' => 'social', 'action' => 'edit', $value['SocialButton']['id']]) ?>" class="btn btn-info"><?= $Lang->get('GLOBAL__EDIT') ?></a>
                                        <a onClick="confirmDel('<?= $this->Html->url(['controller' => 'social', 'action' => 'delete', $value['SocialButton']['id']]) ?>')" class="btn btn-danger"><?= $Lang->get('GLOBAL__DELETE') ?></a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <br>
                    <div class="ajax-msg"></div>
                    <button id="save" class="btn btn-success pull-right active" disabled="disabled"><?= $Lang->get('SOCIAL__SAVE_SUCCESS') ?></button>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(function() {
        $( "#sortable" ).sortable({
            axis: 'y',
            items: '.item:not(.fixed)',
            stop: function (event, ui) {
                $('#save').empty().html('<?= $Lang->get('SOCIAL__SAVE_IN_PROGRESS') ?>');
                var inputs = {};
                var social_button_order = $(this).sortable('serialize');
                inputs['social_button_order'] = social_button_order;
                $('#social_button_order').text(social_button_order);
                inputs['data[_Token][key]'] = '<?= $csrfToken ?>';
                $.post("<?= $this->Html->url(array('controller' => 'social', 'action' => 'save_ajax', 'admin' => true)) ?>", inputs, function(data) {
                    if(data.statut) {
                        $('#save').empty().html('<?= $Lang->get('SOCIAL__SAVE_SUCCESS') ?>');
                    } else if(!data.statut) {
                        $('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('GLOBAL__ERROR') ?> :</b> '+data.msg+'</i></div>').fadeIn(500);
                    } else {
                        $('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('GLOBAL__ERROR') ?> :</b> <?= $Lang->get('ERROR__INTERNAL_ERROR') ?></i></div>');
                    }
                });
            }
        });
    });
</script>
