<h1>Сотрудники</h1>

<div class="table-wrap">
    <table>
        <tr><th>ФИО</th><th>Должность</th><th>Телефон</th><th>Логин</th><th>Роль</th><th>Состояние</th><th>Действие</th></tr>
        <?php foreach ($employees as $employee): ?>
            <tr>
                <td><?= e($employee['full_name']) ?></td>
                <td><?= e($employee['position']) ?></td>
                <td><?= e($employee['phone']) ?></td>
                <td><?= e($employee['username'] ?? '—') ?></td>
                <td><?= $employee['role'] ? e(role_name($employee['role'])) : '—' ?></td>
                <td><?= $employee['is_active'] ? 'Активен' : 'Заблокирован' ?></td>
                <td>
                    <?php if ((int) $employee['user_id'] !== $user['id']): ?>
                        <?= action_form(
                            '/admin/?page=employee_toggle',
                            ['id' => $employee['id']],
                            $employee['is_active'] ? 'Заблокировать' : 'Разблокировать',
                            $employee['is_active'] ? 'Заблокировать сотрудника?' : ''
                        ) ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<section class="panel">
    <h2>Новый сотрудник</h2>
    <form method="post" action="/admin/?page=employees">
        <?= csrf_field() ?>
        <div class="form-row">
            <label>ФИО
                <input type="text" name="full_name" maxlength="150" required value="<?= e($old['full_name']) ?>">
            </label>
            <label>Должность
                <input type="text" name="position" maxlength="100" required value="<?= e($old['position']) ?>">
            </label>
        </div>
        <div class="form-row">
            <label>Телефон
                <input type="tel" name="phone" maxlength="30" value="<?= e($old['phone']) ?>">
            </label>
            <label>Email
                <input type="email" name="email" maxlength="150" value="<?= e($old['email']) ?>">
            </label>
            <label>Дата приёма
                <input type="date" name="hire_date" value="<?= e($old['hire_date']) ?>">
            </label>
        </div>
        <div class="form-row">
            <label>Логин
                <input type="text" name="username" maxlength="60" required value="<?= e($old['username']) ?>">
            </label>
            <label>Пароль
                <input type="password" name="password" minlength="8" required>
            </label>
            <label>Роль
                <select name="role">
                    <?php foreach (['administrator', 'manager', 'housekeeping'] as $role): ?>
                        <option value="<?= e($role) ?>"<?= $old['role'] === $role ? ' selected' : '' ?>><?= e(role_name($role)) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <button type="submit">Добавить</button>
    </form>
</section>
