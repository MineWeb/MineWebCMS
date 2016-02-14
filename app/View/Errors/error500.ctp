<div class="push-nav"></div>
<div class="container">
    <div class="row">

        <div class="col-md-6">
            <h1 style="display: inline-block;"><?= (isset($Lang)) ? $Lang->get('ERROR__500_LABEL') : '500 Error' ?></h1>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
					<?= (isset($Lang)) ? $Lang->get('ERROR__INTERNAL_ERROR') : 'For know reason of this error, please change <pre>Configure::write(\'debug\', 0);</pre> to <pre>Configure::write(\'debug\', 3);</pre> in file <b>app/Config/core.php</b> line 34.' ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /*if (Configure::read('debug') > 0) { ?>
<div class="error-actions">
    <?= $this->element('exception_stack_trace'); ?>
</div>
<?php }*/ ?>