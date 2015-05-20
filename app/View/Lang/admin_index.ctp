<div class="row-fluid">

	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-cog"></i> <?= $Lang->get('LANG') ?></h3>
		</div>

		<div class="well no-padding">
			<?= $this->Form->create(false, array(
				'class' => 'form-horizontal'
			)); ?>
				<?php 
				$lang = $Lang->getall();
				foreach ($lang as $key => $value) { ?>
					<?php if($key != 'FOOTER_ADMIN' AND $key != 'COPYRIGHT') { ?>
						<div class="control-group">
							<label class="control-label"><?= explode('-', $key)[0] ?></label>
							<div class="controls" style="margin-left:300px">
								<?= $this->Form->input(false, array(
									'div' => false,
								    'type' => 'text',
				    				'name' => $key,
				    				'class' => 'span6 m-wrap',
				    				'value' => $value
								)); ?>
								<?php if($key == "FORMATE_DATE") { ?>
									<span class="help-inline"><?= $Lang->get('AVAILABLE_VARIABLES') ?> : {%day}, {%month}, {%year}, {%hour|24}, {%hour|12}, {%minutes}</span>
								<?php } ?>
								<?php if($key == "BANNER_SERVER") { ?>
									<span class="help-inline"><?= $Lang->get('AVAILABLE_VARIABLES') ?> : {MOTD}, {VERSION}, {ONLINE}, {ONLINE_LIMIT}</span>
								<?php } ?>
								<?php if($key == "VOTE_SUCCESS_SERVER") { ?>
									<span class="help-inline"><?= $Lang->get('AVAILABLE_VARIABLES') ?> : {PLAYER}.</span>
								<?php } ?>
							</div>
						</div>
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