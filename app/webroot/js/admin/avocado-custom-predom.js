// -------------------------------------------------------------------
// Avocado Panel Custom JS/jQuery (Pre-Dom)
// -------------------------------------------------------------------

// Theme Changer Save (cookie) 
// -------------------------------------------------------------------
// URL: Lychee Designs
// -------------------------------------------------------------------

if ($.cookie("style") != null) {
  var savedStyle = $.cookie("style");
  $('#theme-style').attr("href", "assets/css/theme/" + savedStyle + ".css");
};

jQuery(document).ready(function() {
	if ($.cookie("style") != null) {
	  $("#theme a").attr("class", "");

	  var currentStyle = $("#theme").find("[data-style='" + savedStyle + "']");
	  $(currentStyle).addClass("active");
	};
});


jQuery(document).ready(function() {

// Top-Bar Padding Removal (Lychee Designs)
// -------------------------------------------------------------------
// URL: Lychee Designs
// -------------------------------------------------------------------

$('.top-bar:has(ul)').css('padding-left', '0px');

// Hide / Show Sidebar (Mobile)
// -------------------------------------------------------------------
// URL: http://jpanelmenu.com/
// -------------------------------------------------------------------

	var jPM = $.jPanelMenu({
	    menu: '#nav',
	    trigger: '.btn-navbar',
	    keyboardShortcuts: false,
	    animated: false
	});

	$('#nav').css("display", "none");

	jPM.on();

	    var $window = $(window);

	    function checkWidth() {
	        var windowsize = $window.width();
	        if (windowsize > 753) {
	            $('#nav').css("display", "block");
	        }
	        else {
	            $('#nav').css("display", "none");
	        }
	    }
	    checkWidth();
	    $(window).resize(checkWidth);

});