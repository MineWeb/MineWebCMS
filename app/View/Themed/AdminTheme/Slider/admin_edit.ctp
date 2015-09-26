<div class="row-fluid">

	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-cog"></i> <?= $Lang->get('EDIT_SLIDER') ?></h3>
		</div>

		<div class="well no-padding">
			<div class="ajax-msg"></div>

			<?php 
			echo $this->Form->create('Slider', array(
				'class' => 'form-horizontal',
				'id' => 'slider_edit'
			)); 
			?>
				<input type="hidden" name="id" value="<?= $slider['id'] ?>">
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('TITLE') ?></label>
					<div class="controls">
						<?php 
						echo $this->Form->input('', array(
						    'type' => 'text',
						    'name' => 'title',
						    'class' => 'span6 m-wrap',
						    'value' => $slider['title']
						));
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('SUBTITLE') ?></label>
					<div class="controls">
						<?php 
						echo $this->Form->input('', array(
						    'type' => 'text',
						    'name' => 'subtitle',
						    'class' => 'span6 m-wrap',
						    'value' => $slider['subtitle']
						));
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('URL_IMG') ?></label>
					<div class="controls">
						<?php 
						echo $this->Form->input('', array(
						    'type' => 'text',
						    'name' => 'url_img',
						    'class' => 'span6 m-wrap',
						    'value' => $slider['url_img']
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
				</div>

			</form>        

		</div>

	</div>

</div>
<script type="text/javascript">
    $("#slider_edit").submit(function( event ) {
    	event.preventDefault();
        var $form = $( this );
        var id = $form.find("input[name='id']").val();
        var title = $form.find("input[name='title']").val();
        var subtitle = $form.find("input[name='subtitle']").val();
        var url_img = $form.find("input[name='url_img']").val();
        $.post("<?= $this->Html->url(array('controller' => 'slider', 'action' => 'edit_ajax', 'admin' => true)) ?>", { id : id, title : title, subtitle : subtitle, url_img : url_img }, function(data) {
          	data2 = data.split("|");
		  	if(data.indexOf('true') != -1) {
          		$('.ajax-msg').empty().html('<div class="alert alert-success" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
          		 document.location.href="<?= $this->Html->url(array('controller' => 'slider', 'action' => 'admin_index', 'admin' => 'true')) ?>";
          	} else if(data.indexOf('false') != -1) {
            	$('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
	        } else {
		    	$('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
		    }
        });
        return false;
    });
</script>