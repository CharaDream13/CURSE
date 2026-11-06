<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Гостинец') ?> — Гостинец</title>
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container bar">
        <a class="brand" href="/">Гостинец</a>
        <nav>
            <a href="/">Главная</a>
            <a href="/?page=rooms">Номера</a>
            <a href="/?page=booking">Бронирование</a>
            <a href="/login.php">Вход для сотрудников</a>
        </nav>
    </div>
</header>
<main class="container">
    <?php foreach (flash_pull() as $message): ?>
        <div class="notice <?= e($message['type']) ?>"><?= e($message['text']) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
</main>
<footer class="site-footer">
    <div class="container">Гостиница «Гостинец», г. Ижевск</div>
</footer>
<script src="/assets/js/app.js"></script>
</body>
</html>
