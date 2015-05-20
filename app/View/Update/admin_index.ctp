<div class="row-fluid">

	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-calendar"></i> <?= $Lang->get('UPDATE') ?></h3>
		</div>

		<div class="well">
			<?= $this->Session->flash(); ?>
      
      <center>
        <p class="text-center"><?= $Lang->get('LAST_VERSION') ?> : <?= $Update->get_version() ?></p>
  			<a class="btn btn-large btn-lg btn-primary" href="<?= $this->Html->url(array('controller' => 'update', 'action' => 'update', 'admin' => true)) ?>"><?= $Lang->get('UPDATE') ?></a>
      </center>
			<br>

      <hr>

      <h5 class="text-center"><?= $Lang->get('LOG_LAST_UPDATE') ?></h5>
      <p><b><?= $Lang->get('VERSION') ?></b> : <?= $logs['head']['version'] ?><br>
      <b><?= $Lang->get('CREATED') ?></b> : <?= $logs['head']['date'] ?></p>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th><?= $Lang->get('ACTION') ?></th>
            <th><?= $Lang->get('STATE') ?></th>
            <th><?= $Lang->get('FILE') ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($logs['update'] as $k => $v) { ?>
          <tr>
            <td><?= $Lang->get(key($v)) ?></td>
            <td><?= $Lang->get(strtoupper($v[key($v)]['statut'])) ?></td>
            <td><?= $v[key($v)]['arg'] ?></td>
            <td></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>

		</div>

		</div>

</div>