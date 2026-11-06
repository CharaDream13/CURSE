<?php

require __DIR__ . '/../app/bootstrap.php';

$controller = new AuthController();

if (query('action') === 'logout') {
    $controller->logout();
} else {
    $controller->login();
}
