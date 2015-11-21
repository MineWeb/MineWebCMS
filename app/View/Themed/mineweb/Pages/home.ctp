<?php if(!isset($theme_config['slider']) || $theme_config['slider'] == "true") { ?>
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
                    <div class="fill" style="background-image:url('http://placehold.it/1905x420&text=1905x420');"></div>
                    <div class="carousel-caption">
                        <h2>Caption 1</h2>
                    </div>
                </div>
                <div class="item">
                    <div class="fill" style="background-image:url('http://placehold.it/1905x420&text=1905x420');"></div>
                    <div class="carousel-caption">
                        <h2>Caption 2</h2>
                    </div>
                </div>
                <div class="item">
                    <div class="fill" style="background-image:url('http://placehold.it/1905x420&text=1905x420');"></div>
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
      <?= ($banner_server) ? '<p>'.$banner_server.'</p>' : '<p class="text-center">'.$Lang->get('SERVER_OFF').'</p>' ?>
    </div>
  </div>
</div>

<div class="container bg">
    <div class="row">
        <?php if(!empty($search_news)) { ?>
        <ul id="items">
        <?php foreach ($search_news as $k => $v) { ?>
            <li class="col-md-4 animated fadeInUp">
                <div>
                    <h2><?= cut($v['News']['title'], 13) ?></h2>
                    <p><?= cut($v['News']['content'], 170) ?></p>
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
                    <a class="btn-skype" target="_blank" href="<?= $skype_link ?>"><img src="theme/mineweb/img/skype.png"></a>
                    <a class="btn-youtube" target="_blank" href="<?= $youtube_link ?>"><img src="theme/mineweb/img/yt.png"></a>
                    <span><?= $Lang->get('JOIN_US') ?></span>
                    <a class="btn-twitter" target="_blank" href="<?= $twitter_link ?>"><img src="theme/mineweb/img/twitter.png"></a>
                    <a class="btn-facebook" target="_blank" href="<?= $facebook_link ?>"><img src="theme/mineweb/img/fb.png"></a>
                </center>
            </div>
        </div>
    </div>

    <?= $Module->loadModules('home') ?>