<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $Lang->get('GLOBAL__ADMIN_PANEL'); ?> <small>Version 3.0</small></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a
                                href="<?= $this->Html->url('/') ?>"><?= $Lang->get('GLOBAL__HOME'); ?></a></li>
                    <li class="breadcrumb-item active"><?= $Lang->get('GLOBAL__ADMIN_PANEL'); ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-lightblue">
                <span class="info-box-icon">
                    <i class="fa fa-user"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text"><?= $Lang->get('USER__NBR_REGISTERED') ?></span>

                    <span class="info-box-number"><?= $registered_users ?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width:0%"></div>
                    </div>
                    <span class="progress-description">
                        + <?= $registered_users_today ?> <?= $Lang->get('GLOBAL__TODAY') ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-red">
                <span class="info-box-icon">
                    <i class="fa fa-rss"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text"><?= $Lang->get('STATS__NBR_VISITS') ?></span>
                    <span class="info-box-number"><?= $count_visits ?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width:0%"></div>
                    </div>
                    <span class="progress-description">
                        + <?= $count_visits_today ?> <?= $Lang->get('GLOBAL__TODAY') ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="clearfix visible-sm-block"></div>

        <?php if ($EyPlugin->isInstalled('eywek.shop')) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-green">
                <span class="info-box-icon">
                    <i class="fa fa-shopping-cart"></i>
                </span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= $Lang->get('DASHBOARD__PURCHASES') ?></span>
                        <span class="info-box-number"><?= $purchase ?></span>
                        <div class="progress">
                            <div class="progress-bar" style="width:0%"></div>
                        </div>
                        <span class="progress-description">
                        + <?= $purchase_today ?> <?= $Lang->get('GLOBAL__TODAY') ?>
                    </span>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-olive">
                <span class="info-box-icon">
                    <i class="fas fa-pencil-ruler"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text"><?= $Lang->get('DASHBOARD__NEWS_WRITTEN') ?></span>
                    <span class="info-box-number"><?= $nbr_news ?></span>
                    <div class="progress">
                        <div class="progress-bar" style="width:0%"></div>
                    </div>
                    <span class="progress-description">
                        <?php
                        if ($nbr_comments_type == "today") {
                            echo '+ ';
                        }
                        echo $nbr_comments;
                        ?>
                        <?= $Lang->get('NEWS__COMMENTS_TITLE') ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <?= $Theme->displayAvailableUpdate() ?>
    <?= $EyPlugin->displayAvailableUpdate() ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get('GLOBAL__VISITORS') ?></h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info"><b><?= $Lang->get('GLOBAL__INFORMATIONS') ?>
                            :</b> <?= $Lang->get('DASHBOARD__VISITS_LAST_DAYS') ?></div>
                    <div class="chart">
                        <canvas id="line-chart" height="75"></canvas>
                        <script>
                            new Chart(document.getElementById("line-chart"), {
                                type: 'line',
                                data: {
                                    labels: ["<?= date('d/m/y', strtotime('-2 day')) ?>", "<?= $Lang->get('GLOBAL__YESTERDAY') ?>", "<?= $Lang->get('GLOBAL__TODAY') ?>"],
                                    datasets: [{
                                        data: [<?= $count_visits_before_yesterday ?>, <?= $count_visits_yesterday ?>, <?= $count_visits_today ?>],
                                        fill: true,
                                        backgroundColor: "rgba(60,141,188,0.8)",
                                        pointBackgroundColor: "rgba(60,141,188,0.8)",
                                        borderColor: "transparent",
                                        pointHighlightStroke: "transparent",
                                        borderCapStyle: 'butt',
                                    }
                                    ]
                                },
                                options: {
                                    title: {
                                        display: false,
                                    },
                                    scaleGridLineWidth: 0,
                                    legend: {display: false},
                                    scales: {
                                        xAxes: [{
                                            gridLines: {
                                                color: "rgba(0, 0, 0, 0)",
                                            }
                                        }],
                                        yAxes: [{
                                            gridLines: {
                                                color: "rgba(0, 0, 0, 0)",
                                            }
                                        }]
                                    },

                                    animation: {
                                        duration: 750,
                                    },


                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-default">
                <div class="card-header with-border">
                    <h3 class="card-title"> <?= $Lang->get('DASHBOARD__EARNINGS') ?></h3>
                </div>
                <div class="card-body">
                    <?php if ($EyPlugin->isInstalled('eywek.shop')) { ?>
                        <?php if (count($items_solded) >= 5) { ?>
                            <div class="alert alert-warning"><b><?= $Lang->get('GLOBAL__INFORMATIONS') ?>
                                    :</b> <?= $Lang->get('DASHBOARD__BIGGEST_SELLERS') ?></div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="chart-responsive">
                                        <canvas id="pie-chart" height="150"></canvas>
                                        <script>
                                            new Chart(document.getElementById("pie-chart"), {
                                                type: 'pie',
                                                data: {
                                                    labels: ["<?= addslashes($items_solded[0]['item_name']) ?>", "<?= addslashes($items_solded['1']['item_name']) ?>", "<?= addslashes($items_solded['2']['item_name']) ?>", "<?= addslashes($items_solded['3']['item_name']) ?>", "<?= addslashes($items_solded['4']['item_name']) ?>"],
                                                    datasets: [{
                                                        backgroundColor: ["#1abc9c", "#2ecc71", "#3498db", "#e67e22", "#e74c3c"],
                                                        data: [<?= $items_solded[0]['count'] ?>, <?= $items_solded['1']['count'] ?>, <?= $items_solded['2']['count'] ?>, <?= $items_solded['3']['count'] ?>, <?= $items_solded['4']['count'] ?>]
                                                    }]
                                                },
                                                options: {
                                                    title: {
                                                        display: false,
                                                    },
                                                    responsive: false,
                                                    legend: {display: false},
                                                }
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <ul class="chart-legend clearfix">
                                        <li><i class="fa fa-circle-o"
                                               style="color:#1abc9c;"></i> <?= $items_solded[0]['item_name'] ?>
                                            (<?= $items_solded[0]['count'] ?> <?= $Lang->get('GLOBAL__SALES') ?>)
                                        </li>
                                        <li><i class="fa fa-circle-o"
                                               style="color:#2ecc71;"></i> <?= $items_solded[1]['item_name'] ?>
                                            (<?= $items_solded[1]['count'] ?> <?= $Lang->get('GLOBAL__SALES') ?>)
                                        </li>
                                        <li><i class="fa fa-circle-o"
                                               style="color:#3498db;"></i> <?= $items_solded[2]['item_name'] ?>
                                            (<?= $items_solded[2]['count'] ?> <?= $Lang->get('GLOBAL__SALES') ?>)
                                        </li>
                                        <li><i class="fa fa-circle-o"
                                               style="color:#e67e22;"></i> <?= $items_solded[3]['item_name'] ?>
                                            (<?= $items_solded[3]['count'] ?> <?= $Lang->get('GLOBAL__SALES') ?>)
                                        </li>
                                        <li><i class="fa fa-circle-o"
                                               style="color:#e74c3c;"></i> <?= $items_solded[4]['item_name'] ?>
                                            (<?= $items_solded[4]['count'] ?> <?= $Lang->get('GLOBAL__SALES') ?>)
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-danger"><i class="icon-shopping-cart"></i>
                                <b><?= $Lang->get('GLOBAL__ERROR') ?>
                                    :</b> <?= $Lang->get('SHOP__DASHBOARD_GRAPH_ERROR') ?></div>
                        <?php } ?>
                    <?php } else {
                        echo $Lang->get('DASHBOARD__PLUGIN_SHOP_NOT_INSTALLED');
                    } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get('DASHBOARD__LAST_ACTIONS') ?></h3>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <th><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                            <th><?= $Lang->get('GLOBAL__CATEGORY') ?></th> <!-- ICI -->
                            <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                            <th><?= $Lang->get('GLOBAL__AUTHOR') ?></th>
                        </tr>
                        <?php foreach ($History->get(false, 5) as $k => $v) { ?>
                            <tr>
                                <td><?= $v['History']['action'] ?></td>
                                <td><?= $v['History']['category'] ?></td>
                                <td><?= $Lang->date($v['History']['created']) ?></td>
                                <td><?= $v['History']['author'] ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div><!-- /.card-body -->
            </div><!-- /.card -->
        </div><!-- /.col -->
    </div>
    <div class="row">
        <?php $i = 0;
        foreach ($servers as $key => $value) {
            $i++; ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title"><?= $Lang->get('SERVER__TITLE') ?> - <?= $value['Server']['name'] ?></h3>
                    </div>

                    <div class="card card-body bg-light">
                        <?php if ($Server->online($value['Server']['id'])) { ?>
                            <?php if ($value['Server']['type'] != 1 && $Permissions->can('SEND_SERVER_COMMAND_FROM_DASHBOARD')) { ?>
                                <div class="row-fluid text-center">
                                    <button class="btn" type="button" data-toggle="modal"
                                            onClick="$('#server_id').val(<?= $value['Server']['id'] ?>)"
                                            data-target="#executeCommand" style="padding: 4px 12px;margin-right: 8px;">
                                        <i class="fa fa-terminal"></i> <?= $Lang->get('SERVER__COMMAND') ?></button>
                                </div>
                                <br>
                            <?php } ?>
                            <button class="btn btn-large btn-block btn-success"
                                    type="button"><?= $Lang->get('SERVER__STATUS_ONLINE') ?> <br>
                                <?php
                                $get = $Server->call(array('GET_PLAYER_COUNT' => array(), 'GET_MAX_PLAYERS' => array()), $value['Server']['id']);
                                echo $get['GET_PLAYER_COUNT'] . '/' . $get['GET_MAX_PLAYERS'];
                                ?>
                            </button>
                        <?php } else { ?>
                            <button class="btn btn-large btn-block btn-danger"
                                    type="button"><?= $Lang->get('SERVER__STATUS_OFFLINE') ?></button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<div class="modal fade" id="executeCommand" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= $Lang->get('SERVER__COMMAND') ?></h4>
            </div>
            <div class="modal-body">
                <form action="" data-ajax="true" method="post">
                    <div>
                        <input type="hidden" id="form_infos" data-ajax="false">
                        <input type="hidden" id="server_id" name="server_id">

                        <div class="col-md-8">
                            <input class="form-control col-md-4" name="cmd" type="text"></input>
                        </div>

                        <button class="btn btn-info" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
                        <br>
                    </div>
                    <br>
                    <div class="col-md-8">
                        <select class="form-control col-md-4" name="cmd2">
                            <?php foreach ($search_cmd as $c) {
                                if ($c['ServerCmd']['server_id'] == $value['Server']['id']) { ?>
                                    <option value="<?= $c['ServerCmd']['cmd'] ?>"><?= $c['ServerCmd']['name'] ?></option>
                                <?php }
                            } ?>
                        </select>
                    </div>

                    <input type="hidden" name="data[_Token][key]" value="<?= $csrfToken ?>">
                    <button class="btn btn-info" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?= $Lang->get('GLOBAL__CANCEL') ?></button>
            </div>
        </div>
    </div>
</div>
