<?php

class TariffController extends Controller
{
    public function index(): void
    {
        $this->view('admin/tariffs', [
            'title' => 'Тарифы',
            'categories' => Category::all(),
            'history' => Category::priceHistory(),
        ]);
    }

    public function update(): void
    {
        $this->requirePost();
        try {
            TariffService::change((int) post('category_id'), post('price'), $this->user['id']);
            flash('Тариф обновлён');
        } catch (InvalidArgumentException $e) {
            flash($e->getMessage(), 'error');
        }
        redirect('/admin/?page=tariffs');
    }
}
