<div class="alert alert-warning alert-dismissible" role="alert">
  <strong><?= (isset($Lang)) ? $Lang->get('GLOBAL__WARNING') : 'Warning' ?> :</strong> <?php echo h($message); ?>
</div>
