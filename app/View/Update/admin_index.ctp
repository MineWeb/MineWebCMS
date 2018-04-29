<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title" style="width:100%;">
            <?= $Lang->get('GLOBAL__UPDATE') ?>
          </h3>
        </div>
        <div class="box-body">

           <div style="text-align: center;">
            <p class="text-center"><?= $Lang->get('UPDATE__LAST_VERSION') ?> : <?= $Update->lastVersion ?></p>
            <?php
            if (explode('.', $Update->lastVersion)[0] > explode('.', $Update->cmsVersion)[0])
                echo '<div class="alert alert-warning">' . $Lang->get('UPDATE__MAJOR_WARNING') . '</div>';
            ?>
            <div class="btn-group">
              <button id="update" class="btn btn-large btn-primary"><?= $Lang->get('GLOBAL__UPDATE') ?></button>
              <a class="btn btn-warning" href="<?= $this->Html->url(array('action' => 'clear_cache')) ?>"><?= $Lang->get('UPDATE__CLEAR_CACHE') ?></a>
              <a href="<?= $this->Html->url(array('action' => 'check')) ?>" class="btn btn-large btn-info"><?= $Lang->get('UPDATE__CHECK_STATUS') ?></a>
              <a href="https://github.com/MineWeb/MineWebCMS/releases" target="_blank" class="btn btn-large btn-default"><?= $Lang->get('UPDATE__VIEW_CHANGELOG') ?></a>
            </div>
            <div id="update-msg"></div>
            <div class="progress progress-striped active" style="display:none;">
              <div class="bar" style="width: 40%;"></div>
            </div>
          </div>
          <br>

          <?php if(!empty($logs)) { ?>
            <hr>
            <h5 class="text-center"><?= $Lang->get('UPDATE__LOGS') ?></h5>
            <div id="log-update">
              <p><b><?= $Lang->get('GLOBAL__VERSION') ?></b> : <?= $logs['head']['version'] ?><br>
              <b><?= $Lang->get('GLOBAL__CREATED') ?></b> : <?= $logs['head']['date'] ?></p>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                    <th><?= $Lang->get('GLOBAL__STATUS') ?></th>
                    <th><?= $Lang->get('UPDATE__FILE') ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(!empty($logs['update'])) { ?>
                    <?php foreach ($logs['update'] as $k => $v) { ?>
                    <tr>
                      <td><?= $Lang->get('UPDATE__LOGS_'.key($v)) ?></td>
                      <td>
                        <?php
                          $status = strtoupper($v[key($v)]['statut']);
                          echo '<div class="label label-';
                          echo ($status == "SUCCESS") ? 'success' : 'danger';
                          echo '">';
                            echo $Lang->get('GLOBAL__'.$status);
                          echo '</div>';
                        ?>
                      </td>
                      <td><?= $v[key($v)]['arg'] ?></td>
                      <td></td>
                    </tr>
                    <?php } ?>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          <?php } ?>

        </div>
      </div>
    </div>
  </div>
</section>
<script>
  function callMAJ(updaterUpdated) {
    var inputs = {};
    inputs["data[_Token][key]"] = '<?= $csrfToken ?>';

    if(updaterUpdated === undefined || updaterUpdated.length == 0) {
      updaterUpdated = '0';
    }

    $.ajax({
      type: 'POST',
      url: '<?= $this->Html->url(array('action' => 'update')) ?>/'+updaterUpdated,
      data: inputs,
      dataType: 'JSON',
      success: function(data) {

        if(data.statut == "success") {
          $('#update-msg').empty().html('<div class="alert alert-success" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><b><?= $Lang->get('GLOBAL__SUCCESS') ?> :</b> '+data.msg+'</i></div>').fadeIn(500);
          $('#update').remove();
          $("#log-update").load("<?= $this->Html->url(array('action' => 'index')) ?> #log-update").fadeIn(500);
          window.location = '<?= $this->Html->url(array('action' => 'clear_cache')) ?>';
        } else if(data.statut == "continue") {
          callMAJ('1');
        } else if(data.statut == "error") {
          $('#update-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><b><?= $Lang->get('GLOBAL__ERROR') ?> :</b> '+data.msg+'</i></div>').fadeIn(500);
        } else {
          alert('Error!');
        }

      },
      error: function() {
        alert('Error!');
      }
    });
  }

  $('#update').click(function() {
    $('#update').attr('disabled', 'disabled');
    $('#update-msg').html('<br><div class="alert alert-info"><?= $Lang->get('UPDATE__LOADING') ?></div>').fadeIn(500);

    callMAJ();
  });

  $('#forced_updates').on('change', function(e) {
    e.preventDefault();

    $.get('<?= $this->Html->url(array('action' => 'switchForceUpdates')) ?>');

  });

</script>
