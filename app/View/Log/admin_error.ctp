<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get("LOG__VIEW_ERROR") ?></h3>
                </div>
                <div class="card-body">
                    <?php
                        if (isset($errorContent) && count($errorContent) >= 1) {
                            foreach($errorContent as $error) { ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <?php
                                                    foreach($error as $line) { ?>
                                                        <p><?= $line ?></p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } 
                        } else { ?>
                            <p><?= $Lang->get("LOG__NO_ERROR") ?></p>
                    <?php } ?> 
                </div>
            </div>
        </div>
    </div>
</section>
