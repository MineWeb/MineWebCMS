<?php 
$this->EyPlugin = new EyPluginComponent;
?>
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('PLUGINS_LIST') ?></h3>
        </div>
        <div class="box-body">
          <?php
          $pluginList = $this->EyPlugin->loadPlugins();
            if(!empty($pluginList)) {
          ?>
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
                  foreach ($pluginList as $key => $value) { ?>
                  <tr>
                    <td><?= $value->name ?></td>
                    <td><?= $value->author ?></td>
                    <td><?= $Lang->date($value->DBinstall) ?></td>
                    <td><?= $value->version ?></td>
                    <td>
                      <?= ($value->active) ? '<span class="label label-success">'.$Lang->get('ENABLED').'</span>' : '<span class="label label-danger">'.$Lang->get('DISABLED').'</span>' ?>
                    </td>
                    <td>
                      <?php if($value->active) { ?>
                        <a href="<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'disable/'.$value->DBid, 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('DISABLED') ?></a>
                       <?php } else { ?>
                        <a href="<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'enable/'.$value->DBid, 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('ENABLED') ?></a>
                       <?php } ?>
                      <a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'delete/'.$value->DBid, 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('DELETE') ?></a>
                      <?php if($value->version != $this->EyPlugin->getPluginLastVersion($value->apiID)) { ?>
                        <a href="<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'update/'.$value->apiID.'/'.$value->slug, 'admin' => true)) ?>" class="btn btn-warning"><?= $Lang->get('UPDATE') ?></a>
                      <?php } ?>
                    </td>
                  </tr>
                  <?php } ?>
              </tbody>
            </table>
          <?php } else { 
            echo '<div class="alert alert-danger">'.$Lang->get('NONE_PLUGIN_INSTALLED').'</div>'; 
          } ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('FREE_PLUGINS_AVAILABLE') ?></h3>
        </div>
        <div class="box-body">
          <?php 
          $free_plugins = $this->EyPlugin->getFreePlugins();
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
                    <a href="<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'install/'.$value['apiID'].'/'.$value['name'], 'admin' => true)) ?>" class="btn btn-success"><?= $Lang->get('INSTALL') ?></a>
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
  </div>
</section>