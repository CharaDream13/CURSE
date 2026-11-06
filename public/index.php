<?php

require __DIR__ . '/../app/bootstrap.php';

$controller = new PublicController();

switch (query('page') ?: 'home') {
    case 'home':
        $controller->home();
        break;
    case 'rooms':
        $controller->rooms();
        break;
    case 'booking':
        $controller->booking();
        break;
    case 'booking_done':
        $controller->bookingDone();
        break;
    default:
        $controller->notFound();
}
