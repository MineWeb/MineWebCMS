<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('NEWS_LIST') ?></h3>
        </div>
        <div class="box-body">
          <a class="btn btn-large btn-block btn-primary" href="<?= $this->Html->url(array('controller' => 'news', 'action' => 'add', 'admin' => true)) ?>"><?= $Lang->get('ADD_NEWS') ?></a>

            <hr>

            <table class="table table-bordered table-responsive">
              <thead>
                <tr>
                  <th><?= $Lang->get('TITLE') ?></th>
                  <th><?= $Lang->get('BY') ?></th>
                  <th><?= $Lang->get('PUBLISHED') ?></th>
                  <th><?= $Lang->get('POSTED_ON') ?></th>
                  <th><?= $Lang->get('NUMBER_OF_COMMENTS') ?></th>
                  <th><?= $Lang->get('NUMBER_OF_LIKES') ?></th>
                  <th><?= $Lang->get('ACTION') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($view_news as $news => $v) { ?>
                <tr>
                  <td><?= $v['News']['title'] ?></td>
                  <td><?= $v['News']['author'] ?></td>
                  <td><?= ($v['News']['published']) ? '<span class="label label-success">'.$Lang->get('YES').'</span>' : '<span class="label label-danger">'.$Lang->get('NO').'</span>'; ?></td>
                  <td><?= $Lang->date($v['News']['created']) ?></td>
                  <td><?= $v['News']['count_comments'] ?> <?= $Lang->get('COMMENTS') ?></td>
                  <td><?= $v['News']['count_likes'] ?> <?= $Lang->get('LIKES') ?></td>
                  <td>
                    <a href="<?= $this->Html->url(array('controller' => 'news', 'action' => 'edit/'.$v['News']['id'], 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('EDIT') ?></a>
                    <a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'news', 'action' => 'delete/'.$v['News']['id'], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('DELETE') ?></a>
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
