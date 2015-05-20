<div class="row-fluid">

	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-cog"></i> <?= $Lang->get('PERMISSIONS') ?></h3>
		</div>

		<div class="well">

			<?= $this->Form->create(false, array(
				'class' => 'form-horizontal'
			)); ?>
				<table class="table table-bordered">
	              <thead>
	                <tr>
	                  <th><?= $Lang->get('PERMISSIONS') ?></th>
	                  <th><?= $Lang->get('NORMAL') ?></th>
	                  <th><?= $Lang->get('MODERATOR') ?></th>
	                  <th><?= $Lang->get('ADMINISTRATOR') ?></th>
	                </tr>
	              </thead>
	              <tbody>
				<?php
				$config = $Permissions->get_all();
				foreach ($config as $key => $value) { ?>
					<tr>
                  		<td><?= $key ?></td>
                  		<td><input type="checkbox" name="<?= $key ?>_0"<?php if($value['0'] == "true") { echo ' checked="checked"'; } ?>></td>
                  		<td><input type="checkbox" name="<?= $key ?>_2"<?php if($value['2'] == "true") { echo ' checked="checked"'; } ?>></td>
                  		<td><input type="checkbox" checked="checked" disabled="disabled"></td>
                	</tr>
				<?php } ?>

				    </tbody>
            	</table>

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