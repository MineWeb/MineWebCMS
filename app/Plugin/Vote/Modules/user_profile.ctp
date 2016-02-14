<?php if(isset($rewards_waiting) && $rewards_waiting) { ?>
	<hr>
	<div class="alert alert-info">
		<?= str_replace('{NBR_REWARDS}', $rewards_waiting, $Lang->get('VOTE__USER_MSG_REWARDS_WAITING')) ?>
		&nbsp;&nbsp;<a href="<?= $this->Html->url(array('controller' => 'voter', 'action' => 'get_reward', 'plugin' => 'vote')) ?>" class="btn btn-info"><?= $Lang->get('VOTE__USER_GET_REWARD') ?></a>
	</div>
<?php } ?>
