<?php if(!isset($theme_config['slider']) || $theme_config['slider'] == "true") { ?>
    <header id="myCarousel" class="carousel slide transition-timer-carousel">
        <div class="carousel-inner">
            <?php if(!empty($search_slider)) { ?>
                <?php $i = 0; foreach ($search_slider as $k => $v) { ?>
                    <div class="item<?php if($i == 0) { echo ' active'; } ?>">
                        <div class="fill" style="background-image:url('<?= $v['Slider']['url_img'] ?>');"></div>
                        <div class="carousel-caption">
                            <h2><?= before_display($v['Slider']['title']) ?></h2>
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
            <h1 class="page-header animated fadeInRight">
                <?= $Lang->get('LAST_NEWS') ?>
            </h1>
        </div>
        <?php if(!empty($search_news)) { ?>
            <ul id="items">
            <?php foreach ($search_news as $k => $v) { ?>
                <li class="col-md-4 animated fadeInUp">
                    <div class="bloc <?= rand_color_news() ?>">
                        <h2><?= substr($v['News']['title'], 0, 15); ?><?php if(strlen($v['News']['title']) > "15") { echo '...'; } ?></h2>
                        <p><?= substr($v['News']['content'], 0, 220); ?><?php if(strlen($v['News']['content']) > "220") { echo '...'; } ?></p>
                        <div class="btn-group">
                          <button id="<?= $v['News']['id'] ?>" type="button" class="btn btn-primary like<?php if(!empty($likes)) { foreach ($likes as $t) { if($t == $v['News']['id']) { echo ' active'; } } } ?>"<?php if(!$isConnected AND $Permissions->can('LIKE_NEWS')) { echo ' disabled'; } ?>><?= $v['News']['like'] ?> <i class="fa fa-thumbs-up"></i></button>
                          <button type="button" class="btn btn-primary"><?= $v['News']['comments'] ?> <i class="fa fa-comments"></i></button>
                        </div>
                        <a href="<?= $this->Html->url(array('controller' => 'blog', 'action' => $v['News']['slug'])) ?>" class="btn btn-success pull-right"><?= $Lang->get('READ_MORE') ?> »</a>
                    </div>
                </li>
            <?php } ?>
            </ul>
            <ol id="pagination"></ol>
            <?php } else { echo '<center><h3>'.$Lang->get('NO_NEWS').'</h3></center>'; } ?>
        </div>

        <hr>

        <div class="well">
            <div class="row">
                <center>
                    <p>
                        <a href="<?= $Configuration->get('facebook') ?>" class="btn btn-lg btn-facebook"><i class="fa fa-facebook-square"></i> <?= $Lang->get('JOIN_US_ON') ?> Facebook</a>
                        <a href="<?= $Configuration->get('twitter') ?>" class="btn btn-lg btn-twitter"><i class="fa fa-twitter"></i> <?= $Lang->get('JOIN_US_ON') ?> Twitter</a>
                        <a href="<?= $Configuration->get('youtube') ?>" class="btn btn-lg btn-youtube"><i class="fa fa-youtube"></i> <?= $Lang->get('JOIN_US_ON') ?> YouTube</a>
                        <a href="<?= $Configuration->get('skype') ?>" class="btn btn-lg btn-skype"><i class="fa fa-skype"></i> <?= $Lang->get('CONTACT_US_ON') ?> Skype</a>
                    </p>
                </center>
            </div>
        </div>

        <?= $Module->loadModules('home') ?>