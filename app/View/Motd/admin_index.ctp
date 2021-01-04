<section class="content">
    <div class="callout callout-info"><h4><?= $Lang->get('MOTD__TITLE') ?></h4><?= $Lang->get('MOTD__TITLE_DESC') ?>
    </div>
    <div class="card">
        <div class="card-header with-border">
            <h3 class="card-title"><?= $Lang->get('MOTD__TITLE') ?></h3>
        </div>
        <div class="card-body">
            <hr>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th><?= $Lang->get('GLOBAL__NAME') ?></th>
                    <th><?= $Lang->get('MOTD__LINE') ?> 1</th>
                    <th><?= $Lang->get('MOTD__LINE') ?> 2</th>
                    <th><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($get_servers as $key => $value) { ?>
                    <tr>
                        <td>
                            <?= $value['name'] ?>
                        </td>
                        <td>
                            <?= $value['motd_line1'] ?>
                        </td>
                        <td>
                            <?= $value['motd_line2'] ?>
                        </td>
                        <td>
                            <a class="btn btn-info"
                               href="<?= $this->Html->url(['action' => 'edit', $key]) ?>"><?= $Lang->get('GLOBAL__EDIT') ?></a>
                            <a onClick="confirmDel('<?= $this->Html->url(['action' => 'reset', $key]) ?>')"
                               class="btn btn-danger"><?= $Lang->get('MOTD__RESET') ?></a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
