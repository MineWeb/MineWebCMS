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
    	$.post(dislike_url, { id : id });
  	} else {
    	$(this).addClass("active");
    	var nbr = $(this).html();
    	nbr = nbr.split('<');
    	nbr = nbr['0'];
    	nbr = parseInt(nbr) + 1;
    	$(this).html(nbr + ' <i class="fa fa-thumbs-up"></i>');
    	var id = $(this).attr("id");
    	$.post(like_url, { id : id });
    }
  });
            

});