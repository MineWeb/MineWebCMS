<?= $this->Html->css('shop-homepage.css') ?>
<?= $this->Html->css('Shop.jquery.bootstrap-touchspin.css') ?>
<div class="container">
  <div class="row">
    <div class="col-md-3">
      <p class="lead"><?= ($isConnected) ? $money.' '.$Configuration->getMoneyName() : $Lang->get('SHOP__TITLE'); ?></p>
        <div class="list-group">
            <?php
            $i = 0;
            foreach ($search_categories as $k => $v) {
              $i++;
            ?>
                <a href="<?= $this->Html->url(array('controller' => 'c/'.$v['Category']['id'], 'plugin' => 'shop')) ?>" class="list-group-item<?= (isset($category) AND $v['Category']['id'] == $category OR !isset($category) AND $i == 1) ? ' active' : ''; ?>"><?= before_display($v['Category']['name']) ?></a>
            <?php } ?>
        </div>
        <?php if($isConnected AND $Permissions->can('CREDIT_ACCOUNT')) { ?>
            <a href="#" data-toggle="modal" data-target="#addmoney" class="btn btn-success btn-block pull-right"><?= $Lang->get('SHOP__ADD_MONEY') ?></a>
        <?php } ?>
        <?php if($isConnected) { ?>
          <a href="#" data-toggle="modal" data-target="#cart-modal" class="btn btn-primary btn-block pull-right"><?= $Lang->get('SHOP__BUY_CART') ?></a>
        <?php } ?>
      </div>
      <div class="col-md-9">
        <div class="row">
          <?= $vouchers->get_vouchers() // Les promotions en cours ?>
        </div>

        <div class="row">
          <?php
          $col = 4;
          $i = 0;
          foreach ($search_items as $k => $v) {
            if(!isset($category) AND $v['Item']['category'] == $search_first_category OR isset($category) AND $v['Item']['category'] == $category) {
              $i++;
              $newRow = ( ( $i % ( (12 / $col) +1 ) ) == 0);
          ?>
              <?= ($newRow) ? '</div>' : '' ?>
              <?= ($newRow) ? '<div class="row">' : '' ?>
                <div class="col-sm-<?= $col ?> col-lg-<?= $col ?> col-md-<?= $col ?>">
                    <div class="thumbnail">
                        <?php if(isset($v['Item']['img_url'])) { ?><img src="<?= $v['Item']['img_url'] ?>" alt=""><?php } ?>
                        <div class="caption" style="height:auto;">
                            <h4 class="pull-right"><?= $v['Item']['price'] ?><?php if($v['Item']['price'] == 1) { echo  ' '.$singular_money; } else { echo  ' '.$plural_money; } ?></h4>
                            <h4><a href="#"><?= before_display($v['Item']['name']) ?></a>
                            </h4>
                            <p><?=
                            $this->Text->truncate(
                              strip_tags($v['Item']['description']),
                              140,
                              array('ellipsis' => '...', 'html' => true)
                            )
                            ?></p>
                            <?php if($isConnected AND $Permissions->can('CAN_BUY')) { ?><button class="btn btn-success pull-right display-item" data-item-id="<?= $v['Item']['id'] ?>"><?= $Lang->get('SHOP__BUY') ?></button> <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
          <?php } ?>
        </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  var LOADING_MSG = '<?= $Lang->get('GLOBAL__LOADING') ?>';
  var ADDED_TO_CART_MSG = '<?= $Lang->get('SHOP__BUY_ADDED_TO_CART') ?> <i class="fa fa-check"></i>';
  var CART_EMPTY_MSG = '<?= $Lang->get('SHOP__BUY_CART_EMPTY') ?>';
  var ITEM_GET_URL = '<?= $this->Html->url(array('controller' => 'shop/ajax_get', 'plugin' => 'shop')); ?>/';
  var VOUCHER_CHECK_URL = '<?= $this->Html->url(array('action' => 'checkVoucher')) ?>/';
  var BUY_URL = '<?= $this->Html->url(array('action' => 'buy_ajax')) ?>';

  var CART_ITEM_NAME_MSG = '<?= $Lang->get('SHOP__ITEM_NAME') ?>';
  var CART_ITEM_PRICE_MSG = '<?= $Lang->get('SHOP__ITEM_PRICE') ?>';
  var CART_ITEM_QUANTITY_MSG = '<?= $Lang->get('SHOP__ITEM_QUANTITY') ?>';
  var CART_ACTIONS_MSG = '<?= $Lang->get('GLOBAL__ACTIONS') ?>';

  var CSRF_TOKEN = '<?= $csrfToken ?>';
</script>
<?= $this->Html->script('Shop.jquery.cookie') ?>
<?= $this->Html->script('Shop.shop') ?>
<?= $this->Html->script('Shop.jquery.bootstrap-touchspin.js') ?>
<div class="modal fade" id="buy-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title"><?= $Lang->get('SHOP__BUY_CONFIRM') ?></h4>
      </div>
      <div class="modal-body">
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="cart-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title"><?= $Lang->get('SHOP__BUY_CART') ?></h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <div class="pull-left">
          <input name="cart-voucher" type="text" class="form-control" autocomplete="off" id="cart-voucher" style="width:245px;" placeholder="<?= $Lang->get('SHOP__BUY_VOUCHER_ASK') ?>">
        </div>
        <button class="btn disabled"><?= $Lang->get('SHOP__ITEM_TOTAL') ?> : <span id="cart-total-price">0</span>  <?= $Configuration->getMoneyName() ?></button>
        <button type="button" class="btn btn-primary" id="buy-cart"><?= $Lang->get('SHOP__BUY') ?></button>
      </div>
    </div>
  </div>
</div>
<?= $this->element('payments_modal') ?>
