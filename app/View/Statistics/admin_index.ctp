<section class="content">
  <div class="row">
    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('REFERING_WEBSITE') ?></h3>
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
                  $hostes[] = 'undefined';
                }

                if(!isset($colors[$i])) {
                  $i = 0;
                }

                echo '{value: '.$value.',';
                echo 'color:"#'.$colors[$i].'",';
                if($key != 'null') {
                  echo 'label : \''.addslashes(urldecode(parse_url($key, PHP_URL_HOST))).'\'';
                } else {
                  echo 'label : \'undefined\'';
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
          <h3 class="box-title"><?= $Lang->get('PAGE_MOST_VISITED') ?></h3>
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
          <h3 class="box-title"><?= $Lang->get('LANG') ?></h3>
        </div>
        <div class="box-body">
        
          <canvas id="barChart" style="height:230px"></canvas>
          <script>
            var barChartCanvas = $("#barChart").get(0).getContext("2d");
            var barChart = new Chart(barChartCanvas);
            var areaChartData = {
            labels: [
              <?php 
              foreach ($language as $key => $value) {
                if(empty($key)) {
                  echo '"undefined",';
                } else {
                  echo '"'.$key.'",';
                }
                $values[] = $value;
              }
              ?>
            ],
            datasets: [
              {
                label: "visits",
                fillColor: "rgba(210, 214, 222, 1)",
                strokeColor: "rgba(210, 214, 222, 1)",
                pointColor: "rgba(210, 214, 222, 1)",
                pointStrokeColor: "#c1c7d1",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: [
                    <?php
                    foreach ($values as $k => $v) {
                      echo '"'.$v.'",';
                    }
                    ?>
                  ]
              },
            ]
          };
            var barChartData = areaChartData;
            barChartData.datasets[0].fillColor = "#00a65a";
            barChartData.datasets[0].strokeColor = "#00a65a";
            barChartData.datasets[0].pointColor = "#00a65a";
            var barChartOptions = {
              //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
              scaleBeginAtZero: true,
              //Boolean - Whether grid lines are shown across the chart
              scaleShowGridLines: true,
              //String - Colour of the grid lines
              scaleGridLineColor: "rgba(0,0,0,.05)",
              //Number - Width of the grid lines
              scaleGridLineWidth: 1,
              //Boolean - Whether to show horizontal lines (except X axis)
              scaleShowHorizontalLines: true,
              //Boolean - Whether to show vertical lines (except Y axis)
              scaleShowVerticalLines: true,
              //Boolean - If there is a stroke on each bar
              barShowStroke: true,
              //Number - Pixel width of the bar stroke
              barStrokeWidth: 2,
              //Number - Spacing between each of the X value sets
              barValueSpacing: 5,
              //Number - Spacing between data sets within X values
              barDatasetSpacing: 1,
              //String - A legend template
              legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
              //Boolean - whether to make the chart responsive
              responsive: true,
              maintainAspectRatio: true
            };

            barChartOptions.datasetFill = false;
            barChart.Bar(barChartData, barChartOptions);
          </script>
          
        </div>
      </div>
    </div>
  </div>
</section>