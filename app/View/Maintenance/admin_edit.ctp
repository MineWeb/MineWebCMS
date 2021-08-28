<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get('MAINTENANCE__TITLE') ?></h3>
                </div>
                <div class="card-body">
                    <form method="post" data-ajax="true" data-redirect-url="<?= $this->Html->url(['controller' => 'maintenance', 'action' => 'index', 'admin' => 'true']) ?>">

                        <div class="form-group">
                            <label><?= $Lang->get("MAINTENANCE__PAGE") ?></label><br>
                            <i><?= $Lang->get("MAINTENANCE__ADD_EXAMPLE") ?></i><br>
                            <i><?= $Lang->get("MAINTENANCE__ADD_EMPTY_URL") ?></i>

                            <input type="text" id="url" name="url" class="form-control" value="<?= $page["url"] ?>">
                        </div>
                        <div class="form-group">
                            <label><?= $Lang->get('MAINTENANCE__REASON') ?></label>
                            <?= $this->Html->script('admin/tinymce/tinymce.min.js') ?>
                            <script type="text/javascript">
                                tinymce.init({
                                    selector: "textarea",
                                    height: 300,
                                    width: '100%',
                                    language: 'fr_FR',
                                    plugins: "textcolor code image link",
                                    toolbar: "fontselect fontsizeselect bold italic underline strikethrough link image forecolor backcolor alignleft aligncenter alignright alignjustify cut copy paste bullist numlist outdent indent blockquote code"
                                });
                            </script>
                            <textarea id="editor" name="reason" cols="30" rows="10"><?= $page["reason"] ?></textarea>
                        </div>

                        <input type="hidden" name="data[_Token][key]" value="<?= $csrfToken ?>">

                        <div class="float-right">
                            <a href="<?= $this->Html->url(['controller' => 'maintenance', 'action' => 'index', 'admin' => true, $page["id"]]) ?>"
                               class="btn btn-default"><?= $Lang->get('GLOBAL__CANCEL') ?></a>
                            <button class="btn btn-primary" type="submit"><?= $Lang->get('GLOBAL__SUBMIT') ?></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>
