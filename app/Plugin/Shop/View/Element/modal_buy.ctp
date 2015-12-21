<div class="modal-body">
  <div id="msg_buy"></div>
  <p><b>{LANG-NAME} :</b> {ITEM_NAME}</p>
  <p><b>{LANG-DESCRIPTION} :</b> {ITEM_DESCRIPTION}</p>
  [IF AFFICH_SERVER]
    <p><b>{LANG-SERVER} :</b> {ITEM_SERVERS}</p>
  [/IF AFFICH_SERVER]
  <p><b>{LANG-PRICE} :</b> {ITEM_PRICE} {SITE_MONEY}</p>
  <p><input name="code" type="text" class="form-control" id="code-voucher" style="width:245px;" placeholder="{LANG-HAVE_YOU_VOUCHER}"></p>
</div>
  <div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">{LANG-CLOSE}</button>
  <button type="button" class="btn btn-primary" onClick="buy('{ITEM_ID}')" id="btn-buy">{LANG-BUY}</button>
</div>
