<?php

class RoomController extends Controller
{
    public function index(): void
    {
        $next = Booking::nextByRoom();
        $floors = [];
        foreach (Room::all() as $room) {
            $room['next'] = $next[$room['id']] ?? null;
            $floors[$room['floor_number']][] = $room;
        }

        $this->view('admin/rooms', [
            'title' => 'Номерной фонд',
            'floors' => $floors,
            'statuses' => Room::statuses(),
        ]);
    }

    public function status(): void
    {
        $this->requirePost();
        try {
            RoomService::setStatus((int) post('room_id'), post('status'), $this->user['id']);
            flash('Статус номера обновлён');
        } catch (InvalidArgumentException $e) {
            flash($e->getMessage(), 'error');
        }
        redirect('/admin/?page=rooms');
    }
}
