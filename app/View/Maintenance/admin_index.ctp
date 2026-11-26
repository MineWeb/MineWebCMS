<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get('MAINTENANCE__TITLE') ?></h3>
                </div>
                <div class="card-body">
                    <a class="btn btn-large btn-block btn-primary" href="<?= $this->Html->url(['controller' => 'maintenance', 'action' => 'add', 'admin' => true]) ?>"><?= $Lang->get('MAINTENANCE__ADD_PAGE') ?></a>
                    <hr>
                    <table class="table table-responsive-sm table-bordered">
                        <thead>
                        <tr>
                            <th><?= $Lang->get("MAINTENANCE__PAGE") ?></th>
                            <th><?= $Lang->get("MAINTENANCE__REASON") ?></th>
                            <th><?= $Lang->get("MAINTENANCE__SUB_URL") ?></th>
                            <th><?= $Lang->get("GLOBAL__STATUS") ?></th>
                            <th><?= $Lang->get("GLOBAL__ACTIONS") ?></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($pages as $v) { ?>
                            <tr>
                                <td><?= $v["Maintenance"]["url"] ?></td>
                                <td><?= $v["Maintenance"]["reason"] ?></td>
                                <td><?= $v["Maintenance"]["sub_url"] ? $Lang->get("GLOBAL__YES") : $Lang->get("GLOBAL__NO") ?></td>
                                <td><?= $v["Maintenance"]["active"] != 1 ? $Lang->get("GLOBAL__DISABLED") : $Lang->get("GLOBAL__ENABLED") ?></td>
                                <td>
                                    <a href='<?= $this->Html->url(['action' => 'edit', $v["Maintenance"]['id']]) ?>'
                                       class="btn btn-success"><?= $Lang->get('GLOBAL__EDIT') ?></a>
                                    <?php if ($v["Maintenance"]["active"] == 1) { ?>
                                        <?= $this->Form->postLink($Lang->get('GLOBAL__DISABLE'), ['action' => 'disable', $v["Maintenance"]['id']], [
                                            'class' => 'btn btn-warning',
                                            'confirm' => $Lang->get('GLOBAL__CONFIRM_DELETE')
                                        ]) ?>
                                    <?php } else { ?>
                                        <?= $this->Form->postLink($Lang->get('GLOBAL__ENABLE'), ['action' => 'enable', $v["Maintenance"]['id']], [
                                            'class' => 'btn btn-primary',
                                            'confirm' => $Lang->get('GLOBAL__CONFIRM_DELETE')
                                        ]) ?>
                                    <?php } ?>
                                    <?= $this->Form->postLink($Lang->get('GLOBAL__DELETE'), ['action' => 'delete', $v["Maintenance"]['id']], [
                                        'class' => 'btn btn-danger',
                                        'confirm' => $Lang->get('GLOBAL__CONFIRM_DELETE')
                                    ]) ?>
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
