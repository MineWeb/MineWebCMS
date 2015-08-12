<div class="row-fluid">

	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-cog"></i> <?= $Lang->get('EDIT_USER') ?></h3>
		</div>

		<div class="well no-padding">
			<div class="ajax-msg"></div>

			<?php 
			echo $this->Form->create('User', array(
				'class' => 'form-horizontal',
				'id' => 'edit_user'
			)); 
			?>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('PSEUDO') ?></label>
					<div class="controls">
						<input class="span6 m-wrap" type="text" name="pseudo" value="<?= $user['pseudo'] ?>" disabled="">
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('EMAIL') ?></label>
					<div class="controls">
						<?php 
							echo $this->Form->input('', array(
						   		'type' => 'email',
						   		'name' => 'mail',
						    	'class' => 'span6 m-wrap',
						    	'value' => $user['email'],
							));
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('PASSWORD') ?></label>
					<div class="controls">
						<?php 
							echo $this->Form->input('', array(
						   		'type' => 'password',
						   		'name' => 'password',
						    	'class' => 'span6 m-wrap',
						    	'value' => '',
							));
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('RANK') ?></label>
					<div class="controls">
				<?php 
					if($user['rank'] == 'member') {
						$user['rank'] = $Lang->get('MEMBER');
					} elseif($user['rank'] == 2) {
						$user['rank'] = $Lang->get('MODERATOR');
					} elseif($user['rank'] == 3) {
						$user['rank'] = $Lang->get('ADMINISTRATOR');
					} elseif($user['rank'] == 4) {
						$user['rank'] = $Lang->get('ADMINISTRATOR');
					} elseif($user['rank'] == 5) {
						$user['rank'] = $Lang->get('BANNED');
					} else {
						$user['rank'] = $Lang->get('UNDEFINED');
					}
					echo $this->Form->input('field', array(
					  'label' => false,
					  'div' => false,
					  'name' => 'rank',
				      'options' => array('member' => $Lang->get('MEMBER'), 2 => $Lang->get('MODERATOR'), 3 => $Lang->get('ADMINISTRATOR'), 5 => $Lang->get('BANNED')),
				      'empty' => $user['rank']
				  	));
				?>
					</div>
				</div>
				<?php if($this->EyPlugin->is_installed('Shop')) { ?>
					<div class="control-group">
						<label class="control-label"><?= $Lang->get('MONEY') ?></label>
						<div class="controls">
							<?php 
								echo $this->Form->input('', array(
							   		'type' => 'text',
							   		'name' => 'money',
							    	'class' => 'span6 m-wrap',
							    	'value' => $user['money'],
								));
							?>
						</div>
					</div>
				<?php } ?>
				<?php if($this->EyPlugin->is_installed('Vote')) { ?>
					<div class="control-group">
						<label class="control-label"><?= $Lang->get('NBR_OF_VOTE') ?></label>
						<div class="controls">
							<?php 
								echo $this->Form->input('', array(
							   		'type' => 'text',
							   		'name' => 'nbr_vote',
							    	'class' => 'span6 m-wrap',
							    	'value' => $user['vote'],
								));
							?>
						</div>
					</div>
				<?php } ?>

				<div class="control-group">
					<label class="control-label">IP</label>
					<div class="controls">
						<input class="span6 m-wrap" type="text" placeholder="<?= $user['ip'] ?>" disabled="">
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"><?= $Lang->get('CREATED_SIGNIN') ?></label>
					<div class="controls">
						<input class="span6 m-wrap" type="text" placeholder="<?= $user['created'] ?>" disabled="">
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

			<?php echo $this->Form->end(); ?>        

		</div>

	</div>

</div>
<script type="text/javascript">
    $("#edit_user").submit(function( event ) {
    	event.preventDefault();
        var $form = $( this );
        var pseudo = $form.find("input[name='pseudo']").val();
        var mail = $form.find("input[name='mail']").val();
		var password = $form.find("input[name='password']").val();
		var rank = $form.find("select[name='rank']").val();
		var money = $form.find("input[name='money']").val();
		var nbr_vote = $form.find("input[name='nbr_vote']").val();
        $.post("<?= $this->Html->url(array('controller' => 'user', 'action' => 'edit_ajax', 'admin' => true)) ?>", { pseudo : pseudo, email : mail, password : password, rank : rank, money : money, vote : nbr_vote }, function(data) {
          	data2 = data.split("|");
		  	if(data.indexOf('true') != -1) {
          		$('.ajax-msg').empty().html('<div class="alert alert-success" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
          		 document.location.href="<?= $this->Html->url(array('controller' => 'user', 'action' => 'admin_index', 'admin' => 'true')) ?>";
          	} else if(data.indexOf('false') != -1) {
            	$('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
	        } else {
		    	$('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
		    }
        });
        return false;
    });
</script>