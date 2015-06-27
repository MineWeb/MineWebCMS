<?php 
  
App::import('Component', 'ConnectComponent');
$this->Connect = new ConnectComponent;
?>
    <div class="push-nav"></div>
    <div class="container news">
        <div class="row">
            <div class="news-content">
                <h1><?= $title ?></h1>
                <p class="author">
                    <?= $Lang->get('BY') ?> <?= $author ?>
                </p>

                <?= $content ?>
                <br>
                <p class="created">Le <?= $Lang->date($created); ?></p>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="news-brand">
        <div class="container">
            <center>
            <p>
                <?php if($Permissions->can('LIKE_NEWS')) { ?>
                    <?= $Lang->get('LIKE_THIS') ?>
                    <button id="<?= $id ?>" type="button" class="btn btn-primary btn-lg like<?php if(!empty($likes)) { foreach ($likes as $t) { if($t == $id) { echo ' active'; } } } ?>"<?php if(!$Permissions->can('LIKE_NEWS')) { echo ' disabled'; } ?>><?= $like ?> <i class="fa fa-thumbs-up"></i></button>
                <?php } else { ?>
                    <?= str_replace('%likes%', $like, $Lang->get('THEY_LIKE_THIS'))?>
                <?php } ?>
            </p>
            </center>
        </div>
    </div>
    <div class="container news">
        <div class="row">
            <div class="add-comment"></div>
            <?php foreach ($search_comments as $k => $v) { ?>
                <div class="media" id="comment-<?= $v['Comment']['id'] ?>">
                    <img class="media-object" src="<?= $this->Html->url(array('controller' => 'API', 'action' => 'get_head_skin/')) ?>/<?= $v['Comment']['author'] ?>/64" alt="">
                    <div class="media-body">
                        <?= before_display($v['Comment']['content']) ?>
                        <h4 class="author"><?= $Lang->get('BY') ?> <?= $v['Comment']['author'] ?></h4>
                        <h4 class="created"><?= $Lang->date($v['Comment']['created']); ?></h4>
                    </div>
                     <div class="pull-right">
                        <?php if($Permissions->can('DELETE_COMMENT') OR $Permissions->can('DELETE_HIS_COMMENT') AND $this->Connect->get_pseudo() == $v['Comment']['author']) { ?>
                            <p><a id="<?= $v['Comment']['id'] ?>" title="<?= $Lang->get('DELETE') ?>" class="comment-delete btn btn-danger btn-sm"><icon class="fa fa-times"></icon></a></p>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <?php if($Permissions->can('COMMENT_NEWS')) { ?>
                <center><a href="#" data-toggle="modal" data-target="#postcomment" class="btn btn-primary btn-lg"><?= $Lang->get('LEAVE_COMMENT') ?></a></center>
            <?php } ?>

            </div>
        </div>
    </div>
    <?= $Module->loadModules('news') ?>
<div class="modal fade" id="postcomment" tabindex="-1" role="dialog" aria-labelledby="postcommentLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= $Lang->get('CLOSE') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $Lang->get('LEAVE_COMMENT') ?></h4>
      </div>
      <div class="modal-body">
        <?php if($Permissions->can('COMMENT_NEWS')) { ?>
            <div id="form-comment-fade-out">
                <div id="error-on-post"></div>
                <form method="POST" id="add-comment" role="form">
                    <input name="author" value="<?= $this->Connect->get_pseudo() ?>" type="hidden">
                    <input name="news_id" value="<?= $id ?>" type="hidden">
                    <div class="form-group">
                        <textarea name="content" class="form-control" rows="3"></textarea>
                    </div>
            </div>
        <?php } ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= $Lang->get('CLOSE') ?></button>
        <button type="submit" class="btn btn-primary pull-right"><?= $Lang->get('SUBMIT') ?></button>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
    $(".comment-delete").click(function() {
        comment_delete(this);
    });

    function comment_delete(e) {
        var id = $(e).attr("id");
        $.post("<?= $this->Html->url(array('controller' => 'news', 'action' => 'ajax_comment_delete')) ?>", { id : id }, function(data) {
          if(data == 'true') {
            $('#comment-'+id).fadeOut(500);
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
            var d = new Date();
            var comment = '<div class="media"><img class="media-object" src="<?= $this->Html->url(array('controller' => 'API', 'action' => 'get_head_skin/')) ?>/<?= $this->Connect->get_pseudo() ?>/64" alt=""><div class="media-body">'+content+'<h4 class="author"><?= $Lang->get('BY') ?> '+author+'</h4><h4 class="created">'+d.getHours()+'h'+d.getMinutes()+'</h4></div></div>';
            $('.add-comment').hide().html(comment).fadeIn(1500);
            $('#postcomment').modal('hide')
          } else {
            $('#error-on-post').hide().html('<div class="alert alert-danger" role="alert"><p><b><?= $Lang->get('ERROR') ?> : </b>'+data+'</p></div>').fadeIn(1500);
          }
        });
        return false;
    });
</script>