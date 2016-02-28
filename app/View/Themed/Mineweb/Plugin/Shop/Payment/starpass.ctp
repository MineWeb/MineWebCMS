<div class="push-nav"></div>
<div class="container page">
    <div class="row">
        <div class="page-content">
            <h1 class="title"><?= $Lang->get('SHOP__STARPASS_PAYMENT') ?></h1>
            <p>1 code = <?= $money ?> <?= $Configuration->getMoneyName() ?></p>
           <div id="starpass_<?= $idd ?>"></div>
            <script type="text/javascript" src="http://script.starpass.fr/script.php?idd=<?= $idd ?>&amp;verif_en_php=1&amp;datas=<?= $id ?>"></script>
            <noscript>Veuillez activer le Javascript de votre navigateur s'il vous pla&icirc;t.<br />
              <a href="http://www.starpass.fr/">Micro Paiement StarPass</a>
            </noscript>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="push-nav"></div>
