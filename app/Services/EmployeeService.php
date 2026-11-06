<?php

class EmployeeService
{
    public static function create(array $data, int $userId): void
    {
        if ($data['full_name'] === '' || mb_strlen($data['full_name']) > 150) {
            throw new InvalidArgumentException('Укажите ФИО (до 150 символов)');
        }
        if ($data['position'] === '' || mb_strlen($data['position']) > 100) {
            throw new InvalidArgumentException('Укажите должность (до 100 символов)');
        }
        if ($data['phone'] !== '' && !preg_match('/^\+?[0-9\s\-()]{6,30}$/', $data['phone'])) {
            throw new InvalidArgumentException('Укажите корректный телефон');
        }
        if ($data['email'] !== '' && (mb_strlen($data['email']) > 150 || !filter_var($data['email'], FILTER_VALIDATE_EMAIL))) {
            throw new InvalidArgumentException('Укажите корректный email');
        }
        if ($data['hire_date'] !== '' && !is_valid_date($data['hire_date'])) {
            throw new InvalidArgumentException('Укажите корректную дату приёма');
        }
        if (!preg_match('/^[A-Za-z0-9_.-]{3,60}$/', $data['username'])) {
            throw new InvalidArgumentException('Логин: 3–60 символов, латиница, цифры и _ . -');
        }
        if (mb_strlen($data['password']) < 8) {
            throw new InvalidArgumentException('Пароль должен содержать не менее 8 символов');
        }
        if (!in_array($data['role'], ['administrator', 'manager', 'housekeeping'], true)) {
            throw new InvalidArgumentException('Выберите роль');
        }
        if (Employee::usernameExists($data['username'])) {
            throw new InvalidArgumentException('Такой логин уже существует');
        }
        if ($data['email'] !== '' && Employee::emailExists($data['email'])) {
            throw new InvalidArgumentException('Сотрудник с таким email уже существует');
        }

        $pdo = BDHelper::getConnection();
        $pdo->beginTransaction();
        try {
            $employeeId = Employee::create($data);
            Employee::createUser(
                $employeeId,
                $data['username'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['role']
            );
            Audit::log($userId, 'employee_create', 'employee', $employeeId, $data['username']);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function toggle(int $employeeId, int $userId): void
    {
        $employee = Employee::find($employeeId);
        if (!$employee) {
            throw new InvalidArgumentException('Сотрудник не найден');
        }
        if ($employee['user_id'] !== null && (int) $employee['user_id'] === $userId) {
            throw new InvalidArgumentException('Нельзя заблокировать собственную учётную запись');
        }

        $active = !(bool) $employee['is_active'];
        $pdo = BDHelper::getConnection();
        $pdo->beginTransaction();
        try {
            Employee::setActive($employeeId, $employee['user_id'] === null ? null : (int) $employee['user_id'], $active);
            Audit::log($userId, $active ? 'employee_unblock' : 'employee_block', 'employee', $employeeId);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
