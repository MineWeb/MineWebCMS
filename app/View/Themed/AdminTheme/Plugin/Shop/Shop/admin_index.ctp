<?php
 
$this->Connect = new ConnectComponent;
$this->Configuration = new ConfigurationComponent;
$this->History = new HistoryComponent;
App::import('Component', 'Shop.DiscountVoucherComponent');
$this->DiscountVoucher = new DiscountVoucherComponent;
?>
<div class="row-fluid">
	<?= $this->Session->Flash(); ?>
	<button class="btn pull-left btn-info" href="#" data-toggle="modal" data-target="#manage_vouchers"><?= $Lang->get('MANAGE_VOUCHERS') ?></button>
	<button class="btn pull-right btn-info" id="show"><?= $Lang->get('SHOW_CREDIT') ?></button>
	<button class="btn pull-right btn-info" id="hide" style="display:none;"><?= $Lang->get('HIDE_CREDIT') ?></button>
	<br><br>
</div>
<div id="1">
	<div class="row-fluid">
		<div class="span6">
			<div class="top-bar">
				<h3><i class="icon-calendar"></i> <?= $Lang->get('ITEMS_ON_SALE') ?> &nbsp;&nbsp;<a href="<?php if(!empty($search_categories)) { ?><?= $this->Html->url(array('controller' => 'shop', 'action' => 'add_item', 'admin' => true)) ?><?php } ?>" class="btn btn-success<?php if(empty($search_categories)) { echo ' disabled'; } ?>"><?= $Lang->get('ADD') ?></a></h3>
			</div>

			<div class="well no-padding">

				<table class="data-table">
					<thead>
						<tr>
							<th><?= $Lang->get('NAME') ?></th>
							<th><?= $Lang->get('PRICE') ?></th>
							<th><?= $Lang->get('CATEGORY') ?></th>
							<th class="right"><?= $Lang->get('ACTIONS') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($search_items as $value => $v) { ?>
							<tr>
								<td><?= $v["Item"]["name"] ?></td>
								<td><?= $v["Item"]["price"] ?> <?= $this->Configuration->get_money_name() ?></td>
								<td><?= $categories[$v["Item"]["category"]]['name'] ?></td>
								<td class="right">
									<a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'edit/'.$v["Item"]["id"], 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('EDIT') ?></button>
									<a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'shop', 'action' => 'delete/item/'.$v["Item"]["id"], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('DELETE') ?></button>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>

			</div>
		</div>
		<div class="span6">
			<div class="top-bar">
				<h3><i class="icon-calendar"></i> <?= $Lang->get('CATEGORIES') ?> &nbsp;&nbsp;<a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'add_category', 'admin' => true)) ?>" class="btn btn-success"><?= $Lang->get('ADD') ?></a></h3>
			</div>

			<div class="well no-padding">

				<table class="data-table">
					<thead>
						<tr>
							<th><?= $Lang->get('NAME') ?></th>
							<th class="right"><?= $Lang->get('ACTIONS') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($search_categories as $value => $v) { ?>
							<tr>
								<td><?= $v["Category"]["name"] ?></td>
								<td class="right">
									<a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'shop', 'action' => 'delete/category/'.$v["Category"]["id"], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('DELETE') ?></button>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span6">
			<div class="top-bar">
				<h3><i class="icon-calendar"></i> <?= $Lang->get('PURCHASE_HISTORY') ?></h3>
			</div>

			<div class="well no-padding">

				<table class="data-table">
					<thead>
						<tr>
							<th><?= $Lang->get('ITEM') ?></th>
							<th>Pseudo</th>
							<th><?= $Lang->get('CREATED') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($this->History->get('SHOP', false, false, 'BUY_ITEM') as $value => $v) { ?>
							<tr>
								<td><?= $v['History']['other'] ?></td>
								<td><?= $v['History']['author'] ?></td>
								<td><?= $Lang->date($v['History']['created']) ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>

			</div>
		</div>
		<div class="span6">
			<div class="top-bar">
				<h3><i class="icon-calendar"></i> <?= $Lang->get('PURCHASE_HISTORY') ?> <?= $Lang->get('OF') ?> <?= $this->Configuration->get_money_name() ?></h3>
			</div>

			<div class="well no-padding">

				<table class="data-table">
					<thead>
						<tr>
							<th>Pseudo</th>
							<th><?= $Lang->get('TYPE') ?></th>
							<th><?= ucfirst($this->Configuration->get_money_name()) ?></th>
							<th><?= $Lang->get('CREATED') ?></th>
							<th><?= $Lang->get('ID_PAYPAL') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($this->History->get('SHOP', false, false, 'BUY_MONEY') as $value => $v) { ?>
						<?php
						$other = explode('|', $v['History']['other']);
						$type = $other[0];
						$money = $other[1];
						if($type == "paypal") {
							$id = $other[2];
						} else {
							$id = $Lang->get('UNDEFINED');
						}
						?>
							<tr>
								<td><?= $v['History']['author'] ?></td>
								<td><?= ucfirst($type) ?></td>
								<td><?= $money ?></td>
								<td><?= $Lang->date($v['History']['created']) ?></td>
								<td><?= $id ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div id="2" style="display:none;">
	<div class="row-fluid">
		<div class="span6">
			<div class="top-bar">
				<h3><?= $Lang->get('PAYSAFECARD') ?>&nbsp;&nbsp;<a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'toggle_paysafecard', 'admin' => true)) ?>" class="btn btn-success"><?php if($paysafecard_enabled) { echo $Lang->get('DISABLE'); } else { echo $Lang->get('ENABLE'); } ?></a></h3>
			</div>

			<div class="well no-padding">

				<table class="data-table">
					<thead>
						<tr>
							<th><?= $Lang->get('PSEUDO') ?></th>
							<th><?= $Lang->get('AMOUNT') ?></th>
							<th><?= $Lang->get('CODE') ?></th>
							<th><?= $Lang->get('CREATED') ?></th>
							<th class="right"><?= $Lang->get('ACTIONS') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if(!empty($psc)) { ?>
							<?php foreach ($psc as $key => $value) { ?>
								<tr>
									<td><?= $value['Paysafecard']['author'] ?></td>
									<td><?= $value['Paysafecard']['amount'] ?></td>
									<td><?= $value['Paysafecard']['code'] ?></td>
									<td><?= $Lang->date($value['Paysafecard']['created']) ?></td>
									<td>
										<a href="#" onClick="howmuch(<?= $value['Paysafecard']['id'] ?>)" class="btn btn-success"><?= $Lang->get('VALID') ?></a>
										<a href="<?= $this->Html->url(array('controller' => 'Shop', 'action' => 'paysafecard_invalid/'.$value['Paysafecard']['id'])) ?>" class="btn btn-danger"><?= $Lang->get('INVALID') ?></a>
									</td>
								</tr>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
		<script>
		function howmuch(id) {
		    var money = prompt("<?= $Lang->get('HOW_MUCH_MONEY_GIVE') ?>");
		    
		    if (money != null) {
		        document.location = '<?= $this->Html->url(array('controller' => 'Shop', 'action' => 'paysafecard_valid/')) ?>/'+id+'/'+money;
		    } else {
		    	return false;
		    }
		}
		</script>
		<div class="span6">
			<div class="top-bar">
				<h3><?= $Lang->get('PAYPAL_OFFERS') ?> &nbsp;&nbsp;<a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'add_paypal', 'admin' => true)) ?>" class="btn btn-success"><?= $Lang->get('ADD') ?></a></h3>
			</div>

			<div class="well no-padding">

				<table class="data-table">
					<thead>
						<tr>
							<th><?= $Lang->get('NAME') ?></th>
							<th><?= $Lang->get('MAIL') ?></th>
							<th><?= $Lang->get('PRICE') ?></th>
							<th><?= ucfirst($this->Configuration->get_money_name()) ?></th>
							<th><?= $Lang->get('CREATED') ?></th>
							<th class="right"><?= $Lang->get('ACTIONS') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if(!empty($paypal_offers)) { ?>
							<?php foreach ($paypal_offers as $key => $value) { ?>
								<tr>
									<td><?= $value['Paypal']['name'] ?></td>
									<td><?= $value['Paypal']['email'] ?></td>
									<td><?= $value['Paypal']['price'] ?></td>
									<td><?= $value['Paypal']['money'] ?></td>
									<td><?= $Lang->date($value['Paypal']['created']) ?></td>
									<td class="right">
										<a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'edit_paypal/'.$value["Paypal"]["id"], 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('EDIT') ?></button>
										<a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'shop', 'action' => 'delete/paypal/'.$value["Paypal"]["id"], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('DELETE') ?></button>
									</td>
								</tr>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span6">
			<div class="top-bar">
				<h3><i class="icon-calendar"></i> <?= $Lang->get('STARPASS_OFFERS') ?> &nbsp;&nbsp;<a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'add_starpass', 'admin' => true)) ?>" class="btn btn-success"><?= $Lang->get('ADD') ?></a></h3>
			</div>

			<div class="well no-padding">

				<table class="data-table">
					<thead>
						<tr>
							<th><?= $Lang->get('NAME') ?></th>
							<th><?= ucfirst($this->Configuration->get_money_name()) ?></th>
							<th><?= $Lang->get('CREATED') ?></th>
							<th><?= $Lang->get('ACTIONS') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if(!empty($starpass_offers)) { ?>
							<?php foreach ($starpass_offers as $key => $value) { ?>
								<tr>
									<td><?= $value['Starpass']['name'] ?></td>
									<td><?= $value['Starpass']['money'] ?></td>
									<td><?= $Lang->date($value['Starpass']['created']) ?></td>
									<td class="right">
										<a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'edit_starpass/'.$value["Starpass"]["id"], 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('EDIT') ?></button>
										<a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'shop', 'action' => 'delete/starpass/'.$value["Starpass"]["id"], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('DELETE') ?></button>
									</td>
								</tr>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table>

			</div>
		</div>
		<div class="span6">
			<div class="top-bar">
				<h3><i class="icon-calendar"></i> <?= $Lang->get('SEND_MONEY_HISTORY') ?> <?= $Lang->get('OF') ?> <?= $this->Configuration->get_money_name() ?></h3>
			</div>

			<div class="well no-padding">

				<table class="data-table">
					<thead>
						<tr>
							<th>Pseudo</th>
							<th><?= ucfirst($this->Configuration->get_money_name()) ?></th>
							<th><?= $Lang->get('TO') ?></th>
							<th><?= $Lang->get('CREATED') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($this->History->get('SHOP', false, false, 'SEND_MONEY') as $value => $v) { ?>
						<?php
						$other = explode('|', $v['History']['other']);
						?>
							<tr>
								<td><?= $v['History']['author'] ?></td>
								<td><?= $other[1] ?></td>
								<td><?= $other[0] ?></td>
								<td><?= $Lang->date($v['History']['created']) ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script>
$( "#show" ).click(function() {
  $('#1').slideUp(1000).hide("slide", { direction: "right" }, 1200);
  $('#2').slideDown(1000).show();
  $('#show').hide();
  $('#hide').show();
});
$( "#hide" ).click(function() {
  $('#2').slideUp(1000).hide("slide", { direction: "right" }, 1200);
  $('#1').slideDown(1000).show();
  $('#hide').hide();
  $('#show').show();
});
function confirmDel(url) {
  if (confirm("<?= $Lang->get('CONFIRM_WANT_DELETE') ?>"))
    window.location.href=''+url+'';
  else
    return false;
}
</script>
<div class="modal fade" id="manage_vouchers" tabindex="-1" role="dialog" aria-labelledby="manage_vouchersLabel" aria-hidden="true" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?= $Lang->get('MANAGE_VOUCHERS') ?></h4>
      </div>
      <div class="modal-body">
      	<a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'add_voucher', 'admin' => true)) ?>" class="btn btn-success btn-block"><?= $Lang->get('ADD_VOUCHER') ?></a><br><br>
        <table class="table">
          <thead>
            <tr>
              <th><?= $Lang->get('CODE') ?></th>
              <th><?= $Lang->get('END_DATE') ?></th>
              <th><?= $Lang->get('START_DATE') ?></th>
              <th><?= $Lang->get('AFFICH') ?></th>
              <th><?= $Lang->get('ACTION') ?></th>
            </tr>
          </thead>
          <tbody>
          	<?php foreach ($vouchers as $key => $value) { ?>
	            <tr>
	              <td><?= $value['Voucher']['code'] ?></td>
	              <td><?= $Lang->date($value['Voucher']['end_date']) ?></td>
	              <td><?= $Lang->date($value['Voucher']['created']) ?></td>
	              <td>
	              	<?php 
	              		if($value['Voucher']['affich'] == 1) {
	              			echo $Lang->get('YES');
	              		} else {
	              			echo $Lang->get('NO');
	              		}
	              	?>
	          	  </td>
	          	  <td>
					<a href="<?= $this->Html->url(array('controller' => 'Shop', 'admin' => true, 'action' => 'delete_voucher/'.$value['Voucher']['id'])) ?>" class="btn btn-danger"><?= $Lang->get('DELETE') ?></a>
	          	  </td>
	            </tr>
	        <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= $Lang->get('CANCEL') ?></button>
      </div>
    </div>
  </div>
</div>