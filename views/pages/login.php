<section class="panel narrow">
    <h1>Вход для сотрудников</h1>
    <?php if ($error): ?>
        <div class="notice error"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post" action="/login.php">
        <?= csrf_field() ?>
        <label>Логин
            <input type="text" name="username" maxlength="60" required autofocus value="<?= e($username) ?>">
        </label>
        <label>Пароль
            <input type="password" name="password" required>
        </label>
        <button type="submit">Войти</button>
    </form>
</section>
