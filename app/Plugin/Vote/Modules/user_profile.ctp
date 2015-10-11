<?php if($rewards_waiting) { ?>
	<hr>
	<div class="alert alert-info">
		<?= str_replace('{NBR_REWARDS}', $rewards_waiting, $Lang->get('PROFILE_LIST_REWARDS_WAITING')) ?>
		<button class="btn btn-info"><?= $Lang->get('GET_REWARD') ?></button>
	</div>
<?php } ?>