<?php $Lang = new LangComponent;  ?>
<div class="alert alert-warning alert-dismissible" role="alert">
  <strong><?= $Lang->get('WARNING') ?> :</strong> <?php echo h($message); ?>
</div>
