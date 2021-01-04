<?php
$colors = ['#1abc9c', '#2ecc71', '#3498db', '#e67e22', '#e74c3c'];
$hosts = [];
$ref_pie = [];
$i = 0;
foreach ($referers as $ref => $visitcount) {
    $host = parse_url($ref, PHP_URL_HOST);
    if (!in_array($host, $hosts)) {
        // Check page URL not null
        if ($host != 'null') {
            $hosts[] = $host;
        } else {
            $hosts[] = 'N/A';
        }
        // Loop color if needed
        if (!isset($colors[$i])) {
            $i = 0;
        }
        $color = $colors[$i];

        $ref_pie['color'][] = $color;
        $ref_pie['value'][] = $visitcount;
        $ref_pie['label'][] = $host;
        // Next color
        $i++;
    }
}

$pages_pie = [];
$i = 0;
foreach ($pages as $page => $visitcount) {
    $page = addslashes(urldecode($page));
    // Loop color if needed
    if (!isset($colors[$i])) {
        $i = 0;
    }
    $color = $colors[$i];

    $pages_pie['color'][] = $color;
    $pages_pie['value'][] = $visitcount;
    $pages_pie['label'][] = $page;
    // Next color
    $i++;
}
?>
<section class="content">
    <div class="row">

        <div class="col-md-12">
            <a href="<?= $this->Html->url(['action' => 'reset']) ?>"
               class="btn btn-info btn-block"><?= $Lang->get('STATS__RESET_LABEL') ?></a>
        </div>
        <br><br>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get('STATS__VISITS_REFERING_WEBSITE') ?></h3>
                </div>
                <div class="card-body">
                    <canvas id="pieChart_referers" height="300"></canvas>
                    <script>
                        new Chart(document.getElementById("pieChart_referers"), {
                            type: 'doughnut',
                            data: {
                                labels: <?= json_encode($ref_pie['label']) ?>,
                                datasets: [{
                                    backgroundColor: <?= json_encode($ref_pie['color']) ?>,
                                    data: <?= json_encode($ref_pie['value']) ?>
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
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get('STATS__VISITS_PAGES') ?></h3>
                </div>
                <div class="card-body">

                    <canvas id="pieChart_pages" height="300"></canvas>
                    <script>
                        new Chart(document.getElementById("pieChart_pages"), {
                            type: 'doughnut',
                            data: {
                                labels: <?= json_encode($pages_pie['label']) ?>,
                                datasets: [{
                                    backgroundColor: <?= json_encode($pages_pie['color']) ?>,
                                    data: <?= json_encode($pages_pie['value']) ?>
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
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get('GLOBAL__VISITORS') ?></h3>
                </div>
                <div class="card-body">
                    <?= $this->Html->script('highcharts.js') ?>
                    <div id="visits"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(function () {
        $.getJSON('<?= $this->Html->url(['action' => 'get_visits']) ?>', function (data) {

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
