<div class="modal-body">
  <div id="msg_buy"></div>
  <p><b>{LANG-SHOP__ITEM_NAME} :</b> {ITEM_NAME}</p>
  <p><b>{LANG-SHOP__ITEM_DESCRIPTION} :</b> {ITEM_DESCRIPTION}</p>
  [IF AFFICH_SERVER]
    <p><b>{LANG-SERVER__TITLE} :</b> {ITEM_SERVERS}</p>
  [/IF AFFICH_SERVER]
  <p><b>{LANG-SHOP__ITEM_PRICE} :</b> {ITEM_PRICE} {SITE_MONEY}</p>
  <p><input name="code" type="text" class="form-control" id="code-voucher" style="width:245px;" placeholder="{LANG-SHOP__BUY_VOUCHER_ASK}"></p>
</div>
  <div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">{LANG-GLOBAL__CLOSE}</button>
  <button type="button" class="btn btn-primary" onClick="buy('{ITEM_ID}')" id="btn-buy">{LANG-SHOP__BUY}</button>
</div>
