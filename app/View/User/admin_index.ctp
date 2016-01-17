<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('USER_LIST') ?></h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered" id="users">
            <thead>
              <tr>
                <th><?= $Lang->get('USER') ?></th>
                <th><?= $Lang->get('CREATED') ?></th>
                <th><?= $Lang->get('RANK') ?></th>
                <th class="right"><?= $Lang->get('ACTIONS') ?></th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
$(document).ready(function() {
  $('#users').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": false,
    "info": false,
    "autoWidth": false,
    'searching': true,
    'ajax': '<?= $this->Html->url(array('action' => 'get_users')) ?>',
  });
});
</script>
