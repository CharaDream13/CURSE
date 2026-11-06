<h1>Бронирования</h1>

<section class="panel">
    <form method="get" action="/admin/" class="toolbar">
        <input type="hidden" name="page" value="bookings">
        <label>Статус
            <select name="status">
                <option value="">Все</option>
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= e($status['code']) ?>"<?= $selected === $status['code'] ? ' selected' : '' ?>><?= e($status['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Поиск
            <input type="text" data-filter="bookings-table" placeholder="Номер брони, гость, номер комнаты">
        </label>
        <button type="submit">Применить</button>
        <a class="button" href="/admin/?page=booking_new">Новое бронирование</a>
    </form>
</section>

<div class="table-wrap">
    <table id="bookings-table">
        <tr>
            <th>Номер брони</th><th>Гость</th><th>Номер</th><th>Заезд</th><th>Выезд</th>
            <th>Сумма</th><th>Статус</th><th>Действия</th>
        </tr>
        <?php foreach ($bookings as $booking): ?>
            <?php $fields = ['id' => $booking['id']]; $url = '/admin/?page=booking_action'; ?>
            <tr>
                <td><?= e($booking['booking_number']) ?></td>
                <td><a href="/admin/?page=guest&amp;id=<?= e($booking['guest_id']) ?>"><?= e($booking['guest_name']) ?></a></td>
                <td><?= e($booking['room_number'] ?? 'не назначен') ?> (<?= e($booking['category_name']) ?>)</td>
                <td><?= e(date_ru($booking['check_in'])) ?></td>
                <td><?= e(date_ru($booking['check_out'])) ?></td>
                <td><?= e(money($booking['total_price'])) ?></td>
                <td><?= e($booking['status_name']) ?></td>
                <td class="actions">
                    <?php if (in_array($booking['status_code'], ['pending', 'confirmed'], true)): ?>
                        <a class="button" href="/admin/?page=booking_edit&amp;id=<?= e($booking['id']) ?>">Изменить</a>
                    <?php endif; ?>
                    <?php if ($booking['status_code'] === 'pending'): ?>
                        <?= action_form($url, $fields + ['action' => 'confirm'], 'Подтвердить') ?>
                    <?php endif; ?>
                    <?php if ($booking['status_code'] === 'confirmed'): ?>
                        <?= action_form($url, $fields + ['action' => 'checkin'], 'Заселить') ?>
                    <?php endif; ?>
                    <?php if ($booking['status_code'] === 'checked_in'): ?>
                        <?= action_form($url, $fields + ['action' => 'checkout'], 'Выселить') ?>
                    <?php endif; ?>
                    <?php if (in_array($booking['status_code'], ['pending', 'confirmed'], true)): ?>
                        <?= action_form($url, $fields + ['action' => 'cancel'], 'Отменить', 'Отменить бронирование?') ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$bookings): ?>
            <tr><td colspan="8">Бронирований нет</td></tr>
        <?php endif; ?>
    </table>
</div>
