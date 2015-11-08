$(document).ready(function(){

	// Captcha reload
        
 	$('#reload').click(function() {
        var captcha = $("#captcha_image");
        captcha.attr('src', captcha.attr('src')+'?'+Math.random());
        return false;
    });

    

});