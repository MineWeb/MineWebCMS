<section class="content">
  <div class="row">
    <div class="col-md-8">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('NOTIFICATION__NOTIFICATIONS_LIST') ?></h3>
        </div>
        <div class="box-body">
          <table class="table" style="table-layout: fixed;word-wrap: break-word;">
            <thead>
              <tr>
                <th><?= $Lang->get('USER__USERNAME') ?></th>
                <th><?= $Lang->get('NOTIFICATION__GROUP') ?></th>
                <th><?= $Lang->get('NOTIFICATION__FROM') ?></th>
                <th><?= $Lang->get('NOTIFICATION__CONTENT') ?></th>
                <th><?= $Lang->get('NOTIFICATION__TYPE') ?></th>
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

          <form action="<?= $this->Html->url(array('action' => 'setTo')) ?>" method="post" data-ajax="true" data-callback-function="afterSendNotification">

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
    <div class="col-md-4 col-sm-12 col-xs-12 pull-right">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('NOTIFICATION__OTHER_ACTIONS') ?></h3>
        </div>
        <div class="box-body">

            <a href="<?= $this->Html->url(array('action' => 'clearAllFromAllUsers')) ?>" class="btn btn-danger btn-block" id="delete-all"><?= $Lang->get('NOTIFICATION__DELETE_ALL_FROM_ALL_USERS') ?></a>
            <a href="<?= $this->Html->url(array('action' => 'markAllAsSeenFromAllUsers')) ?>" class="btn btn-default btn-block" id="mark-all-as-seen"><?= $Lang->get('NOTIFICATION__MARK_ALL_AS_SEEN_FROM_ALL_USERS') ?></a>

            <hr>

            <form method="post" action="<?= $this->Html->url(array('action' => 'clearAllFromGroup')) ?>" data-ajax="true" data-callback-function="afterSendNotification">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">#</span>
                    <input type="text" class="form-control" name="group" placeholder="<?= $Lang->get('NOTIFICATION__DELETE_ALL_FROM_GROUP_INPUT') ?>">
                    <span class="input-group-btn">
                        <button class="btn btn-danger"><?= $Lang->get('NOTIFICATION__DELETE_ALL_FROM_GROUP_BTN') ?></button>
                    </span>
                </div>
            </form>

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
    "searching": true,
    "ordering": false,
    "info": false,
    "autoWidth": false,
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": "<?= $this->Html->url(array('action' => 'getAll')) ?>",
    "aoColumns": [
        {mData:"User.pseudo"},
        {mData:"Notification.group"},
        {mData:"Notification.from", "bSearchable": false},
        {mData:"Notification.content"},
        {mData:"Notification.type", "bSearchable": false},
        {mData:"Notification.created"},
        {mData:"Notification.actions", "bSearchable": false}
    ],
  });

  var table = $('table').DataTable();

  $('table tbody').on('click', '.delete-notification', function(e) {

    e.preventDefault();

    var notification = $(this);
    var url = notification.attr('href');

    $.ajax({
      url: url,
      method: 'GET',
      dataType: 'JSON',
      success: function(data) {
        if(data.status) {
          table
            .row(notification.parents('tr'))
            .remove()
            .draw();
        } else {
          alert('Error!');
          console.log(data);
        }
      },
      error: function() {
        alert('Error!');
      }
    });

  });

  $('table tbody').on('click', '.mark-as-seen', function(e) {

    e.preventDefault();

    var btn = $(this);
    var url = btn.attr('href');

    $.ajax({
      url: url,
      method: 'GET',
      dataType: 'JSON',
      success: function(data) {
        if(data.status) {
          btn.addClass('disabled').addClass('active').attr('disabled', true).attr('href', '#').html(btn.attr('data-seen'));
        } else {
          alert('Error!');
          console.log(data);
        }
      },
      error: function() {
        alert('Error!');
      }
    });

  });

  $('#delete-all').on('click', function(e) {
    e.preventDefault();

    var btn = $(this);
    var url = btn.attr('href');

    $.ajax({
      url: url,
      method: 'GET',
      dataType: 'JSON',
      success: function(data) {
        if(data.status) {
          table.ajax.reload();
        } else {
          alert('Error!');
          console.log(data);
        }
      },
      error: function() {
        alert('Error!');
      }
    });

  });

  $('#mark-all-as-seen').on('click', function(e) {
    e.preventDefault();

    var btn = $(this);
    var url = btn.attr('href');

    $.ajax({
      url: url,
      method: 'GET',
      dataType: 'JSON',
      success: function(data) {
        if(data.status) {
          table.ajax.reload();
        } else {
          alert('Error!');
          console.log(data);
        }
      },
      error: function() {
        alert('Error!');
      }
    });

  });

});

function afterSendNotification() {
  var table = $('table').DataTable();
  table.ajax.reload();
}
</script>
