<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="callout callout-danger">
                <h4><?= $Lang->get('SEO__CALLOUT') ?></h4><?= $Lang->get('SEO__CALLOUT_MESSAGE') ?>
            </div>
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get('SEO__TITLE_DEFAULT') ?></h3>
                </div>
                <div class="card-body">
                    <form action="<?= $this->Html->url(['controller' => 'seo', 'action' => 'edit_default', 'admin' => 'true']) ?>"
                          method="post" data-ajax="true" data-upload-image="true"
                          data-redirect-url="<?= $this->Html->url(['controller' => 'seo', 'action' => 'index', 'admin' => 'true']) ?>">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?= $Lang->get('SEO__FORM_TITLE') ?></label>
                                    <input type="text" class="form-control"
                                           value="<?= empty($default['title']) ? "{TITLE} - {WEBSITE_NAME}" : $default['title'] ?>"
                                           name="title">

                                    <small><b>{TITLE}</b> = <?= $Lang->get('SEO__FORM_TITLE_DESC_T') ?>
                                        <br><b>{WEBSITE_NAME}</b> = <?= $Lang->get('SEO__FORM_TITLE_DESC_W') ?></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?= $Lang->get('SEO__FORM_DESCRIPTION') ?></label>
                                    <input type="text" class="form-control" value="<?= $default['description'] ?>"
                                           name="description">
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <em><?= $Lang->get('SEO__FORM_FAVICON_DESC') ?></em>
                                    <?= $this->element('form.input.upload.img', ['img' => $default['favicon_url'], 'filename' => "favicon.png", 'title' => $Lang->get('SEO__FORM_FAVICON')]); ?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?= $Lang->get('SEO__FORM_IMG_URL') ?></label>
                                    <em><?= $Lang->get('SEO__FORM_IMG_URL_DESC') ?></em>
                                    <input type="text" class="form-control" value="<?= $default['img_url'] ?>"
                                           name="img_url">
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary float-right"
                                type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>

                    </form>

                </div>
            </div>
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get('SEO__TITLE_OTHER') ?></h3>
                </div>
                <div class="card-body">

                    <a class="btn btn-large btn-block btn-primary"
                       href="<?= $this->Html->url(['controller' => 'seo', 'action' => 'add', 'admin' => true]) ?>"><?= $Lang->get('SEO__ADD_PAGE') ?></a>

                    <hr>


                    <table class="table table-responsive-sm table-bordered">
                        <thead>
                        <tr>
                            <th><?= $Lang->get('SEO__PAGE') ?></th>
                            <th><?= $Lang->get('SEO__FORM_TITLE') ?></th>
                            <th><?= $Lang->get('SEO__FORM_DESCRIPTION') ?></th>
                            <th><?= $Lang->get('SEO__FORM_FAVICON') ?></th>
                            <th><?= $Lang->get('SEO__FORM_IMG_URL') ?></th>
                            <th><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($seo_other as $v) { ?>
                            <tr>
                                <td><?= $v["Seo"]["page"] ?></td>
                                <td><?= ($v["Seo"]['title']) ? $v["Seo"]['title'] : $Lang->get('SEO__DEFAULT_VALUE') ?></td>
                                <td><?= ($v["Seo"]['description']) ? $v["Seo"]['description'] : $Lang->get('SEO__DEFAULT_VALUE') ?></td>
                                <td><?= ($v["Seo"]['favicon_url']) ? $v["Seo"]['favicon_url'] : $Lang->get('SEO__DEFAULT_VALUE') ?></td>
                                <td><?= ($v["Seo"]['img_url']) ? $v["Seo"]['img_url'] : $Lang->get('SEO__DEFAULT_VALUE') ?></td>
                                <td>
                                    <a class="btn btn-info"
                                       href="<?= $this->Html->url(['action' => 'edit', $v["Seo"]['id']]) ?>"><?= $Lang->get('GLOBAL__EDIT') ?></a>
                                    <a onClick="confirmDel('<?= $this->Html->url(['action' => 'delete', $v["Seo"]['id']]) ?>')"
                                       class="btn btn-danger"><?= $Lang->get('GLOBAL__DELETE') ?></a>
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
