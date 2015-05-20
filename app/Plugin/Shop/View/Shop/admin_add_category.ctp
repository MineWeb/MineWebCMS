<?php require '../../config/lang.php' ?>
<div class="row-fluid">

	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-cog"></i> <?= $Lang->get('ADD_CATEGORY') ?></h3>
		</div>

		<div class="well no-padding">

			<?php 
			echo $this->Form->create('Category', array(
				'class' => 'form-horizontal'
			)); 
			?>
				
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('NAME') ?></label>
					<div class="controls">
						<?php 
							echo $this->Form->input('', array(
						   		'type' => 'text',
						   		'name' => 'name',
						    	'class' => 'span6 m-wrap',
						    	'maxlength' => '50'
							));
						?>
					</div>
				</div>

				<div class="form-actions">
					<?php
					echo $this->Form->button($Lang->get('SUBMIT'), array(
						'type' => 'submit',
						'class' => 'btn btn-primary'
					));
					?>
					<a href="../../" class="btn"><?= $Lang->get('CANCEL') ?></a>  
				</div>        

		</div>

	</div>

</div>