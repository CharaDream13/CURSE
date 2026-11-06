<?php

class Employee
{
    public static function all(): array
    {
        return BDHelper::all(
            'SELECT e.id, e.full_name, e.position, e.phone, e.email, e.hire_date, e.is_active,
                    u.id AS user_id, u.username, u.role, u.is_active AS user_active
             FROM employees e
             LEFT JOIN users u ON u.employee_id = e.id
             ORDER BY e.full_name'
        );
    }

    public static function find(int $id): ?array
    {
        return BDHelper::one(
            'SELECT e.id, e.is_active, u.id AS user_id
             FROM employees e
             LEFT JOIN users u ON u.employee_id = e.id
             WHERE e.id = ?',
            [$id]
        );
    }

    public static function findForLogin(string $username): ?array
    {
        return BDHelper::one(
            'SELECT u.id, u.username, u.password_hash, u.role, e.full_name
             FROM users u
             JOIN employees e ON e.id = u.employee_id
             WHERE u.username = ? AND u.is_active = 1 AND e.is_active = 1',
            [$username]
        );
    }

    public static function isUserActive(int $userId): bool
    {
        return (bool) BDHelper::value(
            'SELECT COUNT(*) FROM users u JOIN employees e ON e.id = u.employee_id
             WHERE u.id = ? AND u.is_active = 1 AND e.is_active = 1',
            [$userId]
        );
    }

    public static function usernameExists(string $username): bool
    {
        return (bool) BDHelper::value('SELECT COUNT(*) FROM users WHERE username = ?', [$username]);
    }

    public static function emailExists(string $email): bool
    {
        return (bool) BDHelper::value('SELECT COUNT(*) FROM employees WHERE email = ?', [$email]);
    }

    public static function create(array $data): int
    {
        BDHelper::run(
            'INSERT INTO employees (full_name, position, phone, email, hire_date) VALUES (?, ?, ?, ?, ?)',
            [
                $data['full_name'], $data['position'],
                $data['phone'] === '' ? null : $data['phone'],
                $data['email'] === '' ? null : $data['email'],
                $data['hire_date'] === '' ? null : $data['hire_date'],
            ]
        );
        return BDHelper::lastId();
    }

    public static function createUser(int $employeeId, string $username, string $hash, string $role): int
    {
        BDHelper::run(
            'INSERT INTO users (employee_id, username, password_hash, role) VALUES (?, ?, ?, ?)',
            [$employeeId, $username, $hash, $role]
        );
        return BDHelper::lastId();
    }

    public static function setActive(int $employeeId, ?int $userId, bool $active): void
    {
        BDHelper::run('UPDATE employees SET is_active = ? WHERE id = ?', [(int) $active, $employeeId]);
        if ($userId) {
            BDHelper::run('UPDATE users SET is_active = ? WHERE id = ?', [(int) $active, $userId]);
        }
    }
}
