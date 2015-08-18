<?php 
$this->Connect = new ConnectComponent;
$theme_config = file_get_contents(ROOT.'/app/View/Themed/Mineweb/config/config.json');
$theme_config = json_decode($theme_config, true);
?>
<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Eywek">

    <title><?= $title_for_layout; ?> - <?= $Configuration->get('name') ?></title>

    <?= $this->Html->css('bootstrap.css') ?>
    <?= $this->Html->css('modern-business.css') ?>
    <?= $this->Html->css('animate.css') ?>
    <?= $this->Html->css('font-awesome.min.css') ?>
    <?= $this->Html->css('mineweb') ?>
	<?= $this->Html->css('../font-awesome-4.1.0/css/font-awesome.min.css') ?>
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,300,900' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Rambla' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
	<?= $this->Html->script('jquery-1.11.0.js') ?>
    <?= $this->Html->script('easy_paginate.js') ?>

    <link rel="icon" type="image/png" href="<?= $theme_config['favicon_url'] ?>" />
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body><!-- grey.png -->
    <?= $this->element($theme_config['navbar']) ?>

            <?php 
            $flash = $this->Session->flash();
            if(!empty($flash)) { ?><br><br><br>
              <div class="container">
                <?= $flash ?>
              </div>
            <?php } ?>
            <?= $this->fetch('content'); ?>
        <!-- Footer -->
    </div>
        <footer>
            <div class="container">
                <p><?= $Lang->get('COPYRIGHT') ?></p>
            </div>
        </footer>
    
    <?= $this->element('login_register') ?>
    <?= $this->element('script') ?>

    <?= $Module->loadModules('user_profile') ?>


    <script>
    $("#ticket-form_post_m").submit(function( event ) {
        event.preventDefault();
        $('#msg-on-post').hide().html('<div class="alert alert-success" role="alert"><p><?= $Lang->get('IN_PROGRESS') ?></p></div>').fadeIn(1500);
        var $form = $( this );
        var title = $form.find("input[name='title']").val();
        var content = $form.find("textarea[name='content']").val();
        if($form.find("input[name='private']").is(':checked')) {
          var ticket_private = '1';
        } else {
          var ticket_private = '0';
        }
        $.post("<?= $this->Html->url(array('plugin' => 'support', 'controller' => 'home', 'action' => 'ajax_post')) ?>", { title : title, content : content, ticket_private : ticket_private }, function(data) {
          if(!isNaN(data) == true) {
            $('#post_ticket').modal('hide');
            // ajout du post js
            var content_tickets = $("#content-tickets").html();
            var new_ticket = '<div class="col-md-12" id="ticket-'+data+'"><div class="panel panel-default panel-ticket"><div class="panel-body"><div class="head"><img class="support" src="<?= $this->Html->url(array('controller' => 'API', 'action' => 'get_head_skin/')) ?>/<?= $this->Connect->get_pseudo() ?>/135" title="<?= $this->Connect->get_pseudo() ?>"><div class="clearfix"></div><p class="author"><?php if(strlen($this->Connect->get_pseudo()) > "8") { echo '<abbr title="'.$this->Connect->get_pseudo().'">'.substr($this->Connect->get_pseudo(), 0, 8).'...</abbr>'; } else { echo $this->Connect->get_pseudo(); } ?></p></div><h3 class="support">'+title+' <div style="display:inline-block;" id="ticket-state-'+data+'"><icon class="fa fa-times" style="color:red;" title="<?= $Lang->get('UNRESOLVED') ?>"></icon></div></h3><div class="pull-right support"><p><a id="'+data+'" title="<?= $Lang->get('DELETE') ?>" class="ticket-delete btn btn-danger btn-sm"><icon class="fa fa-times"></icon></a></p></div><p class="support">'+content+'</p><div class="clearfix"></div></div></div></div>';
            $("#content-tickets").hide().html(new_ticket+content_tickets).fadeIn(1500);
            // fin ajout
            // stats
            var nbr_ticket = $('#nbr-ticket').html();
            nbr_ticket = parseInt(nbr_ticket);
            nbr_ticket = nbr_ticket + 1;
            $('#nbr-ticket').html(nbr_ticket);
            var nbr_ticket_unresolved = $('#nbr-ticket-unresolved').html();
            nbr_ticket_unresolved = parseInt(nbr_ticket_unresolved);
            nbr_ticket_unresolved = nbr_ticket_unresolved + 1;
            $('#nbr-ticket-unresolved').html(nbr_ticket_unresolved);
            // fin stast
          } else {
            $('#msg-on-post').hide().html('<div class="alert alert-danger" role="alert"><p><b><?= $Lang->get('ERROR') ?> : </b>'+data+'</p></div>').fadeIn(1500);
          }
        });
        return false;
    });

$('html').height($(document).height());
$('body').height($(document).height());
    </script>
     

    <?= $this->Html->script('jquery-1.11.0.js') ?>
    <?= $this->Html->script('bootstrap.js') ?>

</body>

</html>
<?php 
$timestamp_fin = microtime(true);
$difference_ms = $timestamp_fin - TIMESTAMP_DEBUT;
echo '<!-- Exécution du script : ' . $difference_ms . ' secondes. -->'; ?>