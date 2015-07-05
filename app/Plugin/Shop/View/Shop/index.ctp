<?php 
  
App::import('Component', 'ConnectComponent');
$this->Connect = new ConnectComponent;
App::import('Component', 'ConfigurationComponent');
$this->Configuration = new ConfigurationComponent;
App::import('Component', 'Shop.DiscountVoucherComponent');
$this->DiscountVoucher = new DiscountVoucherComponent;
?>    
    <?= $this->Html->css('shop-homepage.css') ?>
    <div class="container">
        <div class="row">

            <div class="col-md-3">
                <p class="lead">
                    <?php
                    if($this->Connect->connect()) { 
                        echo $this->Connect->get('money') . ' ';
                        if($this->Connect->get('money') == 1 OR $this->Connect->get('money') == 0) { echo  $this->Configuration->get_money_name(false, true); } else { echo  $this->Configuration->get_money_name(); }
                    } else {
                        echo $Lang->get('SHOP');
                    }
                    ?>
                </p>
                <div class="list-group">
                    <?php $i = 0; foreach ($search_categories as $k => $v) { $i++; ?>
                        <a href="<?= $this->Html->url(array('controller' => 'c/'.$v['Category']['id'], 'plugin' => 'shop')) ?>" class="list-group-item<?php if(isset($category) AND $v['Category']['id'] == $category OR !isset($category) AND $i == 1) { echo ' active'; } ?>"><?= before_display($v['Category']['name']) ?></a>
                    <?php } ?>
                </div>
                <?php if($this->Connect->connect() AND $Permissions->can('CREDIT_ACCOUNT')) { ?>
                    <a href="#" data-toggle="modal" data-target="#addmoney" class="btn btn-success btn-block pull-right"><?= $Lang->get('ADD_MONEY') ?></a>
                <?php } ?>
            </div>

            <div class="col-md-9">

                <div class="row">

                    <?= $this->DiscountVoucher->get_vouchers() // Les promotions en cours ?>
                    
                    <?php foreach ($search_items as $k => $v) { ?>
                        <?php if(!isset($category) AND $v['Item']['category'] == $search_first_category OR isset($category) AND $v['Item']['category'] == $category) { ?>
                            <div class="col-sm-4 col-lg-4 col-md-4">
                                <div class="thumbnail">
                                    <?php if(isset($v['Item']['img_url'])) { ?><img src="<?= $v['Item']['img_url'] ?>" alt=""><?php } ?>
                                    <div class="caption">
                                        <h4 class="pull-right"><?= $v['Item']['price'] ?><?php if($v['Item']['price'] == 1) { echo  ' '.$this->Configuration->get_money_name(false, true); } else { echo  ' '.$this->Configuration->get_money_name(); } ?></h4>
                                        <h4><a href="#"><?= before_display($v['Item']['name']) ?></a>
                                        </h4>
                                        <p><?= substr(before_display($v['Item']['description']), 0, 140); ?><?php if(strlen($v['Item']['description']) > "140") { echo '...'; } ?></p>
                                        <?php if($this->Connect->connect() AND $Permissions->can('CAN_BUY')) { ?><button class="btn btn-success pull-right" onClick="affich_item('<?= $v['Item']['id'] ?>')"><?= $Lang->get('BUY') ?></button> <?php } ?>
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
        function affich_item(id) {
          $('#buy').modal();
          $.ajax({
            url: '<?= $this->Html->url(array('controller' => 'shop/ajax_get', 'plugin' => 'shop')); ?>/'+id,
            type : 'GET',
            dataType : 'html',
            success: function(response) {
                $("#content_buy").html(response);
            },
            error: function(xhr) {
                alert('ERROR');
            }
          });
        }
        function buy(id) {
          $('#buy').modal();
          var code = $('#code-voucher').val();
          $.ajax({
            url: '<?= $this->Html->url(array('controller' => 'shop/buy_ajax', 'plugin' => 'shop')); ?>/'+id,
            data : { code : code },
            type : 'GET',
            dataType : 'html',
            success: function(response) {
                $("#msg_buy").hide().html(response).fadeIn('1500');
            },
            error: function(xhr) {
                alert('ERROR');
            }
          });
        }
    </script>


<div class="modal fade" id="buy" tabindex="-1" role="dialog" aria-labelledby="buyLabel" aria-hidden="true">
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
    
        <?php if($this->Connect->connect() AND $Permissions->can('CREDIT_ACCOUNT')) { ?>
          <?php if(!empty($starpass_offers)) { ?>
            <a class="btn btn-info btn-block" data-toggle="collapse" href="#starpass" aria-expanded="false" aria-controls="starpass">StarPass</a>
            <br>
            <div class="collapse" id="starpass">
                <form method="POST" action="<?= $this->Html->url(array('contoller' => 'shop', 'action' => 'starpass')) ?>">
                  <div class="form-group col-md-8">
                    <select class="form-control" name="offer">
                      <?php foreach ($starpass_offers as $key => $value) { ?>
                        <option value="<?= $value['Starpass']['id'] ?>"><?= $value['Starpass']['money'] ?> <?= $this->Configuration->get_money_name() ?></option>
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
                  <input name="return" type="hidden" value="<?= "http://".$_SERVER['HTTP_HOST'].$this->Html->url(array('controller' => 'shop', 'action' => 'ipn')) ?>" />
                  <input name="cancel_return" type="hidden" value="<?= "http://".$_SERVER['HTTP_HOST'].$this->Html->url(array('controller' => 'shop', 'action' => 'index?error')) ?>" />
                  <input name="notify_url" type="hidden" value="<?= "http://".$_SERVER['HTTP_HOST'].$this->Html->url(array('controller' => 'shop', 'action' => 'ipn')) ?>" />
                  <input name="cmd" type="hidden" value="_xclick" />
                  
                  <input name="business" id="mail_paypal" type="hidden" value="<?= $paypal_offers[0]['Paypal']['email'] ?>" />
                  
                  <input name="item_name" type="hidden" value="Des <?= $this->Configuration->get_money_name() ?> sur <?= $this->Configuration->get('name') ?>" />
                  <input name="no_note" type="hidden" value="1" />
                  <input name="lc" type="hidden" value="FR" />
                  <input name="custom" type="hidden" value="<?= $this->Connect->get_pseudo() ?>">
                  <input name="bn" type="hidden" value="PP-BuyNowBF" />
                  <div class="form-group col-md-8">
                    <select class="form-control" onchange="{if(this.options[this.selectedIndex].onclick != null){this.options[this.selectedIndex].onclick(this);}}" name="amount" id="amount">
                      <?php foreach ($paypal_offers as $key => $value) { ?>
                        <option onClick="$('#mail_paypal').val('<?= $value['Paypal']['email'] ?>')" value="<?= $value['Paypal']['price'] ?>"><?= $value['Paypal']['money'] ?> <?= $this->Configuration->get_money_name() ?></option>
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
              <form class="form-inline" id="add-psc">
                <div class="ajax-msg"></div>
                <div class="form-group" style="margin-right:20px;">
                  <label class="sr-only"><?= $Lang->get('AMOUNT') ?></label>
                  <div class="input-group">
                    <input type="text" class="form-control" name="amount" placeholder="100" maxlength="3" tabindex="1" onkeyup="Autotab(2, this.size, this.value)" style="width:60px;">
                    <div class="input-group-addon">€</div>
                  </div>
                </div>
                <div class="form-group">
                  <input type="text" class="form-control" name="code1" placeholder="XXXX" tabindex="2" onkeyup="Autotab(3, this.size, this.value)" maxlength="4" style="width:60px;">
                </div>
                -
                <div class="form-group">
                  <input type="text" class="form-control" name="code2" placeholder="XXXX" tabindex="3" onkeyup="Autotab(4, this.size, this.value)" maxlength="4" style="width:60px;">
                </div>
                -
                <div class="form-group">
                  <input type="text" class="form-control" name="code3" placeholder="XXXX" tabindex="4" onkeyup="Autotab(5, this.size, this.value)" maxlength="4" style="width:60px;">
                </div>
                -
                <div class="form-group">
                  <input type="text" class="form-control" name="code4" placeholder="XXXX" maxlength="4" tabindex="5" style="width:60px;">
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
<script>
$("#add-psc").submit(function( event ) {
    var $form = $( this );
    var amount = $form.find("input[name='amount']").val();
    var code1 = $form.find("input[name='code1']").val();
    var code2 = $form.find("input[name='code2']").val();
    var code3 = $form.find("input[name='code3']").val();
    var code4 = $form.find("input[name='code4']").val();
    $.post("<?= $this->Html->url(array('controller' => 'shop', 'action' => 'paysafecard')) ?>", { amount : amount, code1 : code1, code2 : code2, code3 : code3, code4 : code4 }, function(data) {
        data2 = data.split("|");
        if(data.indexOf('true') != -1) {
              $('.ajax-msg').empty().html('<div class="alert alert-success" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-exclamation"></i> <b><?= $Lang->get('SUCCESS') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
            } else if(data.indexOf('false') != -1) {
              $('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> '+data2[0]+'</i></div>').fadeIn(500);
          } else {
          $('.ajax-msg').empty().html('<div class="alert alert-danger" style="margin-top:10px;margin-right:10px;margin-left:10px;"><a class="close" data-dismiss="alert">×</a><i class="icon icon-warning-sign"></i> <b><?= $Lang->get('ERROR') ?> :</b> <?= $Lang->get('ERROR_WHEN_AJAX') ?></i></div>');
        }
    });
    return false;
});

function Autotab(box, longueur, texte)
{
    if (texte.length > longueur-1) 
    {
        document.getElementById('TB'+box).focus();
    }
}
</script>