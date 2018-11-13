<section class="content">
  <div class="row">
    <div class="col-md-12">

      <div class="ajax"></div>

      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('PLUGIN__LIST') ?></h3>
        </div>

        <div class="box-body table-responsive">

          <?php
          $pluginList = $EyPlugin->pluginsLoaded;
            if(!empty($pluginList)) {
          ?>
            <table class="table table-bordered" id="plugin-installed">
              <thead>
                <tr>
                  <th><?= $Lang->get('GLOBAL__NAME') ?></th>
                  <th><?= $Lang->get('GLOBAL__AUTHOR') ?></th>
                  <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                  <th><?= $Lang->get('GLOBAL__VERSION') ?></th>
                  <th><?= $Lang->get('PLUGIN__LOADED') ?></th>
                  <th><?= $Lang->get('GLOBAL__STATUS') ?></th>
                  <th><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php
                $versions = $EyPlugin->getPluginsLastVersion(array_map(function ($plugin) {
                  return $plugin->slug;
                }, (array)$pluginList));
                foreach ($pluginList as $key => $value) {
                ?>
                  <tr>
                    <td><?= $value->name ?></td>
                    <td><?= $value->author ?></td>
                    <td><?= $Lang->date($value->DBinstall) ?></td>
                    <td><?= $value->version ?></td>
                    <td>
                      <?= ($value->loaded) ? '<span class="label label-success">'.$Lang->get('GLOBAL__YES').'</span>' : '<span class="label label-danger">'.$Lang->get('GLOBAL__NO').'</span>' ?>
                    </td>
                    <td>
                      <?= ($value->active) ? '<span class="label label-success">'.$Lang->get('GLOBAL__ENABLED').'</span>' : '<span class="label label-danger">'.$Lang->get('GLOBAL__DISABLED').'</span>' ?>
                    </td>
                    <td>
                      <?php if($value->active) { ?>
                        <a href="<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'disable/'.$value->DBid, 'admin' => true)) ?>" class="btn btn-info disable"><?= $Lang->get('GLOBAL__DISABLE') ?></a>
                       <?php } else { ?>
                        <a href="<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'enable/'.$value->DBid, 'admin' => true)) ?>" class="btn btn-info enable"><?= $Lang->get('GLOBAL__ENABLE') ?></a>
                       <?php } ?>
                      <a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'delete/'.$value->DBid, 'admin' => true)) ?>')" class="btn btn-danger delete"><?= $Lang->get('GLOBAL__DELETE') ?></a>
                      <?php
                      $lastVersion = (isset($versions[$value->slug])) ? $versions[$value->slug] : false;
                      if($lastVersion && $value->version != $lastVersion) { ?>
                        <a <?= (explode('.', $lastVersion)[0] > explode('.', $value->version)[0] ? 'data-warning-update' : '') ?> href="<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'update', $value->slug, 'admin' => true)) ?>" class="btn btn-warning update"><?= $Lang->get('GLOBAL__UPDATE') ?></a> <!-- ICI -->
                      <?php } ?>
                    </td>
                  </tr>
                  <?php } ?>
              </tbody>
            </table>
          <?php } else {
            echo '<div class="alert alert-danger">'.$Lang->get('PLUGIN__NONE_INSTALLED').'</div>';
          } ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('PLUGIN__AVAILABLE') ?></h3>
        </div>
        <div class="box-body table-responsive">
          <?php
          $free_plugins = $EyPlugin->getFreePlugins(true, true);
          if(!empty($free_plugins)) { ?>
            <table class="table table-bordered" id="plugin-not-installed">
              <thead>
                <tr>
                  <th><?= $Lang->get('GLOBAL__NAME') ?></th>
                  <th><?= $Lang->get('GLOBAL__AUTHOR') ?></th>
                  <th><?= $Lang->get('GLOBAL__VERSION') ?></th>
                  <th><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($free_plugins as $key => $value) {
                ?>
                  <tr plugin-slug="<?= $value['slug'] ?>">
                    <td><?= $value['name'] ?></td>
                    <td><?= isset($value['author']) ? $value['author'] : '' ?></td>
                    <td><?= isset($value['version']) ? $value['version'] : $Lang->get('PLUGIN__NEED_PURCHASE') ?></td>
                    <td>
                      <?php if ($value['free']): ?>
                        <btn class="btn btn-success install" slug="<?= $value['slug'] ?>"><?= $Lang->get('PLUGIN__INSTALL') ?></btn>
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
            <div class="alert alert-danger"><b><?= $Lang->get('GLOBAL__ERROR') ?> : </b><?= $Lang->get('PLUGIN__NONE_AVAILABLE') ?></div>
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

  $('.install').click(function(e) {
    e.preventDefault();

    var slug = $(this).attr('slug');

    var btn = $(this);

    if(slug !== undefined) {

      // Désactivation de toute action
      $('.install').each(function(e) {
        $(this).addClass('disabled');
      });
      $('.update').each(function(e) {
        $(this).addClass('disabled');
      });
      $('.delete').each(function(e) {
        $(this).addClass('disabled');
      });
      $('.enable').each(function(e) {
        $(this).addClass('disabled');
      });
      $('.disable').each(function(e) {
        $(this).addClass('disabled');
      });

      // Mise à jour du texte sur le bouton
      $(this).html('<?= $Lang->get('PLUGIN__INSTALL_LOADING') ?>...');

      // On préviens l'utilisateur avec un message plus clair
      $('.ajax').empty().html('<div class="alert alert-info"><?= $Lang->get('PLUGIN__INSTALL_LOADING') ?>...</b></div>').fadeIn(500);

      // On lance la requête
      $.get('<?= $this->Html->url(array('action' => 'install')) ?>/'+slug, function(data) {
        if(typeof data != 'object') {
          data = JSON.parse(data);
        }
        if(data !== false) {

          if(data.statut == "success") {
            // on met le message
            $('.ajax').empty().html('<div class="alert alert-success"><b><?= $Lang->get('GLOBAL__SUCCESS') ?> :</b> <?= $Lang->get('PLUGIN__INSTALL_SUCCESS') ?></div>').fadeIn(500);

            // on bouge le plugin dans le tableau dans les plugins installés
            $('table#plugin-not-installed').find('tr[plugin-slug="'+slug+'"]').slideUp(250);

            var tr = '';
            tr += '<tr>';
              tr += '<td>'+data.plugin.name+'</td>';
              tr += '<td>'+data.plugin.author+'</td>';
              tr += '<td>'+data.plugin.dateformatted+'</td>';
              tr += '<td>'+data.plugin.version+'</td>';
              tr += '<td><span class="label label-success"><?= $Lang->get('GLOBAL__YES') ?></span></td>';
              tr += '<td><span class="label label-success"><?= $Lang->get('GLOBAL__ENABLED') ?></span></td>';
              tr += '<td>';
                tr += '<a href="<?= $this->Html->url(array('action' => 'disable')) ?>/'+data.plugin.DBid+'" class="btn btn-info"><?= $Lang->get('GLOBAL__DISABLED') ?></a>';
                tr += "\n";
                tr += '<a onClick="confirmDel(\'<?= $this->Html->url(array('controller' => 'plugin', 'action' => 'delete', 'admin' => true)) ?>/'+data.plugin.DBid+'\')" class="btn btn-danger"><?= $Lang->get('GLOBAL__DELETE') ?></a>';
              tr += '</td>';
            tr += '</tr>';

            $('table#plugin-installed tr:last').after(tr);

          } else if(data.statut == "error") {
            $('.ajax').empty().html('<div class="alert alert-error"><b><?= $Lang->get('GLOBAL__ERROR') ?> : </b>'+data.msg+'</div>').fadeIn(500);
          } else {
            $('.ajax').empty().html('<div class="alert alert-error"><b><?= $Lang->get('GLOBAL__ERROR') ?> : </b><?= addslashes($Lang->get('ERROR__INTERNAL_ERROR')) ?></div>').fadeIn(500);
          }

        }

        // On annule les désactivations
        $('.install').each(function(e) {
          $(this).removeClass('disabled');
        });
        $('.update').each(function(e) {
          $(this).removeClass('disabled');
        });
        $('.delete').each(function(e) {
          $(this).removeClass('disabled');
        });
        $('.enable').each(function(e) {
          $(this).removeClass('disabled');
        });
        $('.disable').each(function(e) {
          $(this).removeClass('disabled');
        });

        // On remet le texte par défaut
        btn.html('<?= $Lang->get('PLUGIN__INSTALL') ?>');

        return;
      });


    }

    return;

  });
</script>
