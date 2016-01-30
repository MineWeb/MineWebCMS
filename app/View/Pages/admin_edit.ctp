<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('PAGE__EDIT') ?></h3>
        </div>
        <div class="box-body">
          <form action="<?= $this->Html->url(array('controller' => 'pages', 'action' => 'edit_ajax')) ?>" method="post" data-ajax="true" data-redirect-url="<?= $this->Html->url(array('controller' => 'pages', 'action' => 'index', 'admin' => 'true')) ?>">

            <div class="ajax-msg"></div>

            <input type="hidden" name="id" value="<?= $page['id'] ?>">

            <div class="form-group">
              <label><?= $Lang->get('GLOBAL__TITLE') ?></label>
              <input name="title" class="form-control" value="<?= $page['title'] ?>" placeholder="<?= $Lang->get('GLOBAL__TITLE') ?>" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('GLOBAL__SLUG') ?></label>
              <div class="input-group">
                <div class="input-group-addon"><?= $this->Html->url('/p/', true) ?></div>
                <input name="slug" id="slug" class="form-control" value="<?= $page['slug'] ?>" placeholder="<?= $Lang->get('GLOBAL__SLUG') ?>" type="text">
                <span class="input-group-btn">
                  <a href="#" id="generate_slug" class="btn btn-info"><?= $Lang->get('GLOBAL__GENERATE') ?></a>
                </span>
              </div>
            </div>

            <div class="form-group">
              <?= $this->Html->script('admin/tinymce/tinymce.min.js') ?>
              <script type="text/javascript">
              tinymce.init({
                  selector: "textarea",
                  height : 300,
                  width : '100%',
                  language : 'fr_FR',
                  plugins: "textcolor code image link",
                  toolbar: "fontselect fontsizeselect bold italic underline strikethrough link image forecolor backcolor alignleft aligncenter alignright alignjustify cut copy paste bullist numlist outdent indent blockquote code"
               });
              </script>
              <textarea id="editor" name="content" cols="30" rows="10"><?= $page['content'] ?></textarea>
            </div>

            <div class="pull-right">
              <a href="<?= $this->Html->url(array('controller' => 'pages', 'action' => 'admin_index', 'admin' => true)) ?>" class="btn btn-default"><?= $Lang->get('GLOBAL__CANCEL') ?></a>
              <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
