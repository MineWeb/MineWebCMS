<?php 
$this->Configuration = new ConfigurationComponent;
?>
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('THEME_LIST') ?></h3>
        </div>
        <div class="box-body">
        
          <table class="table table-bordered">
            <thead>
                <tr>
                  <th><?= $Lang->get('NAME') ?></th>
                  <th><?= $Lang->get('STATE') ?></th>
                  <th><?= $Lang->get('ACTION') ?></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Bootstrap</td>
                  <td>
                    <?php
                    if('default' == $this->Configuration->get('theme')) {
                      echo '<span class="label label-success">'.$Lang->get('ENABLED').'</span>';
                    } else {
                      echo '<span class="label label-danger">'.$Lang->get('DISABLED').'</span>';
                    }
                    ?>
                  </td>
                  <td>
                     <?php if('default' != $this->Configuration->get('theme')) { ?>
                      <a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'enable/default', 'admin' => true)) ?>" class="btn btn-success"><?= $Lang->get('ENABLED') ?></a>
                     <?php } ?>
                     <a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'custom/default', 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('CUSTOMIZATION') ?></a>
                  </td>
                </tr>
                <?php if(!empty($themes)) { ?>
                  <?php foreach ($themes as $key => $value) { ?>
                  <tr>
                    <td><?= $key ?></td>
                    <td>
                      <?php
                      if($key == $this->Configuration->get('theme')) {
                        echo '<span class="label label-success">'.$Lang->get('ENABLED').'</span>';
                      } else {
                        echo '<span class="label label-danger">'.$Lang->get('DISABLED').'</span>';
                      }
                      ?>
                    </td>
                    <td>
                       <?php if($key != $this->Configuration->get('theme')) { ?>
                        <a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'enable/'.$key, 'admin' => true)) ?>" class="btn btn-success"><?= $Lang->get('ENABLED') ?></a>
                       <?php } ?>
                       <?php if($key != "Mineweb") { ?>
                         <a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'theme', 'action' => 'delete/'.$key, 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('DELETE') ?></a>
                        <?php } ?>
                      <?php if(file_exists(ROOT.'/app/View/Themed/'.$key.'/config/config.json')) { ?>
                        <a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'custom/'.$key, 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('CUSTOMIZATION') ?></a>
                      <?php } ?>
                      <?php if(isset($value['last_version'])) { ?>
                        <?php if($value['version'] !== $value['last_version']) { ?>
                          <a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'update/'.$value['theme_id'].'/'.$key, 'admin' => true)) ?>" class="btn btn-warning"><?= $Lang->get('UPDATE') ?></a>
                        <?php } ?>
                      <?php } ?>
                    </td>
                  </tr>
                  <?php } ?>
                <?php } ?>
              </tbody>
          </table>

        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('FREE_THEMES_AVAILABLE') ?></h3>
        </div>
        <div class="box-body">
        
          <?php if(!empty($free_themes_available)) { ?>
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
              <?php foreach ($free_themes_available as $key => $value) { ?>
              <tr>
                <td><?= $value['name'] ?></td>
                <td><?= $value['author'] ?></td>
                <td><?= $value['version'] ?></td>
                <td>
                  <a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'install/'.$value['theme_id'].'/'.$value['name'], 'admin' => true)) ?>" class="btn btn-success"><?= $Lang->get('INSTALL') ?></a>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        <?php } else { ?>
          <div class="alert alert-danger"><b><?= $Lang->get('ERROR') ?> : </b><?= $Lang->get('NONE_THEME_AVAILABLE') ?></div>
        <?php } ?>

        </div>
      </div>
    </div>
  </div>
</section>