<section class="content">
  <div class="row">
    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Choississez votre question</h3>
        </div>
        <div class="box-body">
          <label>Choississez une question</label>

          <?= $this->Html->script('admin/bootstrap-select') ?>
          <?= $this->Html->css('bootstrap-select.min.css') ?>

          <div class="form-group">
            <select class="selectpicker" id="questions" data-live-search="true" title="Sélectionnez une question">
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Explication</h3>
        </div>
        <div class="box-body">
          <blockquote cite="http://mineweb.org">
            <p>Cette page rescence une liste de questions et réponses vous permettant de résoudre rapidement vos problèmes.</p>
            <p>La liste de questions est récupéré automatiquement sur mineweb.org, elle est donc actualisée en temps réel.</p>
          </blockquote>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Réponse à la question</h3>
        </div>
        <div class="box-body">
          <div id="answers">
            <blockquote>
              <small><i>Veuillez sélectioner une question</i></small>
            </blockquote>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Problème non résolu ?</h3>
        </div>
        <div class="box-body">
          <p>Postez directement votre question au support. Le suivi du ticket se fera sur mineweb.org. Un rapport complet de votre CMS sera envoyé à nos serveurs en même temps que le ticket.</p>
          <form action="<?= $this->Html->url(array('action' => 'submitTicket')) ?>" method="post" data-ajax="true" data-callback="afterSubmitTicket">
            <div class="form-group">
              <label>Titre</label>
              <input type="text" class="form-control" name="title" placeholder="Problème de...">
            </div>
            <div class="form-group">
              <label>Expliquez le plus clairement possible votre problème</label>
              <textarea class="form-control" name="content">Bonjour,</textarea>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-info">Envoyer</button>
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
