<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('ADD_NEWS') ?></h3>
        </div>
        <div class="box-body">
          <form action="<?= $this->Html->url(array('controller' => 'news', 'action' => 'add_ajax')) ?>" method="post">
            <input type="hidden" id="form_infos" data-ajax="true" data-redirect-url="<?= $this->Html->url(array('controller' => 'news', 'action' => 'admin_index', 'admin' => 'true')) ?>">

            <div class="ajax-msg"></div>
      
            <div class="form-group">
              <label><?= $Lang->get('TITLE') ?></label>
              <input name="title" class="form-control" placeholder="<?= $Lang->get('TITLE') ?>" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('SLUG') ?></label>
              <div class="input-group">
                <input name="slug" id="slug" class="form-control" placeholder="<?= $Lang->get('SLUG') ?>" type="text">
                <span class="input-group-btn">
                  <a href="#" id="generate_slug" class="btn btn-info"><?= $Lang->get('GENERATE') ?></a>
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
                  plugins: "textcolor code",
                  toolbar: "fontselect fontsizeselect bold italic underline strikethrough forecolor backcolor alignleft aligncenter alignright alignjustify cut copy paste bullist numlist outdent indent blockquote code"
               });
              </script>
              <textarea id="editor" name="content" cols="30" rows="10"></textarea>
            </div>

            <div class="form-group">
              <div class="checkbox">
                <label>
                  <input name="published" type="checkbox"> <?= $Lang->get('PUBLISH_THIS_NEWS') ?>
                </label>
              </div>
            </div>

            <div class="pull-right">
              <a href="<?= $this->Html->url(array('controller' => 'news', 'action' => 'admin_index', 'admin' => true)) ?>" class="btn btn-default"><?= $Lang->get('CANCEL') ?></a>  
              <button class="btn btn-primary" type="submit"><?= $Lang->get('SUBMIT') ?></button>
            </div>
          </form>      
        </div>
      </div>
    </div>
  </div>
</section>