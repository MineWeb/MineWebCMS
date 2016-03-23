<div class="push-nav"></div>
<div class="container news">
    <div class="row">
        <div class="news-content">
            <h1><?= $news['News']['title'] ?></h1>
            <p class="author">
                <?= $Lang->get('GLOBAL__BY') ?> <?= $news['News']['author'] ?>
            </p>

            <div><?= $news['News']['content'] ?></div>
            <br>
            <p class="created">Le <?= $Lang->date($news['News']['created']); ?></p>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="news-brand">
    <div class="container">
        <center>
        <p>
            <?php if($Permissions->can('LIKE_NEWS')) { ?>
                <?= $Lang->get('NEWS__LIKE_THIS_NEWS') ?>
                <button id="<?= $news['News']['id'] ?>" type="button" class="btn btn-primary btn-lg like<?= ($news['News']['liked']) ? ' active' : '' ?>"<?= (!$Permissions->can('LIKE_NEWS')) ? ' disabled' : '' ?>><?= $news['News']['count_likes'] ?> <i class="fa fa-thumbs-up"></i></button>
            <?php } else { ?>
                <?= str_replace('%likes%', $news['News']['count_likes'], $Lang->get('NEWS__NBR_LIKES_ON_THIS_NEWS'))?>
            <?php } ?>
        </p>
        </center>
    </div>
</div>
<div class="container news">
    <div class="row">
        <div class="add-comment"></div>
        <?php foreach ($news['Comment'] as $k => $v) { ?>
            <div class="media" id="comment-<?= $v['id'] ?>">
                <img class="media-object" src="<?= $this->Html->url(array('controller' => 'API', 'action' => 'get_head_skin/')) ?>/<?= $v['author'] ?>/64" alt="">
                <div class="media-body">
                    <?= before_display($v['content']) ?>
                    <h4 class="author"><?= $Lang->get('GLOBAL__BY') ?> <?= $v['author'] ?></h4>
                    <h4 class="created"><?= $Lang->date($v['created']); ?></h4>
                </div>
                 <div class="pull-right">
                    <?php if($Permissions->can('DELETE_COMMENT') OR $Permissions->can('DELETE_HIS_COMMENT') AND $user['pseudo'] == $v['Comment']['author']) { ?>
                        <p><a id="<?= $v['id'] ?>" title="<?= $Lang->get('GLOBAL__DELETE') ?>" class="comment-delete btn btn-danger btn-sm"><icon class="fa fa-times"></icon></a></p>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <?php if($Permissions->can('COMMENT_NEWS')) { ?>
            <center><a href="#" data-toggle="modal" data-target="#postcomment" class="btn btn-primary btn-lg"><?= $Lang->get('NEWS__COMMENT_TITLE') ?></a></center>
        <?php } ?>

        </div>
    </div>
</div>
<?= $Module->loadModules('news') ?>

<?php if($Permissions->can('COMMENT_NEWS')) { ?>
  <div class="modal fade" id="postcomment" tabindex="-1" role="dialog" aria-labelledby="postcommentLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="<?= $Lang->get('GLOBAL__CLOSE') ?>"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel"><?= $Lang->get('NEWS__COMMENT_TITLE') ?></h4>
        </div>
        <div class="modal-body">
          <div id="form-comment-fade-out">
            <div id="error-on-post"></div>
            <form method="POST" data-ajax="true" action="<?= $this->Html->url(array('controller' => 'news', 'action' => 'add_comment')) ?>" data-callback-function="addcomment" data-success-msg="false">
              <input name="news_id" value="<?= $news['News']['id'] ?>" type="hidden">
              <div class="form-group">
                  <textarea name="content" class="form-control" rows="3"></textarea>
              </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><?= $Lang->get('GLOBAL__CLOSE') ?></button>
          <button type="submit" class="btn btn-primary pull-right"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
<script type="text/javascript">
    $(".comment-delete").click(function() {
        comment_delete(this);
    });

    function comment_delete(e) {
      var inputs = {};
      var id = $(e).attr("id");
      inputs["id"] = id;
      inputs["data[_Token][key]"] = '<?= $csrfToken ?>';
      $.post("<?= $this->Html->url(array('controller' => 'news', 'action' => 'ajax_comment_delete')) ?>", inputs, function(data) {
          if(data == 'true') {
            $('#comment-'+id).fadeOut(500);
          } else {
            console.log(data);
          }
        });
    }

    function addcomment(data) {
      var d = new Date();
      var comment = '<div class="media"><img class="media-object" src="<?= $this->Html->url(array('controller' => 'API', 'action' => 'get_head_skin/')) ?>/<?= $user['pseudo'] ?>/64" alt=""><div class="media-body">'+data['content']+'<h4 class="author"><?= $Lang->get('GLOBAL__BY') ?> <?= $user['pseudo'] ?></h4><h4 class="created">'+d.getHours()+'h'+d.getMinutes()+'</h4></div></div>';
      $('.add-comment').hide().html(comment).fadeIn(1500);
      $('#postcomment').modal('hide')
    }
</script>
