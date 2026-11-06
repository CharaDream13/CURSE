<?php

class GuestController extends Controller
{
    public function index(): void
    {
        $old = ['full_name' => '', 'phone' => '', 'email' => ''];

        if ($this->isPost()) {
            csrf_check();
            foreach ($old as $key => $value) {
                $old[$key] = post($key);
            }
            try {
                BookingService::checkGuest($old['full_name'], $old['phone'], $old['email']);
                $id = Guest::create($old['full_name'], $old['phone'], $old['email']);
                Audit::log($this->user['id'], 'guest_create', 'guest', $id);
                flash('Гость добавлен');
                redirect('/admin/?page=guests');
            } catch (InvalidArgumentException $e) {
                flash($e->getMessage(), 'error');
            }
        }

        $this->view('admin/guests', ['title' => 'Гости', 'guests' => Guest::all(), 'old' => $old]);
    }

    public function show(): void
    {
        $guest = Guest::find((int) query('id'));
        if (!$guest) {
            redirect('/admin/?page=guests');
        }
        $this->view('admin/guest', [
            'title' => $guest['full_name'],
            'guest' => $guest,
            'bookings' => Booking::byGuest((int) $guest['id']),
        ]);
    }
}
