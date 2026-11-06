<h1><?= e($guest['full_name']) ?></h1>
<section class="panel">
    <p>Телефон: <?= e($guest['phone']) ?></p>
    <p>Email: <?= e($guest['email'] ?: '—') ?></p>
    <p>В базе с: <?= e(date_ru($guest['created_at'])) ?></p>
</section>

<h2>Бронирования гостя</h2>
<div class="table-wrap">
    <table>
        <tr><th>Номер брони</th><th>Номер</th><th>Заезд</th><th>Выезд</th><th>Сумма</th><th>Статус</th></tr>
        <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?= e($booking['booking_number']) ?></td>
                <td><?= e($booking['room_number'] ?? 'не назначен') ?> (<?= e($booking['category_name']) ?>)</td>
                <td><?= e(date_ru($booking['check_in'])) ?></td>
                <td><?= e(date_ru($booking['check_out'])) ?></td>
                <td><?= e(money($booking['total_price'])) ?></td>
                <td><?= e($booking['status_name']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$bookings): ?>
            <tr><td colspan="6">Бронирований нет</td></tr>
        <?php endif; ?>
    </table>
</div>
<a class="button" href="/admin/?page=guests">Назад</a>
