<?php   ?>
<div class="row-fluid">

	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-calendar"></i> <?= $Lang->get('PAGES_LIST') ?></h3>
		</div>

		<div class="well">
			<?= $this->Session->flash(); ?>
			<a class="btn btn-large btn-block btn-primary" href="<?= $this->Html->url(array('controller' => 'pages', 'action' => 'add', 'admin' => true)) ?>"><?= $Lang->get('ADD_PAGE') ?></a>
			<br><br>

			<table class="table table-bordered">
              <thead>
                <tr>
                  <th><?= $Lang->get('TITLE') ?></th>
                  <th><?= $Lang->get('BY') ?></th>
                  <th><?= $Lang->get('POSTED_ON') ?></th>
                  <th><?= $Lang->get('LAST_UPDATE') ?></th>
                  <th><?= $Lang->get('URL') ?></th>
                  <th><?= $Lang->get('ACTION') ?></th>
                </tr>
              </thead>
              <tbody>
              	<?php foreach ($pages as $key => $value) { ?>
                <tr>
                  <td><?= $value['Page']['title'] ?></td>
                  <td><?= $value['Page']['author'] ?></td>
                  <td><?= $Lang->date($value['Page']['created']) ?></td>
                  <td><?= $Lang->date($value['Page']['updated']) ?></td>
                  <td><a href="<?= $this->Html->url(array('controller' => 'p', 'action' => $value['Page']['slug'], 'admin' => false)) ?>"><?= $value['Page']['slug'] ?></a></td>
                  <td>
                  	<a href="<?= $this->Html->url(array('controller' => 'pages', 'action' => 'edit/'.$value['Page']['id'], 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('EDIT') ?></a>
                  	<a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'pages', 'action' => 'delete/'.$value['Page']['id'], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('DELETE') ?></a>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>

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