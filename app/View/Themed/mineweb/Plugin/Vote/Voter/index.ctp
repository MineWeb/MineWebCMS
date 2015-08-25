<?php 
  
$this->Connect = new ConnectComponent;
$this->Configuration = new ConfigurationComponent;
?>
<div class="push-nav"></div>
<div class="brand-vote">
	<div class="container">
		<p class="text-center"><i class="fa fa-comments-o"></i> Voter pour notre serveur !</p>
		<center>
			<?php if($Permissions->can('VOTE_SHOW_REWARDS')) { ?>
				<button data-toggle="modal" data-target="#rewards" class="btn btn-primary btn-lg center-block"><?= $Lang->get('REWARDS') ?></button>
			<?php } ?>
			<?php if($Permissions->can('VOTE_SHOW_RANKING')) { ?>
				<button data-toggle="modal" data-target="#ranking" class="btn btn-primary btn-lg center-block"><?= $Lang->get('RANKING') ?></button>
			<?php } ?>
		</center>
	</div>
</div>
<div class="container vote">
	<div class="row">
		<div class="panel panel-default panel-step step1">
		  <div class="panel-heading">
		    <h3 class="panel-title"><?= $Lang->get('STEP') ?> 1 : <?= $Lang->get('LOGIN') ?> <i style="display:none" id="icon_step1" class="glyphicon glyphicon-ok"></i></h3>
		  </div>
		  <div class="panel-body">
		    <form class="form-inline" id="step1">
			  <div class="form-group">
			    <label class="sr-only">Pseudo</label>
			    <input type="text" class="form-control input-lg input-step1" name="pseudo" placeholder="<?= $Lang->get('MC_USERNAME') ?>">
			  </div>
			  <button type="submit" class="btn btn-info btn-lg btn-step1"><?= $Lang->get('LOGIN') ?></button>
			</form>
		  </div>
		  	<div class="response_step1"></div>
		</div>

		<div class="panel panel-default panel-step step2" style="opacity : 0.5;">
		  <div class="panel-heading">
		    <h3 class="panel-title"><?= $Lang->get('STEP') ?> 2 : <?= $Lang->get('DO_THE_VOTE') ?> <i style="display:none" id="icon_step2" class="glyphicon glyphicon-ok"></i></h3>
		  </div>
		  <div class="panel-body">
		  	<p><?= $Lang->get('STEP_2_DESC_VOTE') ?></p>
		  	<a class="btn btn-info btn-block btn-step2 disabled" href="<?= $vote_page ?>" target="_blank">Voter</a>
		  </div>
		</div>

		<?php /*<div class="panel panel-default panel-step step3" style="opacity : 0.5;">
		  <div class="panel-heading">
		    <h3 class="panel-title"><?= $Lang->get('STEP') ?> 3 : <?= $Lang->get('TAKE_OUT') ?> <span id="sec"></span><i style="display:none" id="icon_step3" class="glyphicon glyphicon-ok"></i></h3>
		  </div>
		  <div class="panel-body">
		  	<p><?= $Lang->get('STEP_3_DESC_VOTE') ?></p>
			<form class="form-inline" id="step3">
			  <div class="form-group">
			    <label class="sr-only">OUT</label>
			    <input type="text" class="form-control input-step3" name="out" placeholder="<?= $Lang->get('NUMBER_OF_OUT') ?>" disabled="">
			  </div>
			  <button type="submit" class="btn btn-info btn-step3 disabled"><?= $Lang->get('SUBMIT') ?></button>
			</form>
		  </div>
		  	<div class="response_step3"></div>
		</div><?php */ ?>

		<div class="panel panel-default panel-step step4" style="opacity : 0.5;">
		  <div class="panel-heading">
		    <h3 class="panel-title"><?= $Lang->get('STEP') ?> 3 : <?= $Lang->get('REWARDS') ?> <span id="sec"></span><i style="display:none" id="icon_step4" class="glyphicon glyphicon-ok"></i></h3>
		  </div>
		  <div class="panel-body">
		  	<p><?= $Lang->get('STEP_4_DESC_VOTE') ?></p>
		  	<button class="btn btn-success btn-block btn-step4 disabled"><?= $Lang->get('GET_REWARDS') ?></button>
		  </div>
		  	<div class="response_step4"></div>
		</div>
	</div>

	<?php if($Permissions->can('VOTE_SHOW_REWARDS')) { ?>
		<div class="modal fade" id="rewards" tabindex="-1" role="dialog" aria-labelledby="rewardsLabel" aria-hidden="false">
		 	<div class="modal-dialog">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<button type="button" class="close" data-dismiss="modal" aria-label="<?= $Lang->get('CLOSE') ?>"><span aria-hidden="true">×</span></button>
		        		<h4 class="modal-title" id="myModalLabel"><?= $Lang->get('REWARDS') ?></h4>
		      		</div>
		      		<div class="modal-body">
		      			<table class="table table-striped">
				          	<thead>
				            	<tr>
				              		<th>Nom</th>
				            	</tr>
				          	</thead>
				          	<tbody>
			                	<?php
			                	foreach ($rewards as $key => $value) {
									if($value['type'] == "money") {
										echo '<tr><td>'.$value['how'].' '.$this->Configuration->get_money_name().'</td></tr>';
									} else {
										echo '<tr><td>'.$value['name'].'</td></tr>';
									}
								}
								?>
							</tbody>
				        </table>
		            </div>
		      		<div class="modal-footer">
		        		<button type="button" class="btn btn-default" data-dismiss="modal"><?= $Lang->get('CLOSE') ?></button>
		     		</div>
		    	</div>
		  	</div>
		</div>
	<?php } ?>
	<?php if($Permissions->can('VOTE_SHOW_RANKING')) { ?>
		<div class="modal fade" id="ranking" tabindex="-1" role="dialog" aria-labelledby="rankingLabel" aria-hidden="false">
		 	<div class="modal-dialog">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<button type="button" class="close" data-dismiss="modal" aria-label="<?= $Lang->get('CLOSE') ?>"><span aria-hidden="true">×</span></button>
		        		<h4 class="modal-title" id="myModalLabel"><?= $Lang->get('RANKING') ?></h4>
		      		</div>
		      		<div class="modal-body">
		      			<table class="table table-striped">
				          	<thead>
				            	<tr>
				            		<th>#</th>
				              		<th>Nom</th>
				              		<th>Nbr. vote</th>
				            	</tr>
				          	</thead>
				          	<tbody>
			                	<?php
			                	$i = 0;
			                	foreach ($ranking as $key => $value) { 
			                	$i++;
			                	?>
			                		<tr>
			                			<td><?= $i ?></td>
			                			<td><?= $value['User']['pseudo'] ?></td>
			                			<td><?= $value['User']['vote'] ?></td>
			                		</tr>
								<?php } ?>
							</tbody>
				        </table>
		            </div>
		      		<div class="modal-footer">
		        		<button type="button" class="btn btn-default" data-dismiss="modal"><?= $Lang->get('CLOSE') ?></button>
		     		</div>
		    	</div>
		  	</div>
		</div>
	<?php } ?>

	<script type="text/javascript" id="script_step1">

	$('#step1').submit(function(e) {
		e.preventDefault();
		$('.response_step1').html('<div class="panel-footer"><div class="alert alert-info" style="margin-bottom:0px;"><?= $this->Html->image('ajax-loader.gif') ?> <?= $Lang->get('LOADING') ?> ...</div></div>');
		var $form = $( this );
        var pseudo = $form.find("input[name='pseudo']").val();
		$.post("<?= $this->Html->url(array('plugin' => 'vote', 'controller' => 'voter', 'action' => 'ajax')) ?>", { pseudo : pseudo, step : '1' }, function(data) {
			data2 = data.split("|");
		    if(data.indexOf('true') != -1) {
		    	$('#icon_step1').css("display", "");
		    	$('.step1').css("opacity", "0.7");
		    	$('.step2').css("opacity", "");
		    	$('.btn-step1').addClass('disabled');
		    	$('.btn-step2').removeClass('disabled');
		    	$('.input-step1').prop('disabled', true);
		        $('.response_step1').html('<div class="panel-footer"><div class="alert alert-success" style="margin-bottom:0px;"><b><?= $Lang->get('SUCCESS') ?> : </b>'+data2[0]+'</div></div>');
		        $('#script_step1').remove();
		    } else if(data.indexOf('false') != -1) {
		    	$('.response_step1').html('<div class="panel-footer"><div class="alert alert-danger" style="margin-bottom:0px;"><b><?= $Lang->get('ERROR') ?> : </b>'+data2[0]+'</div></div>');
		    } else {
		    	$('.response_step1').html('<div class="panel-footer"><div class="alert alert-danger" style="margin-bottom:0px;"><b><?= $Lang->get('ERROR') ?> : </b><?= $Lang->get('ERROR_WHEN_AJAX') ?></div></div>');
		    }
		});
	});

	</script>

	<script type="text/javascript" id="script_step2">

	$(".btn-step2").click( function() {
      	var i = 15;
      	function compteur() {
        	$("#sec").html(" : "+i);
        	i = i - 1;
        	if(i>-1) {
          		timer = setTimeout(function() {
            		compteur();
          		}, 1000);
        	} else {
          		$("#sec").css("display", "none");
          		$(".step2").css("opacity", "0.7");
          		$(".step3").css("opacity", "");
          		$('.btn-step2').addClass('disabled');
          		$('#icon_step2').css("display", "");
          		$('#script_step2').remove();
		    	/*$('.btn-step3').removeClass('disabled');
		    	$('.input-step3').prop('disabled', false);*/

		    	$('.step4').css("opacity", "");
		    	$('#step4').removeClass('disabled');
		    	$('.btn-step4').removeClass('disabled');
        	}
      	}
        compteur();
    });

    </script>
    <?php /*<script type="text/javascript" id="script_step3">

    $('#step3').submit(function(e) {
		e.preventDefault();
		$('.response_step3').html('<div class="panel-footer"><div class="alert alert-info" style="margin-bottom:0px;"><?= $this->Html->image('ajax-loader.gif') ?> <?= $Lang->get('LOADING') ?> ...</div></div>');
		var $form = $( this );
        var out = $form.find("input[name='out']").val();
		$.post("<?= $this->Html->url(array('plugin' => 'vote', 'controller' => 'voter', 'action' => 'ajax')) ?>", { out : out, step : '3' }, function(data) {
			data2 = data.split("|");
		    if(data.indexOf('true') != -1) {
		    	$('#icon_step3').css("display", "");
		    	$('.step3').css("opacity", "0.7");
		    	$('.step4').css("opacity", "");
		    	$('.btn-step3').addClass('disabled');
		    	$('#step4').removeClass('disabled');
		    	$('.btn-step4').removeClass('disabled');
		    	$('.input-step3').prop('disabled', true);
		        $('.response_step3').html('<div class="panel-footer"><div class="alert alert-success" style="margin-bottom:0px;"><b><?= $Lang->get('SUCCESS') ?> : </b>'+data2[0]+'</div></div>');
		        $('#script_step3').remove();
		    } else if(data.indexOf('false') != -1) {
		    	$('.response_step3').html('<div class="panel-footer"><div class="alert alert-danger" style="margin-bottom:0px;"><b><?= $Lang->get('ERROR') ?> : </b>'+data2[0]+'</div></div>');
		    } else {
		    	$('.response_step3').html('<div class="panel-footer"><div class="alert alert-danger" style="margin-bottom:0px;"><b><?= $Lang->get('ERROR') ?> : </b><?= $Lang->get('ERROR_WHEN_AJAX') ?></div></div>');
		    }
		});
	});

	</script><?php */ ?>
	<script type="text/javascript" id="script_step4">

	$(".btn-step4").click( function(e) {
      	e.preventDefault();
		$('.response_step4').html('<div class="panel-footer"><div class="alert alert-info" style="margin-bottom:0px;"><?= $this->Html->image('ajax-loader.gif') ?> <?= $Lang->get('LOADING') ?> ...</div></div>');
		var out = $('.vote').find("input[name='out']").val();
		var pseudo = $('.vote').find("input[name='pseudo']").val();
		$.post("<?= $this->Html->url(array('plugin' => 'vote', 'controller' => 'voter', 'action' => 'ajax')) ?>", { out : out, pseudo : pseudo, step : '4' }, function(data) {
			data2 = data.split("|");
		    if(data.indexOf('true') != -1) {
		    	$('#icon_step4').css("display", "");
		    	$('.step4').css("opacity", "0.7");
		    	$('.step4 .response_step4').css("opacity", "");
		    	$('.btn-step4').addClass('disabled');
		        $('.response_step4').html('<div class="panel-footer"><div class="alert alert-success" style="margin-bottom:0px;"><b><?= $Lang->get('SUCCESS') ?> : </b>'+data2[0]+'</div></div>');
		        $('#script_step4').remove();
		    } else if(data.indexOf('false') != -1) {
		    	$('.response_step4').html('<div class="panel-footer"><div class="alert alert-danger" style="margin-bottom:0px;"><b><?= $Lang->get('ERROR') ?> : </b>'+data2[0]+'</div></div>');
		    } else {
		    	$('.response_step4').html('<div class="panel-footer"><div class="alert alert-danger" style="margin-bottom:0px;"><b><?= $Lang->get('ERROR') ?> : </b><?= $Lang->get('ERROR_WHEN_AJAX') ?></div></div>');
		    }
		});
    });

	</script>
</div>