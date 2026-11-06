<?php

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view('admin/dashboard', [
            'title' => 'Панель управления',
            'rooms' => Room::counts(),
            'bookings' => Booking::counts(),
            'upcoming' => Booking::upcoming(10),
        ]);
    }
}
