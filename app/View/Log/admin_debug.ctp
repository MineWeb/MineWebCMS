<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title"><?= $Lang->get("LOG__VIEW_DEBUG") ?></h3>
                </div>
                <div class="card-body">
                    <?php
                        if (isset($debugContent) && count($debugContent) >= 1) {
                            foreach($debugContent as $debug) { ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <?php
                                                    foreach($debug as $line) { ?>
                                                        <p><?= $line ?></p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } 
                        } else { ?>
                            <p>Il n'y a pas de debugs</p>
                    <?php } ?> 
                </div>
            </div>
        </div>
    </div>
</section>
