// SCRIPT POUR MINEWEB 
// PAR EYWEK
// TOUS DROIT RESERVES
// AUCUNE COPIE SANS AUTORISATION EST AUTORISEE

// Afficher les conectés


function hideMB(id) {
    if(document.getElementById(id).style.visibility=="hidden")
    {
        document.getElementById(id).style.visibility="visible";
        document.getElementById('hideMB_'+id).innerHTML='- Cacher les connectés -';
    }
    else
    {
        document.getElementById(id).style.visibility="hidden";
        document.getElementById('hideMB_'+id).innerHTML='- Afficher les connectés -';
    }
    return true;
}

function modal_profil() {
    if(document.getElementById('modal_profil').style.display=="none")
    {
        //document.getElementById('modal_profil').style.visibility="visible";
        $('#modal_profil').fadeIn(300);
    }
    else
    {
        $('#modal_profil').fadeOut(300);
        //document.getElementById('modal_profil').style.visibility="hidden";
    }
    return true;
}


/* SUPPORT */ 

$(document).ready(function(){

  $(".reply_toggle").click(function(){
    reply(this);
  });
  
});

function reply(id) {
  var id = $(id).attr("id");
  $(".ticket_reply_"+id).slideToggle("slow");
  $(this).toggleClass("active");
}

  // SIGNIN
$(function() {
	$("#signin").submit(function() {
    document.getElementById('signin_ajax').innerHTML='<div class="success" style="width:auto;margin-bottom:-1px;margin-left:5px;margin-right:18px;">Inscription en cours ...</div>';
		pseudo = $(this).find("input[name=pseudo]").val();
		password = $(this).find("input[name=password]").val();
      	password_2 = $(this).find("input[name=password_2]").val();
      	mail = $(this).find("input[name=mail]").val();
      	ip = $(this).find("input[name=ip]").val();
      	captcha = $(this).find("input[name=captcha]").val();
      	$.post("signin/display",{pseudo:pseudo, password:password, password_2:password_2, mail:mail, ip:ip, captcha:captcha},
			function(data) {
        if(data!='Ok'){
				  document.getElementById('signin_ajax').innerHTML='<div class="error" style="width:auto;margin-bottom:-1px;margin-left:5px;margin-right:18px;padding-right:4px;">'+data+'</div>';
				  document.getElementById('popup_signin').style.height="445px";
			  } else {
			 	 window.location.reload();
			   }
		  });
		return false;
	});
});

$(document).ready(function(){
    
var $carrousel = $('#slider'), // on cible le bloc du carrousel
    $img = $('#slider .item'), // on cible les images contenues dans le carrousel
    indexImg = $img.length - 1, // on définit l'index du dernier élément
    i = 0, // on initialise un compteur
    $currentImg = $img.eq(i); // enfin, on cible l'image courante, qui possède l'index i (0 pour l'instant)

$img.css('display', 'none'); // on cache les images
$currentImg.css('display', 'block'); // on affiche seulement l'image courante

function slideImg(){
    setTimeout(function(){ // on utilise une fonction anonyme
						
        if(i < indexImg){ // si le compteur est inférieur au dernier index
	    i++; // on l'incrémente
	}
	else{ // sinon, on le remet à 0 (première image)
	    i = 0;
	}

	$img.fadeOut(550); 
      //$img.hide("fast");
	$currentImg = $img.eq(i);
  $currentImg.fadeIn(500);
  $(".progressbar").css("width", "0%");
  progressbar(0);
      //$currentImg.show("normal");

	slideImg(); // on oublie pas de relancer la fonction à la fin

    }, 6500); // on définit l'intervalle à 7000 millisecondes (7s)
}

slideImg(); // enfin, on lance la fonction une première fois

});

function progressbar(n) { 
  $(".progressbar").css("width", n + "%");
    if(n < 100) {
      setTimeout(function() {
        progressbar(n + 0.3);
    }, 17.9);
  }
}

tinymce.init({
    selector: "textarea.editor",
    theme: "modern",
      skin: 'light',
    language : 'fr_FR'
 });

/* INPUT MODIFIABLE */
 
$('.modify').click(function(e){
  var el= e.target||event.srcElement;
  var id = el.id;
  var content = $('#'+id).html();
  $('#'+id).removeClass();
  $('#'+id).empty().append('<input type="text" name="'+id+'" value="'+content+'">');
});

/* TAB */

$(document).ready(function(){

  $(".tab a").click(function(){
    tab(this);
  });
  
});

function tab(e) {
  var id = $(e).attr("id");
  $(".tab").find('.active').removeClass();
  $(".tab-content").find('.active').removeClass('active');
  $(".tab-content #"+id).attr('class', 'tab-pane active');
  $(".tab a#"+id).attr('class', 'active');
}

function startTab() {
  var id = $(".tab").find('.1').attr('id');
  $(".tab-content #"+id).attr('class', 'tab-pane active');
  $(".tab").find('.1').attr('class', 'active');
}

$(document).ready(function(){

  $(".tab-server a").click(function(){
    tab_server(this);
  });
  
});

function tab_server(e) {
  var id = $(e).attr("id");
  $(".tab-server").find('.active').removeClass();
  $(".tab-content-server").find('.active').removeClass('active');
  $(".tab-content-server #"+id).attr('class', 'tab-pane-server active');
  $(".tab-server a#"+id).attr('class', 'active');
}

function confirmDel(url) {
  if (confirm("Etes vous sûr de vouloir supprimer ceci ?"))
    window.location.href=''+url+'';
  else
    return false;
}
