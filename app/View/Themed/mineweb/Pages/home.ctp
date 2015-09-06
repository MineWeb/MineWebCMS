<?php 
$this->Configuration = new ConfigurationComponent;
$this->Connect = new ConnectComponent;
$theme_config = file_get_contents(ROOT.'/app/View/Themed/Mineweb/config/config.json');
$theme_config = json_decode($theme_config, true);
?>
    <?php if($theme_config['slider'] == "true") { ?>
        <header id="myCarousel" class="carousel slide transition-timer-carousel">
            <div class="carousel-inner">
                <?php if(!empty($search_slider)) { ?>
                    <?php $i = 0; foreach ($search_slider as $k => $v) { ?>
                    <div class="item<?php if($i == 0) { echo ' active'; } ?>">
                        <div class="fill" style="background-image:url('<?= $v['Slider']['url_img'] ?>');"></div>
                        <div class="carousel-caption">
                            <h2><?= before_display($v['Slider']['title']) ?></h2>
                            <p><?= before_display($v['Slider']['subtitle']) ?></p>
                        </div>
                    </div>
                    <?php $i++; } ?>
                <?php } else { ?>
                    <div class="item active">
                        <div class="fill" style="background-image:url('http://placehold.it/1900x1080&text=1900x1080');"></div>
                        <div class="carousel-caption">
                            <h2>Caption 1</h2>
                        </div>
                    </div>
                    <div class="item">
                        <div class="fill" style="background-image:url('http://placehold.it/1900x1080&text=1900x1080');"></div>
                        <div class="carousel-caption">
                            <h2>Caption 2</h2>
                        </div>
                    </div>
                    <div class="item">
                        <div class="fill" style="background-image:url('http://placehold.it/1900x1080&text=1900x1080');"></div>
                        <div class="carousel-caption">
                            <h2>Caption 3</h2>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                <span class="icon-prev"></span>
            </a>
            <a class="right carousel-control" href="#myCarousel" data-slide="next">
                <span class="icon-next"></span>
            </a>
             <!-- Timer "progress bar" -->
            <hr class="transition-timer-carousel-progress-bar animate">
        </header>
    <?php } ?>
    <div class="mini-navbar mini-navbar-dark hidden-xs">
      <div class="container">
        <div class="col-sm-12">
          <?php 
          $banner_server = $this->Configuration->get('banner_server');
          if(empty($banner_server)) {
            if($Server->online()) {
              echo '<p class="text-center"><?= $Lang->banner_server($Server->banner_infos()) ?></p>';
            } else { 
              echo '<p class="text-center">'.$Lang->get('SERVER_OFF').'</p>';
            }
          } else {
            $banner_server = unserialize($banner_server);
            $server_infos = $Server->banner_infos($banner_server);
            if(!empty($server_infos['getPlayerMax'])) {
              echo '<p class="text-center">'.$Lang->banner_server($server_infos).'</p>';
            } else {
              echo '<p class="text-center">'.$Lang->get('SERVER_OFF').'</p>';
            }
          } 
          ?>
        </div>
      </div>
    </div>

    <div class="container bg">
        <div id="debug"></div>
        <div class="row">
            <?php if(!empty($search_news)) { ?>
            <ul id="items">
            <?php foreach ($search_news as $k => $v) { ?>
                <li class="col-md-4 animated fadeInUp">
                    <div>
                        <h2><?= substr($v['News']['title'], 0, 13); ?><?php if(strlen($v['News']['title']) > "13") { echo '...'; } ?></h2>
                        <?= substr($v['News']['content'], 0, 170); ?><?php if(strlen($v['News']['content']) > "170") { echo '...'; } ?>
                        <div class="btn-like">
                            <p><?= $v['News']['like'] ?> <i class="fa fa-thumbs-up"></i></p>
                        </div>
                        <div class="a-like pull-right">
                            <p><a href="<?= $this->Html->url(array('controller' => 'blog', 'action' => $v['News']['slug'])) ?>"><?= $Lang->get('READ_MORE') ?> »</a></p>
                        </div>
                    </div>
                </li>
            <?php } ?>
            </ul>
            <ol id="pagination"></ol>
            <?php } else { echo '<center><h3>'.$Lang->get('NO_NEWS').'</h3></center>'; } ?>
        </div>
        
        
    </div>
        <div class="brand-social hidden-sm hidden-xs">
            <div class="row">
                <div class="container">
                    <center>
                        <a class="btn-skype" target="_blank" href="<?= $this->Configuration->get('skype') ?>"><img src="theme/mineweb/img/skype.png"></a>
                        <a class="btn-youtube" target="_blank" href="<?= $this->Configuration->get('youtube') ?>"><img src="theme/mineweb/img/yt.png"></a>
                        <span><?= $Lang->get('JOIN_US') ?></span>
                        <a class="btn-twitter" target="_blank" href="<?= $this->Configuration->get('twitter') ?>"><img src="theme/mineweb/img/twitter.png"></a>
                        <a class="btn-facebook" target="_blank" href="<?= $this->Configuration->get('facebook') ?>"><img src="theme/mineweb/img/fb.png"></a>
                    </center>
                </div>
            </div>
        </div>

        <?= $Module->loadModules('home') ?>
        <script>
        $(document).ready(function() {
            $('.carousel').carousel({
                interval: 5000 //changer la vitesse
            })
        });
        </script>
        <script type="text/javascript">
        $(document).ready(function() {    
        //Events that reset and restart the timer animation when the slides change
        $("#myCarousel").on("slide.bs.carousel", function(event) {
            //The animate class gets removed so that it jumps straight back to 0%
            $(".transition-timer-carousel-progress-bar", this)
                .removeClass("animate").css("width", "0%");
        }).on("slid.bs.carousel", function(event) {
            //The slide transition finished, so re-add the animate class so that
            //the timer bar takes time to fill up
            $(".transition-timer-carousel-progress-bar", this)
                .addClass("animate").css("width", "100%");
        });
        
        //Kick off the initial slide animation when the document is ready
        $(".transition-timer-carousel-progress-bar", "#myCarousel")
            .css("width", "100%");
        });


        jQuery(function($){
            
            $('ul#items').easyPaginate({
                step:3
            });
            
        });
        </script>