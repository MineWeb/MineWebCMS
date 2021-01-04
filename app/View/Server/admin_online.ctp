<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get('SERVER__ONLINE_PLAYERS') ?></h3>
                </div>
                <div class="card-body">

                    <?php foreach ($servers as $key => $value) { ?>
                        <a href="<?= $this->Html->url(['controller' => 'server', 'action' => 'online', 'admin' => true, $value['Server']['id']]) ?>"
                           class="btn btn-lg btn-success"><?= $value['Server']['name'] ?></a>
                    <?php } ?>

                    <hr>

                    <?php if ($list != "NEED_SERVER_ON") { ?>
                        <table class="table table-bordered dataTable">
                            <thead>
                            <tr>
                                <th><?= $Lang->get('USER__USERNAME') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($list as $k => $v) { ?>
                                <tr>
                                    <td><?= $v ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <div class="card card-body bg-light">
                            <div class="alert alert-danger"><?= $Lang->get('SERVER__MUST_BE_ON') ?></div>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
</section>
