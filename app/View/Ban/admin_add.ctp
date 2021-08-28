<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get('BAN__HOME') ?></h3>
                </div>
                <div class="card-body">
                    <form method="post" data-ajax="true" data-upload-image="true"
                          data-redirect-url="<?= $this->Html->url(['controller' => 'ban', 'action' => 'index', 'admin' => 'true']) ?>">
                        <table class="table table-responsive-sm table-bordered"
                               style="table-layout: fixed;word-wrap: break-word;" id="users">
                            <thead>
                            <tr>
                                <th><?= $Lang->get('BAN__QUESTION') ?></th>
                                <th><?= $Lang->get('USER__TITLE') ?></th>
                                <th><?= $Lang->get('USER__RANK') ?></th>
                            </tr>
                            </thead>
                        </table>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label><?= $Lang->get('BAN__REASON') ?></label>
                                <input type="text" class="form-control"
                                       value="<?= $page['title'] ?>"
                                       name="reason">
                            </div>
                        </div>

                        <div class="float-right">
                            <a href="<?= $this->Html->url(['controller' => 'ban', 'action' => 'index', 'admin' => true]) ?>"
                               class="btn btn-default"><?= $Lang->get('GLOBAL__CANCEL') ?></a>
                            <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function () {
        $('#users').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": false,
            "info": false,
            "autoWidth": false,
            'searching': true,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?= $this->Html->url(['action' => 'get_users_not_ban']) ?>",
            "aoColumns": [
                {mData: "User.ban", "bSearchable": true},
                {mData: "User.pseudo", "bSearchable": true},
                {mData: "User.rank", "bSearchable": false}
            ]
        });
    });
</script>
