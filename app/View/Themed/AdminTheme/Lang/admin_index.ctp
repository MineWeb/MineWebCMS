<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('LANG') ?></h3>
        </div>
        <div class="box-body">
          <form action="" method="post">
            <input type="hidden" id="form_infos" data-ajax="false">

            <div class="ajax-msg"></div>
            
            <?php 
            $lang = $Lang->getall();
            foreach ($lang as $key => $value) { ?>
              <?php if($key != 'FOOTER_ADMIN' AND $key != 'COPYRIGHT') { ?>
                <div class="form-group">
                  <label><?= explode('-', $key)[0] ?></label>
                  <?php if($key != "RESET_PASSWORD_MAIL") { ?>
                    <input type="text" name="<?= $key ?>" class="form-control" value="<?= $value ?>">
                  <?php } else { ?>
                    <textarea name="<?= $key ?>" class="form-control" cols="30" rows="10"><?= $value ?></textarea>
                  <?php } ?>
                  <?php if($key == "FORMATE_DATE") { ?>
                    <small><?= $Lang->get('AVAILABLE_VARIABLES') ?> : {%day}, {%month}, {%year}, {%hour|24}, {%hour|12}, {%minutes}</small>
                  <?php } ?>
                  <?php if($key == "BANNER_SERVER") { ?>
                    <small><?= $Lang->get('AVAILABLE_VARIABLES') ?> : {MOTD}, {VERSION}, {ONLINE}, {ONLINE_LIMIT}</small>
                  <?php } ?>
                  <?php if($key == "VOTE_SUCCESS_SERVER") { ?>
                    <small><?= $Lang->get('AVAILABLE_VARIABLES') ?> : {PLAYER}.</small>
                  <?php } ?>
                  <?php if($key == "RESET_PASSWORD_MAIL") { ?>
                    <small><?= $Lang->get('AVAILABLE_VARIABLES') ?> : {EMAIL}, {PSEUDO}, {LINK}.</small>
                  <?php } ?>
                </div>
              <?php } ?>
            <?php } ?>

            <div class="pull-right">
              <button class="btn btn-primary" type="submit"><?= $Lang->get('SUBMIT') ?></button>
            </div>
          </form>      
        </div>
      </div>
    </div>
  </div>
</section>