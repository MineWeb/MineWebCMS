<div class="push-nav"></div>
<div class="container">
    <div class="row">

        <div class="col-md-6">
            <h1 style="display: inline-block;"><?= $Lang->get('ERROR__500_LABEL') ?></h1>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
					<div class="alert alert-danger"><b><?= $Lang->get('GLOBAL__ERROR') ?> : </b><?= $Lang->get('LICENSE_ERROR__'.$message) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
