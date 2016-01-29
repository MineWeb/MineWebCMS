<?php $Lang = new LangComponent;  ?>
<div class="alert alert-info alert-dismissible" role="alert">
  <strong><?= $Lang->get('GLOBAL__INFO') ?> :</strong> <?php echo h($message); ?>
</div>
