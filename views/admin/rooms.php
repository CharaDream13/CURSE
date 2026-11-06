<h1>Номерной фонд</h1>

<div class="tabs">
    <button type="button" data-floor-tab="all">Все этажи</button>
    <?php foreach (array_keys($floors) as $floor): ?>
        <button type="button" data-floor-tab="<?= e($floor) ?>"><?= e($floor) ?> этаж</button>
    <?php endforeach; ?>
</div>

<?php foreach ($floors as $floor => $rooms): ?>
    <section data-floor="<?= e($floor) ?>">
        <h2><?= e($floor) ?> этаж</h2>
        <div class="grid">
            <?php foreach ($rooms as $room): ?>
                <article class="panel room status-<?= e($room['status_code']) ?>">
                    <h3>№ <?= e($room['room_number']) ?></h3>
                    <p><?= e($room['category_name']) ?>, до <?= e($room['capacity']) ?> гостей</p>
                    <p><b><?= e($room['status_name']) ?></b></p>
                    <p>
                        <?php if ($room['next']): ?>
                            Ближайшая бронь: <?= e($room['next']['booking_number']) ?>,
                            <?= e(date_ru($room['next']['check_in'])) ?> — <?= e(date_ru($room['next']['check_out'])) ?>
                        <?php else: ?>
                            Бронирований нет
                        <?php endif; ?>
                    </p>
                    <?php if ($room['status_code'] !== 'occupied'): ?>
                        <form method="post" action="/admin/?page=room_status">
                            <?= csrf_field() ?>
                            <input type="hidden" name="room_id" value="<?= e($room['id']) ?>">
                            <select name="status">
                                <?php foreach ($statuses as $status): ?>
                                    <?php if ($status['code'] !== 'occupied'): ?>
                                        <option value="<?= e($status['code']) ?>"<?= $status['code'] === $room['status_code'] ? ' selected' : '' ?>><?= e($status['name']) ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit">Сохранить</button>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endforeach; ?>
