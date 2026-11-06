<?php

class AuthMiddleware
{
    public static function user(): array
    {
        if (empty($_SESSION['user_id']) || !Employee::isUserActive((int) $_SESSION['user_id'])) {
            AuthService::clearSession();
            redirect('/login.php');
        }
        return [
            'id' => (int) $_SESSION['user_id'],
            'role' => $_SESSION['role'],
            'username' => $_SESSION['username'],
            'name' => $_SESSION['name'],
        ];
    }

    public static function allows(array $user, array $roles): bool
    {
        return $roles === [] || in_array($user['role'], $roles, true);
    }
}
