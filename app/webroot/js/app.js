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
