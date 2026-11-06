<section class="panel">
    <h1>Гостиница «Гостинец»</h1>
    <p>Гостиница на 40 номеров в Ижевске. Пять этажей, три категории номеров, понятные цены и быстрое подтверждение заявки.</p>
    <a class="button" href="/?page=booking">Забронировать номер</a>
</section>

<h2>Категории номеров</h2>
<div class="grid">
    <?php foreach ($categories as $category): ?>
        <section class="panel">
            <h3><?= e($category['name']) ?></h3>
            <p><?= e($category['description']) ?></p>
            <p>Вместимость: до <?= e($category['capacity']) ?> гостей</p>
            <p><b><?= $category['price'] !== null ? e(money($category['price'])) . ' за сутки' : 'Цена уточняется' ?></b></p>
        </section>
    <?php endforeach; ?>
</div>

<section class="panel">
    <h2>Преимущества</h2>
    <ul>
        <li>40 номеров на пяти этажах</li>
        <li>Три категории: малый, средний и большой номер</li>
        <li>Прозрачная стоимость проживания за сутки</li>
        <li>Онлайн-заявка на бронирование</li>
    </ul>
</section>

<section class="panel">
    <h2>Контакты</h2>
    <p>Адрес: г. Ижевск</p>
    <p>Телефон: +7 (000) 000-00-00</p>
    <p>Email: info@example.com</p>
</section>
