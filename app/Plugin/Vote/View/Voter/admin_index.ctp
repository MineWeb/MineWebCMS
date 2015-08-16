<?php   ?>
<div class="row-fluid">

	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-cog"></i> <?= $Lang->get('VOTE_TITLE') ?>&nbsp;&nbsp;&nbsp;<a href="<?= $this->Html->url(array('controller' => 'voter', 'action' => 'reset', 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('RESET') ?></a></h3>
		</div>

		<div class="well no-padding">
			<div class="ajax-msg"></div>

			<?php 
			echo $this->Form->create('Vote', array(
				'class' => 'form-horizontal',
				'id' => 'reward_add'
			)); 
			?>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('TIME_VOTE') ?></label>
					<div class="controls">
						<?php 
							echo $this->Form->input('', array(
						   		'type' => 'text',
						   		'name' => 'time_vote',
						    	'class' => 'span6 m-wrap',
						    	'value' => @$vote['time_vote'],
						    	'placeholder' => 'minutes'
							));
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('PAGE_VOTE') ?></label>
					<div class="controls">
						<?php 
							echo $this->Form->input('', array(
						   		'type' => 'text',
						   		'name' => 'page_vote',
						    	'class' => 'span6 m-wrap',
						    	'value' => @$vote['page_vote'],
							));
						?>
					</div>
				</div>
				<?php /*
				<div id="useRPG" style="display:none;">
					<div class="control-group">
						<label class="control-label"><?= $Lang->get('ID_VOTE') ?></label>
						<div class="controls">
							<?php 
								echo $this->Form->input('', array(
							   		'type' => 'text',
							   		'name' => 'id_vote',
							    	'class' => 'span6 m-wrap',
							    	'value' => @$vote['id_vote'],
								));
							?>
						</div>
					</div>
				</div> */ ?>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('REWARDS_TYPE') ?></label>
					<div class="controls">
						<?php
							if(@$vote['rewards_type'] == 0) {
								$options = array('0' => $Lang->get('RANDOM'), '1' => $Lang->get('ALL'));
							} else {
								$options = array('1' => $Lang->get('ALL'), '0' => $Lang->get('RANDOM'));
							}

							echo $this->Form->input('field', array(
								  'label' => false,
								  'div' => false,
								  'name' => 'rewards_type',
							      'options' => $options,
							  	));
						?>
					</div>
				</div>
				<div class="control-group">
					<?php if(!empty($vote['rewards'])) { ?>
						<?php $i = 0; foreach ($vote['rewards'] as $k => $v) { $i++; ?>
							<div class="well" id="reward-<?= $i ?>">
								<div class="control-group">
									<label class="control-label"><?= $Lang->get('REWARD_TYPE') ?></label>
									<div class="controls">
										<select name="type_reward" class="reward_type">
											<?php if($v['type'] == "money") { ?>
												<option value="money"><?= $Lang->get('MONEY') ?></option>
												<option value="server"><?= $Lang->get('SERVER') ?></option>
											<?php } else { ?>
												<option value="server"><?= $Lang->get('SERVER') ?></option>
												<option value="money"><?= $Lang->get('MONEY') ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label"><?= $Lang->get('REWARD_VALUE') ?></label>
									<div class="controls">
										<?php 
										if($v['type'] == "money") {
											$reward_value = $v['how'];
										} else {
											$reward_value = $v['command'];
										}
										echo $this->Form->input('', array(
										    'type' => 'text',
										    'name' => 'reward_value',
										    'class' => 'span6 m-wrap reward_value',
										    'placeholder' => $Lang->get('CMD_OR_MONEY'),
										    'value' => $reward_value
										));
										?>
									</div>
								</div>
							</div>
						<?php } ?>
					<?php } else { $i = 1; ?>
						<div class="well" id="reward-1">
							<div class="control-group">
								<label class="control-label"><?= $Lang->get('REWARD_TYPE') ?></label>
								<div class="controls">
									<select name="type_reward" class="reward_type">
										<option value="money"><?= $Lang->get('MONEY') ?></option>
										<option value="server"><?= $Lang->get('SERVER') ?></option>
									</select>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label"><?= $Lang->get('REWARD_VALUE') ?></label>
								<div class="controls">
									<?php 
									echo $this->Form->input('', array(
									    'type' => 'text',
									    'name' => 'reward_value',
									    'class' => 'span6 m-wrap reward_value',
									    'placeholder' => $Lang->get('CMD_OR_MONEY')
									));
									?>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
				<div id="add-js" data-number="<?= $i ?>"></div>
				<div class="control-group">
					<a href="#" id="add_reward" class="btn btn-success"><?= $Lang->get('ADD_REWARD') ?></a>
				</div>
				<br><br>

				<div class="form-actions">
					<?php
					echo $this->Form->button($Lang->get('SUBMIT'), array(
						'type' => 'submit',
						'class' => 'btn btn-primary'
					));
					?>
					<a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'index', 'admin' => true)) ?>" class="btn"><?= $Lang->get('CANCEL') ?></a>  
				</div>        

		</div>

	</div>

</div>
<script type="text/javascript">
	$('#add_reward').click(function(e) {
		e.preventDefault();
		var how = $('#add-js').attr('data-number');
		how = parseInt(how) + 1;
		var before = $('#add-js').html();
		var add = before+'<div class="control-group"><div class="well" id="reward-'+how+'"><div class="control-group"><label class="control-label"><?= $Lang->get('REWARD_TYPE') ?></label><div class="controls"><select name="type_reward" class="reward_type"><option value="money"><?= $Lang->get('MONEY') ?></option><option value="server"><?= $Lang->get('SERVER') ?></option></select></div></div><div class="control-group"><label class="control-label"><?= $Lang->get('REWARD_VALUE') ?></label><div class="controls"><?php echo $this->Form->input('', array('type' => 'text','name' => 'reward_value','class' => 'span6 m-wrap reward_value','placeholder' => $Lang->get('CMD_OR_MONEY')));?></div></div></div></div>';
		$('#add-js').html(add);
		$('#add-js').attr('data-number', how);
	});
</script>
<script type="text/javascript">
    $("#reward_add").submit(function( event ) {
    	event.preventDefault();
        var $form = $( this );
        var time_vote = $form.find("input[name='time_vote']").val();
        var page_vote = $form.find("input[name='page_vote']").val();
        /*var id_vote = $form.find("input[name='id_vote']").val();*/
        var rewards_type = $form.find("select[name='rewards_type']").val();

	    	var reward_type = $('.reward_type').serialize();
	    	reward_type = reward_type.split('&');
	    	var reward_value = $('.reward_value').serialize();
	    	reward_value = reward_value.split('&');
	    	/*var rewards = {};
	    	var test = "success"
		    for (var key in test = reward_type)
		    { 
		    	var l = test[key].split('=');
		    	l = l[1];
		    	var p = reward_value[key].split('=');
		    	p = p[1];
		    	rewards[l] = p;
		    }*/

        $.post("<?= $this->Html->url(array('controller' => 'voter', 'action' => 'add_ajax', 'admin' => true)) ?>", { time_vote : time_vote, page_vote : page_vote/*, id_vote : id_vote*/, rewards_type : rewards_type, reward_type : reward_type, reward_value : reward_value }, function(data) {
          	data2 = data.split("|");
		  	if(data.indexOf('true') != -1) {
          		$('.ajax-msg').empty().html('<div class="alert alert-success" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
          		 document.location.href="<?= $this->Html->url(array('controller' => 'voter', 'action' => 'admin_index', 'admin' => 'true')) ?>";
          	} else if(data.indexOf('false') != -1) {
            	$('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
	        } else {
		    	$('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
		    }
        });
        return false;
    });
</script>