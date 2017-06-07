<section class="content">
  <div class="row">
    <div class="col-md-3">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('THEME__CUSTOM_FILES_FILES') ?></h3>
        </div>
        <div class="box-body">
          <ul>
            <?php
            foreach ($css_files as $file) {
              echo '<li class="file text-muted"><a href="#" class="viewFile" data-file="'.$file['basename'].'" data-filename="'.$file['name'].'">'.$file['basename'].'</a></li>';

            }
            ?>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-9">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('THEME__CUSTOM_FILES_FILE_CONTENT') ?></h3>
        </div>
        <div class="box-body" style="position:relative;height:1000px;">
          <p id="content">
            <i class="text-muted"><?= $Lang->get('THEME__CUSTOM_FILES_FILE_CONTENT_CHOOSE') ?></i>
          </p>
          <div class="clearfix"></div>
          <form data-ajax="true" action="<?= $this->Html->url(array('action' => 'save_custom_file', $slug)) ?>" data-custom-function="getFileContent">
            <div class="ajax-msg"></div>
            <button id="saveButton" style="display:none;" type="submit" class="btn btn-primary"><?= $Lang->get('GLOBAL__SAVE') ?></button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<div style="height:30px"></div>
<style media="screen">
  #saveButton {
    bottom: -40px;
    position: absolute;
    right: 0;
  }
  .ajax-msg div {
    padding:10px;
  }
  .ajax-msg {
    bottom: -70px;
    position: absolute;
    left: 0;
  }
  ul {
    padding-left: 0;
  }
  ul li {
    list-style-type: none;
  }
  ul li a {
    color: inherit;
    text-decoration: none;
  }
  ul li a:hover {
    color: black;
  }
  ul li.file:before {
    content: "\f15b\00a0\00a0\00a0";
    font-family: 'FontAwesome';
  }

  #editor {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
    }
</style>

<?= $this->Html->script('ace') ?>
<script type="text/javascript">
  $('.viewFile').on('click', function(e) {
    e.preventDefault();

    var btn = $(this);
    var file = btn.attr('data-file');
    var filename = btn.attr('data-filename');

    $.ajax({
      method: 'get',
      url: '<?= $this->Html->url(array('action' => 'get_custom_file', $slug)) ?>'+file,
      success: function(data) {
        $('#content').html('<div id="editor">'+data+'</div>');

        var editor = ace.edit("editor");
        editor.setTheme("ace/theme/monokai");
        editor.getSession().setMode("ace/mode/css");

        $('#saveButton').attr('data-file', file).fadeIn(150);

      },
      error: function() {
        alert('<?= $Lang->get('ERROR__INTERNAL_ERROR') ?>');
      }
    });

  });

  function getFileContent() {
    return {
      'file': $('#saveButton').attr('data-file'),
      'content': ace.edit("editor").getValue()
    }
  }
</script>
