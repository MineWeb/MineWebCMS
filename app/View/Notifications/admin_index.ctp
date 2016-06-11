<section class="content">
  <div class="row">
    <div class="col-md-8">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('NOTIFICATION__NOTIFICATIONS_LIST') ?></h3>
        </div>
        <div class="box-body">
          <table class="table">
            <thead>
              <tr>
                <th><?= $Lang->get('USER__USERNAME') ?></th>
                <th><?= $Lang->get('NOTIFICATION__FROM') ?></th>
                <th><?= $Lang->get('NOTIFICATION__CONTENT') ?></th>
                <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                <th><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('NOTIFICATION__ADD_NOTIFICATION') ?></h3>
        </div>
        <div class="box-body">

          <form action="<?= $this->Html->url(array('action' => 'setTo')) ?>" method="post" data-ajax="true" data-callback="afterSubmitTicket">

            <div class="form-group">
              <label><?= $Lang->get('NOTIFICATION__CONTENT') ?></label>
              <textarea class="form-control" name="content" maxlength="255"></textarea>
            </div>

            <div class="form-group">
              <div class="checkbox">
                <input name="from" type="checkbox">
                <label><?= $Lang->get('NOTIFICATION__DISPLAY_FROM') ?></label>
              </div>
            </div>

            <div class="form-group">
              <label><?= $Lang->get('NOTIFICATION__WHO') ?></label>
              <select class="form-control" name="user_id">
                <option value="all"><?= $Lang->get('NOTIFICATION__ALL') ?></option>
                <option value="user"><?= $Lang->get('NOTIFICATION__USER') ?></option>
              </select>
            </div>

            <script type="text/javascript">
              $('select[name="user_id"]').on('change', function(e) {
                if($(this).val() == 'all') {
                  $('#userInput').slideUp();
                } else {
                  $('#userInput').slideDown();
                }
              });
            </script>

            <div class="form-group" style="display:none;" id="userInput">
              <label><?= $Lang->get('NOTIFICATION__WHO_USERNAME') ?></label>
              <input type="text" name="user_pseudo" class="form-control">
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-info"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
            </div>
          </form>

        </div>
      </div>
    </div>
    <div class="col-md-4 pull-right">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('NOTIFICATION__OTHER_ACTIONS') ?></h3>
        </div>
        <div class="box-body">

          <button type="button" class="btn btn-danger btn-block"><?= $Lang->get('NOTIFICATION__DELETE_ALL_FROM_ALL_USERS') ?></button>

        </div>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
$(document).ready(function() {
  $('table').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": false,
    "info": false,
    "autoWidth": false,
    'searching': true,
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": "<?= $this->Html->url(array('action' => 'getAll')) ?>",
    "aoColumns": [
        {mData:"Notification.pseudo"},
        {mData:"Notification.from"},
        {mData:"Notification.content"},
        {mData:"Notification.created"},
        {mData:"Notification.actions"}
    ],
  });
});
</script>
