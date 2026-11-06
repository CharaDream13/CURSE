<h1>Гости</h1>

<section class="panel">
    <h2>Новый гость</h2>
    <form method="post" action="/admin/?page=guests">
        <?= csrf_field() ?>
        <label>ФИО
            <input type="text" name="full_name" maxlength="150" required value="<?= e($old['full_name']) ?>">
        </label>
        <div class="form-row">
            <label>Телефон
                <input type="tel" name="phone" maxlength="30" required value="<?= e($old['phone']) ?>">
            </label>
            <label>Email
                <input type="email" name="email" maxlength="150" value="<?= e($old['email']) ?>">
            </label>
        </div>
        <button type="submit">Добавить</button>
    </form>
</section>

<section class="panel">
    <label>Поиск
        <input type="text" data-filter="guests-table" placeholder="ФИО, телефон, email">
    </label>
</section>

<div class="table-wrap">
    <table id="guests-table">
        <tr><th>ФИО</th><th>Телефон</th><th>Email</th><th>Бронирований</th></tr>
        <?php foreach ($guests as $guest): ?>
            <tr>
                <td><a href="/admin/?page=guest&amp;id=<?= e($guest['id']) ?>"><?= e($guest['full_name']) ?></a></td>
                <td><?= e($guest['phone']) ?></td>
                <td><?= e($guest['email']) ?></td>
                <td><?= e($guest['bookings_count']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
