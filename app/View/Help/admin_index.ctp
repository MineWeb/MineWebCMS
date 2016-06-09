<section class="content">
  <div class="row">
    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('HELP__CHOOSE_QUESTION') ?></h3>
        </div>
        <div class="box-body">
          <label><?= $Lang->get('HELP__CHOOSE_QUESTION') ?></label>

          <?= $this->Html->script('admin/bootstrap-select') ?>
          <?= $this->Html->css('bootstrap-select.min.css') ?>

          <div class="form-group">
            <select class="selectpicker" id="questions" data-live-search="true" title="<?= $Lang->get('HELP__CHOOSE_QUESTION') ?>">
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('HELP__PAGE_EXPLAIN_TITLE') ?></h3>
        </div>
        <div class="box-body">
          <blockquote cite="http://mineweb.org">
            <?= $Lang->get('HELP__PAGE_EXPLAIN_CONTENT') ?>
          </blockquote>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('HELP__ANSWER_TITLE') ?></h3>
        </div>
        <div class="box-body">
          <div id="answers">
            <blockquote>
              <small><i><?= $Lang->get('HELP__CHOOSE_QUESTION') ?></i></small>
            </blockquote>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('HELP__POST_TICKET_TITLE_BOX') ?></h3>
        </div>
        <div class="box-body">
          <p><?= $Lang->get('HELP__POST_TICKET_EXPLAIN') ?></p>
          <form action="<?= $this->Html->url(array('action' => 'submitTicket')) ?>" method="post" data-ajax="true" data-callback="afterSubmitTicket">
            <div class="form-group">
              <label><?= $Lang->get('HELP__POST_TICKET_TITLE') ?></label>
              <input type="text" class="form-control" name="title" placeholder="Problème de...">
            </div>
            <div class="form-group">
              <label><?= $Lang->get('HELP__POST_TICKET_CONTENT') ?></label>
              <textarea class="form-control" name="content"></textarea>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-info"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
  function afterSubmitTicket(data) {

  }

  $(document).ready(function() {

    $('select#questions').on('changed.bs.select', function(e) {

      var id;

      $('ul.dropdown-menu.inner li').each(function() {
        console.log($(this));
        if($(this).hasClass('selected')) {

          id = $('select#questions option')[$(this).attr('data-original-index')].attributes[0].nodeValue;

          return false;
        }
      });

      $('#answers div[data-question-id]').each(function() {
        $(this).slideUp(150);
      });

      $('#answers blockquote').slideUp(150);

      $('#answers div[data-question-id="'+id+'"]').fadeIn();

    });

  });

  $.get('<?= $this->Html->url(array('action' => 'getQuestionsAndAnswers')) ?>', function(data) {

    for (var i = 0; i < data.length; i++) {

      if(i == 0) {
        $('select#questions').empty();
      }

      var option = '<option';
      option += ' data-id="'+data[i]['id']+'"';
      option += '>';
      option += data[i]['question'];
      option += '</option>';

      var answer = '<div data-question-id="'+data[i]['id']+'" style="display:none;">';
      answer += data[i]['answer'];
      answer += '</div>';

      $('#answers').append(answer);

      $('select#questions').append(option);

      $('select#questions').selectpicker('refresh');

    }

  });
</script>
