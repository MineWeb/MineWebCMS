<?php   ?>
<script type="text/javascript">
    $(".ticket-delete").click(function() {
      ticket_delete(this);
  });

  function ticket_delete(e) {
      var id = $(e).attr("id");
      $.post("<?= $this->Html->url(array('plugin' => 'support', 'controller' => 'home', 'action' => 'ajax_delete')) ?>", { id : id }, function(data) {
          if(data == 'true') {
            $('#ticket-'+id).slideUp(1500); // je le supprime sur la page
            // Calcul pour le changement en temps réél des stats
            var nbr_ticket = $('#nbr-ticket').html();
            nbr_ticket = parseInt(nbr_ticket);
            nbr_ticket = nbr_ticket - 1;
            $('#nbr-ticket').html(nbr_ticket);
            if($('#ticket-state-'+id).html() == '<icon style="color: green;" class="fa fa-check" title="<?= $Lang->get('RESOLVED') ?>"></icon>') {
              var nbr_ticket_resolved = $('#nbr-ticket-resolved').html();
                nbr_ticket_resolved = parseInt(nbr_ticket_resolved);
                nbr_ticket_resolved = nbr_ticket_resolved - 1;
                $('#nbr-ticket-resolved').html(nbr_ticket_resolved);
            } else {
              var nbr_ticket_unresolved = $('#nbr-ticket-unresolved').html();
                nbr_ticket_unresolved = parseInt(nbr_ticket_unresolved);
                nbr_ticket_unresolved = nbr_ticket_unresolved - 1;
                $('#nbr-ticket-unresolved').html(nbr_ticket_unresolved);
            }
            // Fin des stats
          } else {
            alert(data);
          }
      });
  }

    $(".reply-delete").click(function() {
        reply_delete(this);
    });

    function reply_delete(e) {
        var id = $(e).attr("id");
        $.post("<?= $this->Html->url(array('plugin' => 'support', 'controller' => 'home', 'action' => 'ajax_reply_delete')) ?>", { id : id }, function(data) {
          if(data == 'true') {
            $('#ticket-reply-'+id).slideUp(1500);
          } else {
            alert(data);
          }
        });
    }

    $(".dropdown_reply").click(function() {
        dropdown_reply(this);
    });

    function dropdown_reply(e) {
      var id = $(e).attr("id");
      $(".reply_"+id).slideToggle("slow");
      if($(e).attr('class') == 'btn btn-info btn-sm dropdown_reply active') {
        $(e).empty().html('<icon style="font-size: 10px;" class="fa fa-chevron-down"></icon>');
      } else {
        $(e).empty().html('<icon style="font-size: 10px;" class="fa fa-chevron-up"></icon>');
      }
      $(e).toggleClass('active');
    }

  $(".ticket-resolved").click(function() {
      ticket_resolved(this);
  });

  function ticket_resolved(e) {
      var id = $(e).attr("id");
      $.post("<?= $this->Html->url(array('plugin' => 'support', 'controller' => 'home', 'action' => 'ajax_resolved')) ?>", { id : id }, function(data) {
          if(data == 'true') {
            $('#ticket-state-'+id).html('<icon style="color: green;" class="fa fa-check" title="<?= $Lang->get('RESOLVED') ?>"></icon>'); // je le passe en résolu sur la page
            // changement des stats en temps réél
            var nbr_ticket_resolved = $('#nbr-ticket-resolved').html();
            nbr_ticket_resolved = parseInt(nbr_ticket_resolved);
            nbr_ticket_resolved = nbr_ticket_resolved + 1;
            $('#nbr-ticket-resolved').html(nbr_ticket_resolved);
            var nbr_ticket_unresolved = $('#nbr-ticket-unresolved').html();
            nbr_ticket_unresolved = parseInt(nbr_ticket_unresolved);
            nbr_ticket_unresolved = nbr_ticket_unresolved - 1;
            $('#nbr-ticket-unresolved').html(nbr_ticket_unresolved);
            $('.div-ticket-resolved-'+id).remove();
            $('.ticket-reply #'+id).remove();
            // Fin des stats
          } else {
            alert(data);
          }
      });
  }

    $(".ticket-reply").click(function() {
       ticket_reply(this);
    });

    function ticket_reply(e) {
        var id = $(e).attr("id");
        $('#reply_ticket').modal('toggle');
        var content = $('#ticket-'+id).html();
        $('.modal-body .ticket-reply').html(content);
        $('#reply_ticket .pull-right.support').remove();
        $('#reply_ticket .reply_'+id).remove();
        $('#id_reply_form').val(id);
    }

    $("#ticket-form_reply").submit(function( event ) {
            $('#msg-on-reply').hide().html('<div class="alert alert-success" role="alert"><p><?= $Lang->get('IN_PROGRESS') ?></p></div>').fadeIn(1500);
            var $form = $( this );
            var id = $form.find("input[name='id']").val();
            var message = $form.find("textarea[name='reply']").val();
            $.post("<?= $this->Html->url(array('plugin' => 'support', 'controller' => 'home', 'action' => 'ajax_reply')) ?>", { id : id, message : message }, function(data) {
              if(data == 'true') {
                $('#reply_ticket').modal('hide');
                /**
                * Dropdown reply
                **/
                $(".reply_"+id).slideToggle("slow");
                if($('.dropdown_reply #'+id).attr('class') == 'btn btn-info btn-sm dropdown_reply active') {
                    $('.dropdown_reply #'+id).empty().html('<icon style="font-size: 10px;" class="fa fa-chevron-down"></icon>');
                } else {
                    $('.dropdown_reply #'+id).empty().html('<icon style="font-size: 10px;" class="fa fa-chevron-up"></icon>');
                }
                $('.dropdown_reply #'+id).toggleClass('active');
                /**
                * end dropdown
                **/
                var before = $('.reply.reply_'+id).html();
                $('.reply.reply_'+id).hide().html(before+'<div class="line-support"></div><div class="col-md-11 reply-col"><div class="panel panel-default panel-support"><div class="panel-body"><img class="support" src="<?= $this->Html->url(array('controller' => 'API', 'action' => 'get_head_skin/')) ?>/<?= $this->Connect->get_pseudo(); ?>/50" title="<?= $this->Connect->get_pseudo(); ?>"><p class="support">'+message+'</p></div></div></div>').slideDown(1000);
              } else if(data == 1) {
                $('#msg-on-reply').hide().html('<div class="alert alert-danger" role="alert"><p><?= $Lang->get('CANT_BE_EMPTY') ?></p></div>').fadeIn(1500);
              } else {
                $('#msg-on-reply').hide().html('<div class="alert alert-danger" role="alert"><p><b><?= $Lang->get('ERROR') ?> : </b>'+data+'</p></div>').fadeIn(1500);
              }
            });
            return false;
        });
    
    $("#ticket-form_post").submit(function( event ) {
        event.preventDefault();
        $('#msg-on-post').hide().html('<div class="alert alert-success" role="alert"><p><?= $Lang->get('IN_PROGRESS') ?></p></div>').fadeIn(1500);
        var $form = $( this );
        var title = $form.find("input[name='title']").val();
        var content = $form.find("textarea[name='content']").val();
        if($form.find("input[name='private']").is(':checked')) {
          var ticket_private = '1';
        } else {
          var ticket_private = '0';
        }
        $.post("<?= $this->Html->url(array('plugin' => 'support', 'controller' => 'home', 'action' => 'ajax_post')) ?>", { title : title, content : content, ticket_private : ticket_private }, function(data) {
          if(!isNaN(data) == true) {
            $('#post_ticket').modal('hide');
            // ajout du post js
            var content_tickets = $("#content-tickets").html();
            var new_ticket = '<div class="col-md-12" id="ticket-'+data+'"><div class="panel panel-default panel-ticket"><div class="panel-body"><h3 class="support">'+title+' <div style="display:inline-block;" id="ticket-state-'+data+'"><icon class="fa fa-times" style="color:red;" title="<?= $Lang->get('UNRESOLVED') ?>"></icon></div></h3><img class="support" src="<?= $this->Html->url(array('controller' => 'API', 'action' => 'get_head_skin/')) ?>/<?= $this->Connect->get_pseudo() ?>/50" title="<?= $this->Connect->get_pseudo() ?>"><p class="support">'+content+'</p></div></div></div>';
            $("#content-tickets").hide().html(new_ticket+content_tickets).fadeIn(1500);
            // fin ajout
            // stats
            var nbr_ticket = $('#nbr-ticket').html();
            nbr_ticket = parseInt(nbr_ticket);
            nbr_ticket = nbr_ticket + 1;
            $('#nbr-ticket').html(nbr_ticket);
            var nbr_ticket_unresolved = $('#nbr-ticket-unresolved').html();
            nbr_ticket_unresolved = parseInt(nbr_ticket_unresolved);
            nbr_ticket_unresolved = nbr_ticket_unresolved + 1;
            $('#nbr-ticket-unresolved').html(nbr_ticket_unresolved);
            // fin stast
          } else {
            $('#msg-on-post').hide().html('<div class="alert alert-danger" role="alert"><p><b><?= $Lang->get('ERROR') ?> : </b>'+data+'</p></div>').fadeIn(1500);
          }
        });
        return false;
    });

    $(".like").click(function() {
      if($(this).hasClass("active")) {
          $(this).removeClass("active");
          var nbr = $(this).html();
          nbr = nbr.split('<');
          nbr = nbr['0'];
          nbr = parseInt(nbr) - 1;
          $(this).html(nbr+' <i class="fa fa-thumbs-up"></i>');
          var id = $(this).attr("id");
          $.post("<?= $this->Html->url(array('controller' => 'news', 'action' => 'dislike')) ?>", { id : id }, function(data) { $('#debug').html(data); });
      } else {
          $(this).addClass("active");
          var nbr = $(this).html();
          nbr = nbr.split('<');
          nbr = nbr['0'];
          nbr = parseInt(nbr) + 1;
          $(this).html(nbr + ' <i class="fa fa-thumbs-up"></i>');
          var id = $(this).attr("id");
          $.post("<?= $this->Html->url(array('controller' => 'news', 'action' => 'like')) ?>", { id : id });
      }
    });

</script>