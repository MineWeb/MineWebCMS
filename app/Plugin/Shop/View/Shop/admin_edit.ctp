<?php 
 
?>
<div class="row-fluid">

	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-cog"></i> <?= $Lang->get('EDIT_ITEM') ?></h3>
		</div>

		<div class="well no-padding">
			<div class="ajax-msg"></div>

			<?php 
			echo $this->Form->create('Shop_Item', array(
				'class' => 'form-horizontal',
				'id' => 'edit_item'
			)); 
			?>
				<input type="hidden" name="id" value="<?= $item['id'] ?>">
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('NAME') ?></label>
					<div class="controls">
						<?php 
							echo $this->Form->input('', array(
						   		'type' => 'text',
						   		'name' => 'name',
						    	'class' => 'span6 m-wrap',
						    	'value' => $item['name'],
							));
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('DESCRIPTION') ?></label>
					<div class="controls">
						<?php 
							echo $this->Form->textarea('', array(
						   		'type' => 'text',
						   		'name' => 'description',
						    	'class' => 'span6 m-wrap',
						    	'value' => $item['description'],
							));
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('CATEGORY') ?></label>
					<div class="controls">
						<?php
							echo $this->Form->input('field', array(
								  'label' => false,
								  'div' => false,
								  'name' => 'category',
							      'options' => $categories,
							      'empty' => $item['category']
							  	));
						?>
					</div>
				</div>
				<input type="hidden" name="category_default" value="<?= $item['category'] ?>">
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('PRICE') ?></label>
					<div class="controls">
						<?php 
							echo $this->Form->input('', array(
						   		'type' => 'text',
						   		'name' => 'price',
						    	'class' => 'span6 m-wrap',
						    	'value' => $item['price'],
							));
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('IMG_URL') ?></label>
					<div class="controls">
						<?php 
							echo $this->Form->input('', array(
						   		'type' => 'text',
						   		'name' => 'img_url',
						    	'class' => 'span6 m-wrap',
						    	'value' => $item['img_url'],
							));
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('COMMANDS') ?></label>
					<div class="controls">
						<?php 
							echo $this->Form->input('', array(
								'div' => false,
								'label' => false,
						   		'type' => 'text',
						   		'name' => 'commands',
						    	'class' => 'span6 m-wrap',
						    	'value' => $item['commands'],
							));
						?>
						<span class="help-inline"><b>{PLAYER}</b> = Pseudo <br> <b>[{+}]</b> <?= $Lang->get('FOR_NEW_COMMAND') ?> <br><b><?= $Lang->get('EXAMPLE') ?>:</b> <i>give {PLAYER} 1 1[{+}]broadcast {PLAYER} ...</i></span>
					</div>
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
    $("#edit_item").submit(function( event ) {
    	event.preventDefault();
        var $form = $( this );
        var id = $form.find("input[name='id']").val();
        var name = $form.find("input[name='name']").val();
        var description = $form.find("textarea[name='description']").val();
        var category = $form.find("select[name='category']").val();
        var category_default = $form.find("input[name='category_default']").val();
        var price = $form.find("input[name='price']").val();
        var img_url = $form.find("input[name='img_url']").val();
        var commands = $form.find("input[name='commands']").val();
        $.post("<?= $this->Html->url(array('controller' => 'shop', 'action' => 'edit_ajax', 'admin' => true)) ?>", { id : id, name : name, description : description, category : category, category_default : category_default, price : price, img_url : img_url, commands : commands }, function(data) {
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