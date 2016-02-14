<div class="alert alert-danger alert-dismissible" role="alert">
  <strong><?= (isset($Lang)) ? $Lang->get('GLOBAL__ERROR') : 'Error' ?> :</strong> <?php echo h($message); ?>
</div>
