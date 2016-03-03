/*
Afficher un article dans le modal
*/

$('.display-item').click(function(e) {

  e.preventDefault();

  var id = $(this).attr('data-item-id'); // On récupère l'ID

  var loading_html = '<div class="alert alert-info">';
    loading_html += LOADING_MSG+'...';
  loading_html += '</div>';

  $('#buy-modal .modal-body').html(loading_html); // On met le message de chargement dans le modal


  $('#buy-modal').modal(); // On affiche le modal en dernier

  $.ajax({
    url: ITEM_GET_URL+id,
    type : 'GET',
    dataType : 'json',
    success: function(response) {
      $('#buy-modal .modal-body').fadeOut(150, function(){

        if(response.statut) {

          $(this).html(response.html).fadeIn('250');

          var item_infos = response.item_infos;

          $.cookie.json = true; // Pouvoir mettre des objets

          /*
            Si l'article est déjà dans le panier
          */
          var itemsInCart = $.cookie('cart');
          for (var key in itemsInCart) {
            if (itemsInCart[key] !== null && itemsInCart[key]['item_id'] == id) { // Si il est déjà dans le panier
              $(this).find('button.add-to-cart').html(ADDED_TO_CART_MSG);
              $(this).find('button.add-to-cart').addClass('disabled');
              $(this).find('button.add-to-cart').attr('disabled', 'disabled');

              break;
            }
          }

          /*
            On gère la quantité
          */
          $("input[name='quantity']").TouchSpin({
            min: 0,
            max: 100,
            step: 1,
            decimals: 0,
            boostat: 5,
            maxboostedstep: 10
          });

          $("input[name='quantity']").unbind('change');

          $("input[name='quantity']").on('change', function(e) {
            var new_price = item_infos['price'] * $(this).val();
            $("#buy-modal .modal-body").find('#total-price').html(new_price);
          });

          /*
           On gère les codes promos
          */

          $('input[id="code-voucher"]').unbind('keyup');

          $('input[id="code-voucher"]').keyup(function(e) {

            var code = $(this).val();
            code = (code.length == 0) ? 'undefined' : code;

            $.get(VOUCHER_CHECK_URL+code+'/'+id, function(data) {
              if(data.price !== undefined) {
                $("#buy-modal .modal-body").find('#total-price').html(data.price);
              }
            });
          });

          /*
            On gère l'achat d'article
          */
          $('.buy-item').unbind('click');
          $('.buy-item').click(function(e) {

            e.preventDefault();

            var id = $(this).attr('data-item-id');

            var quantity = $("input[name='quantity']").val();

            var code = $('#code-voucher').val();

            if($("#buy-modal .modal-body").find('#ajax-msg').length == 0) {
              $("#buy-modal .modal-body").prepend('<div id="ajax-msg"></div>');
            }

            $('#btn-buy').attr('disabled', true);
            $('#btn-buy').addClass('disabled');

            $("#buy-modal .modal-body").find('#ajax-msg').html('<div class="alert alert-info">'+LOADING_MSG+'...</div>').fadeIn('150');

            var post = {};
            post['items'] = [
              {
                item_id: id,
                quantity: quantity
              }
            ];
            post['code'] = code;
            post["data[_Token][key]"] = CSRF_TOKEN;

            $.ajax({
              url: BUY_URL,
              data : post,
              type : 'post',
              dataType : 'JSON',
              success: function(response) {

                if(response.statut) {
                  $('#btn-buy').attr('disabled', false);
                  $('#btn-buy').removeClass('disabled');

                  $("#buy-modal .modal-body").find('#ajax-msg').html('<div class="alert alert-success">'+response.msg+'</div>').fadeIn('150');
                } else {
                  $('#btn-buy').attr('disabled', false);
                  $('#btn-buy').removeClass('disabled');

                  $("#buy-modal .modal-body").find('#ajax-msg').html('<div class="alert alert-danger">'+response.msg+'</div>').fadeIn('150');
                }
              },
              error: function(xhr) {
                $('#btn-buy').attr('disabled', false);
                $('#btn-buy').removeClass('disabled');
                $("#buy-modal .modal-body").find('#ajax-msg').fadeOut(150);
                alert('ERROR');
              }
            });

          });

          /*
            On gère l'ajout au panier
          */
          $('.add-to-cart').unbind('click');
          $('.add-to-cart').click(function(e) {

            e.preventDefault();

            var id = $(this).attr('data-item-id');

            var cookie = $.cookie('cart');
            if(typeof(cookie) !== 'object' || cookie === undefined) {
              var cart = [];
            } else {
              var cart = cookie;
            }

            console.log(cart);

            var item_id = $(this).attr('data-item-id');

            var quantity = $("input[name='quantity']").val();
            if(quantity === undefined) {
              quantity = '1';
            }

            var item_name = item_infos['name'];
            var item_price = item_infos['price'];

            cart.push({
              'item_id':item_id,
              'quantity':quantity,
              'item_name':item_name,
              'item_price':item_price
            });

            console.log(cart);

            $.cookie('cart', cart);

            $(this).html(ADDED_TO_CART_MSG);
            $(this).addClass('disabled');
            $(this).attr('disabled', 'disabled');

            refreshCart();

          });

        } else {
          $(this).html(response.html).fadeIn('250');
        }


      });
    },
    error: function(xhr) {
      $('#buy-modal').modal('hide');
    }
  });

});

/*
Charger mon panier
*/
$(window).load(function() {

  refreshCart();

  /*
    Acheter le panier
  */
  $('#cart-modal .modal-footer #buy-cart').click(function(e) {

    if($("#cart-modal .modal-body").find('#ajax-msg').length == 0) {
      $("#cart-modal .modal-body").prepend('<div id="ajax-msg"></div>');
    }

    $('#buy-cart').attr('disabled', true);
    $('#buy-cart').addClass('disabled');

    $("#cart-modal .modal-body").find('#ajax-msg').html('<div class="alert alert-info">'+LOADING_MSG+'...</div>').fadeIn('150');

    var items = $.cookie('cart');

    var post = {};
    post['items'] = items;
    post['code'] = $('#cart-voucher').val();;
    post["data[_Token][key]"] = CSRF_TOKEN;

    $.ajax({
      url: BUY_URL,
      data : post,
      type : 'post',
      dataType : 'JSON',
      success: function(response) {

        if(response.statut) {
          $('#buy-cart').attr('disabled', false);
          $('#buy-cart').removeClass('disabled');

          $.cookie('cart', []); // On supprime le panier
          refreshCart();

          if($("#cart-modal .modal-body").find('#ajax-msg').length == 0) { // le refresh a supprimé le ajax-msg
            $("#cart-modal .modal-body").prepend('<div id="ajax-msg"></div>');
          }

          $("#cart-modal .modal-body").find('#ajax-msg').html('<div class="alert alert-success">'+response.msg+'</div>').fadeIn('150');
        } else {
          $('#buy-cart').attr('disabled', false);
          $('#buy-cart').removeClass('disabled');

          $("#cart-modal .modal-body").find('#ajax-msg').html('<div class="alert alert-danger">'+response.msg+'</div>').fadeIn('150');
        }
      },
      error: function(xhr) {
        $('#buy-cart').attr('disabled', false);
        $('#buy-cart').removeClass('disabled');
        $("#cart-modal .modal-body").find('#ajax-msg').fadeOut(150);
        alert('ERROR');
      }
    });

  });


});

function refreshCart() {
  $('#cart-modal .modal-body').html('<div class="alert alert-info">'+LOADING_MSG+'...</div>');

  // On ouvre la table
  var table = '<table class="table table-bordered">';
  table += '<thead>';
    table += '<tr>';
      table += '<th>'+CART_ITEM_NAME_MSG+'</th>';
      table += '<th>'+CART_ITEM_PRICE_MSG+'</th>';
      table += '<th>'+CART_ITEM_QUANTITY_MSG+'</th>';
      table += '<th>'+CART_ACTIONS_MSG+'</th>';
    table += '</tr>';
  table += '</thead>';
  table += '<tbody>';

  // On récupère les cookies
  $.cookie.json = true;

  var cart = $.cookie('cart');

  var notEmpty = false;

  for (var key in cart) {

    if(cart[key] !== null) {

      notEmpty = true;

      table += '<tr data-item-id="'+cart[key]['item_id']+'">';
        table += '<td>'+cart[key]['item_name']+'</td>';
        table += '<td>'+cart[key]['item_price']+'</td>';
        table += '<td>'+cart[key]['quantity']+'</td>';
        table += '<td><button class="btn btn-danger remove-from-cart" data-item-id="'+cart[key]['item_id']+'"><i class="fa fa-close"></i></button></td>';
      table += '</tr>';

    }

  }

  // On ferme la table
  table += '</tbody>';
  table += '</div>';

  if(notEmpty) {
    $('#buy-cart').attr('disabled', false);
    $('#buy-cart').removeClass('disabled');
    $('#cart-modal .modal-body').html(table);
  } else {
    $('#buy-cart').attr('disabled', true);
    $('#buy-cart').addClass('disabled');
    $('#cart-modal .modal-body').html('<div class="alert alert-danger">'+CART_EMPTY_MSG+'</div>');
  }

  // On gère la suppression
  $('.remove-from-cart').unbind('click');
  $('.remove-from-cart').click(function(e) {

    e.preventDefault();

    var cartContent = $.cookie('cart'); // on récupère le panier
    var newCart = [];// le nouveau panier (pour éviter les values null)

    var item_id = $(this).attr('data-item-id');

    for (var k in cartContent) {
      if(cartContent[k] !== null && cartContent[k]['item_id'] != item_id) { // si c'est pas l'article qu'on cherche

        newCart.push(cartContent[k]);

        break; // On arrête la boucle

      }
    }

    $.cookie('cart', newCart); // On le met dans les cookies maintenant

    if(newCart.length > 0) {
      $('#cart-modal .modal-body').find('tr[data-item-id="'+item_id+'"]').slideUp(150); // On l'enlève de la table
    } else {
      $('#buy-cart').attr('disabled', true);
      $('#buy-cart').addClass('disabled');
      $('#cart-modal .modal-body').html('<div class="alert alert-danger">'+CART_EMPTY_MSG+'</div>');
    }

    newCart = undefined;

  });
}
