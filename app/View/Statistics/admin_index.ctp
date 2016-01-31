<section class="content">
  <div class="row">

    <div class="col-md-12">
      <a href="<?= $this->Html->url(array('action' => 'reset')) ?>" class="btn btn-info btn-block"><?= $Lang->get('STATS__RESET_LABEL') ?></a>
    </div>
    <br><br>

    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('STATS__VISITS_REFERING_WEBSITE') ?></h3>
        </div>
        <div class="box-body">

          <canvas id="pieChart_referers" height="150"></canvas>
          <script>
          var pieChartCanvas = $("#pieChart_referers").get(0).getContext("2d");
          var pieChart = new Chart(pieChartCanvas);
          var PieData = [
            <?php
            $colors = array('1abc9c', '2ecc71', '3498db', 'e67e22', 'e74c3c');
            $i = 0;
            $hostes = array();
            foreach ($referers as $key => $value) {
              if(!in_array(parse_url($key, PHP_URL_HOST), $hostes)) {
                if($key != 'null') {
                  $hostes[] = parse_url($key, PHP_URL_HOST);
                } else {
                  $hostes[] = 'N/A';
                }

                if(!isset($colors[$i])) {
                  $i = 0;
                }

                echo '{value: '.$value.',';
                echo 'color:"#'.$colors[$i].'",';
                if($key != 'null') {
                  echo 'label : \''.addslashes(urldecode(parse_url($key, PHP_URL_HOST))).'\'';
                } else {
                  echo 'label : \'N/A\'';
                }
                echo '},';
                $i++;
              }
            }
            ?>
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
    </div>
    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('STATS__VISITS_PAGES') ?></h3>
        </div>
        <div class="box-body">

          <canvas id="pieChart_pages" height="150"></canvas>
          <script>
          var pieChartCanvas = $("#pieChart_pages").get(0).getContext("2d");
          var pieChart = new Chart(pieChartCanvas);
          var PieData = [
            <?php
            $colors = array('1abc9c', '2ecc71', '3498db', 'e67e22', 'e74c3c');
            $i = 0;
            foreach ($pages as $key => $value) {
                if(!isset($colors[$i])) {
                  $i = 0;
                }

                echo '{value: '.$value.',';
                echo 'color:"#'.$colors[$i].'",';
                if($key != 'null') {
                  echo 'label : \''.addslashes(urldecode($key)).'\'';
                } else {
                  echo 'label : \'undefined\'';
                }
                echo '},';
                $i++;
            }
            ?>
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
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('GLOBAL__VISITORS') ?></h3>
        </div>
        <div class="box-body">

          <?= $this->Html->script('highcharts.js') ?>

          <div id="visits"></div>

        </div>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
$(function () {
  $.getJSON('<?= $this->Html->url(array('action' => 'get_visits')) ?>', function (data) {

      $('#visits').highcharts({
          chart: {
              zoomType: 'x'
          },
          title: {
              text: ''
          },
          subtitle: {
              text: false
          },
          xAxis: {
              type: 'datetime',
              title: {
                text: '<?= $Lang->get('GLOBAL__CREATED') ?>'
              }
          },
          yAxis: {
              title: {
                  text: '<?= $Lang->get('GLOBAL__VISITORS') ?>'
              }
          },
          legend: {
              enabled: false
          },
          plotOptions: {
              area: {
                  fillColor: {
                      linearGradient: {
                          x1: 0,
                          y1: 0,
                          x2: 0,
                          y2: 1
                      },
                      stops: [
                          [0, Highcharts.getOptions().colors[0]],
                          [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                      ]
                  },
                  marker: {
                      radius: 2
                  },
                  lineWidth: 1,
                  states: {
                      hover: {
                          lineWidth: 1
                      }
                  },
                  threshold: null
              }
          },

          series: [{
              type: 'area',
              name: '<?= $Lang->get('GLOBAL__VISITORS') ?>',
              data: data
          }]
      });
  });
});
</script>
