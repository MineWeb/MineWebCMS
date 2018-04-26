<style type="text/css">
	.container-market {
		background-color: white;
		padding: 10px;
		border: 1px solid #dddddd;
		border-top: 0px;
	}

	.title-market {
		text-transform: uppercase;
		display: inline-block;
		border-bottom: 2px solid #3c8dbc;
	}

	#Contact-infos-theme {
		display: none;
	}

	#Contact-infos-plugin {
		display: none;
	}
</style>
<section class="content">
	<div class="clearfix">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item active" style="width:50%"><a class="text-center nav-link active"
																	 data-toggle="tab" href="#plugins" role="tab"
																	 aria-expanded="false">Plugins</a></li>
					<li class="nav-item" style="width:50%"><a class="text-center nav-link active" data-toggle="tab"
															  href="#themes" role="tab" aria-expanded="true">Thèmes</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="tab-content">
			<div class="tab-pane fade active in" id="plugins" role="tabpanel" aria-expanded="false">
				<div class="row container-market">
					<div class="col-md-12">
						<div class="row">
<?php
$marketMineweb = json_decode(file_get_contents("https://raw.githubusercontent.com/MineWeb/mineweb.org/gh-pages/market/market.json"));
$marketlist    = $marketMineweb->market;
$plugin        = $marketlist->plugin;
foreach ($plugin as $p) {
    $type   = $p->type;
    $desc   = $p->desc;
    $img    = $p->img;
    $name   = $p->name;
    $author = $p->author;
    $ver    = $p->version;
    switch ($type) {
        case "payant":
            $contacts = $p->contact;
            $button   = "info";
            $infos    = 'Contact';
            $lien     = '';
            $prix     = $p->prix . '   €';
            break;
        case "gratuit":
            $button = "primary";
            $lien   = 'href="' . $p->lienDown . '"';
            $infos  = "Download";
            $prix   = "Gratuit";
            break;
    }
?>
							<div class="col-md-4 text-center" style="margin-top:30px;"><h3
									class="title-market"><?=$name?></h3>
								<div class="img-container"><img class="img-rounded" style="max-width: 100%;" src="<?=$img?>">
								</div>
								<p class="lead"><?= substr($desc, 0, 240)?> ...</p>
								<p><span class="pull-left">Prix :&nbsp;<b><?=$prix?></b></b></span><span
										class="pull-right" style="margin: 10px;">v<?=$ver?></span><br><span
										class="pull-left">Auteur : <b><?=$author?></b></span></p>
								<div class="clearfix"></div>
								<p></p>
								<a class="btn btn-<?=$button?> btn-block" id="Contact-theme" <?=$lien?>>
								<i class="fa fa-plus">
								</i>&nbsp;<?=$infos?></a>
								<div id="Contact-infos-theme">
									<span class="pull-middle">
<?php
    foreach ($contacts as $c) {
        $contact_name = $c->name;
        $contact_desc = $c->desc;
        
        echo '' . $contact_name . ' :&nbsp;<b>' . $contact_desc . '</b><br/>';
    }
?>
									</span>
								</div>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="themes" role="tabpanel" aria-expanded="true">
				<div class="row container-market">
					<div class="col-md-12">
						<div class="row">
<?php
$marketMineweb = json_decode(file_get_contents("https://raw.githubusercontent.com/MineWeb/mineweb.org/gh-pages/market/market.json"));
$marketlist    = $marketMineweb->market;
$theme         = $marketlist->theme;
foreach ($theme as $t) {
    $type   = $t->type;
    $desc   = $t->desc;
    $img    = $t->img;
    $name   = $t->name;
    $author = $t->author;
    $ver    = $t->version;
    switch ($type) {
        case "payant":
            $contacts = $t->contact;
            $button   = "info";
            $infos    = 'Contact';
            $lien     = '';
            $prix     = $t->prix . '   €';
            break;
        case "gratuit":
            $button = "primary";
            $lien   = 'href="' . $t->lienDown . '"';
            $infos  = "Download";
            $prix   = "Gratuit";
            break;
    }
?>
							<div class="col-md-4 text-center" style="margin-top:30px;"><h3
									class="title-market"><?=$name?></h3>
								<div class="img-container"><img class="img-rounded" style="max-width: 100%;" src="<?=$img?>">
								</div>
								<p class="lead"><?=substr($desc, 0, 240)?> ...</p>
								<p><span class="pull-left">Prix :&nbsp;<b><?=$prix?></b></b></span><span
										class="pull-right" style="margin: 10px;">v<?=$ver?></span><br><span
										class="pull-left">Auteur : <b><?=$author?></b></span></p>
								<div class="clearfix"></div>
								<p></p>
								<a class="btn btn-<?=$button?> btn-block" id="Contact-plugin" <?=$lien?>>
								<i class="fa fa-plus">
								</i>&nbsp;<?=$infos?></a>
								<div id="Contact-infos-plugin">
									<span class="pull-middle">
<?php
    foreach ($contacts as $c) {
        $contact_name = $c->name;
        $contact_desc = $c->desc;
        echo $contact_name . ' :&nbsp;<b>' . $contact_desc . '</b><br/>';
    }
?>
									</span>
								</div>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script>
    document.querySelector("#Contact-theme").onclick = function () {
        document.querySelector("#Contact-infos-theme").style.display = (window.getComputedStyle(document.querySelector('#Contact-infos-theme')).display == 'none') ? "block" : "none";
    }
    document.querySelector("#Contact-plugin").onclick = function () {
        document.querySelector("#Contact-infos-plugin").style.display = (window.getComputedStyle(document.querySelector('#Contact-infos-plugin')).display == 'none') ? "block" : "none";
    }
</script>