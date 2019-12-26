<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Installation - MineWeb</title>

    <link rel="stylesheet" href="app/webroot/css/bootstrap.css" media="screen" title="no title" charset="utf-8">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,300,500,700' rel='stylesheet' type='text/css">
    <link rel="stylesheet" href="app/webroot/css/install/flat.css" media="screen" title="no title" charset="utf-8">
    <link rel="stylesheet" href="app/webroot/css/install/animate.min.css">
    <link rel="stylesheet" href="app/webroot/css/install/install.css" media="screen" title="no title" charset="utf-8">
    <link rel="stylesheet" href="app/webroot/css/font-awesome.min.css" media="screen" title="no title" charset="utf-8">

</head>
<body>

<div class="container" style="max-width:700px">
    <svg width='160px' height='160px' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="loader">
        <rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect>
        <rect  x='48.5' y='40' width='3' height='20' rx='5' ry='5' fill='#7c7e7f' transform='rotate(0 50 50) translate(0 -30)'>
            <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0s' repeatCount='indefinite'/>
        </rect>
        <rect  x='48.5' y='40' width='3' height='20' rx='5' ry='5' fill='#7c7e7f' transform='rotate(30 50 50) translate(0 -30)'>
            <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.08333333333333333s' repeatCount='indefinite'/>
        </rect>
        <rect  x='48.5' y='40' width='3' height='20' rx='5' ry='5' fill='#7c7e7f' transform='rotate(60 50 50) translate(0 -30)'>
            <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.16666666666666666s' repeatCount='indefinite'/>
        </rect>
        <rect  x='48.5' y='40' width='3' height='20' rx='5' ry='5' fill='#7c7e7f' transform='rotate(90 50 50) translate(0 -30)'>
            <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.25s' repeatCount='indefinite'/>
        </rect>
        <rect  x='48.5' y='40' width='3' height='20' rx='5' ry='5' fill='#7c7e7f' transform='rotate(120 50 50) translate(0 -30)'>
            <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.3333333333333333s' repeatCount='indefinite'/>
        </rect>
        <rect  x='48.5' y='40' width='3' height='20' rx='5' ry='5' fill='#7c7e7f' transform='rotate(150 50 50) translate(0 -30)'>
            <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.4166666666666667s' repeatCount='indefinite'/>
        </rect>
        <rect  x='48.5' y='40' width='3' height='20' rx='5' ry='5' fill='#7c7e7f' transform='rotate(180 50 50) translate(0 -30)'>
            <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.5s' repeatCount='indefinite'/>
        </rect>
        <rect  x='48.5' y='40' width='3' height='20' rx='5' ry='5' fill='#7c7e7f' transform='rotate(210 50 50) translate(0 -30)'>
            <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.5833333333333334s' repeatCount='indefinite'/>
        </rect>
        <rect  x='48.5' y='40' width='3' height='20' rx='5' ry='5' fill='#7c7e7f' transform='rotate(240 50 50) translate(0 -30)'>
            <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.6666666666666666s' repeatCount='indefinite'/>
        </rect>
        <rect  x='48.5' y='40' width='3' height='20' rx='5' ry='5' fill='#7c7e7f' transform='rotate(270 50 50) translate(0 -30)'>
            <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.75s' repeatCount='indefinite'/>
        </rect>
        <rect  x='48.5' y='40' width='3' height='20' rx='5' ry='5' fill='#7c7e7f' transform='rotate(300 50 50) translate(0 -30)'>
            <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.8333333333333334s' repeatCount='indefinite'/>
        </rect>
        <rect  x='48.5' y='40' width='3' height='20' rx='5' ry='5' fill='#7c7e7f' transform='rotate(330 50 50) translate(0 -30)'>
            <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.9166666666666666s' repeatCount='indefinite'/>
        </rect>
    </svg>

    <img src="data:image/png;base64,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" alt="Logo" class="logo">

    <div class="first center" style="display:none;">
        <h1>Bienvenue,</h1>
        <p>Dans l'installation de MineWeb, merci d'avoir choisi notre CMS ! Vous allez maintenant pouvoir procéder à l'installation du site en toute simplicité.</p>
    </div>

    <div class="compatibilite" style="display:none;" data-need-to-display="<?= ($needAffichCompatibility) ? 'true' : 'false' ?>">
        <table class="table">
            <thead>
            <tr>
                <th>Fonctionnalité</th>
                <th>Installée</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Droits en écriture</td>
                <td><?= affichImg($compatible['chmod']) ?></td>
            </tr>
			<?php if(!$compatible['chmod'] && isset($help['chmod'])): ?>
                <tr>
                    <td>
                        <div class="panel panel-default">
                            <div class="panel-body"><?= $help['chmod']; ?></div>
                        </div>
                    </td>
                    <td></td>
                </tr>
			<?php endif; ?>
            <tr>
                <td>Version de PHP >= 5.6 <= 7.4</td>
                <td><?= affichImg($compatible['phpVersion']) ?></td>
            </tr>
			<?php if(!$compatible['phpVersion'] && isset($help['phpVersion'])): ?>
                <tr>
                    <td>
                        <div class="panel panel-default">
                            <div class="panel-body"><?= $help['phpVersion']; ?></div>
                        </div>
                    </td>
                    <td></td>
                </tr>
			<?php endif; ?>
            <tr>
                <td>PDO</td>
                <td><?= affichImg($compatible['pdo']) ?></td>
            </tr>
			<?php if(!$compatible['pdo'] && isset($help['pdo'])): ?>
                <tr>
                    <td>
                        <div class="panel panel-default">
                            <div class="panel-body"><?= $help['pdo']; ?></div>
                        </div>
                    </td>
                    <td></td>
                </tr>
			<?php endif; ?>
            <tr>
                <td>cURL</td>
                <td><?= affichImg($compatible['curl']) ?></td>
            </tr>
			<?php if(!$compatible['curl'] && isset($help['curl'])): ?>
                <tr>
                    <td>
                        <div class="panel panel-default">
                            <div class="panel-body"><?= $help['curl']; ?></div>
                        </div>
                    </td>
                    <td></td>
                </tr>
			<?php endif; ?>
            <tr>
                <td>Réécriture d'URL - .htaccess activés</td>
                <td><?= affichImg($compatible['rewriteUrl']) ?></td>
            </tr>
			<?php if(!$compatible['rewriteUrl'] && isset($help['rewriteUrl'])): ?>
                <tr>
                    <td>
                        <div class="panel panel-default">
                            <div class="panel-body"><?= $help['rewriteUrl']; ?></div>
                        </div>
                    </td>
                    <td></td>
                </tr>
			<?php endif; ?>
            <tr>
                <td>Librairie GD2 (captcha et image des utilisateurs)</td>
                <td><?= affichImg($compatible['gd2']) ?></td>
            </tr>
			<?php if(!$compatible['gd2'] && isset($help['gd2'])): ?>
                <tr>
                    <td>
                        <div class="panel panel-default">
                            <div class="panel-body"><?= $help['gd2']; ?></div>
                        </div>
                    </td>
                    <td></td>
                </tr>
			<?php endif; ?>
            <tr>
                <td>Ouverture d'un zip (mise à jour)</td>
                <td><?= affichImg($compatible['openZip']) ?></td>
            </tr>
			<?php if(!$compatible['openZip'] && isset($help['openZip'])): ?>
                <tr>
                    <td>
                        <div class="panel panel-default">
                            <div class="panel-body"><?= $help['openZip']; ?></div>
                        </div>
                    </td>
                    <td></td>
                </tr>
			<?php endif; ?>
            <tr>
                <td>OpenSSL</td>
                <td><?= affichImg($compatible['openSSL']) ?></td>
            </tr>
            <tr>
                <td>Ouverture d'un site à distance (mise à jour)</td>
                <td><?= affichImg($compatible['allowGetURL']) ?></td>
            </tr>

            </tbody>
        </table>
        <div class="alert alert-danger"><b>Erreur : </b>Votre hébergeur n'a pas les pré-requis.</div>
    </div>

    <div class="ajax-msg"></div>

    <div class="database" style="display:none;" data-need-to-display="<?= ($needDisplayDatabase) ? 'true' : 'false' ?>">
        <form id="saveDB">
            <div class="form-group">
                <label>Adresse de la base de données</label>
                <input type="text" class="form-control" name="host" placeholder="Ex: localhost">
            </div>
            <div class="form-group">
                <label>Nom de la base de données</label>
                <input type="text" class="form-control" name="database" placeholder="Ex: mineweb">
            </div>
            <div class="form-group">
                <label>Nom d'utilisateur</label>
                <input type="text" class="form-control" name="login" placeholder="Ex: root">
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" class="form-control" name="password" placeholder="Ex: root">
            </div>

            <button type="submit" class="btn btn-success saveDB pull-right">Tester et enregistrer</button>
        </form>
    </div>

    <button type="submit" class="btn btn-primary btn-block installSQL" style="display:none;">Installer la base de données</button>
    <div class="progress SQLprogress" style="display:none;">
        <div class="progress-bar progress-bar-info" style="width: 0%;"></div>
    </div>

    <div class="clearfix"></div>
</div>

<script src="app/webroot/js/jquery.1.12.0.js"></script>
<script>

    // Messages
    var TEXT__LOADING = "Chargement...";
    var TEXT__ERROR = "Erreur";
    var TEXT__INTERNAL_ERROR = "Une erreur interne est survenue";

</script>
<script src="app/webroot/js/install.js"></script>
</body>
</html>
