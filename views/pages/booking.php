<h1>Заявка на бронирование</h1>
<section class="panel">
    <form method="post" action="/?page=booking" data-dates>
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
        <div class="form-row">
            <label>Категория номера
                <select name="category_id" required>
                    <option value="">Выберите</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= e($category['id']) ?>"<?= (string) $old['category_id'] === (string) $category['id'] ? ' selected' : '' ?>>
                            <?= e($category['name']) ?>, до <?= e($category['capacity']) ?> гостей<?= $category['price'] !== null ? ', ' . e(money($category['price'])) : '' ?>
                        </option>
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
        <button type="submit">Отправить заявку</button>
    </form>
</section>
