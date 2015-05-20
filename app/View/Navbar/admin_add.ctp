<?php 
 
$this->EyPlugin = new EyPluginComponent;
$this->Navbar = new NavbarComponent;
?>
<div class="row-fluid">

	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-cog"></i> <?= $Lang->get('ADD_NAV') ?></h3>
		</div>

		<div class="well no-padding">
			<div class="ajax-msg"></div>

			<?php 
			echo $this->Form->create('Navbar', array(
				'class' => 'form-horizontal',
				'id' => 'nav_add'
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
					<label class="control-label"><?= $Lang->get('TYPE') ?></label>
					<div class="controls">
						<label class="radio">
							<input type="radio" id="normal" name="type" value="normal">
						  <?= $Lang->get('NORMAL') ?>
						</label>
						<label class="radio">
							<input type="radio" id="dropdown" name="type" value="dropdown">
						  <?= $Lang->get('DROPDOWN') ?>
						</label>
					</div>
				</div>
				
				<div id="type-normal" class="hidden">
					<div class="control-group">
						<label class="control-label"><?= $Lang->get('URL') ?></label>
						<div class="controls">
							<label class="radio">
								<input type="radio" class="type_plugin" name="url_type" value="plugin">
							  <?= $Lang->get('PLUGIN') ?>
							</label>
							<div class="hidden plugin">
								<select name="url_plugin">
									<?php foreach ($url_plugins as $key => $value) { ?>
								  		<option value="<?= $this->Html->url(array('controller' => $key, 'action' => 'index', 'admin' => false)) ?>"><?= $value ?></option>
								  	<?php } ?>
								</select>
							</div>
							<label class="radio">
								<input type="radio" class="type_page" name="url_type" value="page">
							  <?= $Lang->get('PAGE') ?>
							</label>
							<div class="hidden page">
								<select name="url_page">
								  	<?php foreach ($url_pages as $key => $value) { ?>
								  		<option value="<?= $this->Html->url(array('controller' => 'p', 'action' => $key, 'admin' => false, 'plugin' => false)) ?>"><?= $value ?></option>
								  	<?php } ?>
								</select>
							</div>
							<label class="radio">
								<input type="radio" class="type_custom" name="url_type" value="custom">
							  <?= $Lang->get('CUSTOM') ?>
							</label>
							<input type="text" class="span6 m-wrap hidden custom" placeholder="<?= $Lang->get('YOUR_URL') ?>" name="url_custom">
						</div>
					</div>
				</div>

				<div id="type-dropdown" class="hidden">
					<div class="control-group">
						<div class="well" id="nav-1">
							<div class="control-group">
								<label class="control-label"><?= $Lang->get('NAME_OF_NAV') ?></label>
								<div class="controls">
									<?php 
									echo $this->Form->input('', array(
									    'type' => 'text',
									    'name' => 'name_of_nav',
									    'class' => 'span6 m-wrap name_of_nav',
									));
									?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label"><?= $Lang->get('URL') ?></label>
								<div class="controls">
									<input type="text" class="span6 m-wrap url_of_nav" placeholder="<?= $Lang->get('YOUR_URL') ?>" name="url">
								</div>
							</div>
						</div>
					</div>
					<div id="add-js" data-number="1"></div>
					<div class="control-group">
						<a href="#" id="add_nav" class="btn btn-success"><?= $Lang->get('ADD_NAV') ?></a>
					</div>
				</div>


				<div class="form-actions">
					<?php
					echo $this->Form->button($Lang->get('SUBMIT'), array(
						'type' => 'submit',
						'class' => 'btn btn-primary', 
						'style' => 'float:right;'
					));
					?>
				</div>

			</form>        

		</div>

	</div>

</div>
<script type="text/javascript">
	$('#add_nav').click(function(e) {
		e.preventDefault();
		var how = $('#add-js').attr('data-number');
		how = parseInt(how) + 1;
		var before = $('#add-js').html();
		var add = before+'<div class="control-group"><div class="well" id="nav-'+how+'"><div class="control-group"><label class="control-label"><?= addslashes($Lang->get('NAME_OF_NAV')) ?></label><div class="controls"><?php echo $this->Form->input('', array('type' => 'text','name' => 'name_of_nav','class' => 'span6 m-wrap name_of_nav',));?></div></div><div class="control-group"><label class="control-label"><?= $Lang->get('URL') ?></label><div class="controls"><input type="text" class="span6 m-wrap url_of_nav" placeholder="<?= $Lang->get('YOUR_URL') ?>" name="url"></div></div></div></div>'
		$('#add-js').html(add);
		$('#add-js').attr('data-number', how);
	});
</script>
<script type="text/javascript">
	$("#normal").change(function() {
		if($("#normal").is(':checked')) {
			$("#type-normal").removeClass('hidden');
			$("#type-dropdown").addClass('hidden');
		} else {
			$("#type-normal").addClass('hidden');
			$("#type-dropdown").removeClass('hidden');
		}
	});
	$("#dropdown").change(function() {
		if($("dropdown").is(':checked')) {
			$("#type-dropdown").addClass('hidden');
			$("#type-normal").removeClass('hidden');
		} else {
			$("#type-dropdown").removeClass('hidden');
			$("#type-normal").addClass('hidden');
		}
	});

	$(".type_plugin").change(function() {
		if($(".type_plugin").is(':checked')) {
			$(".page").addClass('hidden');
			$(".custom").addClass('hidden');
			$(".plugin").removeClass('hidden');
		} else {
			$(".plugin").addClass('hidden');
		}
	});

	$(".type_page").change(function() {
		if($(".type_page").is(':checked')) {
			$(".page").removeClass('hidden');
			$(".custom").addClass('hidden');
			$(".plugin").addClass('hidden');
		} else {
			$(".page").addClass('hidden');
		}
	});

	$(".type_custom").change(function() {
		if($(".type_custom").is(':checked')) {
			$(".page").addClass('hidden');
			$(".custom").removeClass('hidden');
			$(".plugin").addClass('hidden');
		} else {
			$(".custom").addClass('hidden');
		}
	});
</script>
<script type="text/javascript">
    $("#nav_add").submit(function( event ) {
    	event.preventDefault();
        var $form = $( this );
        var name = $form.find("input[name='name']").val();
        var type = $form.find("input[type='radio'][name='type']:checked").val();
        if(type == "normal") {
	        if($form.find("input[name='url_type']:checked").val() == "custom") {
	        	var url = $form.find("input[name='url_custom']").val();
	        } else if($form.find("input[name='url_type']:checked").val() == "plugin") {
	        	var url = $form.find("select[name='url_plugin']").val();
	        } else if($form.find("input[name='url_type']:checked").val() == "page") {
	        	var url = $form.find("select[name='url_page']").val();
	        } else {
	        	var url = "undefined";
	        }
	    } else {
	    	var names = $('.name_of_nav').serialize();
	    	names = names.split('&');
	    	var urls = $('.url_of_nav').serialize();
	    	urls = urls.split('&');
	    	var url = {};
	    	var test = "success"
		    for (var key in test = names)
		    { 
		    	var l = test[key].split('=');
		    	l = l[1];
		    	console.log(l);
		    	var p = urls[key].split('=');
		    	p = p[1];
		    	url[l] = p;
		    }
		    console.log(url);
	    }
        $.post("<?= $this->Html->url(array('controller' => 'navbar', 'action' => 'add_ajax', 'admin' => true)) ?>", { name : name, type : type, url : url }, function(data) {
          	data2 = data.split("|");
		  	if(data.indexOf('true') != -1) {
          		$('.ajax-msg').empty().html('<div class="alert alert-success" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
          		 document.location.href="<?= $this->Html->url(array('controller' => 'navbar', 'action' => 'admin_index', 'admin' => 'true')) ?>";
          	} else if(data.indexOf('false') != -1) {
            	$('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
	        } else {
		    	$('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
		    }
        });
        return false;
    });
</script>