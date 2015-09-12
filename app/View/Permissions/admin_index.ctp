<div class="row-fluid">

	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-cog"></i> <?= $Lang->get('PERMISSIONS') ?></h3>
		</div>

		<div class="well">
			
			<button data-toggle="modal" data-target="#addRank" class="btn btn-block btn-success"><?= $Lang->get('ADD_RANK') ?></button>

			<hr>

			<?= $this->Form->create(false, array(
				'class' => 'form-horizontal'
			)); ?>
				<table class="table table-bordered">
	              <thead>
	                <tr>
	                  <th><?= $Lang->get('PERMISSIONS') ?></th>
	                  <th><?= $Lang->get('NORMAL') ?></th>
	                  <th><?= $Lang->get('MODERATOR') ?></th>
	                  <th><?= $Lang->get('ADMINISTRATOR') ?></th>
	                  <?php 
	                  	if(!empty($custom_ranks)) {
	                  		foreach ($custom_ranks as $k => $data) {
	                  			echo '<th>'.$data['Rank']['name'].'</th>';
	                  		}
	                  	} 
	                  ?>
	                </tr>
	              </thead>
	              <tbody>
				<?php
				$config = $Permissions->get_all();
				foreach ($config as $key => $value) { ?>
					<tr>
                  		<td><?= $Lang->get('PERMISSIONS__'.$key) ?></td>
                  		<td><input type="checkbox" name="<?= $key ?>-0"<?php if($value['0'] == "true") { echo ' checked="checked"'; } ?>></td>
                  		<td><input type="checkbox" name="<?= $key ?>-2"<?php if($value['2'] == "true") { echo ' checked="checked"'; } ?>></td>
                  		<td><input type="checkbox" checked="checked" disabled="disabled"></td>
                  		<?php if(!empty($custom_ranks)) { ?>
	                  		<?php foreach ($custom_ranks as $k => $data) { ?>
									<td><input type="checkbox" name="<?= $key ?>-<?= $data['Rank']['rank_id'] ?>"<?php if(!empty($value[$data['Rank']['rank_id']]) && $value[$data['Rank']['rank_id']] == "true") { echo ' checked="checked"'; } ?>></td>
			                <?php } ?>
			            <?php } ?>
                	</tr>
				<?php } ?>

				    </tbody>
            	</table>

				<div class="form-actions">
					<?= $this->Form->button($Lang->get('SUBMIT'), array(
						'type' => 'submit',
						'class' => 'btn btn-primary'
					)); ?>
					<a href="<?= $this->Html->url(array('controller' => '', 'action' => '', 'admin' => true)) ?>" type="button" class="btn"><?= $Lang->get('CANCEL') ?></a>     
				</div>

			</form>          

		</div>

	</div>

</div>
<div class="modal fade" id="addRank" tabindex="-1" role="dialog" aria-labelledby="addRankLabel" aria-hidden="true" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"><?= $Lang->get('ADD_RANK') ?></h4>
      </div>
      <div class="modal-body">
        <form action="" id="addRank" method="post">
        	<div class="ajax-msg"></div>
          <div class="input-append">
              <input class="no-margin span4" placeholder="<?= $Lang->get('NAME') ?>" name="name" type="text"></input>
              <button class="btn btn-info" type="submit"><?= $Lang->get('SUBMIT') ?></button>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= $Lang->get('CANCEL') ?></button>
      </div>
    </div>
  </div>
</div>
<script>
	$("#addRank").on('submit', function( event ) {
        event.preventDefault();
        var $form = $( this );

        $form.find('.ajax-msg').empty().html('<div class="alert alert-info" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><?= $Lang->get('LOADING') ?> ...</div>').fadeIn(500);
        event.preventDefault();
        var name = $form.find("input[name='name']").val();
        $.post("<?= $this->Html->url(array('controller' => 'permissions', 'action' => 'add_rank', 'admin' => true)) ?>", { name : name }, function(data) {
            data2 = data.split("|");
            if(data.indexOf('true') != -1) {
                $form.find('.ajax-msg').empty().html('<div class="alert alert-success" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
                document.location.href="<?= $this->Html->url(array('controller' => 'permissions', 'action' => 'index', 'admin' => true)) ?>";
            } else if(data.indexOf('false') != -1) {
                $form.find('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
            } else {
                $form.find('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
            }
        });
        return false;
    });
</script>