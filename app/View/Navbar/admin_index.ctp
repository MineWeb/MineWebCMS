<?php   ?>

<div class="row-fluid">

	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-calendar"></i> <?= $Lang->get('NAVBAR') ?></h3>
		</div>

		<div class="well">
			<?= $this->Session->flash(); ?>
      <a class="btn btn-large btn-block btn-primary" href="<?= $this->Html->url(array('controller' => 'navbar', 'action' => 'add', 'admin' => true)) ?>"><?= $Lang->get('ADD_NAV') ?></a>
      <br><br>

			<table class="table table-bordered">
        <thead>
          <tr>
            <th><?= $Lang->get('NAME') ?></th>
            <th><?= $Lang->get('URL') ?></th>
            <th><?= $Lang->get('ACTION') ?></th>
          </tr>
        </thead>
        <tbody id="sortable">
        	<?php $i=0; foreach ($navbars as $key => $value) { $i++; ?>
          <li id="<?= $value['Navbar']['name'] ?>-<?= $i ?>">
            <tr style="cursor:move;" id="<?= $value['Navbar']['name'] ?>-<?= $i ?>">
              <td><?= $value['Navbar']['name'] ?></td>
              <?php if($value['Navbar']['url'] != '#') { ?>
                <td><a href="<?= $value['Navbar']['url'] ?>"><?= $value['Navbar']['url'] ?></a></td>
              <?php } else { ?>
                <td><a href="#"><?= $Lang->get('DROPDOWN') ?></a></td>
              <?php } ?>
              <td>
              	<a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'navbar', 'action' => 'delete/'.$value['Navbar']['id'], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('DELETE') ?></a>
              </td>
            </tr>
          </li>
          <?php } ?>
        </tbody>
      </table>
      <br>
      <div class="ajax-msg"></div>
      <button id="save" class="btn btn-success pull-right active" disabled="disabled"><?= $Lang->get('NAV_SAVED') ?></button>
		</div>

		</div>

</div>
<script>
function confirmDel(url) {
  if (confirm("<?= $Lang->get('CONFIRM_WANT_DELETE') ?>"))
    window.location.href=''+url+'';
  else
    return false;
}
</script>
<style>
  li {
    list-style-type: none;
  }
</style>
<script>
$(function() {
  $( "#sortable" ).sortable({
    axis: 'y',
    stop: function (event, ui) {
        $('#save').empty().html('<?= $Lang->get('ON_SAVE') ?>');
        var data = $(this).sortable('serialize');
        $('#yolo').text(data);
        $.post("<?= $this->Html->url(array('controller' => 'navbar', 'action' => 'save_ajax', 'admin' => true)) ?>", { data : data }, function(data) {
          data2 = data.split("|");
          if(data.indexOf('true') != -1) {
                $('#save').empty().html('<?= $Lang->get('NAV_SAVED') ?>');
              } else if(data.indexOf('false') != -1) {
                $('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
            } else {
            $('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
          }
        });
      }
  });
  $( "#sortable" ).disableSelection();
});
</script>