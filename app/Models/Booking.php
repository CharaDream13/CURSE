<?php

class Booking
{
    private const SELECT = 'SELECT b.*, g.full_name AS guest_name, g.phone AS guest_phone,
            r.room_number, c.name AS category_name, c.capacity, s.name AS status_name
        FROM bookings b
        JOIN guests g ON g.id = b.guest_id
        LEFT JOIN rooms r ON r.id = b.room_id
        JOIN room_categories c ON c.id = b.category_id
        JOIN booking_statuses s ON s.code = b.status_code';

    public static function search(string $status): array
    {
        if ($status !== '') {
            return BDHelper::all(self::SELECT . ' WHERE b.status_code = ? ORDER BY b.check_in DESC, b.id DESC', [$status]);
        }
        return BDHelper::all(self::SELECT . ' ORDER BY b.check_in DESC, b.id DESC');
    }

    public static function find(int $id): ?array
    {
        return BDHelper::one(self::SELECT . ' WHERE b.id = ?', [$id]);
    }

    public static function lock(int $id): ?array
    {
        return BDHelper::one('SELECT * FROM bookings WHERE id = ? FOR UPDATE', [$id]);
    }

    public static function byGuest(int $guestId): array
    {
        return BDHelper::all(self::SELECT . ' WHERE b.guest_id = ? ORDER BY b.check_in DESC', [$guestId]);
    }

    public static function upcoming(int $limit): array
    {
        return BDHelper::all(
            self::SELECT . " WHERE b.status_code IN ('pending', 'confirmed') AND b.check_in >= CURDATE()
             ORDER BY b.check_in LIMIT " . (int) $limit
        );
    }

    public static function nextByRoom(): array
    {
        $rows = BDHelper::all(
            "SELECT room_id, booking_number, check_in, check_out, status_code FROM bookings
             WHERE room_id IS NOT NULL AND status_code IN ('confirmed', 'checked_in') AND check_out > CURDATE()
             ORDER BY check_in"
        );
        $map = [];
        foreach ($rows as $row) {
            if (!isset($map[$row['room_id']])) {
                $map[$row['room_id']] = $row;
            }
        }
        return $map;
    }

    public static function counts(): array
    {
        $counts = [];
        foreach (BDHelper::all('SELECT status_code, COUNT(*) AS total FROM bookings GROUP BY status_code') as $row) {
            $counts[$row['status_code']] = (int) $row['total'];
        }
        return $counts;
    }

    public static function statuses(): array
    {
        return BDHelper::all('SELECT code, name FROM booking_statuses ORDER BY code');
    }

    public static function insert(array $data): int
    {
        BDHelper::run(
            'INSERT INTO bookings
             (booking_number, guest_id, room_id, category_id, check_in, check_out,
              price_per_night, total_price, status_code, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['booking_number'], $data['guest_id'], $data['room_id'], $data['category_id'],
                $data['check_in'], $data['check_out'], $data['price_per_night'], $data['total_price'],
                $data['status_code'], $data['created_by'],
            ]
        );
        return BDHelper::lastId();
    }

    public static function update(int $id, array $fields): void
    {
        $allowed = ['booking_number', 'room_id', 'check_in', 'check_out', 'total_price', 'status_code'];
        $set = [];
        $params = [];
        foreach ($fields as $name => $value) {
            if (in_array($name, $allowed, true)) {
                $set[] = $name . ' = ?';
                $params[] = $value;
            }
        }
        $params[] = $id;
        BDHelper::run('UPDATE bookings SET ' . implode(', ', $set) . ' WHERE id = ?', $params);
    }
}
