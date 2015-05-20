<?php   ?>
<div class="row-fluid">
	
	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-eye-open"></i> <?= $Lang->get('USER_LIST') ?></h3>
		</div>

		<div class="well no-padding">
			
			<table class="data-table">
				<thead>
					<tr>
						<th><?= $Lang->get('USER') ?></th>
						<th><?= $Lang->get('CREATED') ?></th>
						<th><?= $Lang->get('RANK') ?></th>
						<th class="right"><?= $Lang->get('ACTIONS') ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($users as $value => $v) { ?>
						<tr>
							<td><?= $v["User"]["pseudo"] ?></td>
							<td>Le <?= $Lang->date($v["User"]["created"]) ?></td>
							<?php if($v["User"]["rank"] == 3 OR $v["User"]["rank"] == 4) { ?>
							<td><span class="label label-important"><?= $Lang->get('ADMINISTRATOR') ?></span></td>
							<?php } elseif($v["User"]["rank"] == 2) { ?>
							<td><span class="label label-warning"><?= $Lang->get('MODERATOR') ?></span></td>
							<?php } elseif($v["User"]["rank"] == 5) { ?>
							<td><span class="label label-primary"><?= $Lang->get('BANNED') ?></span></td>
							<?php } else { ?>
							<td><span class="label label-success"><?= $Lang->get('MEMBER') ?></span></td>
							<?php } ?>
							<td class="right">
								<a href="<?= $this->Html->url(array('controller' => 'user', 'action' => 'edit/'.$v["User"]["id"], 'admin' => true)) ?>" class="btn btn-info">Modifier</button>
								<a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'user', 'action' => 'delete/'.$v["User"]["id"], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('DELETE') ?></button>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

		</div>

	</div>
</div>
<script>
function confirmDel(url) {
  if (confirm("<?= $Lang->get('CONFIRM_WANT_DELETE') ?>"))
    window.location.href=''+url+'';
  else
    return false;
}
</script>