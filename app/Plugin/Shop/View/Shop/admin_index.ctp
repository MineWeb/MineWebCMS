<section class="content">
  <div class="row">
    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('SHOP__CONFIG') ?></h3>
        </div>
        <div class="box-body">
          <form action="<?= $this->Html->url(array('action' => 'config_items')) ?>" method="post" data-ajax="true">

            <div class="form-group">
              <label><?= $Lang->get('SHOP__CONFIG_BROADCAST_GLOBAL') ?></label>
              <input name="broadcast_global" class="form-control" type="text"<?= (isset($config['broadcast_global'])) ? ' value="'.$config['broadcast_global'].'"' : '' ?>>
            </div>

            <div class="form-group">
              <div class="checkbox">
                <input name="disabled-sort_by_server" type="checkbox"<?= (isset($config['sort_by_server']) && $config['sort_by_server']) ? ' checked=""' : '' ?> disabled>
                <label><?= $Lang->get('SHOP__CONFIG_SORT_BY_SERVER') ?></label>
              </div>
              <small><?= $Lang->get('GLOBAL__TEMPORALY_DISABLED') ?></small>
            </div>

            <div class="form-group">
              <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
            </div>

          </form>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title"><?= $Lang->get('SHOP__CONFIG_EXPLAIN_TITLE') ?></h3>
        </div>
        <div class="box-body">
          <blockquote>
            <p><?= $Lang->get('SHOP__CONFIG_EXPLAIN') ?></p>
          </blockquote>
          <p><b><?= $Lang->get('SHOP__CONFIG_VARIABLES') ?> : </b></p>
          <p><em>{ITEM_NAME}</em> : <?= $Lang->get('SHOP__CONFIG_VARIABLE_ITEM') ?></p>
          <p><em>{QUANTITY}</em> : <?= $Lang->get('SHOP__CONFIG_VARIABLE_QUANTITY') ?></p>
          <p><em>{PLAYER}</em> : <?= $Lang->get('SHOP__CONFIG_VARIABLE_PLAYER') ?></p>
        </div>
      </div>
    </div>
  </div>
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><?= $Lang->get('SHOP__ITEMS_AVAILABLE') ?> &nbsp;&nbsp;<a href="<?php if(!empty($search_categories)) { ?><?= $this->Html->url(array('controller' => 'shop', 'action' => 'add_item', 'admin' => true)) ?><?php } ?>" class="btn btn-success<?php if(empty($search_categories)) { echo ' disabled'; } ?>"><?= $Lang->get('GLOBAL__ADD') ?></a></h3>
          </div>
          <div class="box-body">

            <table class="table table-bordered dataTable">
              <thead>
                <tr>
                  <th><?= $Lang->get('GLOBAL__NAME') ?></th>
                  <th><?= $Lang->get('SHOP__ITEM_PRICE') ?></th>
                  <th><?= $Lang->get('SHOP__CATEGORY') ?></th>
                  <th class="right"><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($search_items as $value => $v) { ?>
                  <tr>
                    <td><?= $v["Item"]["name"] ?></td>
                    <td><?= $v["Item"]["price"] ?> <?= $Configuration->getMoneyName() ?></td>
                    <td><?= $categories[$v["Item"]["category"]]['name'] ?></td>
                    <td class="right">
                      <a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'edit/'.$v["Item"]["id"], 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('GLOBAL__EDIT') ?></a>
                      <a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'shop', 'action' => 'delete/item/'.$v["Item"]["id"], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('GLOBAL__DELETE') ?></a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>

          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><?= $Lang->get('SHOP__CATEGORIES') ?> &nbsp;&nbsp;<a href="<?= $this->Html->url(array('controller' => 'shop', 'action' => 'add_category', 'admin' => true)) ?>" class="btn btn-success"><?= $Lang->get('GLOBAL__ADD') ?></a></h3>
          </div>
          <div class="box-body">

            <table class="table table-bordered dataTable">
              <thead>
                <tr>
                  <th><?= $Lang->get('GLOBAL__NAME') ?></th>
                  <th class="right"><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($search_categories as $value => $v) { ?>
                  <tr>
                    <td><?= $v["Category"]["name"] ?></td>
                    <td class="right">
                      <a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'shop', 'action' => 'delete/category/'.$v["Category"]["id"], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('GLOBAL__DELETE') ?></button>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>

          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><?= $Lang->get('SHOP__HISTORY_PURCHASES') ?></h3>
          </div>
          <div class="box-body">

            <table class="table table-bordered dataTable">
              <thead>
                <tr>
                  <th><?= $Lang->get('SHOP__ITEM') ?></th>
                  <th>Pseudo</th>
                  <th><?= $Lang->get('GLOBAL__CREATED') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($histories_buy as $value => $v) { ?>
                  <tr>
                    <td><?= (isset($items[$v['ItemsBuyHistory']['item_id']])) ? $items[$v['ItemsBuyHistory']['item_id']] : $items[$v['ItemsBuyHistory']['item_id']] ?></td>
                    <td><?= (isset($users[$v['ItemsBuyHistory']['user_id']])) ? $users[$v['ItemsBuyHistory']['user_id']] : $users[$v['ItemsBuyHistory']['user_id']] ?></td>
                    <td><?= $Lang->date($v['ItemsBuyHistory']['created']) ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
