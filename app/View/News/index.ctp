<?php 
  
App::import('Component', 'ConnectComponent');
$this->Connect = new ConnectComponent;
?>
<br><br><br>
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h1><?= before_display($title) ?></h1>
                <p class="lead">
                    <?= $Lang->get('BY') ?> <a href="#"><?= $author ?></a>
                </p>

                <hr>
                <p><span class="glyphicon glyphicon-time"></span> <?= $Lang->get('POSTED_ON') . ' ' . $Lang->date($created); ?></p>

                <hr>
                <p class="lead"><?= /*before_display($content)*/ $content ?></p>
                <button id="<?= $id ?>" type="button" class="btn btn-primary pull-right like<?php if(!empty($likes)) { foreach ($likes as $t) { if($t == $id) { echo ' active'; } } } ?>"<?php if(!$Permissions->can('LIKE_NEWS')) { echo ' disabled'; } ?>><?= $like ?> <i class="fa fa-thumbs-up"></i></button><br>
                <?php if($Permissions->can('COMMENT_NEWS')) { ?>
                    <div id="form-comment-fade-out">
                        <hr>
                        <div class="well">
                            <h4><?= $Lang->get('LEAVE_COMMENT') ?> :</h4>
                            <div id="error-on-post"></div>
                            <form method="POST" id="add-comment" role="form">
                                <input name="author" value="<?= $this->Connect->get_pseudo() ?>" type="hidden">
                                <input name="news_id" value="<?= $id ?>" type="hidden">
                                <div class="form-group">
                                    <textarea name="content" class="form-control" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary"><?= $Lang->get('SUBMIT') ?></button>
                            </form>
                        </div>
                    </div>
                <?php } ?>
                <hr>
                <div class="add-comment"></div>
                <?php foreach ($search_comments as $k => $v) { ?>
                    <div class="media" id="comment-<?= $v['Comment']['id'] ?>">
                        <a class="pull-left" href="#">
                            <img class="media-object" src="<?= $this->Html->url(array('controller' => 'API', 'action' => 'get_head_skin/')) ?>/  <?= $v['Comment']['author'] ?>/64" alt="">
                        </a>
                        <div class="media-body">
                            <h4 class="media-heading"><?= $v['Comment']['author'] ?>
                                <small><?= $Lang->date($v['Comment']['created']); ?></small>
                            </h4>
                            <?= before_display($v['Comment']['content']) ?>
                        </div>
                        <div class="pull-right">
                            <?php if($Permissions->can('DELETE_COMMENT') OR $Permissions->can('DELETE_HIS_COMMENT') AND $this->Connect->get_pseudo() == $v['Comment']['author']) { ?>
                                <p><a id="<?= $v['Comment']['id'] ?>" title="<?= $Lang->get('DELETE') ?>" class="comment-delete btn btn-danger btn-sm"><icon class="fa fa-times"></icon></a></p>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <script type="text/javascript">
                $(".comment-delete").click(function() {
                    comment_delete(this);
                });

                function comment_delete(e) {
                    var id = $(e).attr("id");
                    $.post("<?= $this->Html->url(array('controller' => 'news', 'action' => 'ajax_comment_delete')) ?>", { id : id }, function(data) {
                      if(data == 'true') {
                        $('#comment-'+id).slideUp(500);
                      } else {
                        console.log(data);
                      }
                    });
                }
                $("#add-comment").submit(function( event ) {
                    var $form = $( this );
                    var author = $form.find("input[name='author']").val();
                    var content = $form.find("textarea[name='content']").val();
                    var news_id = $form.find("input[name='news_id']").val();
                    $.post("<?= $this->Html->url(array('controller' => 'news', 'action' => 'add_comment')) ?>", { author : author, content : content, news_id : news_id }, function(data) {
                      if(data == 'true') {
                        var comment = '<div class="media"><a class="pull-left" href="#"><img class="media-object" src="<?= $this->Html->url(array('controller' => 'API', 'action' => 'get_head_skin/')) ?>/<?= $this->Connect->get_pseudo() ?>/64" alt=""></a><div class="media-body"><h4 class="media-heading">'+author+' <small>August 25, 2014 at 9:30 PM</small></h4>'+content+'</div></div>';
                        $('.add-comment').hide().html(comment).fadeIn(1500);
                        $('#form-comment-fade-out').slideUp(1500);
                      } else {
                        $('#error-on-post').hide().html('<div class="alert alert-danger" role="alert"><p><b><?= $Lang->get('ERROR') ?> : </b>'+data+'</p></div>').fadeIn(1500);
                      }
                    });
                    return false;
                });
            </script>

        </div>
            <div class="col-md-4">
                <div class="well">
                    <h4><?= $Lang->get('LAST_NEWS') ?></h4>
                    <div class="row">
                        <div class="col-lg-6">
                            <ul class="list-unstyled">
                                <?php foreach ($search_news as $k => $v) { ?>
                                    <li><a href="<?= $this->Html->url(array('controller' => 'blog', 'action' => $v['News']['slug'])) ?>"><?= $v['News']['title'] ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="well">
                    <h4><?= $Lang->get('INFORMATION') ?></h4>
                    <p><b><?= $Lang->get('LAST_UPDATE') ?> : </b><?= $Lang->date($updated) ?></p>
                    <p><b><?= $Lang->get('NUMBER_OF_COMMENTS') ?> : </b><?= $comments ?></p>
                    <p><b><?= $Lang->get('NUMBER_OF_LIKES') ?> : </b><?= $like ?></p>
                </div>
            </div> 
            </div>
        </div>
        <?= $Module->loadModules('news') ?>
    </div>