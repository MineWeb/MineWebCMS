<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('VOTE_TITLE') ?>&nbsp;&nbsp;&nbsp;<a href="<?= $this->Html->url(array('controller' => 'voter', 'action' => 'reset', 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('RESET') ?></a></h3>
        </div>
        <div class="box-body">
        
          <form action="<?= $this->Html->url(array('controller' => 'voter', 'action' => 'add_ajax', 'admin' => true)) ?>" method="post">
            <input type="hidden" id="form_infos" data-ajax="true" data-redirect-url="<?= $this->Html->url(array('controller' => 'voter', 'action' => 'index', 'admin' => true)) ?>">

            <div class="ajax-msg"></div>
            

            <div class="form-group">
              <label><?= $Lang->get('TIME_VOTE') ?></label>
              <input name="time_vote" class="form-control" value="<?= @$vote['time_vote'] ?>" placeholder="minutes" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('PAGE_VOTE') ?></label>
              <input name="page_vote" class="form-control" value="<?= @$vote['page_vote'] ?>" placeholder="minutes" type="text">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('SERVER') ?></label>
              <select class="form-control" name="servers" multiple>
                <?php foreach ($servers as $key => $value) { ?>
                    <option value="<?= $key ?>"<?= (in_array($key, $selected_server)) ? ' selected' : '' ?>><?= $value ?></option>
                <?php } ?>
              </select>
            </div>

            <div class="form-group">
              <label><?= $Lang->get('REWARDS_TYPE') ?></label>
              <select class="form-control" name="rewards_type">
                <?php
                if(@$vote['rewards_type'] == 0) {
                  $options = array('0' => $Lang->get('RANDOM'), '1' => $Lang->get('ALL'));
                } else {
                  $options = array('1' => $Lang->get('ALL'), '0' => $Lang->get('RANDOM'));
                }
                ?>
                <?php foreach ($options as $key => $value) { ?>
                    <option value="<?= $key ?>"><?= $value ?></option>
                <?php } ?>
              </select>
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