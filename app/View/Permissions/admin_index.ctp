<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('PERMISSIONS') ?></h3>
        </div>
        <div class="box-body">

          <button data-toggle="modal" data-target="#addRank" class="btn btn-block btn-success"><?= $Lang->get('ADD_RANK') ?></button>

          <hr>

          <form action="" method="post">
            <input name="data[_Token][key]" value="<?= $csrfToken ?>" type="hidden">

            <table class="table table-bordered">
                <thead>
                  <tr>
                    <th><?= $Lang->get('PERMISSIONS') ?></th>
                    <th><?= $Lang->get('NORMAL') ?></th>
                    <th><?= $Lang->get('MODERATOR') ?></th>
                    <th><?= $Lang->get('ADMINISTRATOR') ?></th>
                    <?php
                      if(!empty($custom_ranks)) {
                        foreach ($custom_ranks as $k => $data) {
                          echo '<th>'.$data['Rank']['name'].'</th>';
                        }
                      }
                    ?>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $config = $Permissions->get_all();
                  foreach ($config as $key => $value) { ?>
                    <tr>
                      <td><?= $Lang->get('PERMISSIONS__'.$key) ?></td>
                      <td><input type="checkbox" name="<?= $key ?>-0"<?php if($value['0'] == "true") { echo ' checked="checked"'; } ?>></td>
                      <td><input type="checkbox" name="<?= $key ?>-2"<?php if($value['2'] == "true") { echo ' checked="checked"'; } ?>></td>
                      <td><input type="checkbox" checked="checked" disabled="disabled"></td>
                      <?php if(!empty($custom_ranks)) { ?>
                        <?php foreach ($custom_ranks as $k => $data) { ?>
                      <td><input type="checkbox" name="<?= $key ?>-<?= $data['Rank']['rank_id'] ?>"<?php if(!empty($value[$data['Rank']['rank_id']]) && $value[$data['Rank']['rank_id']] == "true") { echo ' checked="checked"'; } ?>></td>
                      <?php } ?>
                    <?php } ?>
                    </tr>
                  <?php } ?>
                  <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <?php
                    if(!empty($custom_ranks)) {
                      foreach ($custom_ranks as $k => $data) {
                        echo '<td><a class="btn btn-danger" href="'.$this->Html->url(array('controller' => 'permissions', 'action' => 'delete_rank', 'admin' => true, $data['Rank']['rank_id'])).'">'.$Lang->get('DELETE').'</a></td>';
                      }
                    }
                  ?>
                  </tr>
                </tbody>
              </table>

            <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>

          </form>

        </div>
      </div>
    </div>
  </div>
</section>
<div class="modal fade" id="addRank" tabindex="-1" role="dialog" aria-labelledby="addRankLabel" aria-hidden="true" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"><?= $Lang->get('ADD_RANK') ?></h4>
      </div>
      <div class="modal-body">
        <form action="<?= $this->Html->url(array('controller' => 'permissions', 'action' => 'add_rank', 'admin' => true)) ?>" method="post">
          <input type="hidden" data-ajax="true" data-redirect-url="<?= $this->Html->url(array('controller' => 'permissions', 'action' => 'index', 'admin' => true)) ?>">
          <div class="ajax-msg"></div>
          <div class="input-group">
            <input type="text" class="form-control" name="name" placeholder="<?= $Lang->get('NAME') ?>">
            <span class="input-group-btn">
              <button class="btn btn-info btn-flat" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
            </span>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= $Lang->get('CANCEL') ?></button>
      </div>
    </div>
  </div>
</div>
