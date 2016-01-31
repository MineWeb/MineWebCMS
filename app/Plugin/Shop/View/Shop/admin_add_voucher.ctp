<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('ADD_VOUCHER') ?></h3>
        </div>
        <div class="box-body">

          <form action="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'admin_add_voucher_ajax', 'admin' => true)) ?>" method="post" data-ajax="true" data-redirect-url="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'index', 'admin' => true, 'plugin' => 'shop')) ?>">

            <div class="ajax-msg"></div>

            <div class="form-group">
              <label><?= $Lang->get('CODE') ?></label>
              <div class="input-group">
                <input name="code" id="random" class="form-control" placeholder="<?= $Lang->get('CODE') ?>" type="text">
                <span class="input-group-btn">
                  <button class="btn btn-info" type="button" onClick="$('#random').val(random_code(10))"><?= $Lang->get('GENERATE_CODE') ?></button>
                </span>
              </div>
            </div>

            <div class="form-group">
              <label><?= $Lang->get('EFFECTIVE_ON') ?></label>
              <select onChange="hide_or_not(this.value)" class="form-control" name="effective_on">
                <option value="" selected><?= $Lang->get('CHOOSE_OPTION') ?></option>
                <option value="categories"><?= $Lang->get('CATEGORIES') ?></option>
                <option value="items"><?= $Lang->get('ITEMS') ?></option>
                <option value="all"><?= $Lang->get('GLOBAL__ALL') ?></option>
              </select>
            </div>

            <div id="hidden_items" style="display:none;">
              <div class="form-group">
                <label><?= $Lang->get('CHOOSE_ITEM') ?></label>
                <select class="form-control" name="effective_on_item" multiple>
                  <?php if(!empty($items)) { ?>
                    <?php foreach ($items as $key => $value) { ?>
                      <option value="<?= $key ?>"><?= $value ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div id="hidden_categories" style="display:none;">
              <div class="form-group">
                <label><?= $Lang->get('CHOOSE_CATEGORY') ?></label>
                <select class="form-control" name="effective_on_categorie" multiple>
                  <?php if(!empty($categories)) { ?>
                    <?php foreach ($categories as $key => $value) { ?>
                      <option value="<?= $key ?>"><?= $value ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label><?= $Lang->get('TYPE') ?></label>
              <select class="form-control" name="type">
                <option value="" selected><?= $Lang->get('CHOOSE_TYPE') ?></option>
                <option value="2"><?= $Configuration->get_money_name(false, true) ?></option>
                <option value="1"><?= $Lang->get('PERCENTAGE') ?></option>
              </select>
            </div>

            <div class="form-group">
              <label><?= $Lang->get('REDUCTION') ?></label>
              <input class="form-control" placeholder="<?= $Lang->get('IN').' '.$Configuration->get_money_name(false, true).' '.$Lang->get('OR_PERCENTAGE') ?>" type="text" name="reduction">
              <small>Ex: 10</small>
            </div>

            <div class="form-group">
              <label><?= $Lang->get('END_DATE') ?></label>
              <input type="text" class="form-control" name="end_date" placeholder="<?= 'Format : '.$Lang->get('GLOBAL__DATE_YEAR').'-'.$Lang->get('GLOBAL__DATE_MONTH').'-'.$Lang->get('GLOBAL__DATE_DAY').' '.$Lang->get('GLOBAL__DATE_HOUR').':'.$Lang->get('GLOBAL__DATE_MINUTES').':'.$Lang->get('GLOBAL__DATE_SECONDS') ?>">
            </div>

            <div class="form-group">
              <label><?= $Lang->get('LIMIT_BY_USER') ?></label>
              <input type="text" class="form-control" name="limit_per_user" placeholder="<?= $Lang->get('VOUCHER_0_FOR_NO_LIMIT') ?>">
            </div>

            <div class="form-group">
              <div class="checkbox">
                <label>
                  <input name="affich" type="checkbox"> <?= $Lang->get('AFFICH') ?>
                </label>
                <br><small><?= $Lang->get('AFFICH_ON_SHOP') ?></small>
              </div>
            </div>

            <div class="pull-right">
              <a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'index', 'admin' => true, 'plugin' => 'shop')) ?>" class="btn btn-default"><?= $Lang->get('GLOBAL__CANCEL') ?></a>
              <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
  $('#affich').change(function(){
    if($('#affich').is(':checked')) {
      $('#affich').attr('value', '0');
    } else {
      $('#affich').attr('value', '1');
    }
  });
  function hide_or_not(val) {
    $("#hidden_categories").css("display", "none");
      $("#hidden_items").css("display", "block");
    if(val=="categories") {
      $("#hidden_items").css("display", "none");
      $("#hidden_categories").css("display", "block");
    }
    if(val=="items") {
      $("#hidden_categories").css("display", "none");
      $("#hidden_items").css("display", "block");
    }
    if(val=="all") {
      $("#hidden_categories").css("display", "none");
      $("#hidden_items").css("display", "none");
    }
  }

  function random_code(nbcar) {
    var ListeCar = new Array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","0","1","2","3","4","5","6","7","8","9");
    var Chaine ='';
    for(i = 0; i < nbcar; i++)
    {
      Chaine = Chaine + ListeCar[Math.floor(Math.random()*ListeCar.length)];
    }
    return Chaine;
  }
</script>
