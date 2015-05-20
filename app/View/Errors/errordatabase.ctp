<?php   ?>
<div class="error-template">
	<div class="well">
	    <h1><?= $Lang->get('PROBLEM_DATABASE_NO_CONNECTION') ?></h1>
	    <div class="error-details">
	        <?= $Lang->get('PROBLEM_DATABASE_NO_CONNECTION_HOW_DO') ?>
	    </div>
	    <?php if (Configure::read('debug') > 0) { ?>
	    <div class="error-actions">
	        <?= $this->element('exception_stack_trace'); ?>
	    </div>
	    <?php } ?>
	</div>
</div>