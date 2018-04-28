<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('NAVBAR__TITLE') ?></h3>
        </div>
        <div class="box-body">

          <a class="btn btn-large btn-block btn-primary" href="<?= $this->Html->url(array('controller' => 'navbar', 'action' => 'add', 'admin' => true)) ?>"><?= $Lang->get('NAVBAR__ADD_LINK') ?></a>

          <hr>


          <table class="table table-bordered">
            <thead>
              <tr>
                <th><?= $Lang->get('GLOBAL__NAME') ?></th>
                <th><?= $Lang->get('URL') ?></th>
                <th><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
              </tr>
            </thead>
            <tbody id="sortable">
              <?php $i=0; foreach ($navbars as $key => $value) { $i++; ?>
              <li id="<?= $value['Navbar']['name'] ?>-<?= $i ?>">
                <tr style="cursor:move;" id="<?= $value['Navbar']['name'] ?>-<?= $i ?>">
                  <td>
				  <?php if(!empty($value['Navbar']['icon'])): ?> 
                     <i class="fa fa-<?= $value['Navbar']['icon'] ?>"></i>
                     <?php endif; ?>
                     <?= $value['Navbar']['name'] ?></td>
                  <?php if($value['Navbar']['url'] != '#' && $value['Navbar']['url'] !== false) { ?>
                    <td><a href="<?= $value['Navbar']['url'] ?>"><?= $value['Navbar']['url'] ?></a></td>
                  <?php } elseif($value['Navbar']['url'] === false) { ?>
                    <td>
                      <span class="label label-danger"><?= $Lang->get('PLUGIN__ERROR_UNINSTALLED') ?></span>
                    </td>
                  <?php } else { ?>
                    <td><a href="#"><?= $Lang->get('NAVBAR__LINK_TYPE_DROPDOWN') ?></a></td>
                  <?php } ?>
                  <td>
                    <a class="btn btn-info" href="<?= $this->Html->url(array('action' => 'edit', $value['Navbar']['id'])) ?>"><?= $Lang->get('GLOBAL__EDIT') ?></a>
                    <a onClick="confirmDel('<?= $this->Html->url(array('action' => 'delete', $value['Navbar']['id'])) ?>')" class="btn btn-danger"><?= $Lang->get('GLOBAL__DELETE') ?></a>
                  </td>
                </tr>
              </li>
              <?php } ?>
            </tbody>
          </table>
          <br>
          <div class="ajax-msg"></div>
          <button id="save" class="btn btn-success pull-right active" disabled="disabled"><?= $Lang->get('NAVBAR__SAVE_SUCCESS') ?></button>

        </div>
      </div>
    </div>
  </div>
</section>
<style>
  li {
    list-style-type: none;
  }
</style>
<script>
$(function() {
  $( "#sortable" ).sortable({
    axis: 'y',
    stop: function (event, ui) {
        $('#save').empty().html('<?= $Lang->get('NAVBAR__SAVE_IN_PROGRESS') ?>');
        var inputs = {};
        var nav = $(this).sortable('serialize');
        inputs['nav'] = nav;
        $('#yolo').text(nav);
        inputs['data[_Token][key]'] = '<?= $csrfToken ?>';
        $.post("<?= $this->Html->url(array('controller' => 'navbar', 'action' => 'save_ajax', 'admin' => true)) ?>", inputs, function(data) {
          data2 = data.split("|");
          if(data.indexOf('true') != -1) {
                $('#save').empty().html('<?= $Lang->get('NAVBAR__SAVE_SUCCESS') ?>');
              } else if(data.indexOf('false') != -1) {
                $('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('GLOBAL__ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
            } else {
            $('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('GLOBAL__ERROR') ?> :</b> <?= $Lang->get('ERROR__INTERNAL_ERROR') ?></i></div>');
          }
        });
      }
  });
  //$( "#sortable" ).disableSelection();
});
</script>
