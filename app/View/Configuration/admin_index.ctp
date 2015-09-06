<?php
 
App::import('Component', 'ConfigurationComponent');
$this->Configuration = new ConfigurationComponent();
?>
		<div class="row-fluid">

			<div class="span12">

				<div class="top-bar">
					<h3><i class="icon-cog"></i> <?= $Lang->get('SETTINGS') ?></h3>
				</div>

				<div class="well no-padding">
					<?= $this->Form->create(false, array(
						'class' => 'form-horizontal'
					)); ?>
						<?php 
						$config = $this->Configuration->get_all();
						$config = $config['Configuration'];
						foreach ($config as $key => $value) { ?>
							<?php if(strpos($key, 'maintenance') === false AND strpos($key, 'id') === false AND strpos($key, 'layout') === false AND strpos($key, 'theme') === false AND strpos($key, 'server_') === false) { ?>
								<?php if(strpos($key, 'version') === false AND $key != 'mineguard' AND strpos($key, 'banner_server') === false) { ?>
									<div class="control-group">
										<label class="control-label"><?= $Lang->get(strtoupper($key)) ?></label>
										<div class="controls">
											<?= $this->Form->input(false, array(
												'div' => false,
											    'type' => 'text',
							    				'name' => $key,
							    				'class' => 'span6 m-wrap',
							    				'value' => $value
											)); ?>
											<?php if($key == "lang") { ?>
												<span class="help-inline"><?= $Lang->get('AVAILABLE') ?> : fr, en. <?= $Lang->get('DEFAULT') ?> : fr.</span>
											<?php } ?>
										</div>
									</div>
								<?php } elseif($key == 'mineguard') { ?>
									<div class="control-group">
										<label class="control-label"><?= $Lang->get('MINEGUARD') ?></label>
										<div class="controls">
											<label class="radio">
												<input type="radio" name="mineguard" value="true" <?php if($value == 'true') { echo 'checked=""'; } ?>>
											  <?= $Lang->get('ENABLED') ?>
											</label>
											<label class="radio">
												<input type="radio" name="mineguard" value="false" <?php if($value == 'false') { echo 'checked=""'; } ?>>
											  <?= $Lang->get('DISABLED') ?>
											</label>
										</div>
									</div>
								<?php } elseif($key == 'banner_server') { ?>
									<div class="control-group">
										<label class="control-label"><?= $Lang->get('BANNER_SERVER_CHOOSE') ?></label>
										<div class="controls">
											<?php
												echo $this->Form->input('field', array(
														'multiple' => true,
													  	'label' => false,
													  	'div' => false,
													  	'name' => 'banner_server',
												      	'options' => $servers,
												      	'selected' => $selected_server
												  	));
											?>
										</div>
									</div>
								<?php } else { ?>
									<div class="control-group">
										<label class="control-label"><?= $Lang->get(strtoupper($key)) ?></label>
										<div class="controls">
											<?= $this->Form->input(false, array(
												'div' => false,
											    'type' => 'text',
							    				'name' => $key,
							    				'disabled' => 'disabled',
							    				'class' => 'span6 m-wrap',
							    				'placeholder' => $value
											)); ?>
										</div>
									</div>
								<?php } ?>
							<?php } ?>
						<?php } ?>

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