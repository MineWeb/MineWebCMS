<div class="push-nav"></div>
<div class="container">
    <div class="row">

        <div class="col-md-6">
            <h1 style="display: inline-block;"><?= $Lang->get('ERROR_500_TITLE') ?></h1>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
					<?= $Lang->get('INTERNAL_ERROR') ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (Configure::read('debug') > 0) { ?>
<div class="error-actions">
    <?= $this->element('exception_stack_trace'); ?>
</div>
<?php } ?>