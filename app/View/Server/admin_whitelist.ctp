<div class="row-fluid">

    <div class="span12">

        <div class="top-bar">
            <h3><i class="icon-cog"></i> <?= $Lang->get('WHITELIST') ?></h3>
        </div>
        <div class="well no-padding">
            <div class="well">
                <?php foreach ($servers as $key => $value) { ?>
                    <a href="<?= $this->Html->url(array('controller' => 'server', 'action' => 'whitelist', 'admin' => true, $value['Server']['id'])) ?>" class="btn btn-lg btn-success"><?= $value['Server']['name'] ?></a>
                <?php } ?>
            </div>
            <?php if($list != "NEED_SERVER_ON") { ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th><?= $Lang->get('PSEUDO') ?></th>
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
                <div class="well">
                    <div class="alert alert-danger"><?= $Lang->get('NEED_SERVER_ON') ?></div>
                </div>
            <?php } ?>
        </div>

    </div>

</div>