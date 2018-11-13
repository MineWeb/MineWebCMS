<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('THEME__LIST') ?></h3>
        </div>
        <div class="box-body table-responsive">

          <table class="table table-bordered">
            <thead>
                <tr>
                  <th><?= $Lang->get('GLOBAL__NAME') ?></th>
				          <th><?= $Lang->get('GLOBAL__AUTHOR') ?></th>
                  <th><?= $Lang->get('GLOBAL__VERSION') ?></th>
                  <th><?= $Lang->get('GLOBAL__STATUS') ?></th>
                  <th><?= $Lang->get('THEME__SUPPORTED_STATUS') ?></th>
                  <th><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Bootstrap</td>
				          <td>Eywek</td>
                  <td>N/A</td>
                  <td>
                    <?php
                    if('default' == $Configuration->getKey('theme')) {
                      echo '<span class="label label-success">'.$Lang->get('GLOBAL__ENABLED').'</span>';
                    } else {
                      echo '<span class="label label-danger">'.$Lang->get('GLOBAL__DISABLED').'</span>';
                    }
                    ?>
                  </td>
                  <td>
                    <span class="label label-success"><?= $Lang->get('GLOBAL__YES') ?></span>
                  </td>
                  <td>
                     <?php if('default' != $Configuration->getKey('theme')) { ?>
                      <a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'enable', 'default', 'admin' => true)) ?>" class="btn btn-success"><?= $Lang->get('GLOBAL__ENABLE') ?></a>
                     <?php } ?>
                     <a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'custom', 'default', 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('THEME__CUSTOMIZATION') ?></a>
                     <a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'custom_files', 'default', 'admin' => true)) ?>" class="btn btn-primary"><?= $Lang->get('THEME__CUSTOM_FILES') ?></a>
                  </td>
                </tr>
                <?php if(!empty($themesInstalled)) { ?>
                  <?php foreach ($themesInstalled as $key => $value) { ?>
                    <tr>
                      <td><?= $value->name ?></td>
					            <td><?= $value->author ?></td>
                      <td><?= $value->version ?></td>
                      <td>
                        <?php
                        if($value->slug == $Configuration->getKey('theme')) {
                          echo '<span class="label label-success">'.$Lang->get('GLOBAL__ENABLED').'</span>';
                        } else {
                          echo '<span class="label label-danger">'.$Lang->get('GLOBAL__DISABLED').'</span>';
                        }
                        ?>
                      </td>
                      <td>
                        <?php
                          if($value->supported) {
                            echo '<span class="label label-success">'.$Lang->get('GLOBAL__YES').'</span>';
                          } else {
                            echo '<span class="label label-danger">'.$Lang->get('GLOBAL__NO').'</span><br>';
                            echo '<small><i>'.$Lang->get('THEME__SUPPORTED_EXPLAIN').'</i></small>';
                          }
                        ?>
                      </td>
                      <td>
                        <?php if($value->slug != $Configuration->getKey('theme') && $value->valid) { ?>
                          <a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'enable', $value->slug, 'admin' => true)) ?>" class="btn btn-success"><?= $Lang->get('GLOBAL__ENABLE') ?></a>
                        <?php } ?>
                          <a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'theme', 'action' => 'delete', $value->slug, 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('GLOBAL__DELETE') ?></a>
                        <?php if(file_exists(ROOT.'/app/View/Themed/'.$value->slug.'/Config/view.ctp')) { ?>
                          <a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'custom', $value->slug, 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('THEME__CUSTOMIZATION') ?></a>
                        <?php } ?>
                        <a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'custom_files', $value->slug, 'admin' => true)) ?>" class="btn btn-primary"><?= $Lang->get('THEME__CUSTOM_FILES') ?></a>
                        <?php if(isset($value->lastVersion)) { ?>
                          <?php if($value->version !== $value->lastVersion) { ?>
                            <a <?= (explode('.', $value->lastVersion)[0] > explode('.', $value->version)[0] ? 'data-warning-update' : '') ?> href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'update', 'admin' => true, $value->slug)) ?>" class="btn btn-warning"><?= $Lang->get('GLOBAL__UPDATE') ?></a>
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
          <h3 class="box-title"><?= $Lang->get('THEME__AVAILABLE') ?></h3>
        </div>
        <div class="box-body table-responsive">

          <?php if(!empty($themesAvailable)) { ?>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th><?= $Lang->get('GLOBAL__NAME') ?></th>
                <th><?= $Lang->get('GLOBAL__AUTHOR') ?></th>
                <th><?= $Lang->get('GLOBAL__VERSION') ?></th>
                <th><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($themesAvailable as $key => $value) { ?>
              <tr>
                <td><?= $value['name'] ?></td>
                <td><?= isset($value['author']) ? $value['author'] : '' ?></td>
                <td><?= isset($value['version']) ? $value['version'] : $Lang->get('THEME__NEED_PURCHASE') ?></td>
                <td>
                  <?php if ($value['free']): ?>
                    <a href="<?= $this->Html->url(array('controller' => 'theme', 'action' => 'install', 'admin' => true, $value['slug'])) ?>" class="btn btn-success"><?= $Lang->get('INSTALL__INSTALL') ?></a>
                  <?php 
                  else: // display contact 
                    foreach ($value['contact'] as $contact) {
                      if ($contact['type'] == 'discord') {
                        echo '<button class="btn btn-info" style="background-color: #7289da;border-color: #7289da;">Discord - ' . $contact['value'] . '</button>';
                      } else if ($contact['type'] === 'email') {
                        echo '<button class="btn btn-info">Email - ' . $contact['value'] . '</button>';
                      }
                      echo '&nbsp;&nbsp;';
                    }
                  endif;
                  ?>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        <?php } else { ?>
          <div class="alert alert-danger"><b><?= $Lang->get('GLOBAL__ERROR') ?> : </b><?= $Lang->get('THEME__NONE_AVAILABLE') ?></div>
        <?php } ?>

        </div>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
    $('a[data-warning-update]').on('click', function (e) {
        e.preventDefault();
        if (confirm("<?= $Lang->get('UPDATE__MAJOR_WARNING_EXTENSION') ?>"))
            window.location = $(this).attr('href');
    });
</script>
