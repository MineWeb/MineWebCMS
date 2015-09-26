<?php
 
App::import('Component', 'ConfigurationComponent');
$this->Configuration = new ConfigurationComponent();
?>
		<div class="row-fluid">

			<div class="span12">

				<div class="top-bar">
					<h3><i class="icon-cog"></i> <?= $Lang->get('MAINTENANCE') ?></h3>
				</div>

				<div class="well no-padding">
					<?= $this->Form->create(false, array(
						'class' => 'form-horizontal'
					)); ?>
						<div class="control-group">
							<label class="control-label"><?= $Lang->get('STATE') ?></label>
							<div class="controls">
								<label class="radio">
									<input type="radio" class="enabled" name="state" value="enabled" <?php if($this->Configuration->get('maintenance') != '0') { echo 'checked=""'; } ?>>
								  <?= $Lang->get('ENABLED') ?>
								</label>
								<label class="radio">
									<input type="radio" class="disabled" name="state" value="disabled" <?php if($this->Configuration->get('maintenance') == '0') { echo 'checked=""'; } ?>>
								  <?= $Lang->get('DISABLED') ?>
								</label>
							</div>
						</div>
						<div class="control-group reason <?php if($this->Configuration->get('maintenance') == '0') { echo 'hidden'; } ?>">
							<label class="control-label"><?= $Lang->get('REASON') ?></label>
							<div class="controls">
								<?php if($this->Configuration->get('maintenance') != '0') { ?>
									<?= $this->Form->textarea(false, array(
										'div' => false,
					    				'name' => 'reason',
					    				'class' => 'span6 m-wrap',
					    				'value' => $this->Configuration->get('maintenance')
									)); ?>
								<?php } else { ?>
									<?= $this->Form->textarea(false, array(
										'div' => false,
					    				'name' => 'reason',
					    				'class' => 'span6 m-wrap'
									)); ?>
								<?php } ?>
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
<script type="text/javascript">
	$(".enabled").change(function() {
		if($(".enabled").is(':checked')) {
			$(".reason").removeClass('hidden');
		} else {
			$(".reason").addClass('hidden');
		}
	});
	$(".disabled").change(function() {
		if($(".disabled").is(':checked')) {
			$(".reason").addClass('hidden');
		} else {
			$(".reason").removeClass('hidden');
		}
	});
</script>