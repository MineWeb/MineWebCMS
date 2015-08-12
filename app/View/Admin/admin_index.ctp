<?php 
 
$this->History = new HistoryComponent;
$this->EyPlugin = new EyPluginComponent;
?>
        <div class="row-fluid">

            <div class="span8">

                <div class="top-bar">
                    <ul class="tab-container">
                      <li class="active"><a href="#tab-traffic"><i class="icon-bar-chart"></i><?= $Lang->get('VISITORS') ?></a></li>
                    </ul>
                </div>

                <div class="well">
                    <div class="tab-content">

                      <div class="tab-pane active" id="tab-traffic">

                            <div class="alert alert-info">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <i class="icon-info-sign"></i> <b><?= $Lang->get('INFORMATION') ?> : </b><?= $Lang->get('VISITS_LAST_DAYS') ?>
                            </div>
                            
                            <canvas id="Visits" width="720" height="360"></canvas>
                            <script type="text/javascript">
                            var ctx = $("#Visits").get(0).getContext("2d");
                            var data = {
                              labels : ["<?= date('d/m/y', strtotime('-3 day')) ?>","<?= date('d/m/y', strtotime('-2 day')) ?>","<?= $Lang->get('YESTERDAY') ?>","<?= $Lang->get('TODAY') ?>"],
                              datasets : [
                                {
                                  fillColor : "rgba(151,187,205,0.5)",
                                  strokeColor : "rgba(151,187,205,1)",
                                  pointColor : "rgba(151,187,205,1)",
                                  pointStrokeColor : "#fff",
                                  data : [<?= $count_visits_before_before_yesterday ?>,<?= $count_visits_before_yesterday ?>,<?= $count_visits_yesterday ?>,<?= $count_visits_today ?>]
                                }
                              ]
                            }
                            new Chart(ctx).Line(data);
                            </script>
                            

                        </div>

                    </div>

                </div>

            </div>
            
            <div class="span4">

                <div class="top-bar">
                    <h3><i class="icon-money"></i> <?= $Lang->get('EARNINGS') ?></h3>
                </div>

                <div class="well">
                    
                    <?php if($this->EyPlugin->is_installed('Shop')) { ?>
                      <?php if($counts_items >= 5) { ?>
                        <div class="alert">
                          <button type="button" class="close" data-dismiss="alert">&times;</button>
                          <i class="icon-shopping-cart"></i> <b><?= $Lang->get('INFORMATION') ?> :</b> <?= $Lang->get('BIGGEST_SELLERS') ?> 
                        </div>
                        <canvas id="buy" width="350" height="280"></canvas>
                        <script type="text/javascript">
                        var ctx = $("#buy").get(0).getContext("2d");
                        var data = [
                          {
                            value: <?= $how['0'] ?>,
                            color:"#1abc9c"
                          },
                          {
                            value : <?= $how['1'] ?>,
                            color : "#2ecc71"
                          },
                          {
                            value : <?= $how['2'] ?>,
                            color : "#3498db"
                          },
                          {
                            value : <?= $how['3'] ?>,
                            color : "#e67e22"
                          },
                          {
                            value : <?= $how['4'] ?>,
                            color : "#e74c3c"
                          }   
                        ]
                        new Chart(ctx).Pie(data);

                        </script>
                        <div class="legend">
                          <div class="legend-color" style="background-color:#1abc9c;"></div>
                          <span class="legend-text"><?= $items_solded['0']['History']['other'] ?> (<?= $how['0'] ?> <?= $Lang->get('SALES') ?>)</span>
                        </div>
                        <div class="legend">
                          <div class="legend-color" style="background-color:#2ecc71;"></div>
                          <span class="legend-text"><?= $items_solded['1']['History']['other'] ?> (<?= $how['1'] ?> <?= $Lang->get('SALES') ?>)</span>
                        </div>
                        <div class="legend">
                          <div class="legend-color" style="background-color:#3498db;"></div>
                          <span class="legend-text"><?= $items_solded['2']['History']['other'] ?> (<?= $how['2'] ?> <?= $Lang->get('SALES') ?>)</span>
                        </div>
                        <div class="legend">
                          <div class="legend-color" style="background-color:#e67e22;"></div>
                          <span class="legend-text"><?= $items_solded['3']['History']['other'] ?> (<?= $how['3'] ?> <?= $Lang->get('SALES') ?>)</span>
                        </div>
                        <div class="legend">
                          <div class="legend-color" style="background-color:#e74c3c;"></div>
                          <span class="legend-text"><?= $items_solded['4']['History']['other'] ?> (<?= $how['4'] ?> <?= $Lang->get('SALES') ?>)</span>
                        </div>
                      <?php } else { ?>
                        <div class="alert alert-error">
                          <button type="button" class="close" data-dismiss="alert">&times;</button>
                          <i class="icon-shopping-cart"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('NEED_MORE_5_ITEMS') ?>
                        </div>
                      <?php } ?>
                    <?php } else {
                      echo $Lang->get('PLUGIN_SHOP_NOT_INSTALLED');
                    } ?>

                </div>

            </div>
            
        </div>

        <div class="row-fluid">

            <div class="span8">
                <div class="top-bar">
                    <h3><i class="icon-edit"></i><?= $Lang->get('LAST_ACTIONS') ?></h3>
                </div>
                <div class="well no-padding">
                    <table class="table">
                      <thead>
                        <tr>
                          <th><?= $Lang->get('ACTION') ?></th>
                          <th><?= $Lang->get('CATEGORY') ?></th>
                          <th><?= $Lang->get('CREATED') ?></th>
                          <th><?= $Lang->get('AUTHOR') ?></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($this->History->get(false, 5) as $k => $v) { ?>
                        <tr>
                          <td><?= $v['History']['action'] ?></td>
                          <td><?= $v['History']['category'] ?></td>
                          <td><?= $Lang->date($v['History']['created']) ?></td>
                          <td><?= $v['History']['author'] ?></td>
                        </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                </div>

            </div>
            
            <div class="span4">

                <div class="top-bar">
                        <h3><i class="icon-hdd"></i> <?= $Lang->get('SERVER') ?></h3>
                </div>

                <div class="well">
                    <?php if(Configure::read('server.online')) { ?>
                        <div class="row-fluid text-center">
                            <button class="btn" type="button" data-toggle="modal" data-target="#executeCommand" style="padding: 4px 12px;margin-right: 8px;"><i class="icon-terminal"></i><?= $Lang->get('COMMAND') ?></button>
                            <a href="<?= $this->Html->url(array('controller' => 'admin', 'admin' => true, 'action' => 'stop')) ?>" class="btn" type="button" style="padding: 4px 12px;margin-right: 8px;"><i class="icon-off"></i><?= $Lang->get('SHUTDOWN') ?></a>
                        </div>
                        <br>
                        <button class="btn btn-large btn-block btn-success" type="button"><?= $Lang->get('ONLINE') ?> <br> 
                          <?php 
                          $get = $Server->call(array('getPlayerCount' => 'server', 'getPlayerMax' => 'server'));
                          echo $get['getPlayerCount'].'/'.$get['getPlayerMax'];
                          ?>
                        </button>
                    <?php } else { ?>
                        <button class="btn btn-large btn-block btn-danger" type="button"><?= $Lang->get('OFFLINE') ?></button>
                    <?php } ?>
                </div>
            </div>
        </div> 
</div>

<!-- Modal -->
<div class="modal fade" id="executeCommand" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Exécuter une commande</h4>
      </div>
      <div class="modal-body">
        <form action="" method="post">
          <div class="input-append">
              <input class="no-margin span4" name="cmd" type="text"></input>
              <button class="btn btn-info" type="submit">Envoyer</button>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
      </div>
    </div>
  </div>
</div>