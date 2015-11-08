<script>
	$("form").on("submit", function(e) {
		form = $(this);

		if(form.attr('data-ajax') === undefined || form.attr('data-ajax') == "false") {
			return;
		}

		e.preventDefault();

		if(form.find("input[type='submit']").length == 0) {
			var submit = form.find("button[type='submit']");
		} else {
			var submit = form.find("input[type='submit']");
		}

		if(form.find('.ajax-msg') === undefined || form.find('.ajax-msg').length == 0) {
			form.prepend('<div class="ajax-msg"></div>');
		}

		form.find('.ajax-msg').empty().html('<div class="alert alert-info"><a class="close" data-dismiss="alert">×</a><?= $Lang->get('LOADING') ?> ...</div>').fadeIn(500);

	    var submit_btn_content = form.find('button[type=submit]').html();
	    submit.html('<?= $Lang->get('LOADING') ?>...').attr('disabled', 'disabled').fadeIn(500);

	    // Data

	    var array = form.serialize();
	    array = array.split('&');

	   	form.find('input[type="checkbox"]').each(function(){
	   		if(!$(this).is(':checked')) {
	   			array.push($(this).attr('name')+'=off');
	   		}
        });

	    var inputs = {};

	    var i = 0;
	    for (var key in args = array)
	    { 
	    	input = args[i];
	    	input = input.split('=');
	    	input_name = input[0];

	    	if(form.find('input[name="'+input_name+'"]').attr('type') == "text" || form.find('input[name="'+input_name+'"]').attr('type') == "hidden" || form.find('input[name="'+input_name+'"]').attr('type') == "email" || form.find('input[name="'+input_name+'"]').attr('type') == "password") {
	    		inputs[input_name] = form.find('input[name="'+input_name+'"]').val(); // je récup la valeur comme ça pour éviter la sérialization
	    	} else if(form.find('input[name="'+input_name+'"]').attr('type') == "radio") {
	    		inputs[input_name] = form.find('input[name="'+input_name+'"][type="radio"]:checked').val();
	    	} else if(form.find('input[name="'+input_name+'"]').attr('type') == "checkbox") {
	    		if(form.find('input[name="'+input_name+'"]:checked').val() !== undefined) {
	    			inputs[input_name] = 1;
	    		} else {
	    			inputs[input_name] = 0;
	    		}
	    	} else if(form.find('textarea[name="'+input_name+'"]').attr('id') == "editor") {
	          	inputs[input_name] = tinymce.get('editor').getContent();
	        } else if(form.find('textarea[name="'+input_name+'"]').length > 0) {
	        	inputs[input_name] = form.find('textarea[name="'+input_name+'"]').val();
	    	} else if(form.find('select[name="'+input_name+'"]').val() !== undefined) {
	    		inputs[input_name] = form.find('select[name="'+input_name+'"]').val();
	    	}
	      
	      i++;
	    }

	    //

		$.post(form.attr('action'), inputs, function(data) {
          	var json = JSON.parse(data);
		  	if(json.statut === true) {
          		form.find('.ajax-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+json.msg+'</i></div>').fadeIn(500);
          		if(form.attr('data-redirect-url') !== undefined) {
          			document.location.href=form.attr('data-redirect-url');
          		}
				submit.html(submit_btn_content).attr('disabled', false).fadeIn(500);
          	} else if(json.statut === false) {
            	form.find('.ajax-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+json.msg+'</i></div>').fadeIn(500);
				submit.html(submit_btn_content).attr('disabled', false).fadeIn(500);
	        } else {
		    	form.find('.ajax-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
				submit.html(submit_btn_content).attr('disabled', false).fadeIn(500);
		    }
        });
	});
</script>