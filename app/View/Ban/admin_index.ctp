<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get("BAN__HOME") ?></h3>
                </div>
                <div class="card-body">
                    <table class="table table-responsive-sm table-bordered">
                        <thead>
                            <tr>
                                <th><?= $Lang->get("USER__USERNAME") ?></th>
                                <th><?= $Lang->get("BAN__REASON") ?></th>
                                <th><?= $Lang->get("GLOBAL__ACTIONS")?></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($banned_users as $v) { ?>
                                <tr>
                                    <?php foreach ($users as $user) {
                                        if ($user["User"]["id"] == $v["Ban"]["user_id"]) { ?>
                                            <td><?= $user["User"]["pseudo"] ?></td>
                                            <?php break;
                                        }
                                    } ?>
                                    <td><?= $v["Ban"]["reason"] ?></td>
                                    <td>
                                        <a onClick="confirmDel('<?= $this->Html->url(['action' => 'unban', $v["Ban"]['id']]) ?>')"
                                           class="btn btn-danger"><?= $Lang->get('BAN__UNBAN') ?></a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-body">
                    <a class="btn btn-large btn-block btn-primary"
                       href="<?= $this->Html->url(['controller' => 'ban', 'action' => 'add', 'admin' => true]) ?>"><?= $Lang->get('BAN__ADD') ?></a>
                </div>
            </div>
        </div>
    </div>
</section>
