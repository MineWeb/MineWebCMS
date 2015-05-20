<?php 
  
?>    
<div class="push-nav"></div>
<div class="container page">
    <div class="row">
        <div class="page-content">
            <h1 class="title"><?= before_display($page['title']) ?></h1>
            <p class="author"><?= $Lang->get('AUTHOR') ?> : <?= $page['author'] ?></p>

            <p><?= $page['content'] ?></p>
            <br>
            <p class="created"><?= $Lang->get('LAST_UPDATE') ?> : <?= $Lang->date($page['updated']) ?></p>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="push-nav"></div>