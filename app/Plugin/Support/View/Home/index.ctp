<?php 
  
App::import('Component', 'ConnectComponent');
$this->Connect = new ConnectComponent;
?>
    <div class="container">
        <div class="row">

            <div class="col-md-6">
				<h1 style="display: inline-block;"><?= $Lang->get('SUPPORT') ?></h1>
				<?php if($isConnected AND $Permissions->can('POST_TICKET')) { ?>
                	<a href="#" style="margin-top:-15px;"  data-toggle="modal" data-target="#post_ticket" class="btn btn-success hidden-md hidden-lg"><icon class="fa fa-pencil-square-o"></icon> <?= $Lang->get('POST_A_TICKET') ?></a>
                <?php } ?>
            </div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-9" id="content-tickets">
				<?php if(!empty($tickets)) { ?>
					<?php foreach ($tickets as $key => $value) { ?>
						<?php if($Permissions->can('VIEW_TICKETS') AND $value['Ticket']['private'] == 0 OR $isConnected AND $this->Connect->if_admin() OR $isConnected AND $this->Connect->get_pseudo() == $value['Ticket']['author'] OR $Permissions->can('VIEW_ALL_TICKETS')) { ?>
							<!-- Un ticket -->
							<div class="col-md-12" id="ticket-<?= $value['Ticket']['id'] ?>">
								<div class="panel panel-default">
								  <div class="panel-body">
								  	<h3 class="support"><?= $value['Ticket']['title'] ?> <?php if($value['Ticket']['state'] == 1) { echo '<icon style="color: green;" class="fa fa-check" title="'.$Lang->get('RESOLVED').'"></icon>'; } else { echo '<div style="display:inline-block;" id="ticket-state-'.$value['Ticket']['id'].'"><icon class="fa fa-times" style="color:red;" title="'.$Lang->get('UNRESOLVED').'"></icon></div>'; } ?></h3>
								    <img class="support" src="<?= $this->Html->url(array('controller' => 'API', 'action' => 'get_head_skin/', 'plugin' => false)) ?>/<?= $value['Ticket']['author'] ?>/50" title="<?= $value['Ticket']['author'] ?>">
								    <div class="pull-right support">
								    	<?php if($isConnected AND $this->Connect->if_admin() OR $isConnected AND $this->Connect->get_pseudo() == $value['Ticket']['author'] AND $Permissions->can('DELETE_HIS_TICKET') OR $Permissions->can('DELETE_ALL_TICKETS')) { ?>
									    <p><a id="<?= $value['Ticket']['id'] ?>" title="<?= $Lang->get('DELETE') ?>" class="ticket-delete btn btn-danger btn-sm"><icon class="fa fa-times"></icon></a></p>
									    <?php } ?>
									    <?php if($value['Ticket']['state'] == 0) { ?>
										    <?php if($isConnected AND $this->Connect->if_admin() OR $isConnected AND $this->Connect->get_pseudo() == $value['Ticket']['author'] AND $Permissions->can('RESOLVE_HIS_TICKET') OR $Permissions->can('RESOLVE_ALL_TICKETS')) { ?>
										    <p class="div-ticket-resolved-<?= $value['Ticket']['id'] ?>"><a id="<?= $value['Ticket']['id'] ?>" title="<?= $Lang->get('RESOLVED') ?>" class="ticket-resolved btn btn-success btn-sm"><icon style="font-size: 10px;" class="fa fa-check"></icon></a></p>
										    <?php } ?>
										<?php } ?>
										<?php if($Permissions->can('SHOW_TICKETS_ANWSERS')) { ?>
									    	<p><button id="<?= $value['Ticket']['id'] ?>" title="<?= $Lang->get('SHOW_ANSWER') ?>" class="btn btn-info btn-sm dropdown_reply"><icon style="font-size: 10px;" class="fa fa-chevron-down"></icon></button></p>
									    <?php } ?>
									    <?php if($value['Ticket']['state'] == 0 AND $isConnected AND $this->Connect->if_admin() OR $isConnected AND $this->Connect->get_pseudo() == $value['Ticket']['author'] AND $value['Ticket']['state'] == 0 AND $Permissions->can('REPLY_TO_HIS_TICKETS') OR $Permissions->can('REPLY_TO_ALL_TICKETS')) { ?>
									    <p><button id="<?= $value['Ticket']['id'] ?>" title="<?= $Lang->get('REPLY') ?>" class="btn btn-warning btn-sm ticket-reply"><icon class="fa fa-mail-reply" style="font-size: 10px;"></icon></button></p>
										<?php } ?>
									</div>
								    <p class="support"><?= before_display($value['Ticket']['content']) ?></p>
								  </div>
								</div>
								<div class="reply reply_<?= $value['Ticket']['id'] ?>">
									<!-- Une réponse --> 
									<?php if($Permissions->can('SHOW_TICKETS_ANWSERS')) { ?>
										<?php foreach ($reply_tickets as $k => $v) { ?>
											<?php if($v['ReplyTicket']['ticket_id'] == $value['Ticket']['id']) { ?>
											<div id="ticket-reply-<?= $v['ReplyTicket']['id'] ?>">
												<div class="line-support"></div>
												<div class="col-md-11 reply-col">
													<div class="panel panel-default">
													  <div class="panel-body">
													    <img class="support" src="<?= $this->Html->url(array('controller' => 'API', 'action' => 'get_head_skin/', 'plugin' => false)) ?>/<?= $v['ReplyTicket']['author']; ?>/50" title="<?= $v['ReplyTicket']['author']; ?>">
													    <?php if($isConnected AND $this->Connect->if_admin()) { ?>
													    <div class="pull-right">
														    <p><button id="<?= $v['ReplyTicket']['id'] ?>" title="<?= $Lang->get('DELETE') ?>" class="btn btn-danger btn-sm reply-delete"><icon class="fa fa-times"></icon></button></p>
														</div>
														<?php } ?>
													    <p class="support"><?= before_display($v['ReplyTicket']['reply']); ?></p>
													  </div>
													</div>
												</div>
											</div>
											<?php } ?>
										<?php } ?>
									<?php } ?>
									<!-- - - - - -->
								</div>
							</div>
							<!-- - - - - -->
						<?php } ?>
					<?php } ?>
				<?php } else { echo $Lang->get('NO_TICKETS'); } ?>
			</div>
			<div class="col-md-3 hidden-xs hidden-sm">
                <div class="well">
                    <h4><?= $Lang->get('STATS') ?></h4>
                    <p><b><?= $Lang->get('NUMBER_OF_TICKETS') ?> : </b><span id="nbr-ticket"><?= $nbr_tickets ?></span></p>
                    <p><b><?= $Lang->get('NUMBER_OF_RESOLVED') ?> : </b><span id="nbr-ticket-resolved"><?= $nbr_tickets_resolved ?></span></p>
                    <p><b><?= $Lang->get('NUMBER_OF_UNRESOLVED') ?> : </b><span id="nbr-ticket-unresolved"><?= $nbr_tickets_unresolved ?></span></p>
                </div>
                <?php if($isConnected AND $Permissions->can('POST_TICKET')) { ?>
                	<a href="#" data-toggle="modal" data-target="#post_ticket" class="btn btn-success btn-lg btn-block"><icon class="fa fa-pencil-square-o"></icon> <?= $Lang->get('POST_A_TICKET') ?></a>
                <?php } ?>
            </div> 
		</div>
    </div>

    <div class="modal fade" id="post_ticket" tabindex="-1" role="dialog" aria-labelledby="post_ticketLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= $Lang->get('CLOSE') ?></span></button>
            <h4 class="modal-title" id="myModalLabel"><?= $Lang->get('POST_A_TICKET') ?></h4>
          </div>
          <div class="modal-body">
          	<div id="msg-on-post"></div>
          	<form id="ticket-form_post" method="post" class="form-horizontal">
			  <div class="form-group">
			    <label for="inputEmail3" class="col-sm-2 control-label"><?= $Lang->get('TITLE') ?></label>
			    <div class="col-sm-10">
			      <input type="text" name="title" class="form-control" id="inputEmail3" placeholder="">
			    </div>
			  </div>
			  <div class="form-group">
			    <label for="inputPassword3" class="col-sm-2 control-label"><?= $Lang->get('PROBLEM') ?></label>
			    <div class="col-sm-10">
			      <textarea name="content" class="form-control" rows="3"></textarea>
			    </div>
			  </div>
			  <div class="checkbox">
			    <label>
			      <input id="private" name="private" type="checkbox"> <?= $Lang->get('PRIVATE_TICKET') ?>
			    </label>
			  </div>
          </div>
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-default"><?= $Lang->get('CLOSE') ?></button>
            <button type="submit" class="btn btn-primary"><?= $Lang->get('SUBMIT') ?></button>
        </form>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="reply_ticket" tabindex="-1" role="dialog" aria-labelledby="reply_ticketLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= $Lang->get('CLOSE') ?></span></button>
            <h4 class="modal-title" id="myModalLabel"><?= $Lang->get('REPLY_TO_TICKET') ?></h4>
          </div>
          <div class="modal-body">
          	<div id="msg-on-reply"></div>
          	<div class="ticket-reply">Javascript désactiver ?</div>
          	<div style="margin-right:490px;" class="line-support"></div>
          	<form class="form-horizontal" id="ticket-form_reply" method="post" role="form">
          		<input id="id_reply_form" type="hidden" name="id" value="ID">
          		<textarea name="reply" class="form-control" rows="3" placeholder="<?= $Lang->get('YOUR_REPLY') ?>"></textarea>
          </div>
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-default"><?= $Lang->get('CLOSE') ?></button>
            <button type="submit" class="btn btn-primary"><?= $Lang->get('SUBMIT') ?></button>
           </form>
          </div>
        </div>
      </div>
    </div>
    <script>
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
</script>
