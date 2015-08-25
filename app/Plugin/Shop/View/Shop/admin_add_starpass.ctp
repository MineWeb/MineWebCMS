<?php 
 
$this->Configuration = new ConfigurationComponent;
?>
<div class="row-fluid">

	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-cog"></i> <?= $Lang->get('ADD_OFFER_STARPASS') ?></h3>
		</div>
		<div class="well">
			<p><b>URL 1 :</b> <?= $this->Html->url(array('controller' => 'shop', 'action' => 'starpass', 'plugin' => 'shop', 'admin' => false), true) ?></p>
			<p><b>URL 2 :</b> <?= $this->Html->url(array('controller' => 'shop', 'action' => 'starpass_verif', 'plugin' => 'shop', 'admin' => false), true) ?></p>
			<p><b>URL 3 :</b> <?= $this->Html->url(array('controller' => 'shop', 'action' => 'starpass', 'plugin' => 'shop', 'admin' => false, 'error'), true) ?></p>
		</div>
		<div class="well no-padding">
			<div class="ajax-msg"></div>

			<?php 
			echo $this->Form->create('Starpass', array(
				'class' => 'form-horizontal',
				'id' => 'add_starpass'
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
							));
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('IDD') ?></label>
					<div class="controls">
						<?php 
							echo $this->Form->input('', array(
						   		'type' => 'text',
						   		'name' => 'idd',
						    	'class' => 'span6 m-wrap',
							));
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('IDP') ?></label>
					<div class="controls">
						<?php 
							echo $this->Form->input('', array(
						   		'type' => 'text',
						   		'name' => 'idp',
						    	'class' => 'span6 m-wrap',
							));
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('HOW_MONEY_OFFER_PAYPAL') ?> <?= $this->Configuration->get_money_name() ?></label>
					<div class="controls">
						<?php 
							echo $this->Form->input('', array(
						   		'type' => 'text',
						   		'name' => 'money',
						    	'class' => 'span6 m-wrap',
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
					<a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'index', 'admin' => true)) ?>" class="btn"><?= $Lang->get('CANCEL') ?></a>  
				</div>        

		</div>

	</div>

</div>
<script type="text/javascript">
    $("#add_starpass").submit(function( event ) {
    	event.preventDefault();
        var $form = $( this );
        var name = $form.find("input[name='name']").val();
        var idd = $form.find("input[name='idd']").val();
        var idp = $form.find("input[name='idp']").val();
        var money = $form.find("input[name='money']").val();
        $.post("<?= $this->Html->url(array('controller' => 'shop', 'action' => 'add_starpass_ajax', 'admin' => true)) ?>", { name : name, idd : idd, idp : idp, money : money }, function(data) {
          	data2 = data.split("|");
		  	if(data.indexOf('true') != -1) {
          		$('.ajax-msg').empty().html('<div class="alert alert-success" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
          		 document.location.href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'index', 'admin' => 'true')) ?>";
          	} else if(data.indexOf('false') != -1) {
            	$('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
	        } else {
		    	$('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
		    }
        });
        return false;
    });
</script>