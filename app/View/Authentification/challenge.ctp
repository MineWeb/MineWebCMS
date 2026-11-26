<div class="container">
    <form method="post" action="<?= $this->Html->url(['plugin' => null, 'controller' => 'authentification', 'action' => $verificationAction]) ?>">
        <input type="hidden" name="data[_Token][key]" value="<?= h($csrfToken) ?>">
        <input type="hidden" name="standalone" value="1">
        <div class="form-group">
            <label for="login-code"><?= $Lang->get('USER__LOGIN_CODE') ?></label>
            <input id="login-code" class="form-control" type="text" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" required>
        </div>
        <button type="submit" class="btn btn-primary"><?= $Lang->get($verificationAction === 'disable' ? 'GLOBAL__DISABLE' : 'USER__LOGIN') ?></button>
        <?php if ($verificationAction === 'disable'): ?>
            <?= $this->Html->link($Lang->get('GLOBAL__CANCEL'), ['plugin' => null, 'controller' => 'user', 'action' => 'profile'], ['class' => 'btn btn-default']) ?>
        <?php endif; ?>
    </form>
</div>
