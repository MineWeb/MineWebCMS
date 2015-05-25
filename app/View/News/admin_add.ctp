<?php require '../../config/lang.php' ?>
<div class="row-fluid">

	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-cog"></i> <?= $Lang->get('ADD_NEWS') ?></h3>
		</div>

		<div class="well no-padding">
			<div class="ajax-msg"></div>

			<?php 
			echo $this->Form->create('News', array(
				'class' => 'form-horizontal',
				'id' => 'news_add'
			)); 
			?>
				
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('TITLE') ?></label>
					<div class="controls">
						<?php 
						$this->Form->unlockField('News.content');
						echo $this->Form->input('', array(
						    'type' => 'text',
						    'name' => 'title',
						    'class' => 'span6 m-wrap',
						    'placeholder' => $Lang->get('TITLE')
						));
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?= $Lang->get('SLUG') ?></label>
					<div class="controls">
						<?php 
						$this->Form->unlockField('Page.content');
						echo $this->Form->input('', array(
							'label' => false,
							'div' => false,
						    'type' => 'text',
						    'name' => 'slug',
						    'class' => 'span6 m-wrap',
						    'style' => 'display:inline-block;',
						    'id' => 'slug'
						));
						?>
						<a href="#" id="generate_slug" class="btn btn-info"><?= $Lang->get('GENERATE') ?></a>
					</div>
				</div>
				<div class="control-group">
				<?= $this->Html->script('admin/tinymce/tinymce.min.js') ?>
				<script type="text/javascript">
				tinymce.init({
				    selector: "textarea",
				    height : 300,
				    width : '100%',
				    language : 'fr_FR',
				    plugins: "textcolor code",
    				toolbar: "fontselect fontsizeselect bold italic underline strikethrough forecolor backcolor alignleft aligncenter alignright alignjustify cut copy paste bullist numlist outdent indent blockquote code"
				 });
				</script>
				<?php
				$this->Form->unlockField('News.content');
				echo $this->Form->textarea('News.content');
				?>
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
    $("#news_add").submit(function( event ) {
    	event.preventDefault();
        var $form = $( this );
        var title = $form.find("input[name='title']").val();
        var content = tinymce.get('NewsContent').getContent();
        var slug = $form.find("input[name='slug']").val();
		slug = string_to_slug(slug);
        $.post("<?= $this->Html->url(array('controller' => 'news', 'action' => 'add_ajax', 'admin' => true)) ?>", { title : title, content : content, slug : slug }, function(data) {
          	data2 = data.split("|");
		  	if(data.indexOf('true') != -1) {
          		$('.ajax-msg').empty().html('<div class="alert alert-success" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
          		 document.location.href="<?= $this->Html->url(array('controller' => 'news', 'action' => 'admin_index', 'admin' => 'true')) ?>";
          	} else if(data.indexOf('false') != -1) {
            	$('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
	        } else {
		    	$('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
		    }
        });
        return false;
    });

	function string_to_slug(str) {
	  str = str.replace(/^\s+|\s+$/g, ''); // trim
	  str = str.toLowerCase();
	  
	  // remove accents, swap ñ for n, etc
	  var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
	  var to   = "aaaaeeeeiiiioooouuuunc------";
	  for (var i=0, l=from.length ; i<l ; i++) {
	    str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
	  }

	  str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
	    .replace(/\s+/g, '-') // collapse whitespace and replace by -
	    .replace(/-+/g, '-'); // collapse dashes

	  return str;
	}

	$("#generate_slug").click(function( event ) {
	    event.preventDefault();
	    var $form = $( this );
        var title = $('body').find("input[name='title']").val();
		$('#slug').val(string_to_slug(title));
	    return false;
	});
</script>