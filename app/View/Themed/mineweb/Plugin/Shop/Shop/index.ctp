<?= $this->Html->css('shop-homepage.css') ?>
<div class="push-nav"></div>
<div class="container shop">
    <div class="row">
    	<div class="ribbon">
    		<div class="ribbon-stitches-top"></div>
    		<div class="ribbon-content"><p>
    			<?php if($isConnected) { ?>
    				<span class="pull-left hidden-xs"><?= $Lang->get('HAVE_CURRENTLY') ?> : <span class="info"><?= $money ?></span></span>
    			<?php } else { ?>
				    <span class="text-center"><?= $Lang->get('NEED_CONNECT_FOR_BUY') ?></span>
    			<?php } ?>
			     <?php if($isConnected AND $Permissions->can('CREDIT_ACCOUNT')) { ?>
      			<a href="#" data-toggle="modal" data-target="#addmoney" class="btn btn-primary pull-right"><?= $Lang->get('ADD_MONEY') ?></a>
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

							<?php foreach ($search_items as $key => $value) { ?>

								<?php if($value['Item']['category'] == $v['Category']['id']) { ?>

									<div class="col-sm-3 col-lg-3 col-md-3">
                    <div class="thumbnail">
                    	<h4><?= before_display($value['Item']['name']) ?></h4>
                    	<div class="caption">
                        	<?php if(isset($value['Item']['img_url'])) { ?><img src="<?= $value['Item']['img_url'] ?>" alt=""><?php } ?>
                        </div>
                        <span class="info pull-left"><?= $value['Item']['price'] ?><?php if($value['Item']['price'] == 1) { echo  ' '.$singular_money; } else { echo  ' '.$plural_money; } ?></span>
                        <?php if($isConnected AND $Permissions->can('CAN_BUY')) { ?><button class="btn btn-primary btn-clear pull-right" onClick="affich_item('<?= $value['Item']['id'] ?>')"><?= $Lang->get('BUY') ?></button> <?php } ?>
                        <div class="clearfix"></div>
                    </div>
                </div>

								<?php } ?>

							<?php } ?>

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
          $("#content_buy").hide().html('<div class="modal-body"><div class="alert alert-info"><?= $Lang->get('LOADING') ?>...</div></div>').fadeIn('250');
          $.ajax({
            url: '<?= $this->Html->url(array('controller' => 'shop/ajax_get', 'plugin' => 'shop')); ?>/'+id,
            type : 'GET',
            dataType : 'html',
            success: function(response) {
                $("#content_buy").hide().html(response).fadeIn('250');
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
            url: '<?= $this->Html->url(array('controller' => 'shop/buy_ajax', 'plugin' => 'shop')); ?>/'+id,
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


<div class="modal modal-medium fade" id="buy" tabindex="-1" role="dialog" aria-labelledby="buyLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $Lang->get('WANT_TO_BUY') ?></h4>
      </div>
        <div id="content_buy"></div>
    </div>
  </div>
</div>

<div class="modal fade" id="addmoney" tabindex="-1" role="dialog" aria-labelledby="addmoneyLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?= $Lang->get('CLOSE') ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $Lang->get('ADD_MONEY') ?></h4>
      </div>
      <div class="modal-body">

        <?php if($isConnected AND $Permissions->can('CREDIT_ACCOUNT')) { ?>
          <?php if(!empty($starpass_offers)) { ?>
              <a class="btn btn-info btn-block" data-toggle="collapse" href="#starpass" aria-expanded="false" aria-controls="starpass">StarPass</a>
              <br>
              <div class="collapse" id="starpass">
                  <form method="POST" action="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'starpass')) ?>" data-ajax="false">
                    <input name="data[_Token][key]" value="<?= $csrfToken ?>" type="hidden">
                    <div class="form-group col-md-8">
                      <select class="form-control" name="offer">
                        <?php foreach ($starpass_offers as $key => $value) { ?>
                          <option value="<?= $value['Starpass']['id'] ?>"><?= $value['Starpass']['money'] ?> <?= $plural_money ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= $Lang->get('SUBMIT') ?></button>
                  </form>
                  <br>
              </div>
          <?php } ?>
          <?php if(!empty($paypal_offers)) { ?>
            <a class="btn btn-info btn-block" data-toggle="collapse" href="#PayPal" aria-expanded="false" aria-controls="PayPal">PayPal</a>
            <br>
            <div class="collapse" id="PayPal">

                <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                  <input name="currency_code" type="hidden" value="EUR" />
                  <input name="shipping" type="hidden" value="0.00" />
                  <input name="tax" type="hidden" value="0.00" />
                  <input name="return" type="hidden" value="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'ipn'), true) ?>" />
                  <input name="cancel_return" type="hidden" value="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'index?error'), true) ?>" />
                  <input name="notify_url" type="hidden" value="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'ipn'), true) ?>" />
                  <input name="cmd" type="hidden" value="_xclick" />

                  <input name="business" id="mail_paypal" type="hidden" value="<?= $paypal_offers[0]['Paypal']['email'] ?>" />

                  <input name="item_name" type="hidden" value="Des <?= $plural_money ?> sur <?= $website_name ?>" />
                  <input name="no_note" type="hidden" value="1" />
                  <input name="lc" type="hidden" value="FR" />
                  <input name="custom" type="hidden" value="<?= $user['pseudo'] ?>">
                  <input name="bn" type="hidden" value="PP-BuyNowBF" />
                  <div class="form-group col-md-8">
                    <select class="form-control" onchange="{if(this.options[this.selectedIndex].onclick != null){this.options[this.selectedIndex].onclick(this);}}" name="amount" id="amount">
                      <?php foreach ($paypal_offers as $key => $value) { ?>
                        <option onClick="$('#mail_paypal').val('<?= $value['Paypal']['email'] ?>')" value="<?= $value['Paypal']['price'] ?>"><?= (isset(explode('.', $value['Paypal']['money'])[1]) && explode('.', $value['Paypal']['money'])[1] == '00') ? explode('.', $value['Paypal']['money'])[0] : $value['Paypal']['money'] ?> <?= $plural_money ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <button type="submit" class="btn btn-primary"><?= $Lang->get('SUBMIT') ?></button>
                </form>
                <br>
            </div>
          <?php } ?>
          <?php if($paysafecard_enabled) { ?>
            <a class="btn btn-info btn-block" data-toggle="collapse" href="#PaySafeCard" aria-expanded="false" aria-controls="PaySafeCard">PaySafeCard</a>
            <br>
            <div class="collapse" id="PaySafeCard">
              <form class="form-inline" data-ajax="true" action="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'paysafecard')) ?>">
                <div class="ajax-msg"></div>
                <div class="form-group" style="margin-right:20px;">
                  <label class="sr-only"><?= $Lang->get('AMOUNT') ?></label>
                  <div class="input-group">
                    <input type="text" class="form-control" name="amount" placeholder="XXX" data-type="numbers" maxlength="3" tabindex="1" style="width:60px;">
                    <div class="input-group-addon">€</div>
                  </div>
                </div>
                <div class="form-group">
                  <input type="text" class="form-control" name="code1" placeholder="XXXX" data-type="numbers" tabindex="2" maxlength="4" style="width:60px;">
                </div>
                -
                <div class="form-group">
                  <input type="text" class="form-control" name="code2" placeholder="XXXX" data-type="numbers" tabindex="3" maxlength="4" style="width:60px;">
                </div>
                -
                <div class="form-group">
                  <input type="text" class="form-control" name="code3" placeholder="XXXX" data-type="numbers" tabindex="4" maxlength="4" style="width:60px;">
                </div>
                -
                <div class="form-group">
                  <input type="text" class="form-control" name="code4" placeholder="XXXX" data-type="numbers" maxlength="4" tabindex="5" style="width:60px;">
                </div>
                <button type="submit" class="btn btn-default pull-right"><?= $Lang->get('SUBMIT') ?></button>
              </form>
            </div>
          <?php } ?>
        <?php } else { ?>
            <p><?= $Lang->get('NEED_CONNECT') ?></p>
        <?php } ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= $Lang->get('CLOSE') ?></button>
        </form>
      </div>
    </div>
  </div>
</div>
