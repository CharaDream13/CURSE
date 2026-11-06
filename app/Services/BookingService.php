<?php

class BookingService
{
    public static function create(array $data, ?int $userId): array
    {
        $in = $data['check_in'];
        $out = $data['check_out'];
        self::checkDates($in, $out, true);

        $category = Category::find((int) $data['category_id']);
        if (!$category || $category['price'] === null) {
            throw new InvalidArgumentException('Выберите категорию номера');
        }

        $guestsCount = (int) ($data['guests'] ?? 1);
        if ($guestsCount < 1 || $guestsCount > (int) $category['capacity']) {
            throw new InvalidArgumentException('Число гостей не подходит для выбранной категории');
        }

        $guestId = (int) ($data['guest_id'] ?? 0);
        if ($guestId > 0) {
            if (!Guest::find($guestId)) {
                throw new InvalidArgumentException('Гость не найден');
            }
        } else {
            self::checkGuest($data['full_name'], $data['phone'], $data['email']);
        }

        $roomId = (int) ($data['room_id'] ?? 0);
        $pdo = BDHelper::getConnection();
        $pdo->beginTransaction();
        try {
            if ($roomId > 0) {
                $room = self::lockRoom($roomId);
                if ((int) $room['category_id'] !== (int) $category['id']) {
                    throw new InvalidArgumentException('Номер не относится к выбранной категории');
                }
                self::checkRoomFree($room, $in, $out, 0);
            } elseif (!Room::freeForPeriod((int) $category['id'], $in, $out)) {
                throw new InvalidArgumentException('На выбранные даты нет свободных номеров этой категории');
            }

            if ($guestId === 0) {
                $guestId = Guest::findExisting($data['full_name'], $data['phone'])
                    ?? Guest::create($data['full_name'], $data['phone'], $data['email']);
            }

            $price = (float) $category['price'];
            $id = Booking::insert([
                'booking_number' => 'TMP-' . bin2hex(random_bytes(8)),
                'guest_id' => $guestId,
                'room_id' => $roomId > 0 ? $roomId : null,
                'category_id' => (int) $category['id'],
                'check_in' => $in,
                'check_out' => $out,
                'price_per_night' => $price,
                'total_price' => round($price * self::nights($in, $out), 2),
                'status_code' => $roomId > 0 ? 'confirmed' : 'pending',
                'created_by' => $userId,
            ]);
            $number = sprintf('BK-%s-%04d', date('Y'), $id);
            Booking::update($id, ['booking_number' => $number]);
            Audit::log($userId, 'booking_create', 'booking', $id, $number);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        return [$id, $number];
    }

    public static function update(int $id, array $data, int $userId): void
    {
        $in = $data['check_in'];
        $out = $data['check_out'];
        self::checkDates($in, $out, false);
        $roomId = (int) ($data['room_id'] ?? 0);

        $pdo = BDHelper::getConnection();
        $pdo->beginTransaction();
        try {
            $booking = Booking::lock($id);
            if (!$booking) {
                throw new InvalidArgumentException('Бронирование не найдено');
            }
            if (!in_array($booking['status_code'], ['pending', 'confirmed'], true)) {
                throw new InvalidArgumentException('Это бронирование нельзя изменить');
            }

            if ($roomId > 0) {
                $room = self::lockRoom($roomId);
                if ((int) $room['category_id'] !== (int) $booking['category_id']) {
                    throw new InvalidArgumentException('Номер не относится к категории бронирования');
                }
                self::checkRoomFree($room, $in, $out, $id);
            } elseif ($booking['status_code'] === 'confirmed') {
                throw new InvalidArgumentException('Для подтверждённого бронирования нужен номер');
            }

            Booking::update($id, [
                'check_in' => $in,
                'check_out' => $out,
                'room_id' => $roomId > 0 ? $roomId : null,
                'total_price' => round((float) $booking['price_per_night'] * self::nights($in, $out), 2),
            ]);
            Audit::log($userId, 'booking_update', 'booking', $id, $booking['booking_number']);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function transition(int $id, string $action, int $userId): void
    {
        $pdo = BDHelper::getConnection();
        $pdo->beginTransaction();
        try {
            $booking = Booking::lock($id);
            if (!$booking) {
                throw new InvalidArgumentException('Бронирование не найдено');
            }
            $status = $booking['status_code'];
            $roomId = (int) $booking['room_id'];

            if ($action === 'confirm') {
                if ($status !== 'pending') {
                    throw new InvalidArgumentException('Подтвердить можно только заявку в ожидании');
                }
                if ($roomId === 0) {
                    throw new InvalidArgumentException('Сначала назначьте номер');
                }
                self::checkRoomFree(self::lockRoom($roomId), $booking['check_in'], $booking['check_out'], $id);
                Booking::update($id, ['status_code' => 'confirmed']);
            } elseif ($action === 'cancel') {
                if (!in_array($status, ['pending', 'confirmed'], true)) {
                    throw new InvalidArgumentException('Это бронирование нельзя отменить');
                }
                Booking::update($id, ['status_code' => 'cancelled']);
            } elseif ($action === 'checkin') {
                if ($status !== 'confirmed') {
                    throw new InvalidArgumentException('Заселить можно только подтверждённое бронирование');
                }
                $room = self::lockRoom($roomId);
                if ($room['status_code'] !== 'available') {
                    throw new InvalidArgumentException('Номер ещё не готов к заселению');
                }
                Room::setStatus($roomId, 'occupied');
                Booking::update($id, ['status_code' => 'checked_in']);
            } elseif ($action === 'checkout') {
                if ($status !== 'checked_in') {
                    throw new InvalidArgumentException('Выселить можно только заселённого гостя');
                }
                Room::setStatus($roomId, 'cleaning');
                Booking::update($id, ['status_code' => 'checked_out']);
            } else {
                throw new InvalidArgumentException('Неизвестное действие');
            }

            Audit::log($userId, 'booking_' . $action, 'booking', $id, $booking['booking_number']);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    private static function checkDates(string $in, string $out, bool $futureOnly): void
    {
        if (!is_valid_date($in) || !is_valid_date($out)) {
            throw new InvalidArgumentException('Укажите корректные даты заезда и выезда');
        }
        if ($out <= $in) {
            throw new InvalidArgumentException('Дата выезда должна быть позже даты заезда');
        }
        if ($futureOnly && $in < date('Y-m-d')) {
            throw new InvalidArgumentException('Дата заезда не может быть в прошлом');
        }
        if (self::nights($in, $out) > 60) {
            throw new InvalidArgumentException('Максимальный срок проживания — 60 суток');
        }
    }

    public static function checkGuest(string $name, string $phone, string $email): void
    {
        if ($name === '' || mb_strlen($name) > 150) {
            throw new InvalidArgumentException('Укажите ФИО (до 150 символов)');
        }
        if (!preg_match('/^\+?[0-9\s\-()]{6,30}$/', $phone)) {
            throw new InvalidArgumentException('Укажите корректный телефон');
        }
        if ($email !== '' && (mb_strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL))) {
            throw new InvalidArgumentException('Укажите корректный email');
        }
    }

    private static function lockRoom(int $roomId): array
    {
        $room = Room::find($roomId, true);
        if (!$room) {
            throw new InvalidArgumentException('Номер не найден');
        }
        return $room;
    }

    private static function checkRoomFree(array $room, string $in, string $out, int $excludeId): void
    {
        if ($room['status_code'] === 'maintenance') {
            throw new InvalidArgumentException('Номер ' . $room['room_number'] . ' находится на обслуживании');
        }
        if (!Room::isFree((int) $room['id'], $in, $out, $excludeId)) {
            throw new InvalidArgumentException('Номер ' . $room['room_number'] . ' занят на выбранный период');
        }
    }

    private static function nights(string $in, string $out): int
    {
        return (new DateTime($in))->diff(new DateTime($out))->days;
    }
}
