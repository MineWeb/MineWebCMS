<?php 
  
$this->EyPlugin = new EyPluginComponent;
?>
<div class="row-fluid">

	<div class="span12">

		<div class="top-bar">
			<h3><i class="icon-calendar"></i> <?= $Lang->get('PLUGINS_LIST') ?></h3>
		</div>

		<div class="well">
			<?= $this->Session->flash(); ?>

			<table class="table table-bordered">
              <thead>
                <tr>
                  <th><?= $Lang->get('NAME') ?></th>
                  <th><?= $Lang->get('AUTHOR') ?></th>
                  <th><?= $Lang->get('CREATED') ?></th>
                  <th><?= $Lang->get('VERSION') ?></th>
                  <th><?= $Lang->get('STATE') ?></th>
                  <th><?= $Lang->get('ACTION') ?></th>
                </tr>
              </thead>
              <tbody>
              	<?php 
                $plugin_list = $this->EyPlugin->get_list();
                if($plugin_list != false) {
                  foreach ($plugin_list as $key => $value) { ?>
                  <tr>
                    <td><?= $value['plugins']['name'] ?></td>
                    <td><?= $value['plugins']['author'] ?></td>
                    <td><?= $Lang->date($value['plugins']['created']) ?></td>
                    <td><?= $value['plugins']['version'] ?></td>
                    <td>
                      <?php
                      if($value['plugins']['state'] == 1) {
                        echo '<span class="label label-success">'.$Lang->get('ENABLED').'</span>';
                      } else {
                        echo '<span class="label label-danger">'.$Lang->get('DISABLED').'</span>';
                      }
                      ?>
                    </td>
                    <td>
                      <?php if($value['plugins']['state'] == 1) { ?>
                    	  <a href="<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'disable/'.$value['plugins']['id'], 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('DISABLED') ?></a>
                       <?php } else { ?>
                        <a href="<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'enable/'.$value['plugins']['id'], 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('ENABLED') ?></a>
                       <?php } ?>
                    	<a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'delete/'.$value['plugins']['id'], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('DELETE') ?></a>
                      <?php if($value['plugins']['version'] != $this->EyPlugin->get_last_version($value['plugins']['plugin_id'])) { ?>
                        <a href="<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'update/'.$value['plugins']['plugin_id'].'/'.$value['plugins']['name'], 'admin' => true)) ?>" class="btn btn-warning"><?= $Lang->get('UPDATE') ?></a>
                      <?php } ?>
                    </td>
                  </tr>
                  <?php } } else { echo 'Aucun plugin installé'; } ?>
              </tbody>
            </table>

		</div>

	</div>

</div>

<div class="row-fluid">

    <div class="span12">

      <div class="top-bar">
        <h3><i class="icon-calendar"></i> <?= $Lang->get('FREE_PLUGINS_AVAILABLE') ?></h3>
      </div>

      <div class="well">
        <?= $this->Session->flash(); ?>
        
        <?php 
        $free_plugins = $this->EyPlugin->get_free_plugins();
        if(!empty($free_plugins)) { ?>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th><?= $Lang->get('NAME') ?></th>
                <th><?= $Lang->get('AUTHOR') ?></th>
                <th><?= $Lang->get('VERSION') ?></th>
                <th><?= $Lang->get('ACTION') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($free_plugins as $key => $value) { ?>
              <tr>
                <td><?= $value['name'] ?></td>
                <td><?= $value['author'] ?></td>
                <td><?= $value['version'] ?></td>
                <td>
                  <a href="<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'install/'.$value['plugin_id'].'/'.$value['name'], 'admin' => true)) ?>" class="btn btn-success"><?= $Lang->get('INSTALL') ?></a>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        <?php } else { ?>
          <div class="alert alert-danger"><b><?= $Lang->get('ERROR') ?> : </b><?= $Lang->get('NONE_PLUGIN_AVAILABLE') ?></div>
        <?php } ?>

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