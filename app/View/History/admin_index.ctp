<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= $Lang->get('HISTORY__VIEW_GLOBAL') ?></h3>
                </div>
                <div class="box-body">
                    <table class="table" style="table-layout: fixed;word-wrap: break-word;">
                        <thead>
                        <tr>
                            <th><?= $Lang->get('USER__USERNAME') ?></th>
                            <th><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                            <th><?= $Lang->get('GLOBAL__CATEGORY') ?></th>
                            <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function() {
        $('table').DataTable({
            "paging": true,
            "lengthChange": false,
            "ordering": false,
            "info": false,
            "autoWidth": false,
            'searching': true,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?= $this->Html->url(array('action' => 'getAll')) ?>",
            "aoColumns": [
                {mData: "User.pseudo"},
                {mData: "History.action"},
                {mData: "History.category"},
                {mData: "History.created"}
            ],
        });
    })
</script>