<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= $Lang->get('MOTD__EDIT_TITLE') ?></h3>
                </div>
                <div class="box-body">
                    <form method="post" action="<?= $this->Html->url(array('action' => 'edit_ajax', $get['id'])) ?>"
                          data-ajax="true" data-redirect-url="<?= $this->Html->url(array('action' => 'index')) ?>">
                        <div class="form-group">
                            <label><?= $Lang->get('GLOBAL__NAME') ?></label>
                            <input disabled class="form-control" value="<?= $get['name'] ?>">
                        </div>
                        <div class="form-group">
                            <label>Motd</label>
                            <p><?= $Lang->get('MOTD__DESC') ?></p>
                            <div class="input-group">
                                <div class="input-group-addon"><?= $Lang->get('MOTD__LINE') ?> 1</div>
                                <input name="motd_line1" class="form-control" type="text" value="<?= $get['motd_line1'] ?>">
                            </div><br>
                            <div class="input-group">
                                <div class="input-group-addon"><?= $Lang->get('MOTD__LINE') ?> 2</div>
                                <input name="motd_line2" class="form-control" type="text" value="<?= $get['motd_line2'] ?>">
                            </div>
                            <br>

                            <p><b><?= $Lang->get('MOTD__VARIABLES') ?> : </b></p>
                            <p><em>{PLAYERS}</em> : <?= $Lang->get('MOTD__VARIABLE_PLAYERS') ?></p>
                        </div>
                        <div class="pull-right">
                            <a href="<?= $this->Html->url(array('controller' => 'motd', 'action' => 'admin_index', 'admin' => true)) ?>"
                               class="btn btn-default"><?= $Lang->get('GLOBAL__CANCEL') ?></a>
                            <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__EDIT') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
