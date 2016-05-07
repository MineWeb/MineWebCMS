<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('CONFIG__LANG_LABEL') ?></h3>
        </div>
        <div class="box-body">
          <form action="" method="post">

            <div class="ajax-msg"></div>

            <?php
            foreach ($messages as $key => $value) { ?>
              <?php if($key != 'FOOTER_ADMIN') { ?>
                <div class="form-group">
                  <label><?= explode('-', $key)[0] ?></label>
                  <?php if($key != "RESET_PASSWORD_MAIL") { ?>
                    <input type="text" name="<?= $key ?>" class="form-control" value="<?= htmlentities($value) ?>">
                  <?php } else { ?>
                    <textarea name="<?= $key ?>" class="form-control" cols="30" rows="10"><?= $value ?></textarea>
                  <?php } ?>
                  <?php if($key == "GLOBAL__FORMAT_DATE") { ?>
                    <small><?= $Lang->get('CONFIG__LANG_AVAILABLE_VARIABLES') ?> : {%day}, {%month}, {%year}, {%hour|24}, {%hour|12}, {%minutes}</small>
                  <?php } ?>
                  <?php if($key == "SERVER__STATUS_MESSAGE") { ?>
                    <small><?= $Lang->get('CONFIG__LANG_AVAILABLE_VARIABLES') ?> : {MOTD}, {VERSION}, {ONLINE}, {ONLINE_LIMIT}</small>
                  <?php } ?>
                  <?php if($key == "VOTE_SUCCESS_SERVER") { ?>
                    <small><?= $Lang->get('CONFIG__LANG_AVAILABLE_VARIABLES') ?> : {PLAYER}.</small>
                  <?php } ?>
                  <?php if($key == "RESET_PASSWORD_MAIL") { ?>
                    <small><?= $Lang->get('CONFIG__LANG_AVAILABLE_VARIABLES') ?> : {EMAIL}, {PSEUDO}, {LINK}.</small>
                  <?php } ?>
                  <?php if($key == "COPYRIGHT") { ?>
                    <small><?= $Lang->get('CONFIG__INFO_LANG') ?></small>
                  <?php } ?>
                </div>
              <?php } ?>
            <?php } ?>

            <input type="hidden" name="data[_Token][key]" value="<?= $csrfToken ?>">

            <div class="pull-right">
              <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
