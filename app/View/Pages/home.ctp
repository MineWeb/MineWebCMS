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
        <hr class="transition-timer-carousel-progress-bar animate" />
    </header>
<?php } ?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header animated fadeInRight home">
                <?= $Lang->get('LAST_NEWS') ?>
            </h1>
        </div>
        <?php if(!empty($search_news)) { ?>
        <ul id="items">
        <?php foreach ($search_news as $k => $v) { ?>
            <li class="col-md-4 animated fadeInUp">
                <div class="bloc <?= rand_color_news() ?>" style="width:100%;">
                    <h2><?= cut($v['News']['title'], 15) ?></h2>
                    <p><?= cut($v['News']['content'], 220) ?></p>
                    <div class="btn-group">
                      <button id="<?= $v['News']['id'] ?>" type="button" class="btn btn-primary like<?= ($v['News']['liked']) ? ' active' : ''; ?>"<?= ($can_like) ? '' : ' disabled' ?>><?= $v['News']['count_likes'] ?> <i class="fa fa-thumbs-up"></i></button>
                      <button type="button" class="btn btn-primary"><?= $v['News']['count_comments'] ?> <i class="fa fa-comments"></i></button>
                    </div>
                    <a href="<?= $this->Html->url(array('controller' => 'blog', 'action' => $v['News']['slug'])) ?>" class="btn btn-success pull-right"><?= $Lang->get('READ_MORE') ?> »</a>
                </div>
            </li>
        <?php } ?>
        </ul>
        <ol id="pagination"></ol>
        <?php } else { echo '<center><h3>'.$Lang->get('NO_NEWS').'</h3></center>'; } ?>
    </div>
    <div class="row">
      <?php
        if(!empty($facebook_link)) {
          echo '<a href="'.$facebook_link.'" target="_blank" class="btn btn-lg btn-primary" style="margin: 5px 5px;"><i class="fa fa-facebook-square"></i> '.$Lang->get('JOIN_US_ON').' Facebook</a>';
        }
        if(!empty($twitter_link)) {
          echo '<a href="'.$twitter_link.'" target="_blank" class="btn btn-lg btn-info" style="margin: 5px 5px;"><i class="fa fa-twitter"></i> '.$Lang->get('JOIN_US_ON').' Twitter</a>';
        }
        if(!empty($youtube_link)) {
          echo '<a href="'.$youtube_link.'" target="_blank" class="btn btn-lg btn-danger" style="margin: 5px 5px;"><i class="fa fa-youtube"></i> '.$Lang->get('JOIN_US_ON').' YouTube</a>';
        }
        if(!empty($skype_link)) {
          echo '<a href="'.$skype_link.'" target="_blank" class="btn btn-lg btn-info" style="margin: 5px 5px;"><i class="fa fa-skype"></i> '.$Lang->get('JOIN_US_ON').' Skype</a>';
        }
        foreach ($findSocialButtons as $key => $value) {
          echo '<a class="btn btn-default" style="background-color:'.$value['SocialButton']['color'].';color:white;color:white;margin: 0 5px;" target="_blank" href="'.$value['SocialButton']['url'].'">';
          if(!empty($value['SocialButton']['img'])) {
            echo '<img src="'.$value['SocialButton']['img'].'">';
          }
          if(!empty($value['SocialButton']['title'])) {
            echo (!empty($value['SocialButton']['img'])) ? '<br>'.$value['SocialButton']['title'] : $value['SocialButton']['title'];
          }
          echo '</a>';
        }
      ?>
    </div>

    <?= $Module->loadModules('home') ?>
