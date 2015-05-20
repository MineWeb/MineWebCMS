<div class="row-fluid">

    <div class="span12">

        <div class="top-bar">
            <h3><i class="icon-cog"></i> <?= $Lang->get('BANLIST') ?></h3>
        </div>
        <div class="well no-padding">
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