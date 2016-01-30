<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('GLOBAL__UPDATE') ?></h3>
        </div>
        <div class="box-body">

           <center>
            <p class="text-center"><?= $Lang->get('LAST_VERSION') ?> : <?= $Update->update['version'] ?></p>
            <div class="btn-group">
              <button id="update" class="btn btn-large btn-primary"><?= $Lang->get('GLOBAL__UPDATE') ?></button>
              <a href="<?= $this->Html->url(array('action' => 'check')) ?>" class="btn btn-large btn-info"><?= $Lang->get('UPDATE__CHECK_STATUS') ?></a>
              <a href="http://mineweb.org/changelog" target="_blank" class="btn btn-large btn-default"><?= $Lang->get('UPDATE__VIEW_CHANGELOG') ?></a>
            </div>
            <div id="update-msg"></div>
            <div class="progress progress-striped active" style="display:none;">
              <div class="bar" style="width: 40%;"></div>
            </div>
          </center>
          <br>

          <?php if(!empty($logs)) { ?>
            <hr>
            <h5 class="text-center"><?= $Lang->get('LOG_LAST_UPDATE') ?></h5>
            <div id="log-update">
              <p><b><?= $Lang->get('GLOBAL__VERSION') ?></b> : <?= $logs['head']['version'] ?><br>
              <b><?= $Lang->get('GLOBAL__CREATED') ?></b> : <?= $logs['head']['date'] ?></p>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                    <th><?= $Lang->get('GLOBAL__STATUS') ?></th>
                    <th><?= $Lang->get('FILE') ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(!empty($logs['update'])) { ?>
                    <?php foreach ($logs['update'] as $k => $v) { ?>
                    <tr>
                      <td><?= $Lang->get(key($v)) ?></td>
                      <td><?= $Lang->get(strtoupper($v[key($v)]['statut'])) ?></td>
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
  $('#update').click(function() {
    $('#update').attr('disabled', 'disabled');
    $('#update-msg').html('<br><div class="alert alert-info"><?= $Lang->get('ON_UPDATE') ?></div>').fadeIn(500);
    $.ajax({
      xhr: function() {
            $('.progress').css('display', 'block');
            var xhr = new window.XMLHttpRequest();
            xhr.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    $('.progress .bar').css('width', '' + (100 * e.loaded / e.total) + '%');
                }
            });
            return xhr;
        },
      type: 'POST',
      url: '<?= $this->Html->url(array('controller' => 'update', 'action' => 'update', 'admin' => true)) ?>',
      data: {},
      complete: function(response, status, xhr) {
        data2 = response['responseText'].split("|");
        if(data2.indexOf('true') != -1) {
          $('#update-msg').empty().html('<div class="alert alert-success" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><b><?= $Lang->get('GLOBAL__SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
            $('#update').remove();
            $("#log-update").load("<?= $this->Html->url(array('controller' => 'update', 'action' => 'index', 'admin' => true)) ?> #log-update").fadeIn(500);
        } else if(data2.indexOf('false') != -1) {
          $('#update-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><b><?= $Lang->get('GLOBAL__ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
        }
        $('.progress').remove();
      }
    });
  });
</script>
