<?php

class Room
{
    public static function all(): array
    {
        return BDHelper::all(
            'SELECT r.id, r.room_number, r.status_code, s.name AS status_name, f.floor_number,
                    c.id AS category_id, c.name AS category_name, c.capacity
             FROM rooms r
             JOIN floors f ON f.id = r.floor_id
             JOIN room_categories c ON c.id = r.category_id
             JOIN room_statuses s ON s.code = r.status_code
             ORDER BY r.room_number'
        );
    }

    public static function find(int $id, bool $lock = false): ?array
    {
        return BDHelper::one('SELECT * FROM rooms WHERE id = ?' . ($lock ? ' FOR UPDATE' : ''), [$id]);
    }

    public static function statuses(): array
    {
        return BDHelper::all('SELECT code, name FROM room_statuses ORDER BY code');
    }

    public static function setStatus(int $id, string $code): void
    {
        BDHelper::run('UPDATE rooms SET status_code = ? WHERE id = ?', [$code, $id]);
    }

    public static function counts(): array
    {
        $counts = [];
        foreach (BDHelper::all('SELECT status_code, COUNT(*) AS total FROM rooms GROUP BY status_code') as $row) {
            $counts[$row['status_code']] = (int) $row['total'];
        }
        return $counts;
    }

    public static function isFree(int $roomId, string $in, string $out, int $excludeBookingId = 0): bool
    {
        $count = BDHelper::value(
            "SELECT COUNT(*) FROM bookings
             WHERE room_id = ? AND status_code IN ('confirmed', 'checked_in')
               AND check_in < ? AND check_out > ? AND id <> ?",
            [$roomId, $out, $in, $excludeBookingId]
        );
        return (int) $count === 0;
    }

    public static function freeForPeriod(int $categoryId, string $in, string $out): array
    {
        return BDHelper::all(
            "SELECT r.id, r.room_number FROM rooms r
             WHERE r.category_id = ? AND r.status_code <> 'maintenance'
               AND NOT EXISTS (
                   SELECT 1 FROM bookings b
                   WHERE b.room_id = r.id AND b.status_code IN ('confirmed', 'checked_in')
                     AND b.check_in < ? AND b.check_out > ?
               )
             ORDER BY r.room_number",
            [$categoryId, $out, $in]
        );
    }
}
