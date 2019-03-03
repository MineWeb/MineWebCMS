<div style="padding: 15px;">
    <?= $Theme->displayAvailableUpdate() ?>
    <?= $EyPlugin->displayAvailableUpdate() ?>
</div>
<section class="content-header">
  <h1>
    <?= $Lang->get('GLOBAL__ADMIN_PANEL'); ?>
    <small>Version 2.0</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="<?= $this->Html->url('/') ?>"><i class="fa fa-dashboard"></i> <?= $Lang->get('GLOBAL__HOME'); ?></a></li>
    <li class="active"><?= $Lang->get('GLOBAL__ADMIN_PANEL'); ?></li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?= $registered_users ?></h3>
          <p><?= $Lang->get('USER__NBR_REGISTERED') ?></p>
        </div>
        <div class="icon">
          <i class="fa fa-user"></i>
        </div>
        <a href="#" class="small-box-footer">
          + <?= $registered_users_today ?> <?= $Lang->get('GLOBAL__TODAY') ?>
        </a>
      </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="small-box bg-red">
        <div class="inner">
          <h3><?= $count_visits ?></h3>
          <p><?= $Lang->get('STATS__NBR_VISITS') ?></p>
        </div>
        <div class="icon">
          <i class="fa fa-rss"></i>
        </div>
        <a href="#" class="small-box-footer">
          + <?= $count_visits_today ?> <?= $Lang->get('GLOBAL__TODAY') ?>
        </a>
      </div>
    </div>

    <div class="clearfix visible-sm-block"></div>

    <?php if($EyPlugin->isInstalled('eywek.shop')) { ?>
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?= $purchase ?></h3>
            <p><?= $Lang->get('DASHBOARD__PURCHASES') ?></p>
          </div>
          <div class="icon">
            <i class="fa fa-shopping-cart"></i>
          </div>
          <a href="#" class="small-box-footer">
            + <?= $purchase_today ?> <?= $Lang->get('GLOBAL__TODAY') ?>
          </a>
        </div>
      </div>
    <?php } ?>
    <div class="col-md-3 col-sm-6 col-xs-12">
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3><?= $nbr_news ?></h3>
          <p><?= $Lang->get('DASHBOARD__NEWS_WRITTEN') ?></p>
        </div>
        <div class="icon">
          <i class="fa fa-pencil"></i>
        </div>
        <a href="#" class="small-box-footer">
          <?php
          if($nbr_comments_type == "today") {
            echo '+ ';
          }
          echo $nbr_comments;
          ?>
           <?= $Lang->get('NEWS__COMMENTS_TITLE') ?>
        </a>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-8">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('GLOBAL__VISITORS') ?></h3>
          <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <div class="alert alert-info"><b><?= $Lang->get('GLOBAL__INFORMATIONS') ?> :</b> <?= $Lang->get('DASHBOARD__VISITS_LAST_DAYS') ?></div>
          <div class="chart">
            <canvas id="visitsChart" style="height: 180px;"></canvas>
            <script>
              var visitsChart = $("#visitsChart").get(0).getContext("2d");
              // This will get the first returned node in the jQuery collection.
              var visitsChart = new Chart(visitsChart);

              var visitsChartData = {
                labels: ["<?= date('d/m/y', strtotime('-3 day')) ?>","<?= date('d/m/y', strtotime('-2 day')) ?>","<?= $Lang->get('GLOBAL__YESTERDAY') ?>","<?= $Lang->get('GLOBAL__TODAY') ?>"],
                datasets: [
                  {
                    fillColor: "rgba(60,141,188,0.9)",
                    strokeColor: "rgba(60,141,188,0.8)",
                    pointColor: "#3b8bba",
                    pointStrokeColor: "rgba(60,141,188,1)",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(60,141,188,1)",
                    data: [<?= $count_visits_before_before_yesterday ?>,<?= $count_visits_before_yesterday ?>,<?= $count_visits_yesterday ?>,<?= $count_visits_today ?>]
                  },
                ]
              };

              var visitsChartOptions = {
                //Boolean - If we should show the scale at all
                showScale: true,
                //Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines: false,
                //String - Colour of the grid lines
                scaleGridLineColor: "rgba(0,0,0,.05)",
                //Number - Width of the grid lines
                scaleGridLineWidth: 1,
                //Boolean - Whether to show horizontal lines (except X axis)
                scaleShowHorizontalLines: true,
                //Boolean - Whether to show vertical lines (except Y axis)
                scaleShowVerticalLines: true,
                //Boolean - Whether the line is curved between points
                bezierCurve: true,
                //Number - Tension of the bezier curve between points
                bezierCurveTension: 0.3,
                //Boolean - Whether to show a dot for each point
                pointDot: false,
                //Number - Radius of each point dot in pixels
                pointDotRadius: 4,
                //Number - Pixel width of point dot stroke
                pointDotStrokeWidth: 1,
                //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                pointHitDetectionRadius: 20,
                //Boolean - Whether to show a stroke for datasets
                datasetStroke: true,
                //Number - Pixel width of dataset stroke
                datasetStrokeWidth: 2,
                //Boolean - Whether to fill the dataset with a color
                datasetFill: true,
                //String - A legend template
                legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%=datasets[i].label%></li><%}%></ul>",
                //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                maintainAspectRatio: true,
                //Boolean - whether to make the chart responsive to window resizing
                responsive: true
              };

              //Create the line chart
              visitsChart.Line(visitsChartData, visitsChartOptions);
            </script>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title"> <?= $Lang->get('DASHBOARD__EARNINGS') ?></h3>
          <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <?php if($EyPlugin->isInstalled('eywek.shop')) { ?>
            <?php if(count($items_solded) >= 5) { ?>
              <div class="alert alert-warning"><b><?= $Lang->get('GLOBAL__INFORMATIONS') ?> :</b> <?= $Lang->get('DASHBOARD__BIGGEST_SELLERS') ?></div>
              <div class="row">
                <div class="col-md-8">
                  <div class="chart-responsive">
                    <canvas id="pieChart" height="150"></canvas>
                    <script>
                    var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
                    var pieChart = new Chart(pieChartCanvas);
                    var PieData = [
                      {
                        value: <?= $items_solded[0]['count'] ?>,
                        color:"#1abc9c",
                        label : '<?= addslashes($items_solded[0]['item_name']) ?>'
                      },
                      {
                        value : <?= $items_solded['1']['count'] ?>,
                        color : "#2ecc71",
                        label : '<?= addslashes($items_solded[1]['item_name']) ?>'
                      },
                      {
                        value : <?= $items_solded['2']['count'] ?>,
                        color : "#3498db",
                        label : '<?= addslashes($items_solded[2]['item_name']) ?>'
                      },
                      {
                        value : <?= $items_solded['3']['count'] ?>,
                        color : "#e67e22",
                        label : '<?= addslashes($items_solded[3]['item_name']) ?>'
                      },
                      {
                        value : <?= $items_solded['4']['count'] ?>,
                        color : "#e74c3c",
                        label : '<?= addslashes($items_solded[4]['item_name']) ?>'
                      }
                    ];
                    var pieOptions = {
                      //Boolean - Whether we should show a stroke on each segment
                      segmentShowStroke: true,
                      //String - The colour of each segment stroke
                      segmentStrokeColor: "#fff",
                      //Number - The width of each segment stroke
                      segmentStrokeWidth: 1,
                      //Number - The percentage of the chart that we cut out of the middle
                      percentageInnerCutout: 50, // This is 0 for Pie charts
                      //Number - Amount of animation steps
                      animationSteps: 100,
                      //String - Animation easing effect
                      animationEasing: "easeOutBounce",
                      //Boolean - Whether we animate the rotation of the Doughnut
                      animateRotate: true,
                      //Boolean - Whether we animate scaling the Doughnut from the centre
                      animateScale: false,
                      //Boolean - whether to make the chart responsive to window resizing
                      responsive: true,
                      // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                      maintainAspectRatio: false,
                      //String - A legend template
                      legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
                      //String - A tooltip template
                      tooltipTemplate: "<%=label%> (<%=value %>)"
                    };
                    //Create pie or douhnut chart
                    // You can switch between pie and douhnut using the method below.
                    pieChart.Doughnut(PieData, pieOptions);
                    </script>
                  </div>
                </div>
                <div class="col-md-4">
                  <ul class="chart-legend clearfix">
                    <li><i class="fa fa-circle-o" style="color:#1abc9c;"></i> <?= $items_solded[0]['item_name'] ?> (<?= $items_solded[0]['count'] ?> <?= $Lang->get('GLOBAL__SALES') ?>)</li>
                    <li><i class="fa fa-circle-o" style="color:#2ecc71;"></i> <?= $items_solded[1]['item_name'] ?> (<?= $items_solded[1]['count'] ?> <?= $Lang->get('GLOBAL__SALES') ?>)</li>
                    <li><i class="fa fa-circle-o" style="color:#3498db;"></i> <?= $items_solded[2]['item_name'] ?> (<?= $items_solded[2]['count'] ?> <?= $Lang->get('GLOBAL__SALES') ?>)</li>
                    <li><i class="fa fa-circle-o" style="color:#e67e22;"></i> <?= $items_solded[3]['item_name'] ?> (<?= $items_solded[3]['count'] ?> <?= $Lang->get('GLOBAL__SALES') ?>)</li>
                    <li><i class="fa fa-circle-o" style="color:#e74c3c;"></i> <?= $items_solded[4]['item_name'] ?> (<?= $items_solded[4]['count'] ?> <?= $Lang->get('GLOBAL__SALES') ?>)</li>
                  </ul>
                </div>
              </div>
            <?php } else { ?>
              <div class="alert alert-danger"><i class="icon-shopping-cart"></i> <b><?= $Lang->get('GLOBAL__ERROR') ?> :</b> <?= $Lang->get('SHOP__DASHBOARD_GRAPH_ERROR') ?></div>
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
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('DASHBOARD__LAST_ACTIONS') ?></h3>
        </div><!-- /.box-header -->
        <div class="box-body">
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
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div><!-- /.col -->
  </div>
  <div class="row">
    <?php $i = 0; foreach($servers as $key => $value) { $i++; ?>
      <div class="col-md-4">
        <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title"><?= $Lang->get('SERVER__TITLE') ?> - <?= $value['Server']['name'] ?></h3>
            </div>

            <div class="well">
              <?php if($Server->online($value['Server']['id'])) { ?>
                <?php if($value['Server']['type'] != 1 && $Permissions->can('SEND_SERVER_COMMAND_FROM_DASHBOARD')) { ?>
                  <div class="row-fluid text-center">
                    <button class="btn" type="button" data-toggle="modal" onClick="$('#server_id').val(<?= $value['Server']['id'] ?>)" data-target="#executeCommand" style="padding: 4px 12px;margin-right: 8px;"><i class="fa fa-terminal"></i> <?= $Lang->get('SERVER__COMMAND') ?></button>
                  </div>
                  <br>
                <?php } ?>
                <button class="btn btn-large btn-block btn-success" type="button"><?= $Lang->get('SERVER__STATUS_ONLINE') ?> <br>
                  <?php
                  $get = $Server->call(array('GET_PLAYER_COUNT' => array(), 'GET_MAX_PLAYERS' => array()), $value['Server']['id']);
                  echo $get['GET_PLAYER_COUNT'].'/'.$get['GET_MAX_PLAYERS'];
                  ?>
                </button>
              <?php } else { ?>
                  <button class="btn btn-large btn-block btn-danger" type="button"><?= $Lang->get('SERVER__STATUS_OFFLINE') ?></button>
              <?php } ?>
            </div>
        </div>
      </div>
    <?php } ?>
  </div>
</section>

<div class="modal fade" id="executeCommand" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display:none;">
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

              <button class="btn btn-info" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button><br>
          </div><br>
            <div class="col-md-8">
                <select class="form-control col-md-4" name="cmd2">
                <?php foreach($search_cmd as $c) { if($c['ServerCmd']['server_id'] == $value['Server']['id']) { ?>
                      <option value="<?= $c['ServerCmd']['cmd'] ?>"><?= $c['ServerCmd']['name'] ?></option>
                <?php }} ?>
                </select>
            </div>
            
            <input type="hidden" name="data[_Token][key]" value="<?= $csrfToken ?>">
            <button class="btn btn-info" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= $Lang->get('GLOBAL__CANCEL') ?></button>
      </div>
    </div>
  </div>
</div>
