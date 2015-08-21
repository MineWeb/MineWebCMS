<style>
    .control-label {
        width:115px!important;
        -webkit-hyphens: auto;
        -moz-hyphens: auto;
        -ms-hyphens: auto;
        -o-hyphens: auto;
        hyphens: auto;
    }
</style>
<div class="row-fluid">

    <div class="span12">
        <div class="top-bar">
            <h3><i class="icon-cog"></i> <?= $Lang->get('CONFIG_SERVER') ?></h3>
        </div>

        <div class="well no-padding">
            <?= $this->Form->create(false, array(
                'class' => 'form-horizontal',
                'id' => 'config'
            )); ?>
            <div class="ajax-msg"></div>
                <div class="control-group">
                    <label class="control-label"><?= $Lang->get('TIMEOUT') ?></label>
                    <div class="controls">
                        <?= $this->Form->input(false, array(
                            'div' => false,
                            'type' => 'text',
                            'name' => 'timeout',
                            'class' => 'span6 m-wrap',
                            'placeholder' => 'Ex: 3',
                            'value' => $timeout
                        )); ?>
                        <span class="help-inline"><?= $Lang->get('IN_SECONDS') ?></span>
                    </div>
                </div>

                <div class="form-actions">
                    <?= $this->Form->button($Lang->get('SUBMIT'), array(
                        'type' => 'submit',
                        'class' => 'btn btn-primary'
                    )); ?>  
                </div>

            </form>
        </div>
    </div>

</div>
<?php if(!empty($servers)) { ?>
    <?php foreach ($servers as $key => $value) { ?>
        <div class="row">
            <div class="span12">
                <div class="top-bar">
                    <h3><i class="icon-cog"></i> <?= $Lang->get('LINK_SERVER') ?></h3>
                </div>
                <div class="well no-padding">
                    <?= $this->Form->create(false, array(
                        'class' => 'form-horizontal link',
                    )); ?>
                    <div class="ajax-msg"></div>
                        <input type="hidden" name="id" value="<?= $value['Server']['id'] ?>">
                        <div class="control-group">
                            <label class="control-label"><?= $Lang->get('NAME') ?></label>
                            <div class="controls">
                                <?= $this->Form->input(false, array(
                                    'div' => false,
                                    'type' => 'text',
                                    'name' => 'name',
                                    'class' => 'span6 m-wrap',
                                    'placeholder' => 'Ex: MineWeb',
                                    'value' => $value['Server']['name']
                                )); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label"><?= $Lang->get('SERVER_HOST') ?></label>
                            <div class="controls">
                                <?= $this->Form->input(false, array(
                                    'div' => false,
                                    'type' => 'text',
                                    'name' => 'host',
                                    'class' => 'span6 m-wrap',
                                    'placeholder' => 'Ex: 127.0.0.1',
                                    'value' => $value['Server']['ip']
                                )); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label"><?= $Lang->get('PORT') ?></label>
                            <div class="controls">
                                <?= $this->Form->input(false, array(
                                    'div' => false,
                                    'type' => 'text',
                                    'name' => 'port',
                                    'class' => 'span6 m-wrap',
                                    'placeholder' => 'Ex: 8080',
                                    'value' => $value['Server']['port']
                                )); ?>
                            </div>
                        </div>
                        <div class="form-actions">
                            <?= $this->Form->button($Lang->get('SUBMIT'), array(
                                'type' => 'submit',
                                'class' => 'btn btn-primary'
                            )); ?>  
                        </div>
                    </form>          
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>

<div id="add_server_content"></div>
<div class="btn btn-success btn-block" id="add_server"><?= $Lang->get('ADD_SERVER') ?></div>

<script>

    $("#add_server").click(function() {
        var before = $('#add_server_content').html();
        var new_server = '<div class="row"><div class="span12"><div class="top-bar"><h3><i class="icon-cog"></i> <?= $Lang->get('LINK_SERVER') ?></h3></div><div class="well no-padding"><form method="post" class="form-horizontal link"><div class="ajax-msg"></div><div class="control-group"><label class="control-label"><?= $Lang->get('NAME') ?></label><div class="controls"><input type="text" name="name" class="span6 m-wrap" placeholder="Ex: MineWeb"></div></div><div class="control-group"><label class="control-label"><?= $Lang->get('SERVER_HOST') ?></label><div class="controls"><input type="text" name="host" class="span6 m-wrap" placeholder="Ex: 127.0.0.1"></div></div><div class="control-group"><label class="control-label"><?= $Lang->get('PORT') ?></label><div class="controls"><input type="text" name="port" class="span6 m-wrap" placeholder="Ex: 8080"></div></div><div class="form-actions"><button type="submit" class="btn btn-primary"><?= $Lang->get('SUBMIT') ?></button></div></form></div></div></div>'+"\n";
        $('#add_server_content').html(before+new_server);
        $(".link").on('submit', function( event ) {
            event.preventDefault();
            var $form = $( this );

            $form.find('.ajax-msg').empty().html('<div class="alert alert-info" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><?= $Lang->get('LOADING') ?> ...</div>').fadeIn(500);
            event.preventDefault();
            var name = $form.find("input[name='name']").val();
            var host = $form.find("input[name='host']").val();
            var port = $form.find("input[name='port']").val();
            var id = $form.find("input[name='id']").val();
            $.post("<?= $this->Html->url(array('controller' => 'server', 'action' => 'link_ajax', 'admin' => true)) ?>", { name : name, host : host, port : port, id : id }, function(data) {
                data2 = data.split("|");
                if(data.indexOf('true') != -1) {
                    $form.find('.ajax-msg').empty().html('<div class="alert alert-success" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
                } else if(data.indexOf('false') != -1) {
                    $form.find('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
                } else {
                    $form.find('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
                }
            });
            return false;
        });
    });

    $("#config").submit(function( event ) {
        $('#config .ajax-msg').empty().html('<div class="alert alert-info" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><?= $Lang->get('LOADING') ?> ...</div>').fadeIn(500);
        event.preventDefault();
        var $form = $( this );
        var timeout = $form.find("input[name='timeout']").val();
        $.post("<?= $this->Html->url(array('controller' => 'server', 'action' => 'config', 'admin' => true)) ?>", { timeout : timeout }, function(data) {
            data2 = data.split("|");
            if(data.indexOf('true') != -1) {
                $('#config .ajax-msg').empty().html('<div class="alert alert-success" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
            } else if(data.indexOf('false') != -1) {
                $('#config .ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
            } else {
                $('#config .ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
            }
        });
        return false;
    });

    $(".link").on('submit', function( event ) {
        event.preventDefault();
        var $form = $( this );

        $form.find('.ajax-msg').empty().html('<div class="alert alert-info" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><?= $Lang->get('LOADING') ?> ...</div>').fadeIn(500);
        event.preventDefault();
        var name = $form.find("input[name='name']").val();
        var host = $form.find("input[name='host']").val();
        var port = $form.find("input[name='port']").val();
        var id = $form.find("input[name='id']").val();
        $.post("<?= $this->Html->url(array('controller' => 'server', 'action' => 'link_ajax', 'admin' => true)) ?>", { name : name, host : host, port : port, id : id }, function(data) {
            data2 = data.split("|");
            if(data.indexOf('true') != -1) {
                $form.find('.ajax-msg').empty().html('<div class="alert alert-success" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
            } else if(data.indexOf('false') != -1) {
                $form.find('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
            } else {
                $form.find('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
            }
        });
        return false;
    });
</script>