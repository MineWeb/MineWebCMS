<?= $this->Html->css('shop-homepage.css') ?>
<div class="push-nav"></div>
<div class="container shop">
    <div class="row">
    	<div class="ribbon">
    		<div class="ribbon-stitches-top"></div>
    		<div class="ribbon-content"><p>
    			<?php if($isConnected) { ?>
    				<span class="pull-left hidden-xs"><?= $Lang->get('SHOP__MONEY_CURRENTLY') ?> : <span class="info"><?= $money ?></span></span>
    			<?php } else { ?>
				    <span class="text-center"><?= $Lang->get('SHOP__BUY_ERROR_NEED_LOGIN') ?></span>
    			<?php } ?>
			     <?php if($isConnected AND $Permissions->can('CREDIT_ACCOUNT')) { ?>
      			<a href="#" data-toggle="modal" data-target="#addmoney" class="btn btn-primary pull-right"><?= $Lang->get('SHOP__ADD_MONEY') ?></a>
            <a href="#" data-toggle="modal" data-target="#cart-modal" class="btn btn-default pull-right"><?= $Lang->get('SHOP__BUY_CART') ?></a>
			     <?php } ?>
    		</p></div>
    		<div class="ribbon-stitches-bottom"></div>
    	</div>
			<div class="shop-content">
				<?= $vouchers->get_vouchers() // Les promotions en cours ?>
				<div role="tabpanel">

				  <ul class="nav nav-tabs" role="tablist">
					<?php $i = 0; foreach ($search_categories as $k => $v) { $i++; ?>
						<li role="presentation" class="<?php if($i == 1) { echo 'active'; } ?>"><a href="#<?= $v['Category']['id'] ?>" aria-controls="<?= $v['Category']['id'] ?>" role="tab" data-toggle="tab"><?= before_display($v['Category']['name']) ?></a></li>
                    <?php } ?>
				  </ul>

				  <div class="tab-content">
				  	<?php $i = 0; foreach ($search_categories as $k => $v) { $i++; ?>

						<div role="tabpanel" class="tab-pane<?php if($i == 1) { echo ' active'; } ?>" id="<?= $v['Category']['id'] ?>">

              <div class="row">
  							<?php
                $col = 3;
                $i = 0;
                foreach ($search_items as $key => $value) { ?>

  								<?php
                  if($value['Item']['category'] == $v['Category']['id']) {
                    $i++;
                    $newRow = ( ( $i % ( (12 / $col) +1 ) ) == 0);
                  ?>

                    <?= ($newRow) ? '</div>' : '' ?>
                    <?= ($newRow) ? '<div class="row">' : '' ?>

  									<div class="col-sm-3 col-lg-3 col-md-3">
                      <div class="thumbnail">
                      	<h4><?= before_display($value['Item']['name']) ?></h4>
                      	<div class="caption">
                          	<?php if(isset($value['Item']['img_url'])) { ?><img src="<?= $value['Item']['img_url'] ?>" alt=""><?php } ?>
                          </div>
                          <span class="info pull-left"><?= $value['Item']['price'] ?><?php if($value['Item']['price'] == 1) { echo  ' '.$singular_money; } else { echo  ' '.$plural_money; } ?></span>
                          <?php if($isConnected AND $Permissions->can('CAN_BUY')) { ?><button class="btn btn-primary btn-clear pull-right display-item" data-item-id="<?= $value['Item']['id'] ?>"><?= $Lang->get('SHOP__BUY') ?></button> <?php } ?>
                          <div class="clearfix"></div>
                      </div>
                  </div>

  								<?php } ?>

  							<?php } ?>
              </div>

						</div>

                    <?php } ?>

					<div class="clearfix"></div>

				  </div>

				</div>
			</div>
        </div>
    </div>

    <script type="text/javascript">
        function affich_item(id) {
          $('#buy').modal();
          $("#content_buy").hide().html('<div class="modal-body"><div class="alert alert-info"><?= $Lang->get('GLOBAL__LOADING') ?>...</div></div>').fadeIn('250');
          $.ajax({
            url: '<?= $this->Html->url(array('controller' => 'shop/ajax_get', 'plugin' => 'shop')); ?>/'+id,
            type : 'GET',
            dataType : 'html',
            success: function(response) {
                $("#content_buy").hide().html(response).fadeIn('250');

                $('input[id="code-voucher"]').unbind('keyup');

                $('input[id="code-voucher"]').keyup(function(e) {

                  var code = $(this).val();

                  $.get('<?= $this->Html->url(array('action' => 'checkVoucher')) ?>/'+code+'/'+id, function(data) {
                    if(data.price !== undefined) {
                      $("#content_buy").find('#total-price').html(data.price);
                    }
                  });

                });
            },
            error: function(xhr) {
                alert('ERROR');
            }
          });
        }
        function buy(id) {
          $('#buy').modal();
          var code = $('#code-voucher').val();
          $('#btn-buy').attr('disabled', true);
          $('#btn-buy').addClass('disabled');
          $.ajax({
            url: '<?= $this->Html->url(array('action' => 'buy_ajax')); ?>/'+id,
            data : { code : code },
            type : 'GET',
            dataType : 'html',
            success: function(response) {
              $('#btn-buy').attr('disabled', false);
              $('#btn-buy').removeClass('disabled');
              $("#msg_buy").hide().html(response).fadeIn('1500');
            },
            error: function(xhr) {
              $('#btn-buy').attr('disabled', false);
              $('#btn-buy').removeClass('disabled');
              alert('ERROR');
            }
          });
        }

    </script>

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
