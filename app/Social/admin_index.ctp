<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get("SOCIAL__HOME") ?></h3>
                </div>
                <div class="card-body">
                    <a class="btn btn-large btn-block btn-primary" href="<?= $this->Html->url(['controller' => 'social', 'action' => 'add', 'admin' => true]) ?>"><?= $Lang->get('SOCIAL__ADD') ?></a>
                    <hr>
                    <table class="table table-responsive-sm table-bordered">
                        <thead>
                            <tr>
                                <th><?= $Lang->get("SOCIAL__BUTTON_TITLE") ?></th>
                                <th><?= $Lang->get("SOCIAL__BUTTON_TYPE") ?></th>
                                <th><?= $Lang->get("SOCIAL__BUTTON_URL") ?></th>
                                <th><?= $Lang->get("SOCIAL__BUTTON_COLOR") ?></th>
                                <th class="right"><?= $Lang->get("GLOBAL__ACTIONS")?></th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <?php $i = 0;foreach ($social_buttons as $key => $value) { $i++ ?>
                                <tr>
                                    <td><?= htmlentities($value['SocialButton']['title']) ?></td>
                                    <td class="d-flex">
                                        <?php if($value['SocialButton']['img']) { ?>
                                            <img src="<?= $value['SocialButton']['img'] ?>" class="m-auto" alt="Image représentant <?= htmlentities($value['SocialButton']['title']) ?>" width="50">
                                        <?php } if($value['SocialButton']['fa']) { ?>
                                            <i class="<?= $value['SocialButton']['fa'] ?> fa-3x m-auto"></i>
                                        <?php } ?>
                                    </td>
                                    <td><a href="<?= $value['SocialButton']['url'] ?>"><?= $value['SocialButton']['url'] ?></a></td>
                                    <td><div class="socialbutton-color p-2 text-center" style="background-color: <?= $value['SocialButton']['color'] ?>"><?= $value['SocialButton']['color'] ?><div></td>
                                    <td>
                                        <a href="<?= $this->Html->url(['action' => 'edit', $value['SocialButton']['id']]) ?>" class="btn btn-info"><?= $Lang->get('GLOBAL__EDIT') ?></a>
                                        <a onClick="confirmDel('<?= $this->Html->url(['action' => 'delete', $value['SocialButton']['id']]) ?>')" class="btn btn-danger"><?= $Lang->get('GLOBAL__DELETE') ?></a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>