<div class="row-fluid">

	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-cog"></i> <?= $Lang->get('API') ?></h3>
		</div>

		<div class="well no-padding">
			<?= $this->Form->create(false, array(
				'class' => 'form-horizontal'
			)); ?>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('SKIN') ?></label>
					<div class="controls">
						<label class="radio">
							<input type="radio" name="skins" value="1"<?php if($config['skins'] == 1) { echo ' checked="checked"'; } ?>>
						  <?= $Lang->get('ENABLED') ?>
						</label>
						<label class="radio">
							<input type="radio" name="skins" value="0"<?php if($config['skins'] == 0) { echo ' checked="checked"'; } ?>>
						  <?= $Lang->get('DISABLED') ?>
						</label>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"><?= $Lang->get('SKIN_FREE') ?></label>
					<div class="controls">
						<label class="radio">
							<input type="radio" name="skin_free" value="1"<?php if($config['skin_free'] == 1) { echo ' checked="checked"'; } ?>>
						  <?= $Lang->get('ENABLED') ?>
						</label>
						<label class="radio">
							<input type="radio" name="skin_free" value="0"<?php if($config['skin_free'] == 0) { echo ' checked="checked"'; } ?>>
						  <?= $Lang->get('DISABLED') ?>
						</label>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"><?= $Lang->get('FILENAME') ?></label>
					<div class="controls">
						<div class="input-group">
      						<div class="input-group-addon"><?= $this->Html->url('/', true) ?></div>
					      	<input type="text" class="form-control" name="skin_filename" value="<?= $config['skin_filename'] ?>" placeholder="<?= $Lang->get('DEFAULT') ?> : skins/{PLAYER}">
					      	<div class="input-group-addon">.png</div>
					    </div>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"><?= $Lang->get('CAPE') ?></label>
					<div class="controls">
						<label class="radio">
							<input type="radio" name="capes" value="1"<?php if($config['capes'] == 1) { echo ' checked="checked"'; } ?>>
						  <?= $Lang->get('ENABLED') ?>
						</label>
						<label class="radio">
							<input type="radio" name="capes" value="0"<?php if($config['capes'] == 0) { echo ' checked="checked"'; } ?>>
						  <?= $Lang->get('DISABLED') ?>
						</label>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"><?= $Lang->get('CAPE_FREE') ?></label>
					<div class="controls">
						<label class="radio">
							<input type="radio" name="cape_free" value="1"<?php if($config['cape_free'] == 1) { echo ' checked="checked"'; } ?>>
						  <?= $Lang->get('ENABLED') ?>
						</label>
						<label class="radio">
							<input type="radio" name="cape_free" value="0"<?php if($config['cape_free'] == 0) { echo ' checked="checked"'; } ?>>
						  <?= $Lang->get('DISABLED') ?>
						</label>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"><?= $Lang->get('FILENAME') ?></label>
					<div class="controls">
						<div class="input-group">
      						<div class="input-group-addon"><?= $this->Html->url('/', true) ?></div>
					      	<input type="text" class="form-control" name="cape_filename" value="<?= $config['cape_filename'] ?>" placeholder="<?= $Lang->get('DEFAULT') ?> : capes/{PLAYER}">
					      	<div class="input-group-addon">.png</div>
					    </div>
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
<style>
.input-group{position:relative;display:table;border-collapse:separate}.input-group[class*="col-"]{float:none;padding-left:0;padding-right:0}.input-group .form-control{position:relative;z-index:2;float:left;width:200px;margin-bottom:0}.input-group-addon,.input-group-btn,.input-group .form-control{display:table-cell}.input-group-addon:not(:first-child):not(:last-child),.input-group-btn:not(:first-child):not(:last-child),.input-group .form-control:not(:first-child):not(:last-child){border-radius:0}.input-group-addon,.input-group-btn{white-space:nowrap;vertical-align:middle}.input-group-addon{padding:6px 12px;font-size:14px;font-weight:400;line-height:1;color:#555;text-align:center;background-color:#eee;border:1px solid #ccc;border-radius:4px}.input-group-addon.input-sm{padding:5px 10px;font-size:12px;border-radius:3px}.input-group-addon.input-lg{padding:10px 16px;font-size:18px;border-radius:6px}.input-group-addon input[type="radio"],.input-group-addon input[type="checkbox"]{margin-top:0}.input-group .form-control:first-child,.input-group-addon:first-child,.input-group-btn:first-child > .btn,.input-group-btn:first-child > .btn-group > .btn,.input-group-btn:first-child > .dropdown-toggle,.input-group-btn:last-child > .btn:not(:last-child):not(.dropdown-toggle),.input-group-btn:last-child > .btn-group:not(:last-child) > .btn{border-bottom-right-radius:0;border-top-right-radius:0}.input-group-addon:first-child{border-right:0}.input-group .form-control:last-child,.input-group-addon:last-child,.input-group-btn:last-child > .btn,.input-group-btn:last-child > .btn-group > .btn,.input-group-btn:last-child > .dropdown-toggle,.input-group-btn:first-child > .btn:not(:first-child),.input-group-btn:first-child > .btn-group:not(:first-child) > .btn{border-bottom-left-radius:0;border-top-left-radius:0}.input-group-addon:last-child{border-left:0}.input-group-btn{position:relative;font-size:0;white-space:nowrap}.input-group-btn > .btn{position:relative}.input-group-btn > .btn + .btn{margin-left:-1px}.input-group-btn > .btn:hover,.input-group-btn > .btn:focus,.input-group-btn > .btn:active{z-index:2}.input-group-btn:first-child > .btn,.input-group-btn:first-child > .btn-group{margin-right:-1px}.input-group-btn:last-child > .btn,.input-group-btn:last-child > .btn-group{margin-left:-1px}
</style>