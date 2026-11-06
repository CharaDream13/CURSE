<?php

class BookingController extends Controller
{
    public function index(): void
    {
        $statuses = Booking::statuses();
        $selected = query('status');
        if (!in_array($selected, array_column($statuses, 'code'), true)) {
            $selected = '';
        }

        $this->view('admin/bookings', [
            'title' => 'Бронирования',
            'bookings' => Booking::search($selected),
            'statuses' => $statuses,
            'selected' => $selected,
        ]);
    }

    public function create(): void
    {
        $old = [
            'guest_id' => '', 'full_name' => '', 'phone' => '', 'email' => '',
            'category_id' => '', 'room_id' => '', 'guests' => '1', 'check_in' => '', 'check_out' => '',
        ];

        if ($this->isPost()) {
            csrf_check();
            foreach ($old as $key => $value) {
                $old[$key] = post($key);
            }
            try {
                [, $number] = BookingService::create($old, $this->user['id']);
                flash('Бронирование ' . $number . ' создано');
                redirect('/admin/?page=bookings');
            } catch (InvalidArgumentException $e) {
                flash($e->getMessage(), 'error');
            }
        }

        $this->view('admin/booking_new', [
            'title' => 'Новое бронирование',
            'old' => $old,
            'guests' => Guest::all(),
            'categories' => Category::all(),
            'rooms' => Room::all(),
        ]);
    }

    public function edit(): void
    {
        $id = (int) query('id');
        $booking = Booking::find($id);
        if (!$booking) {
            redirect('/admin/?page=bookings');
        }

        if ($this->isPost()) {
            csrf_check();
            try {
                BookingService::update($id, [
                    'check_in' => post('check_in'),
                    'check_out' => post('check_out'),
                    'room_id' => post('room_id'),
                ], $this->user['id']);
                flash('Бронирование обновлено');
                redirect('/admin/?page=bookings');
            } catch (InvalidArgumentException $e) {
                flash($e->getMessage(), 'error');
                $booking['check_in'] = post('check_in');
                $booking['check_out'] = post('check_out');
                $booking['room_id'] = post('room_id');
            }
        }

        $rooms = array_filter(Room::all(), fn ($room) => (int) $room['category_id'] === (int) $booking['category_id']);
        $this->view('admin/booking_edit', [
            'title' => 'Изменение бронирования',
            'booking' => $booking,
            'rooms' => $rooms,
        ]);
    }

    public function action(): void
    {
        $this->requirePost();
        try {
            BookingService::transition((int) post('id'), post('action'), $this->user['id']);
            flash('Готово');
        } catch (InvalidArgumentException $e) {
            flash($e->getMessage(), 'error');
        }
        redirect('/admin/?page=bookings');
    }
}
