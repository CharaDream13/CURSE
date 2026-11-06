<h1>Тарифы</h1>

<div class="table-wrap">
    <table>
        <tr><th>Категория</th><th>Текущая цена за сутки</th><th>Новая цена</th></tr>
        <?php foreach ($categories as $category): ?>
            <tr>
                <td><?= e($category['name']) ?></td>
                <td><?= $category['price'] !== null ? e(money($category['price'])) : '—' ?></td>
                <td>
                    <form method="post" action="/admin/?page=tariff_update" class="toolbar" data-confirm="Изменить тариф?">
                        <?= csrf_field() ?>
                        <input type="hidden" name="category_id" value="<?= e($category['id']) ?>">
                        <input type="number" name="price" min="1" max="1000000" step="0.01" required>
                        <button type="submit">Сохранить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<h2>История тарифов</h2>
<div class="table-wrap">
    <table>
        <tr><th>Категория</th><th>Цена</th><th>Действует с</th><th>Действует до</th></tr>
        <?php foreach ($history as $row): ?>
            <tr>
                <td><?= e($row['name']) ?></td>
                <td><?= e(money($row['price'])) ?></td>
                <td><?= e(date_ru($row['valid_from'])) ?></td>
                <td><?= e($row['valid_to'] ? date_ru($row['valid_to']) : 'по настоящее время') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
