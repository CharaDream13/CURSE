<?php

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function money($value): string
{
    return number_format((float) $value, 0, ',', ' ') . ' руб.';
}

function date_ru(?string $date): string
{
    return $date ? date('d.m.Y', strtotime($date)) : '—';
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function post(string $key): string
{
    return trim((string) ($_POST[$key] ?? ''));
}

function query(string $key): string
{
    return trim((string) ($_GET[$key] ?? ''));
}

function is_valid_date(string $value): bool
{
    $date = DateTime::createFromFormat('Y-m-d', $value);
    return $date && $date->format('Y-m-d') === $value;
}

function flash(string $text, string $type = 'info'): void
{
    $_SESSION['flash'][] = ['text' => $text, 'type' => $type];
}

function flash_pull(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

function csrf_field(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}

function csrf_check(): void
{
    $token = (string) ($_POST['csrf'] ?? '');
    if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(403);
        exit('Недействительный токен формы');
    }
}

function action_form(string $url, array $fields, string $label, string $confirm = ''): string
{
    $html = '<form method="post" action="' . e($url) . '" class="inline"';
    if ($confirm !== '') {
        $html .= ' data-confirm="' . e($confirm) . '"';
    }
    $html .= '>' . csrf_field();
    foreach ($fields as $name => $value) {
        $html .= '<input type="hidden" name="' . e($name) . '" value="' . e($value) . '">';
    }
    return $html . '<button type="submit">' . e($label) . '</button></form>';
}

function role_name(string $role): string
{
    $names = [
        'administrator' => 'Администратор',
        'manager' => 'Управляющий',
        'housekeeping' => 'Персонал',
    ];
    return $names[$role] ?? $role;
}
