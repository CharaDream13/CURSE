<?php

require __DIR__ . '/../../app/bootstrap.php';

$user = AuthMiddleware::user();

$staff = ['administrator', 'manager'];
$manager = ['manager'];

$routes = [
    'dashboard' => ['DashboardController', 'index', []],
    'rooms' => ['RoomController', 'index', []],
    'room_status' => ['RoomController', 'status', []],
    'bookings' => ['BookingController', 'index', $staff],
    'booking_new' => ['BookingController', 'create', $staff],
    'booking_edit' => ['BookingController', 'edit', $staff],
    'booking_action' => ['BookingController', 'action', $staff],
    'guests' => ['GuestController', 'index', $staff],
    'guest' => ['GuestController', 'show', $staff],
    'tariffs' => ['TariffController', 'index', $manager],
    'tariff_update' => ['TariffController', 'update', $manager],
    'employees' => ['EmployeeController', 'index', $manager],
    'employee_toggle' => ['EmployeeController', 'toggle', $manager],
    'audit' => ['AuditController', 'index', $staff],
];

$page = query('page') ?: 'dashboard';

if (!isset($routes[$page])) {
    http_response_code(404);
    View::render('admin/message', ['title' => 'Не найдено', 'text' => 'Страница не найдена', 'user' => $user], 'admin');
    exit;
}

[$class, $method, $roles] = $routes[$page];

if (!AuthMiddleware::allows($user, $roles)) {
    http_response_code(403);
    View::render('admin/message', ['title' => 'Доступ запрещён', 'text' => 'У вас нет прав для этого раздела', 'user' => $user], 'admin');
    exit;
}

(new $class($user))->$method();
