<?php
 
App::import('Component', 'ConfigurationComponent');
$this->Configuration = new ConfigurationComponent();
?>
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
            <h3><i class="icon-cog"></i> <?= $Lang->get('LINK_SERVER') ?></h3>
        </div>

        <div class="well no-padding">
            <?= $this->Form->create(false, array(
                'class' => 'form-horizontal',
                'id' => 'link'
            )); ?>
            <div class="ajax-msg"></div>
                <div class="control-group">
                    <label class="control-label"><?= $Lang->get('SERVER_HOST') ?></label>
                    <div class="controls">
                        <?= $this->Form->input(false, array(
                            'div' => false,
                            'type' => 'text',
                            'name' => 'host',
                            'class' => 'span6 m-wrap',
                            'placeholder' => 'Ex: 127.0.0.1',
                            'value' => $server_host
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
                            'value' => $port
                        )); ?>
                    </div>
                </div>
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
                    <a href="<?= $this->Html->url(array('controller' => '', 'action' => '', 'admin' => true)) ?>" type="button" class="btn"><?= $Lang->get('CANCEL') ?></a>     
                </div>

            </form>          

        </div>

    </div>

</div>
<script>
    $("#link").submit(function( event ) {
        $('.ajax-msg').empty().html('<div class="alert alert-info" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><?= $Lang->get('LOADING') ?> ...</div>').fadeIn(500);
        event.preventDefault();
        var $form = $( this );
        var host = $form.find("input[name='host']").val();
        var port = $form.find("input[name='port']").val();
        var timeout = $form.find("input[name='timeout']").val();
        $.post("<?= $this->Html->url(array('controller' => 'server', 'action' => 'link_ajax', 'admin' => true)) ?>", { host : host, port : port, timeout : timeout }, function(data) {
            data2 = data.split("|");
            if(data.indexOf('true') != -1) {
                $('.ajax-msg').empty().html('<div class="alert alert-success" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
            } else if(data.indexOf('false') != -1) {
                $('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
            } else {
                $('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
            }
        });
        return false;
    });
</script>