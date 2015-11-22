$(document).ready(function(){

	// Captcha reload
        
 	$('#reload').click(function() {
        var captcha = $("#captcha_image");
        captcha.attr('src', captcha.attr('src')+'?'+Math.random());
        return false;
    });

    // Caroussel
    if($('.carousel').length > 0) {
	    $('.carousel').carousel({
	        interval: 5000 //changer la vitesse
	    })
	        
	    //Events that reset and restart the timer animation when the slides change
	    $("#myCarousel").on("slide.bs.carousel", function(event) {
	        //The animate class gets removed so that it jumps straight back to 0%
	        $(".transition-timer-carousel-progress-bar", this).removeClass("animate").css("width", "0%");
	    }).on("slid.bs.carousel", function(event) {
	        //The slide transition finished, so re-add the animate class so that
	        //the timer bar takes time to fill up
	            $(".transition-timer-carousel-progress-bar", this).addClass("animate").css("width", "100%");
	    });
	}
        
    //Kick off the initial slide animation when the document is ready
    $(".transition-timer-carousel-progress-bar", "#myCarousel").css("width", "100%");
            
    // Home news
    if($('ul#items').length > 0) {
	    $('ul#items').easyPaginate({
	        step:3
	    });
	}

	// Like des news 
	$(".like").click(function() {
  	if($(this).hasClass("active")) {
    	$(this).removeClass("active");
    	var nbr = $(this).html();
    	nbr = nbr.split('<');
    	nbr = nbr['0'];
    	nbr = parseInt(nbr) - 1;
    	$(this).html(nbr+' <i class="fa fa-thumbs-up"></i>');
    	var id = $(this).attr("id");
      var inputs = {};
      inputs["id"] = id;
      inputs["data[_Token][key]"] = CSRF_TOKEN;
    	$.post(DISLIKE_URL, inputs);
  	} else {
    	$(this).addClass("active");
    	var nbr = $(this).html();
    	nbr = nbr.split('<');
    	nbr = nbr['0'];
    	nbr = parseInt(nbr) + 1;
    	$(this).html(nbr + ' <i class="fa fa-thumbs-up"></i>');
    	var id = $(this).attr("id");
      var inputs = {};
      inputs["id"] = id;
      inputs["data[_Token][key]"] = CSRF_TOKEN;
    	$.post(LIKE_URL, inputs);
    }
  });

  // Form ajax
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

    form.find('.ajax-msg').empty().html('<div class="alert alert-info"><a class="close" data-dismiss="alert">×</a>'+LOADING_MSG+' ...</div>').fadeIn(500);

      var submit_btn_content = form.find('button[type=submit]').html();
      submit.html(LOADING_MSG+'...').attr('disabled', 'disabled').fadeIn(500);

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
        input_name = decodeURI(input[0]);

        /*console.log('Name :', input_name+"\n");
        console.log('Type :', form.find('input[name="'+input_name+'"]').attr('type')+"\n");
        console.log('Value :', form.find('input[name="'+input_name+'"]').val()+"\n\n")*/

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

      // CSRF 
      inputs["data[_Token][key]"] = CSRF_TOKEN;

      //

    $.post(form.attr('action'), inputs, function(data) {
      var json = JSON.parse(data);
      if(json.statut === true) {
        if(form.attr('data-success-msg') === undefined || form.attr('data-success-msg') == "true") {
          form.find('.ajax-msg').html('<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b>'+SUCCESS_MSG+' :</b> '+json.msg+'</i></div>').fadeIn(500);
        }
        if(form.attr('data-callback-function') !== undefined) {
          window[form.attr('data-callback-function')](inputs);
        }
        if(form.attr('data-redirect-url') !== undefined) {
          document.location.href=form.attr('data-redirect-url');
        }
        submit.html(submit_btn_content).attr('disabled', false).fadeIn(500);
      } else if(json.statut === false) {
        form.find('.ajax-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b>'+ERROR_MSG+' :</b> '+json.msg+'</i></div>').fadeIn(500);
        submit.html(submit_btn_content).attr('disabled', false).fadeIn(500);
      } else {
        form.find('.ajax-msg').html('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b>'+ERROR_MSG+' :</b> '+INTERNAL_ERROR_MSG+'</i></div>');
        submit.html(submit_btn_content).attr('disabled', false).fadeIn(500);
      }
    });
  });

  function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
  }

  // type (numbers)
  $('body').find('input[data-type]').each(function(e) {
    var element = $(this);
    element.on('keyup', function(e) {
      var last_char = element.val().substr(element.val().length - 1);
      var val_without_last = element.val().substr(0, element.val().length - 1);
      if(element.attr('data-type') == "numbers") {
        if(!isNumber(element.val())) {
          element.val(val_without_last);
        }
      }
    });
  });

  // Autotab 

  $('body').find('input[maxlength]').each(function(e) {
    var all_inputs = $('body').find('input[maxlength]');
    var element = $(this);
    element.on('keyup', function(e) {
      if(element.attr('maxlength') <= element.val().length) {
        var next_tab_index = parseInt(element.attr('tabindex'))+1;
        $('body').find('input[maxlength][tabindex="'+next_tab_index+'"]').focus();
      }
    })
  });
            

});