<?php 
  
App::import('Component', 'ConnectComponent');
$this->Connect = new ConnectComponent;
?>

	<div class="push-nav"></div>
	<div class="brand-support">
		<div class="container">
			<p class="text-center"><i class="fa fa-comments-o"></i> Vous avez besoin d’aide ? Alors n’hésitez pas !</p>
			<button data-toggle="modal" data-target="#<?php if($this->Connect->connect() AND $Permissions->can('POST_TICKET')) { echo 'post_ticket'; } else { echo 'login'; } ?>" class="btn btn-primary btn-lg center-block">Poster un ticket</button>
		</div>
	</div>
	<div class="container support">
		<div class="row">
			<div id="content-tickets">
				<?php if(!empty($tickets)) { ?>
					<?php foreach ($tickets as $key => $value) { ?>
						<?php if($value['Ticket']['private'] == 0 OR $this->Connect->connect() AND $this->Connect->if_admin() OR $this->Connect->connect() AND $this->Connect->get_pseudo() == $value['Ticket']['author'] OR $Permissions->can('VIEW_ALL_TICKETS')) { ?>
							<!-- Un ticket -->
							<div class="col-md-12" id="ticket-<?= $value['Ticket']['id'] ?>">
								<div class="panel panel-default panel-ticket">
								  <div class="panel-body">
								  	<div class="head">
								    	<img class="support" src="<?= $this->Html->url(array('controller' => 'API', 'action' => 'get_head_skin/', 'plugin' => false)) ?>/<?= $value['Ticket']['author'] ?>/135" title="<?= $value['Ticket']['author'] ?>">
								    	<div class="clearfix"></div>
								    	<p class="author"><?php if(strlen($value['Ticket']['author']) > "8") { echo '<abbr title="'.$value['Ticket']['author'].'">'.substr($value['Ticket']['author'], 0, 8).'...</abbr>'; } else { echo $value['Ticket']['author']; } ?></p>
								    </div>
								    <h3 class="support"><?= $value['Ticket']['title'] ?> <?php if($value['Ticket']['state'] == 1) { echo '<icon style="color: green;" class="fa fa-check" title="'.$Lang->get('RESOLVED').'"></icon>'; } else { echo '<div style="display:inline-block;" id="ticket-state-'.$value['Ticket']['id'].'"><icon class="fa fa-times" style="color:red;" title="'.$Lang->get('UNRESOLVED').'"></icon></div>'; } ?></h3>
								    <div class="pull-right support">
								    	<?php if($this->Connect->connect() AND $this->Connect->if_admin() OR $this->Connect->connect() AND $this->Connect->get_pseudo() == $value['Ticket']['author'] AND $Permissions->can('DELETE_HIS_TICKET') OR $Permissions->can('DELETE_ALL_TICKETS')) { ?>
									    <p><a id="<?= $value['Ticket']['id'] ?>" title="<?= $Lang->get('DELETE') ?>" class="ticket-delete btn btn-danger btn-sm"><icon class="fa fa-times"></icon></a></p>
									    <?php } ?>
									    <?php if($value['Ticket']['state'] == 0) { ?>
										    <?php if($this->Connect->connect() AND $this->Connect->if_admin() OR $this->Connect->connect() AND $this->Connect->get_pseudo() == $value['Ticket']['author'] AND $Permissions->can('RESOLVE_HIS_TICKET') OR $Permissions->can('RESOLVE_ALL_TICKETS')) { ?>
										    <p class="div-ticket-resolved-<?= $value['Ticket']['id'] ?>"><a id="<?= $value['Ticket']['id'] ?>" title="<?= $Lang->get('RESOLVED') ?>" class="ticket-resolved btn btn-success btn-sm"><icon style="font-size: 10px;" class="fa fa-check"></icon></a></p>
										    <?php } ?>
										<?php } ?>
										<?php if($Permissions->can('SHOW_TICKETS_ANWSERS')) { ?>
									    	<p><button id="<?= $value['Ticket']['id'] ?>" title="<?= $Lang->get('SHOW_ANSWER') ?>" class="btn btn-info btn-sm dropdown_reply"><icon style="font-size: 10px;" class="fa fa-chevron-down"></icon></button></p>
									    <?php } ?>
									    <?php if($value['Ticket']['state'] == 0 AND $this->Connect->connect() AND $this->Connect->if_admin() OR $this->Connect->connect() AND $this->Connect->get_pseudo() == $value['Ticket']['author'] AND $value['Ticket']['state'] == 0 AND $Permissions->can('REPLY_TO_HIS_TICKETS') OR $Permissions->can('REPLY_TO_ALL_TICKETS')) { ?>
									    <p><button id="<?= $value['Ticket']['id'] ?>" title="<?= $Lang->get('REPLY') ?>" class="btn btn-warning btn-sm ticket-reply"><icon class="fa fa-mail-reply" style="font-size: 10px;"></icon></button></p>
										<?php } ?>
									</div>
								    <p class="support"><?= before_display($value['Ticket']['content']) ?></p>
								    <div class="clearfix"></div>
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
													    <img class="support" src="<?= $this->Html->url(array('controller' => 'API', 'action' => 'get_head_skin/', 'plugin' => false)) ?>/<?= $v['ReplyTicket']['author']; ?>/60" title="<?= $v['ReplyTicket']['author']; ?>">
													    <?php if($this->Connect->connect() AND $this->Connect->if_admin()) { ?>
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
          	<form id="ticket-form_post_m" method="post" class="form-horizontal">
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

    <div class="modal modal-large fade support support-modal" id="reply_ticket" tabindex="-1" role="dialog" aria-labelledby="reply_ticketLabel" aria-hidden="true">
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
