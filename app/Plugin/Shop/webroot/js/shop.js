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
