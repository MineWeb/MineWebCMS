<div class="push-nav"></div>
<div class="container page">
    <div class="row">
        <div class="page-content">
            <h1 class="title"><?= $Lang->get('RANKING_FACTION__PAGE_TITLE') ?></h1>

            <table class="table dataTable" id="classement">
                <thead>
                    <tr>
                        <th>#</th>
                        <?php foreach ($affich as $key => $value) {
                            echo '<th>'.$Lang->get('RANKING_FACTION__AFFICH_'.strtoupper($value)).'</th>';
                        } ?>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

            <?php if($cache_time) { ?>
                <p class="created"><?= str_replace('{CACHE_TIME}', $cache_time, $Lang->get('RANKING_FACTION__CACHED')) ?></p>
            <?php } ?>
        </div>
    </div>
</div>
<?= $this->Html->css('dataTables.bootstrap.css'); ?>
<?= $this->Html->script('jquery.dataTables.min.js') ?>
<?= $this->Html->script('dataTables.bootstrap.min.js') ?>
<style>
  .dataTables_paginate {
    display: inline-block;
    padding-left: 0;
    margin: 20px 0;
    border-radius: 4px;
    cursor: pointer;
  }
  .dataTables_paginate span span,
  .dataTables_paginate a {
    display: inline;
  }
  .dataTables_paginate span span,
  .dataTables_paginate a {
    position: relative;
    float: left;
    padding: 6px 12px;
    margin-left: -1px;
    line-height: 1.42857143;
    color: #5e729f;
    text-decoration: none;
    background-color: #fff;
    border: 1px solid #ddd;
  }
  .dataTables_paginate > a:first-child {
    margin-left: 0;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
  }
  .dataTables_paginate > a:last-child {
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
  }
  .dataTables_paginate span span:hover,
  .dataTables_paginate span span:focus,
  .dataTables_paginate a:hover,
  .dataTables_paginate a:focus {
    color: #F1B418;
    background-color: #eee;
    border-color: #ddd;
  }
  .dataTables_paginate .current,
  .dataTables_paginate .current:hover,
  .dataTables_paginate .current:focus {
    z-index: 2;
    color: #fff;
    cursor: default;
    background-color: #5e729f;
    border-color: #5e729f;
  }
  .dataTables_paginate .disabled,
  .dataTables_paginate .disabled:hover,
  .dataTables_paginate .disabled:focus {
    color: #777;
    cursor: not-allowed;
    background-color: #fff;
    border-color: #ddd;
  }

  #classement_wrapper .row .col-sm-5 {
    width: 0px;
  }

  #classement_paginate.dataTables_paginate.paging_simple_numbers {
    margin-right: 25px !important;
  }

  #classement_wrapper {
    margin-top:-55px;
  }

  #classement_filter {
    float: right;
    margin-bottom: 20px;
  }

  #classement_filter input {
    display: block;
    width: 100%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #eee;
    background-image: none;
    border: 2px solid #5e729f;
    border-radius: 8px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
  }

#classement_filter input:hover {
  background: #F5F5F5;
  border: 2px solid #5e729f;
}
#classement_filter input:focus {
  outline: 0;
}
</style>
<script>
    $(function () {
      $('table.dataTable').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": false,
        "info": false,
        "autoWidth": false,
        'searching': true,
        "language": {
            "infoEmpty": "<?= $Lang->get('RANKING_FACTION__EMPTY_DATA') ?>",
            "loadingRecords": "<?= $Lang->get('GLOBAL__LOADING') ?>...",
            "search":         "<b><?= $Lang->get('RANKING_FACTION__SEARCH') ?></b>:",
            "zeroRecords":    "<?= $Lang->get('RANKING_FACTION__ZERO_RECORDS') ?>",
            "paginate": {
                "first":      "<?= $Lang->get('RANKING_FACTION__FIRST') ?>",
                "last":       "<?= $Lang->get('RANKING_FACTION__LAST') ?>",
                "next":       "<?= $Lang->get('RANKING_FACTION__NEXT') ?>",
                "previous":   "<?= $Lang->get('RANKING_FACTION__PREVIOUS') ?>"
            },
        },
        'ajax': '#',
        'columns': [
            { "data" : "position"},
            <?php foreach ($affich as $key => $value) {
                if($value == "leader" || $value == "officers") {
                    echo '{ data : "'.$value.'[, ]"},';
                } else {
                    echo '{ data : "'.$value.'"},';
                }
            } ?>
        ],
        "fnInitComplete": function(oSettings, json) {
          $("[data-toggle=popover]").each(function(e) {
                $(this).popover();
            });
        }
      });
    });
</script>