<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('PAGE__LIST') ?></h3>
        </div>
        <div class="box-body">

          <a class="btn btn-large btn-block btn-primary" href="<?= $this->Html->url(array('controller' => 'pages', 'action' => 'add', 'admin' => true)) ?>"><?= $Lang->get('PAGE__ADD') ?></a>

          <hr>

          <table class="table table-bordered">
            <thead>
              <tr>
                <th><?= $Lang->get('GLOBAL__TITLE') ?></th>
                <th><?= $Lang->get('GLOBAL__BY') ?></th>
                <th><?= $Lang->get('PAGE__POSTED_ON') ?></th>
                <th><?= $Lang->get('GLOBAL__UPDATED') ?></th>
                <th><?= $Lang->get('GLOBAL__SLUG') ?></th>
                <th><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($pages as $key => $value) { ?>
              <tr>
                <td><?= $value['Page']['title'] ?></td>
                <td><?= $value['Page']['author'] ?></td>
                <td><?= $Lang->date($value['Page']['created']) ?></td>
                <td><?= $Lang->date($value['Page']['updated']) ?></td>
                <td><a href="<?= $this->Html->url(array('controller' => 'p', 'action' => $value['Page']['slug'], 'admin' => false)) ?>"><?= $value['Page']['slug'] ?></a></td>
                <td>
                  <a href="<?= $this->Html->url(array('controller' => 'pages', 'action' => 'edit/'.$value['Page']['id'], 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('GLOBAL__EDIT') ?></a>
                  <a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'pages', 'action' => 'delete/'.$value['Page']['id'], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('GLOBAL__DELETE') ?></a>
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
