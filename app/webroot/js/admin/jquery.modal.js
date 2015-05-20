//Lorsque vous cliquez sur un lien de la classe poplight et que le href commence par #
$('a.poplight[href^=#]').click(function() {
  var popID = $(this).attr('rel'); //Trouver la pop-up correspondante
  var popURL = $(this).attr('href'); //Retrouver la largeur dans le href

  //Récupérer les variables depuis le lien
  var query= popURL.split('?');
  var dim= query[1].split('&amp;');
  var popWidth = dim[0].split('=')[1]; //La première valeur du lien

  //Faire apparaitre la pop-up et ajouter le bouton de fermeture
  $('#' + popID).fadeIn().css({
    'width': Number(popWidth)
  })
  .prepend(/*'<a href="#" class="close"><img src="img/close_pop.png" class="btn_close" title="Fermer" alt="Fermer" /></a>'*/'');

  //Récupération du margin, qui permettra de centrer la fenêtre - on ajuste de 80px en conformité avec le CSS
  var popMargTop = ($('#' + popID).height() + 80) / 2;
  var popMargLeft = ($('#' + popID).width() + 80) / 2;

  //On affecte le margin
  $('#' + popID).css({
    'margin-top' : -popMargTop,
    'margin-left' : -popMargLeft
  });

  //Effet fade-in du fond opaque
  $('body').append('<div id="fade"></div>'); //Ajout du fond opaque noir
  //Apparition du fond - .css({'filter' : 'alpha(opacity=80)'}) pour corriger les bogues de IE
  $('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();

  return false;
});

$('body').on('click', 'a.close, #fade', function() { //Au clic sur le body...
		$('#fade , .popup_block').fadeOut(function() {
			$('#fade, a.close').remove();  
	}); //...ils disparaissent ensemble
		
		return false;
	});