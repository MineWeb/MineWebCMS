<?php $Lang = new LangComponent;  ?>
<div class="alert alert-danger alert-dismissible" role="alert">
  <strong><?= $Lang->get('ERROR') ?> :</strong> <?php echo h($message); ?>
</div>