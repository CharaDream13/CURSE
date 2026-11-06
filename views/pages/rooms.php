<h1>Номера и тарифы</h1>
<div class="grid">
    <?php foreach ($categories as $category): ?>
        <section class="panel">
            <h2><?= e($category['name']) ?></h2>
            <p><?= e($category['description']) ?></p>
            <table>
                <tr><th>Вместимость</th><td>до <?= e($category['capacity']) ?> гостей</td></tr>
                <tr><th>Номеров в гостинице</th><td><?= e($category['rooms_count']) ?></td></tr>
                <tr><th>Цена за сутки</th><td><?= $category['price'] !== null ? e(money($category['price'])) : 'Уточняется' ?></td></tr>
            </table>
            <p><a class="button" href="/?page=booking&amp;category=<?= e($category['id']) ?>">Забронировать</a></p>
        </section>
    <?php endforeach; ?>
</div>
