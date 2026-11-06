<?php

class AuthService
{
    public static function login(string $username, string $password): ?string
    {
        if ($username === '' || $password === '') {
            return 'Введите логин и пароль';
        }
        if (Audit::failedLogins($username) >= 5) {
            return 'Слишком много неудачных попыток. Повторите позже';
        }

        $user = Employee::findForLogin($username);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            Audit::log(null, 'login_failed', null, null, $username);
            return 'Неверный логин или пароль';
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['full_name'];
        Audit::log((int) $user['id'], 'login');
        return null;
    }

    public static function logout(): void
    {
        if (!empty($_SESSION['user_id'])) {
            Audit::log((int) $_SESSION['user_id'], 'logout');
        }
        self::clearSession();
    }

    public static function clearSession(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}
