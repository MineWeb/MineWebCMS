<div class="row-fluid">
	<div class="span12">
		<div class="top-bar">
			<h3><i class="icon-bar-chart"></i> <?= $Lang->get('REFERING_WEBSITE') ?></h3>
		</div>

		<div class="well">			
			<table class="data-table">
				<thead>
					<tr>
						<th><?= $Lang->get('WEBSITE_OR_PAGE') ?></th>
						<th><?= $Lang->get('NBR_VISITS') ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($referers as $key => $value) { ?>
						<tr>
							<td><?= $key ?></td>
							<td><?= $value ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>