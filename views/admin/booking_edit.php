<h1>Бронирование <?= e($booking['booking_number']) ?></h1>
<section class="panel">
    <p>Гость: <?= e($booking['guest_name']) ?></p>
    <p>Категория: <?= e($booking['category_name']) ?>, <?= e(money($booking['price_per_night'])) ?> за сутки</p>
    <p>Статус: <?= e($booking['status_name']) ?></p>
</section>
<section class="panel">
    <form method="post" action="/admin/?page=booking_edit&amp;id=<?= e($booking['id']) ?>" data-dates>
        <?= csrf_field() ?>
        <div class="form-row">
            <label>Дата заезда
                <input type="date" name="check_in" required value="<?= e($booking['check_in']) ?>">
            </label>
            <label>Дата выезда
                <input type="date" name="check_out" required value="<?= e($booking['check_out']) ?>">
            </label>
        </div>
        <label>Номер
            <select name="room_id">
                <option value="">Не назначен</option>
                <?php foreach ($rooms as $room): ?>
                    <?php if ($room['status_code'] !== 'maintenance'): ?>
                        <option value="<?= e($room['id']) ?>"<?= (string) $booking['room_id'] === (string) $room['id'] ? ' selected' : '' ?>><?= e($room['room_number']) ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Сохранить</button>
        <a class="button" href="/admin/?page=bookings">Назад</a>
    </form>
</section>
