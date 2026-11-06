<?php

class Guest
{
    public static function all(): array
    {
        return BDHelper::all(
            'SELECT g.id, g.full_name, g.phone, g.email, g.created_at,
                    (SELECT COUNT(*) FROM bookings b WHERE b.guest_id = g.id) AS bookings_count
             FROM guests g
             ORDER BY g.full_name'
        );
    }

    public static function find(int $id): ?array
    {
        return BDHelper::one('SELECT * FROM guests WHERE id = ?', [$id]);
    }

    public static function create(string $name, string $phone, string $email): int
    {
        BDHelper::run(
            'INSERT INTO guests (full_name, phone, email) VALUES (?, ?, ?)',
            [$name, $phone, $email === '' ? null : $email]
        );
        return BDHelper::lastId();
    }

    public static function findExisting(string $name, string $phone): ?int
    {
        $id = BDHelper::value('SELECT id FROM guests WHERE full_name = ? AND phone = ? LIMIT 1', [$name, $phone]);
        return $id ? (int) $id : null;
    }
}
