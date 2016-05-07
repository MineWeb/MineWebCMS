$(window).load(function() {

  // La page chargée, on enlève le svg qui fais patienter
  $('.container svg.loader').fadeOut(550, affichFirst);


  // On affiche alors le petit texte explicatif + logo
  function affichFirst() {

    $(".logo").animate({'margin-top': '25%', 'margin-bottom': '0'}, 1500);

    $('div.first').fadeIn(1500, affichCompatibilite);
  }

  // Si le php nous a dis d'afficher la compatiblité car il manque quelque chose on l'affiche. Et on s'arrête là.
  function affichCompatibilite() {

    if($('div.compatibilite').attr('data-need-to-display') == "true") {
      $('div.compatibilite').fadeIn(550);

      $('div.compatibilite thead').addClass('animated fadeInLeft');

      var i = 0;
      $('div.compatibilite tbody tr').each(function(e) {
        i++;
        var side = (i%2 == 0) ? 'Left' : 'Right';
        $(this).addClass('animated fadeIn'+side);
      });

    } else {
      affichDB();
    }

  }

  // Si on a besoin d'afficher les inputs de bdd on les affiches (si elle déjà configuré like hébergement, on passe direct à la suite)

  function affichDB() {
    if($('div.database').attr('data-need-to-display') == "true") {
      $('div.database').fadeIn(550);

      $('div.database div.form-group').each(function(e) {
        $(this).addClass('animated shake');
      });

      $('.saveDB').addClass('animated tada');

    } else {
      affichContinue();
    }
  }

  // On affiche le bouton d'installation des tables (sinon on serait plus sur cette page déjà)

  function affichContinue() {

    $('button.installSQL').fadeIn(250).addClass('animated bounce');

  }

  // TRAITEMENT AJAX

  // Identifiants bdd

    $('form#saveDB').on('submit', function(e) {
      e.preventDefault();

      var button = $(this).find('.saveDB');

      button.attr('disabled', 'disabled');
      button.addClass('disabled');
      var submitContent = button.html();
      button.html(TEXT__LOADING);

      var form = $(this);

      var inputs = {};
      inputs['host'] = $(this).find('input[name="host"]').val();
      inputs['login'] = $(this).find('input[name="login"]').val();
      inputs['database'] = $(this).find('input[name="database"]').val();
      inputs['password'] = $(this).find('input[name="password"]').val();

      $.ajax({
        url: '?action=db',
        method: 'POST',
        data: inputs,
        dataType: 'JSON',

        success: function(data){

          if(data.status) {
            $('.ajax-msg').fadeOut(250);
            form.fadeOut(550, affichContinue);
          } else {
            $('.ajax-msg').html('<div class="alert alert-danger animated fadeInTop"><b>'+TEXT__ERROR+' : </b> '+data.msg+'</div>');
            button.html(submitContent);
            button.removeClass('disabled');
            button.attr('disabled', false);
          }

          // PENSE A VERIFIER QU'ON MODIFIE PAS LE FICHIER DB SI C'EST UN HERBERGEMENT
        },
        error: function(xhr) {

          $('.ajax-msg').html('<div class="alert alert-danger animated fadeInTop"><b>'+TEXT__ERROR+' : </b> '+TEXT__INTERNAL_ERROR+' ('+xhr.status+').</div>');
          button.html(submitContent);
          button.removeClass('disabled');
          button.attr('disabled', false);

        }
      })

    });

    $('.installSQL').on('click', function(e) {
      e.preventDefault();

      var button = $(this);

      button.attr('disabled', 'disabled');
      button.addClass('disabled');
      var submitContent = button.html();
      button.html(TEXT__LOADING);

      $('.SQLprogress').fadeIn(250).addClass('animated fadeInTop');


      $.ajax({
        url: '#',
        method: 'POST',

        xhr: function() { // xhr qui traite la barre de progression
          myXhr = $.ajaxSettings.xhr();

          myXhr.addEventListener('progress', function(e) {
            console.log(e);
            if(e.lengthComputable){
              width = (e.loaded / e.total) * 100;
            } else {
              width = (e.loaded / 1000000) * 100; // on part sur la base de 1 MO (1000000 o)
            }

            $('.SQLprogress .progress-bar').css('width', width+'%');

          }, false);

          console.log(myXhr);
          return myXhr;

        },

        success: function(data){

          window.location=window.location;

        },
        error: function(xhr) {

          $('.ajax-msg').html('<div class="alert alert-danger animated fadeInTop"><b>'+TEXT__ERROR+' : </b> '+TEXT__INTERNAL_ERROR+' ('+xhr.status+').</div>');
          button.html(submitContent);
          button.removeClass('disabled');
          button.attr('disabled', false);

        }
      })

    })

});
