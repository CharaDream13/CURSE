<?php
$role = $user['role'];
$page = query('page') ?: 'dashboard';
$links = [
    ['dashboard', 'Панель', ['administrator', 'manager', 'housekeeping']],
    ['rooms', 'Номера', ['administrator', 'manager', 'housekeeping']],
    ['bookings', 'Бронирования', ['administrator', 'manager']],
    ['guests', 'Гости', ['administrator', 'manager']],
    ['tariffs', 'Тарифы', ['manager']],
    ['employees', 'Сотрудники', ['manager']],
    ['audit', 'Журнал', ['administrator', 'manager']],
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Панель') ?> — Гостинец</title>
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container bar">
        <a class="brand" href="/admin/">Гостинец: панель</a>
        <div class="user-box">
            <span><?= e($user['name']) ?> (<?= e(role_name($role)) ?>)</span>
            <form method="post" action="/login.php?action=logout" class="inline">
                <?= csrf_field() ?>
                <button type="submit">Выйти</button>
            </form>
        </div>
    </div>
    <div class="container">
        <nav class="admin-nav">
            <?php foreach ($links as [$key, $label, $roles]): ?>
                <?php if (in_array($role, $roles, true)): ?>
                    <a href="/admin/?page=<?= e($key) ?>"<?= $page === $key ? ' class="active"' : '' ?>><?= e($label) ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
            <a href="/">Сайт</a>
        </nav>
    </div>
</header>
<main class="container">
    <?php foreach (flash_pull() as $message): ?>
        <div class="notice <?= e($message['type']) ?>"><?= e($message['text']) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
</main>
<script src="/assets/js/app.js"></script>
</body>
</html>
