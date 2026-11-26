<div class="container">
    <form method="post">
        <input type="hidden" name="data[_Token][key]" value="<?= h($csrfToken) ?>">
        <button type="submit" class="btn btn-primary"><?= h($Lang->get($actionLabel)) ?></button>
    </form>
</div>
