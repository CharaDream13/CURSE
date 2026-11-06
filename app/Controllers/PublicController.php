<?php

class PublicController extends Controller
{
    public function home(): void
    {
        $this->view('pages/home', ['title' => 'Главная', 'categories' => Category::all()], 'public');
    }

    public function rooms(): void
    {
        $this->view('pages/rooms', ['title' => 'Номера', 'categories' => Category::all()], 'public');
    }

    public function booking(): void
    {
        $old = [
            'full_name' => '', 'phone' => '', 'email' => '', 'guests' => '1',
            'category_id' => query('category'), 'check_in' => '', 'check_out' => '',
        ];

        if ($this->isPost()) {
            csrf_check();
            foreach ($old as $key => $value) {
                $old[$key] = post($key);
            }
            try {
                BookingService::checkGuest($old['full_name'], $old['phone'], $old['email']);
                [, $number] = BookingService::create($old, null);
                redirect('/?page=booking_done&number=' . urlencode($number));
            } catch (InvalidArgumentException $e) {
                flash($e->getMessage(), 'error');
            }
        }

        $this->view('pages/booking', [
            'title' => 'Бронирование',
            'categories' => Category::all(),
            'old' => $old,
        ], 'public');
    }

    public function bookingDone(): void
    {
        $number = query('number');
        if (!preg_match('/^BK-\d{4}-\d{4,}$/', $number)) {
            redirect('/?page=booking');
        }
        $this->view('pages/booking_done', ['title' => 'Заявка принята', 'number' => $number], 'public');
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->view('pages/404', ['title' => 'Страница не найдена'], 'public');
    }
}
