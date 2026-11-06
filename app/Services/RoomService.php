<?php

class RoomService
{
    public static function setStatus(int $roomId, string $code, int $userId): void
    {
        $room = Room::find($roomId);
        if (!$room) {
            throw new InvalidArgumentException('Номер не найден');
        }
        $codes = array_column(Room::statuses(), 'code');
        if (!in_array($code, $codes, true)) {
            throw new InvalidArgumentException('Неизвестный статус');
        }
        if ($code === 'occupied') {
            throw new InvalidArgumentException('Статус «Занят» устанавливается при заселении гостя');
        }
        if ($room['status_code'] === 'occupied') {
            throw new InvalidArgumentException('Номер занят. Статус изменится после выезда гостя');
        }

        Room::setStatus($roomId, $code);
        Audit::log($userId, 'room_status', 'room', $roomId, $room['room_number'] . ': ' . $code);
    }
}
