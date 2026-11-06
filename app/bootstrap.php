<?php

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(function (string $class): void {
    foreach (['Controllers', 'Models', 'Services', 'Helpers', 'Middleware'] as $dir) {
        $file = BASE_PATH . '/app/' . $dir . '/' . $class . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

require BASE_PATH . '/app/Helpers/functions.php';

date_default_timezone_set('Europe/Samara');
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/error.log');

set_exception_handler(function (Throwable $e): void {
    error_log($e->getMessage());
    http_response_code(500);
    echo 'Ошибка сервера. Попробуйте позже.';
});

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']),
]);
session_start();
