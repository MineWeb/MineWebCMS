<div class="push-nav"></div>
<div class="container page">
  <div class="row">
    <div class="page-content">
      <h1 class="title"><?= $Lang->get('SHOP__DEDIPASS_PAYMENT') ?></h1>
      <div data-dedipass="<?= $dedipassPublicKey ?>">
        <div class="alert alert-info"><?= $Lang->get('GLOBAL__LOADING') ?>...</div>
      </div>
      <script src="//api.dedipass.com/v1/pay.js"></script>
      <div class="clearfix"></div>
    </div>
  </div>
</div>
<div class="push-nav"></div>
