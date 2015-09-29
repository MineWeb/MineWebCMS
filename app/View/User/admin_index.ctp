<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('USER_LIST') ?></h3>
        </div>
        <div class="box-body"> 
            <table class="table table-bordered dataTable">
            <thead>
              <tr>
                <th><?= $Lang->get('USER') ?></th>
                <th><?= $Lang->get('CREATED') ?></th>
                <th><?= $Lang->get('RANK') ?></th>
                <th class="right"><?= $Lang->get('ACTIONS') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $value => $v) { ?>
                <tr>
                  <td><?= $v["User"]["pseudo"] ?></td>
                  <td>Le <?= $Lang->date($v["User"]["created"]) ?></td>
                  <?php foreach ($available_ranks as $key => $value) {
                    if($v['User']['rank'] == $key) {
                      switch ($key) {
                        case 3:
                          $label = 'danger';
                          break;
                        case 4:
                          $label = 'danger';
                          break;
                        case 2:
                          $label = 'warning';
                          break;
                        case 5:
                          $label = 'primary';
                          break;
                        case 0:
                          $label = 'success';
                          break;
                        
                        default:
                          $label = 'info';
                          break;
                      }
                     echo '<td><span class="label label-'.$label.'">'.$value.'</span></td>';
                      unset($label);
                    }
                  } ?>
                  <td class="right">
                    <a href="<?= $this->Html->url(array('controller' => 'user', 'action' => 'edit/'.$v["User"]["id"], 'admin' => true))?>" class="btn btn-info"><?= $Lang->get('EDIT') ?></a>
                    <a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'user', 'action' => 'delete/'.$v["User"]["id"], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('DELETE') ?></button>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>