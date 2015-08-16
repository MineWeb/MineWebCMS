<?php 
  
App::import('Component', 'ConnectComponent');
$this->Connect = new ConnectComponent;
$this->Configuration = new ConfigurationComponent;
$this->EyPlugin = new EyPluginComponent;
if($this->Connect->connect()) {
?>
	<div class="push-nav"></div>
	<div class="container bg profile">
		<div class="row">
        	<div class="ribbon">
        		<div class="ribbon-stitches-top"></div>
        		<div class="ribbon-content"><p>
        				<?php if($this->EyPlugin->is_installed('Shop')) { ?>
        					<span class="pull-left hidden-xs"><span class="info"><span class="money"><?= $this->Connect->get('money') ?></span><?php if($this->Connect->get('money') == 1) { echo  ' '.$this->Configuration->get_money_name(false, true); } else { echo  ' '.$this->Configuration->get_money_name(); } ?></span></span> 
        				<?php } ?>
						<span class="text-center"><?= $this->Connect->get_pseudo() ?></span>
						<?php if($this->EyPlugin->is_installed('Vote')) { ?>
	        				<span class="pull-right hidden-xs"><span class="info"><?= $this->Connect->get('vote') ?> <?= $Lang->get('VOTE') ?></span></span> 
	        			<?php } elseif($this->EyPlugin->is_installed('Shop')) { ?>
							<a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'index')) ?>" class="btn btn-primary pull-right"><?= $Lang->get('SHOP') ?></a>
	        			<?php } ?>
        		</p></div>
        		<div class="ribbon-stitches-bottom"></div>
        	</div>
			<div class="profile-content">
				<?php 
				  	if($search_psc_msg != false AND !empty($search_psc_msg)) {
				  		foreach ($search_psc_msg as $key => $value) {
				  			if($value['PaysafecardMessage']['type'] == 1) {
				  				echo '<div class="alert alert-success"><b>'.$Lang->get('SUCCESS').' :</b> '.$Lang->get('YOUR_PSC_OF').' '.$value['PaysafecardMessage']['amount'].'€ '.$Lang->get('IS_VALID_GAIN').' : '.$value['PaysafecardMessage']['added_points'].' '.$this->Configuration->get_money_name().'.</div>';
				  			} elseif ($value['PaysafecardMessage']['type'] == 0) {
				  				echo '<div class="alert alert-danger"><b>'.$Lang->get('ERROR').' :</b> '.$Lang->get('YOUR_PSC_OF').' '.$value['PaysafecardMessage']['amount'].'€ '.$Lang->get('IS_INVALID').'</div>';
				  			}
				  		}
				  	}
				  ?>
				<div class="section">
					<p><b><?= $Lang->get('PSEUDO') ?> :</b> <?= $this->Connect->get('pseudo'); ?></p>
				</div>
				<div class="section">
					<p><b><?= $Lang->get('EMAIL') ?> :</b> <span id="email"><?= $this->Connect->get('email'); ?></span></p>
				</div>
				<div class="section">
					<p>
						<b><?= $Lang->get('RANK') ?> :</b> 
						<?php 
						if($this->Connect->get('rank') == 4) { 
							echo $Lang->get('ADMINISTRATOR');
						} elseif ($this->Connect->get('rank') == 3) {
							echo $Lang->get('ADMINISTRATOR');
						} elseif ($this->Connect->get('rank') == 2) {
							echo $Lang->get('MODERATOR');
						} elseif ($this->Connect->get('rank') == 1) {
							echo $Lang->get('WRITER');
						} elseif ($this->Connect->get('rank') == 0) {
							echo $Lang->get('MEMBER');
						}
						?>
					</p>
				</div>
				<?php if($this->EyPlugin->is_installed('Shop')) { ?>
					<div class="section">
						<p><b><?= $Lang->get('MONEY') ?> :</b> <span class="money"><?= $this->Connect->get('money'); ?></span></p>
					</div>
				<?php } ?>

				<div class="section">
					<p><b><?= $Lang->get('IP') ?> :</b> <?= $this->Connect->get('ip'); ?></p>
				</div>

				<div class="section">
					<p><b><?= $Lang->get('CREATED') ?> :</b> <?= $Lang->date($this->Connect->get('created')) ?></p>
				</div>

				<div class="clearfix"></div>

				<hr>

				<h3><?= $Lang->get('CHANGE_PASSWORD') ?></h3>

				<form id="change_pw">
					<div class="ajax-msg-pw"></div>
					<div class="section password input">
						<input type="password" class="form-control" name="password" placeholder="<?= $Lang->get('PASSWORD_CONFIRMATION') ?>">
					</div>
					<div class="section password input">
						<input type="password" class="form-control" name="password_confirmation" placeholder="<?= $Lang->get('PASSWORD') ?>">
					</div>

					<div class="clearfix"></div>

					<center><button class="btn btn-primary"><?= $Lang->get('SUBMIT') ?></button></center>
				</form>

				<hr>

				<h3><?= $Lang->get('CHANGE_EMAIL') ?></h3>

				<form id="change_email">
					<div class="ajax-msg-mail"></div>
					<div class="section password input">
						<input type="email" class="form-control" name="email" placeholder="<?= $Lang->get('EMAIL_CONFIRMATION') ?>">
					</div>
					<div class="section password input">
						<input type="email" class="form-control" name="email_confirmation" placeholder="<?= $Lang->get('EMAIL') ?>">
					</div>

					<div class="clearfix"></div>

					<center><button class="btn btn-primary"><?= $Lang->get('SUBMIT') ?></button></center>
				</form>

				<?php if($shop_active) { ?>

					<hr>

					<h3><?= $Lang->get('SEND_POINTS') ?></h3>

					<form id="send_points">
						<div class="ajax-msg-points"></div>
						<div class="section password input">
							<input type="text" class="form-control" name="to" placeholder="<?= $Lang->get('TO') ?>">
						</div>
						<div class="section password input">
							<input type="text" class="form-control" name="how" placeholder="<?= $Lang->get('HOW') ?>">
						</div>

						<div class="clearfix"></div>

						<center><button class="btn btn-primary"><?= $Lang->get('SUBMIT') ?></button></center>
					</form>

				<?php } ?>

				<?php if($this->Configuration->get('mineguard') == "true") { ?>

					<hr>

					<h3><?= $Lang->get('YOUR_ALLOWED_IP') ?></h3>

					<p><?= $Lang->get('WHAT_IS_MINEGUARD') ?></p>
					<div class="row">
						<div class="col-md-8">
							<table class="table">
								<thead>
									<tr>
										<th><?= $Lang->get('IP') ?></th>
										<th><?= $Lang->get('ACTIONS') ?></th>
									</tr>
								</thead>
								<tbody id="table-ip">
									<?php 
									foreach ($api as $key => $value) { ?>
										<tr id="<?= $key ?>">
											<th><?= $value ?></th>
											<th><button data-ip-id="<?= $key ?>" class="btn btn-danger delete_ip"><?= $Lang->get('DELETE') ?></button></th>
										</tr>
									<?php } ?>
								</tbody>

							</table>
						</div>
			
						<div class="col-md-4">
							<form id="allowed_ip">
								<div class="ajax-msg-ip"></div>
								<div class="form-group">
									<input type="text" class="form-control" name="ip" placeholder="<?= $Lang->get('IP') ?>">
								</div>

								<div class="form-group">
									<button class="btn btn-success"><?= $Lang->get('ADD') ?></button>
								</div>
							</form>
						</div>
					</div>
				<?php } ?>

				<?php if($can_skin) { ?>
					<hr>

					<h3><?= $Lang->get('SKIN') ?></h3>

					<form class="form-inline" method="post" enctype="multipart/form-data">
						<input type="hidden" name="MAX_FILE_SIZE" value="<?= $skin_max_size ?>" />
						<input type="hidden" name="skin_form" value="1">
					  <div class="form-group">
					    <label><?= $Lang->get('CHOOSE_YOUR_FILE') ?></label>
					    <input name="skin" type="file">
					  </div>
					  <button type="submit" class="btn btn-default"><?= $Lang->get('SUBMIT') ?></button>
					  <div class="form-group">&nbsp;&nbsp;&nbsp;&nbsp;</div>
					  <div class="form-group">
					  	<u><?= $Lang->get('FILE_NEED') ?> :</u><br>

                		- <?= $Lang->get('BE_PNG') ?><br>
                		- <?= $Lang->get('WIDTH_MAX') ?><br>
                		- <?= $Lang->get('HEIGHT_MAX') ?><br>
					  </div>
					</form>
				<?php } ?>

				<?php if($can_cape) { ?>
					<hr>

					<h3><?= $Lang->get('CAPE') ?></h3>	

					<form class="form-inline" method="post" enctype="multipart/form-data">
						<input type="hidden" name="MAX_FILE_SIZE" value="<?= $cape_max_size ?>" />
						<input type="hidden" name="cape_form" value="1">
					  <div class="form-group">
					    <label><?= $Lang->get('CHOOSE_YOUR_FILE') ?></label>
					    <input name="cape" type="file">
					  </div>
					  <button type="submit" class="btn btn-default"><?= $Lang->get('SUBMIT') ?></button>
					  <div class="form-group">&nbsp;&nbsp;&nbsp;&nbsp;</div>
					  <div class="form-group">
					  	<u><?= $Lang->get('FILE_NEED') ?> :</u><br>

                		- <?= $Lang->get('BE_PNG') ?><br>
                		- <?= $Lang->get('WIDTH_MAX') ?><br>
                		- <?= $Lang->get('HEIGHT_MAX') ?><br>
					  </div>
					</form>	
				<?php } ?>

				<?= $Module->loadModules('user_profile') ?>

				<div class="clearfix"></div>
			</div>
		</div>
	</div>

<script type="text/javascript">
	<?php if($this->Configuration->get('mineguard') == "true") { ?>
		$("#allowed_ip").submit(function( event ) {
			$('.ajax-msg-ip').empty().html('<div class="alert alert-info"><a class="close" data-dismiss="alert">×</a><?= $Lang->get('LOADING') ?>...</div>').fadeIn(500);
	    	event.preventDefault();
	        var $form = $( this );
	        var ip = $form.find("input[name='ip']").val();
	        $.post("<?= $this->Html->url(array('controller' => 'api', 'action' => 'add_ip', 'admin' => false)) ?>", { ip : ip }, function(data) {
	          	data2 = data.split("|");
			  	if(data.indexOf('true') != -1) {
	          		$('.ajax-msg-ip').empty().html('<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
	          		var table_ip = $('#table-ip').html();
	          		$('#table-ip').html(table_ip+'<tr><th>'+ip+'</th></tr>');
	          	} else if(data.indexOf('false') != -1) {
	            	$('.ajax-msg-ip').empty().html('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
		        } else {
			    	$('.ajax-msg-ip').empty().html('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
			    }
	        });
	        return false;
	    });
		$(".delete_ip").click(function( event ) {
	    	event.preventDefault();
	    	var ip = $(this).attr('data-ip-id');
	    	console.log(ip);
	        $.post("<?= $this->Html->url(array('controller' => 'api', 'action' => 'delete_ip', 'admin' => false)) ?>", { ip : ip }, function(data) {
	          	data2 = data.split("|");
			  	if(data.indexOf('true') != -1) {
	          		$('#'+ip).fadeOut(500);
	          	} else if(data.indexOf('false') != -1) {
	            	$('.ajax-msg-ip').empty().html('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
		        } else {
			    	$('.ajax-msg-ip').empty().html('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
			    }
	        });
	        return false;
	    });
	<?php } ?>

	$("#change_pw").submit(function( event ) {
		$('.ajax-msg-pw').empty().html('<div class="alert alert-info"><a class="close" data-dismiss="alert">×</a><?= $Lang->get('LOADING') ?>...</div>').fadeIn(500);
    	event.preventDefault();
        var $form = $( this );
        var password = $form.find("input[name='password']").val();
        var password_confirmation = $form.find("input[name='password_confirmation']").val();
        $.post("<?= $this->Html->url(array('controller' => 'user', 'action' => 'change_pw', 'admin' => false)) ?>", { password : password, password_confirmation : password_confirmation }, function(data) {
          	data2 = data.split("|");
		  	if(data.indexOf('true') != -1) {
          		$('.ajax-msg-pw').empty().html('<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
          	} else if(data.indexOf('false') != -1) {
            	$('.ajax-msg-pw').empty().html('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
	        } else {
		    	$('.ajax-msg-pw').empty().html('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
		    }
        });
        return false;
    });
    $("#change_email").submit(function( event ) {
    	$('.ajax-msg-mail').empty().html('<div class="alert alert-info"><a class="close" data-dismiss="alert">×</a><?= $Lang->get('LOADING') ?>...</div>').fadeIn(500);
    	event.preventDefault();
        var $form = $( this );
        var email = $form.find("input[name='email']").val();
        var email_confirmation = $form.find("input[name='email_confirmation']").val();
        $.post("<?= $this->Html->url(array('controller' => 'user', 'action' => 'change_email', 'admin' => false)) ?>", { email : email, email_confirmation : email_confirmation }, function(data) {
          	data2 = data.split("|");
		  	if(data.indexOf('true') != -1) {
          		$('.ajax-msg-mail').empty().html('<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
          		$('#email').empty().html(email).fadeIn(500);
          	} else if(data.indexOf('false') != -1) {
            	$('.ajax-msg-mail').empty().html('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
	        } else {
		    	$('.ajax-msg-mail').empty().html('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
		    }
        });
        return false;
    });
	$("#send_points").submit(function( event ) {
		$('.ajax-msg-points').empty().html('<div class="alert alert-info"><a class="close" data-dismiss="alert">×</a><?= $Lang->get('LOADING') ?>...</div>').fadeIn(500);
    	event.preventDefault();
        var $form = $( this );
        var to = $form.find("input[name='to']").val();
        var how = $form.find("input[name='how']").val();
        $.post("<?= $this->Html->url(array('controller' => 'user', 'action' => 'send_points', 'admin' => false)) ?>", { to : to, how : how }, function(data) {
          	data2 = data.split("|");
		  	if(data.indexOf('true') != -1) {
          		$('.ajax-msg-points').empty().html('<div class="alert alert-success"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
          		var money = $('.money').html();
          		$('.money').html(parseInt(money) - parseInt(how));
          	} else if(data.indexOf('false') != -1) {
            	$('.ajax-msg-points').empty().html('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
	        } else {
		    	$('.ajax-msg-points').empty().html('<div class="alert alert-danger"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
		    }
        });
        return false;
    });
</script>

<?php } else { 
	echo $Lang->get('NEED_TO_BE_CONNECT');
} ?>