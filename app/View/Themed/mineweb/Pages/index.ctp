<?php

?>
<div class="push-nav"></div>
<div class="container page">
    <div class="row">
        <div class="page-content">
            <h1 class="title"><?= before_display($page['title']) ?></h1>
            <p class="author"><?= $Lang->get('GLOBAL__AUTHOR') ?> : <?= $page['author'] ?></p>

            <p style="color:black;"><?= $page['content'] ?></p>
            <br>
            <p class="created"><?= $Lang->get('GLOBAL__UPDATED') ?> : <?= $Lang->date($page['updated']) ?></p>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="push-nav"></div>
