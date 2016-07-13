<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('USER__LIST') ?></h3>
        </div>
        <div class="box-body">
          <?php if($type == '0') { ?>
            <table class="table table-bordered" id="users">
              <thead>
                <tr>
                  <th><?= $Lang->get('USER__TITLE') ?></th>
                  <th><?= $Lang->get('USER__EMAIL') ?></th>
                  <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                  <th><?= $Lang->get('USER__RANK') ?></th>
                  <th class="right"><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          <?php } else { ?>
            <form action="<?= $this->Html->url(array('action' => 'liveSearch')) ?>" method="search">

              <div class="form-group">
                <label><?= $Lang->get('GLOBAL__SEARCH') ?></label>
                <input type="text" name="search" placeholder="Pseudo..." autocomplete="off" class="form-control">
                <div class="list-group" style="display:none;">
                </div>
              </div>

            </form>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
<?php if($type == '0') { ?>
  $(document).ready(function() {
    $('#users').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": false,
      "info": false,
      "autoWidth": false,
      'searching': true,
      "bProcessing": true,
      "bServerSide": true,
      "sAjaxSource": "<?= $this->Html->url(array('action' => 'get_users')) ?>",
      "aoColumns": [
          {mData:"User.pseudo","bSearchable": true},
          {mData:"User.email","bSearchable": true},
          {mData:"User.created","bSearchable": true},
          {mData:"User.rank","bSearchable": false},
          {mData:"actions","bSearchable": false}
      ]
    });
  });
<?php } else { ?>
  $('form[method="search"]').each(function(e) {

    $(this).on('submit', function(e) {
      e.preventDefault();
      var val = $(this).find('input[name="search"]').val();
      window.location = '<?= $this->Html->url(array('action' => 'edit')) ?>/'+val;
    });

    var url = $(this).attr('action');
    var form = $(this);

    $(this).find('input[name="search"]').keyup(function(e) {

      var value = $(this).val();

      $.ajax({
        url: url+'/'+encodeURI(value),
        method: 'GET',
        dataType: 'JSON',
        success: function(data) {

          form.find('.list-group').empty();

          if(data.status) {

            var users = data.data;

            for (var i = 0; i < users.length; i++) {

              console.log(users[i]);

              form.find('.list-group').prepend('<a href="<?= $this->Html->url(array('action' => 'edit')) ?>/'+users[i]['id']+'" class="list-group-item">'+users[i]['pseudo']+'</a>')

            }

            form.find('.list-group').slideDown(250);

          } else {
            form.find('.list-group').slideUp(250);
          }

        },
        error: function(data) { form.find('.list-group').slideUp(250); }
      })

    });
  });
<?php } ?>
</script>
