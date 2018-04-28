<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Eywek">

    <title><?= $title_for_layout; ?> - MineWeb</title>

    <?= $this->Html->css('bootstrap.css') ?>
    <?= $this->Html->css('install.css') ?>
    <?= $this->Html->css('custom.css') ?>
    <?= $this->Html->css('prettify.css') ?>

    <link href='https://fonts.googleapis.com/css?family=Roboto:400,400italic,300,300italic,700,700italic'
          rel='stylesheet' type='text/css'>

    <?= $this->Html->css("install/bootstrap.install.min.css") ?>
    <?= $this->Html->css("install/flat.css") ?>
    <?= $this->Html->css("install/animate.min.css") ?>
    <?= $this->Html->css("install/install.css") ?>

</head>
<body>
<div class="page-container">

    <div class="container">
        <div class="row row-offcanvas row-offcanvas-left">

            <br>

            <?php if ($this->params['action'] == 'end') { ?>
                <ul class="nav nav-tabs nav-pills nav-stacked col-xs-6 col-sm-3" style="max-width: 300px;">

                    <li role="presentation"><a
                                title="<?= $Lang->get('INSTALL__NO_SKIP') ?>"><?= $Lang->get('INSTALL__STEP_1_TITLE') ?></a>
                    </li>
                    <li role="presentation" class="active"><a
                                title="<?= $Lang->get('INSTALL__NO_SKIP') ?>"><?= $Lang->get('INSTALL__STEP_2_TITLE') ?></a>
                    </li>
                </ul>
            <?php } else { ?>
            <div id="tabsleft" class="tabbable tabs-left">
                <ul class="nav nav-tabs nav-pills nav-stacked col-xs-6 col-sm-3" style="max-width: 300px;">
                    <li role="presentation" class=""><a href="#tabsleft-tab2" data-toggle="tab" data-toggle="tab"
                                                        title="<?= $Lang->get('INSTALL__NO_SKIP') ?>"><?= $Lang->get('INSTALL__STEP_1_TITLE') ?></a>
                    </li>
                    <li role="presentation" class=""><a href="#tabsleft-tab3" data-toggle="tab"
                                                        title="<?= $Lang->get('INSTALL__NO_SKIP') ?>"><?= $Lang->get('INSTALL__STEP_2_TITLE') ?></a>
                    </li>
                </ul>
                <?php } ?>

                <?= $this->Session->flash() ?>

                <?= $this->fetch('content'); ?>

            </div><!--/.row-->
        </div><!--/.container-->
    </div><!--/.page-container-->
    <!-- script references -->
    <?= $this->Html->script('jquery-1.11.0.js') ?>
    <?= $this->Html->script('bootstrap.js') ?>
    <?= $this->Html->script('jquery.bootstrap.wizard.min.js') ?>
    <?= $this->Html->script('prettify.js') ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#tabsleft').bootstrapWizard({

                'tabClass': 'nav nav-tabs',

                'debug': false,

                onNext: function (tab, navigation, index) {
                    if (index == 1) {

                        var $form = $('#step3');
                        if ($form.find("input[name='step3']").val() == "true") {

                            return true;

                        } else {

                            $('input').each(function () {
                                $(this).addClass('disabled').attr('disabled', true);
                            });
                            $('li.next a').each(function () {
                                $(this).addClass('disabled').attr('disabled', true);
                            });
                            $('.ajax-msg-step3').empty().html('<div class="alert alert-info">Chargement...</div>');

                            var inputs = {};
                            inputs['data[_Token][key]'] = "<?= $this->Session->read('_Token')['key'] ?>";
                            inputs['pseudo'] = $form.find("input[name='pseudo']").val();
                            inputs['password'] = $form.find("input[name='password']").val();
                            inputs['password_confirmation'] = $form.find("input[name='password_confirmation']").val();
                            inputs['email'] = $form.find("input[name='email']").val();

                            var step3Success = false;

                            $.ajax({
                                type: 'POST',
                                url: "<?= $this->Html->url(array('action' => 'step_1')) ?>",
                                data: inputs,
                                dataType: 'JSON',
                                async: false,
                                success: function (data) {

                                    $('input').each(function () {
                                        $(this).removeClass('disabled').attr('disabled', false);
                                    });
                                    $('li.next a').each(function () {
                                        $(this).removeClass('disabled').attr('disabled', false);
                                    });

                                    if (data.statut) {
                                        $('.ajax-msg-step3').empty().html('<div class="alert alert-success"><b><?= $Lang->get('GLOBAL__SUCCESS') ?> :</b> ' + data.msg + '</div>').fadeIn(500);
                                        step3Success = true;
                                        return true;
                                    } else {
                                        $('.ajax-msg-step3').empty().html('<div class="alert alert-danger"><b><?= $Lang->get('GLOBAL__ERROR') ?> :</b> ' + data.msg + '</div>').fadeIn(500);
                                        step3Success = false;
                                        return false;
                                    }

                                },
                                error: function (data) {

                                    $('input').each(function () {
                                        $(this).removeClass('disabled').attr('disabled', false);
                                    });
                                    $('li.next a').each(function () {
                                        $(this).removeClass('disabled').attr('disabled', false);
                                    });

                                    $('.ajax-msg-step3').empty().html('<div class="alert alert-danger"><b><?= $Lang->get('GLOBAL__ERROR') ?> :</b> <?= $Lang->get('ERROR__INTERNAL_ERROR') ?></div>');
                                    var step3Success = false;
                                    return false;

                                }
                            });

                            return step3Success;
                        }
                    }

                },

                onTabClick: function (tab, navigation, index) {
                    alert('<?= $Lang->get('INSTALL__NO_SKIP') ?>');
                    return false;
                },

                onTabShow: function (tab, navigation, index) {
                    var $total = navigation.find('li').length;
                    var $current = index + 1;
                    var $percent = ($current / $total) * 100;
                    $('#tabsleft').find('.progress-bar').css({width: $percent + '%'});

                    // If it's the last tab then hide the last button and show the finish instead
                    if ($current >= $total) {
                        $('#tabsleft').find('.pager .next').hide();
                        $('#tabsleft').find('.pager .finish').show();
                        $('#tabsleft').find('.pager .finish').removeClass('disabled');
                        $('#tabsleft').find('.pager .finish').removeClass('hidden');
                    } else {
                        $('#tabsleft').find('.pager .next').show();
                        $('#tabsleft').find('.pager .finish').hide();
                    }

                }
            });

            $('#tabsleft .finish').click(function () {
                document.location.href = "<?= $this->Html->url(array('controller' => 'install', 'action' => 'end')) ?>";
            });
        });
    </script>
</body>
</html>
