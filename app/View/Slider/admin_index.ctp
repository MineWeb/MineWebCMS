<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $Lang->get('SLIDER__LIST') ?></h3>
        </div>
        <div class="box-body">

          <a class="btn btn-large btn-block btn-primary" href="<?= $this->Html->url(array('controller' => 'slider', 'action' => 'add', 'admin' => true)) ?>"><?= $Lang->get('SLIDER__ADD') ?></a>

          <hr>

          <table class="table table-bordered">
            <thead>
              <tr>
                <th><?= $Lang->get('GLOBAL__TITLE') ?></th>
                <th><?= $Lang->get('SLIDER__SUBTITLE') ?></th>
                <th><?= $Lang->get('GLOBAL__IMAGE') ?></th>
                <th><?= $Lang->get('GLOBAL__ACTIONS') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($sliders as $slider => $v) { ?>
              <tr>
                <td><?= $v['Slider']['title'] ?></td>
                <td><?= $v['Slider']['subtitle'] ?></td>
                <td><img width="50px" height="50px" src="<?= $v['Slider']['url_img'] ?>" alt=""></td>
                <td>
                  <a href="<?= $this->Html->url(array('controller' => 'slider', 'action' => 'edit/'.$v['Slider']['id'], 'admin' => true)) ?>" class="btn btn-info"><?= $Lang->get('GLOBAL__EDIT') ?></a>
                  <a onClick="confirmDel('<?= $this->Html->url(array('controller' => 'slider', 'action' => 'delete/'.$v['Slider']['id'], 'admin' => true)) ?>')" class="btn btn-danger"><?= $Lang->get('GLOBAL__DELETE') ?></a>
              </tr>
              <?php } ?>
            </tbody>
          </table>

        </div>
      </div>
    </div>
  </div>
</section>
