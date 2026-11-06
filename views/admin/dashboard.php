<h1>Панель управления</h1>

<div class="grid">
    <section class="panel stat"><b><?= e($rooms['available'] ?? 0) ?></b>Свободных номеров</section>
    <section class="panel stat"><b><?= e($rooms['occupied'] ?? 0) ?></b>Занятых номеров</section>
    <section class="panel stat"><b><?= e($rooms['cleaning'] ?? 0) ?></b>На уборке</section>
    <section class="panel stat"><b><?= e($rooms['maintenance'] ?? 0) ?></b>На обслуживании</section>
    <section class="panel stat"><b><?= e(($bookings['confirmed'] ?? 0) + ($bookings['checked_in'] ?? 0)) ?></b>Активных бронирований</section>
    <section class="panel stat"><b><?= e($bookings['pending'] ?? 0) ?></b>Заявок в ожидании</section>
</div>

<?php if (in_array($user['role'], ['administrator', 'manager'], true)): ?>
    <h2>Ожидаемые заезды</h2>
    <div class="table-wrap">
        <table>
            <tr><th>Номер брони</th><th>Гость</th><th>Заезд</th><th>Выезд</th><th>Номер</th><th>Статус</th></tr>
            <?php foreach ($upcoming as $booking): ?>
                <tr>
                    <td><?= e($booking['booking_number']) ?></td>
                    <td><?= e($booking['guest_name']) ?></td>
                    <td><?= e(date_ru($booking['check_in'])) ?></td>
                    <td><?= e(date_ru($booking['check_out'])) ?></td>
                    <td><?= e($booking['room_number'] ?? $booking['category_name']) ?></td>
                    <td><?= e($booking['status_name']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$upcoming): ?>
                <tr><td colspan="6">Нет ожидаемых заездов</td></tr>
            <?php endif; ?>
        </table>
    </div>
    <a class="button" href="/admin/?page=booking_new">Новое бронирование</a>
<?php endif; ?>
