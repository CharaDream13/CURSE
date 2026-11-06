# Гостинец

Веб-сайт гостиницы на PHP 8.0–8.1, MySQL, HTML5, CSS3 и JavaScript.

## Запуск

1. Выполнить по порядку в MySQL: `database/database.sql`, `database/data.sql`, `database/users.sql`.
2. Параметры подключения задаются в `config/database.php` или переменными окружения `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`.
3. Запустить сервер из корня проекта: `php -S localhost:8000 -t public`
4. Открыть `http://localhost:8000`

Корень веб-сервера — каталог `public`.

## Учётные записи

| Логин   | Пароль     | Роль         |
|---------|------------|--------------|
| admin   | admin123   | Администратор |
| manager | manager123 | Управляющий  |
| staff   | staff123   | Персонал     |

Пароли нужно сменить перед реальным использованием.
