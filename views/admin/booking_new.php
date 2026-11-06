<h1>Новое бронирование</h1>
<section class="panel">
    <form method="post" action="/admin/?page=booking_new" data-dates>
        <?= csrf_field() ?>
        <label>Гость
            <select name="guest_id">
                <option value="">Новый гость</option>
                <?php foreach ($guests as $guest): ?>
                    <option value="<?= e($guest['id']) ?>"<?= (string) $old['guest_id'] === (string) $guest['id'] ? ' selected' : '' ?>>
                        <?= e($guest['full_name']) ?>, <?= e($guest['phone']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <p>Для нового гостя заполните поля ниже.</p>
        <label>ФИО
            <input type="text" name="full_name" maxlength="150" value="<?= e($old['full_name']) ?>">
        </label>
        <div class="form-row">
            <label>Телефон
                <input type="tel" name="phone" maxlength="30" value="<?= e($old['phone']) ?>">
            </label>
            <label>Email
                <input type="email" name="email" maxlength="150" value="<?= e($old['email']) ?>">
            </label>
        </div>
        <div class="form-row">
            <label>Категория
                <select name="category_id" required data-category-select="room">
                    <option value="">Выберите</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= e($category['id']) ?>"<?= (string) $old['category_id'] === (string) $category['id'] ? ' selected' : '' ?>>
                            <?= e($category['name']) ?><?= $category['price'] !== null ? ', ' . e(money($category['price'])) : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Номер
                <select name="room_id" id="room">
                    <option value="">Назначить позже</option>
                    <?php foreach ($rooms as $room): ?>
                        <?php if ($room['status_code'] !== 'maintenance'): ?>
                            <option value="<?= e($room['id']) ?>" data-category="<?= e($room['category_id']) ?>"<?= (string) $old['room_id'] === (string) $room['id'] ? ' selected' : '' ?>>
                                <?= e($room['room_number']) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Число гостей
                <input type="number" name="guests" min="1" max="10" required value="<?= e($old['guests']) ?>">
            </label>
        </div>
        <div class="form-row">
            <label>Дата заезда
                <input type="date" name="check_in" min="<?= e(date('Y-m-d')) ?>" required value="<?= e($old['check_in']) ?>">
            </label>
            <label>Дата выезда
                <input type="date" name="check_out" min="<?= e(date('Y-m-d', strtotime('+1 day'))) ?>" required value="<?= e($old['check_out']) ?>">
            </label>
        </div>
        <button type="submit">Создать</button>
        <a class="button" href="/admin/?page=bookings">Назад</a>
    </form>
</section>
