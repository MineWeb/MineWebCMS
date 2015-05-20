<?php 
 
?>
<div class="row-fluid">

	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-cog"></i> <?= $Lang->get('ADD_VOUCHER') ?></h3>
		</div>

		<div class="well no-padding">

			<?php 
			echo $this->Form->create('Coupon', array(
				'class' => 'form-horizontal',
				'id' => 'add_voucher'
			)); 
			?>

			<div class="ajax-msg"></div>
				
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('CODE') ?></label>
					<div class="controls">
						<div class="input-append">
							<?php 
								echo $this->Form->input('', array(
									'div' => false,
									'label' => false,
							   		'type' => 'text',
							   		'name' => 'code',
							   		'id' => 'random',
							    	'class' => 'no-margin span6 m-wrap',
							    	'placeholder' => $Lang->get('CODE'),
							    	'maxlength' => '20',
							    	'style' => 'width: 470px;'
								));
							?>
							<button class="btn btn-info" type="button" onClick="$('#random').val(random_code(10))"><?= $Lang->get('GENERATE_CODE') ?></button>
							<script type="text/javascript">
								function random_code(nbcar) {
								  var ListeCar = new Array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","0","1","2","3","4","5","6","7","8","9");
								  var Chaine ='';
								  for(i = 0; i < nbcar; i++)
								  {
								    Chaine = Chaine + ListeCar[Math.floor(Math.random()*ListeCar.length)];
								  }
								  return Chaine;
								}
							</script>
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('EFFECTIVE_ON') ?></label>
					<div class="controls">
						<?php
							echo $this->Form->input('field', array(
								  'label' => false,
								  'div' => false,
								  'name' => 'effective_on',
							      'options' => array('categories' => $Lang->get('CATEGORIES'), 'items' => $Lang->get('ITEMS'), 'all' => $Lang->get('ALL')),
							      'empty' => $Lang->get('CHOOSE_OPTION'),
							      'onChange' => 'hide_or_not(this.value)',
							  	));
						?>
					</div>
				</div>
				<div id="hidden_items" style="display:none;">
					<div class="control-group">
						<label class="control-label"><?= $Lang->get('CHOOSE_ITEM') ?></label>
						<div class="controls">
							<?php
								echo $this->Form->input('field', array(
									  'label' => false,
									  'div' => false,
									  'id' => 'effective_on_item',
								      'options' => $items,
								      'data-placeholder' => $Lang->get('CHOOSE_ITEM'),
								      'multiple' => 'multiple'
								  	));
							?>
						</div>
					</div>
				</div>
				<div id="hidden_categories" style="display:none;">
					<div class="control-group">
						<label class="control-label"><?= $Lang->get('CHOOSE_CATEGORY') ?></label>
						<div class="controls">
							<?php
								echo $this->Form->input('field', array(
									  'label' => false,
									  'div' => false,
									  'id' => 'effective_on_categorie',
								      'options' => $categories,
								      'data-placeholder' => $Lang->get('CHOOSE_CATEGORY'),
								      'multiple' => 'multiple'
								  	));
							?>
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('TYPE') ?></label>
					<div class="controls">
						<?php
							echo $this->Form->input('field', array(
								  'label' => false,
								  'div' => false,
								  'name' => 'type',
							      'options' => array(2 => $Configuration->get_money_name(false, true), 1 => $Lang->get('PERCENTAGE')),
							      'empty' => $Lang->get('CHOOSE_TYPE')
							  	));
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('REDUCTION') ?></label>
					<div class="controls">
						<?php 
							echo $this->Form->input('', array(
								'div' => false,
								'label' => false,
						   		'type' => 'text',
						   		'name' => 'reduction',
						    	'class' => 'span6 m-wrap',
						    	'placeholder' => $Lang->get('IN').' '.$Configuration->get_money_name(false, true).' '.$Lang->get('OR_PERCENTAGE')
							));
						?>
						<span class="help-inline">Ex: 10</span>
					</div>
				</div>
				<?php /* Pour une prochaine version :) ?>
				<div class="control-group">
					<label class="control-label">Limite</label>
					<div class="controls">
						<?php 
							echo $this->Form->input('', array(
								'div' => false,
								'label' => false,
						   		'type' => 'text',
						   		'name' => 'limit_per_ip',
						    	'class' => 'span6 m-wrap',
						    	'placeholder' => 'Limite d\'utilisation par IP'
							));
						?>
						<span class="help-inline">Mettre 0 pour que il n'y est pas de restriction</span>
					</div>
				</div><?php */ ?>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('AFFICH') ?></label>
					<div class="controls">
						<?= $this->Form->checkbox(false, array(
								'div' => false,
								'value' => '1',
								'id' => 'affich'
							)) ?>
						<span class="help-inline"><?= $Lang->get('AFFICH_ON_SHOP') ?></span>
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
<script type="text/javascript">
	$('#affich').change(function(){
		if($('#affich').is(':checked')) {
			$('#affich').attr('value', '0');
		} else {
			$('#affich').attr('value', '1');
		}
	});
	function hide_or_not(val) {
		$("#hidden_categories").css("display", "none");
			$("#hidden_items").css("display", "block");
		if(val=="categories") {
			$("#hidden_items").css("display", "none");
			$("#hidden_categories").css("display", "block");
		}
		if(val=="items") {
			$("#hidden_categories").css("display", "none");
			$("#hidden_items").css("display", "block");
		} 
		if(val=="all") {
			$("#hidden_categories").css("display", "none");
			$("#hidden_items").css("display", "none");
		} 
	}

	$("#add_voucher").submit(function( event ) {
    	event.preventDefault();
        var $form = $( this );
        var code = $form.find("input[name='code']").val();
        var effective_on = $form.find("select[name='effective_on']").val();
        var effective_on_item = $form.find("#effective_on_item").val();
        var effective_on_categorie = $form.find("#effective_on_categorie").val();
        var type = $form.find("select[name='type']").val();
        var reduction = $form.find("input[name='reduction']").val();
        var affich = $form.find("#affich").val();
        $.post("<?= $this->Html->url(array('controller' => 'shop', 'action' => 'admin_add_voucher_ajax', 'admin' => true)) ?>", { code : code, effective_on : effective_on, effective_on_item : effective_on_item, effective_on_categorie : effective_on_categorie, type : type, reduction : reduction, affich : affich }, function(data) {
          	data2 = data.split("|");
		  	if(data.indexOf('true') != -1) {
          		$('.ajax-msg').empty().html('<div class="alert alert-success" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
          		 document.location.href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'admin_index', 'admin' => 'true')) ?>";
          	} else if(data.indexOf('false') != -1) {
            	$('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
	        } else {
		    	$('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
		    }
        });
        return false;
    });
</script>