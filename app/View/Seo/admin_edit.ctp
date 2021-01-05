<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get('SEO__EDIT_PAGE') ?></h3>
                </div>
                <div class="card-body">
                    <form method="post" data-ajax="true" data-upload-image="true"
                          data-redirect-url="<?= $this->Html->url(['controller' => 'seo', 'action' => 'index', 'admin' => 'true']) ?>">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label><?= $Lang->get('SEO__PAGE') ?></label>
                                    <br>
                                    <em><?= $Lang->get('SEO__PAGE_DESC') ?></em>
                                    <input type="text" class="form-control" name="page" value="<?= $page['page'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?= $Lang->get('SEO__FORM_TITLE') ?></label>
                                    <br>
                                    <em><?= $Lang->get('SEO__KEEP_EMPTY') ?></em>
                                    <input type="text" class="form-control"
                                           value="<?= $page['title'] ?>"
                                           name="title">

                                    <small><b>{TITLE}</b> = <?= $Lang->get('SEO__FORM_TITLE_DESC_T') ?>
                                        <br><b>{WEBSITE_NAME}</b> = <?= $Lang->get('SEO__FORM_TITLE_DESC_W') ?></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?= $Lang->get('SEO__FORM_DESCRIPTION') ?></label>
                                    <br>
                                    <em><?= $Lang->get('SEO__KEEP_EMPTY') ?></em>
                                    <input type="text" class="form-control" value="<?= $page['description'] ?>"
                                           name="description">
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <em><?= $Lang->get('SEO__FORM_FAVICON_DESC') ?></em>
                                    <br>
                                    <em><?= $Lang->get('SEO__KEEP_EMPTY') ?></em>
                                    <?= $this->element('form.input.upload.img', ['img' => $page['favicon_url'], 'filename' => "favicon.png", 'title' => $Lang->get('SEO__FORM_FAVICON')]); ?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label><?= $Lang->get('SEO__FORM_IMG_URL') ?></label>
                                    <em><?= $Lang->get('SEO__FORM_IMG_URL_DESC') ?></em>
                                    <br>
                                    <em><?= $Lang->get('SEO__KEEP_EMPTY') ?></em>
                                    <input type="text" class="form-control" value="<?= $page['img_url'] ?>"
                                           name="img_url">
                                </div>
                            </div>
                        </div>

                        <div class="float-right">
                            <a href="<?= $this->Html->url(['controller' => 'seo', 'action' => 'index', 'admin' => true]) ?>"
                               class="btn btn-default"><?= $Lang->get('GLOBAL__CANCEL') ?></a>
                            <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</section>
