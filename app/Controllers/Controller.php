<?php

abstract class Controller
{
    protected array $user;

    public function __construct(array $user = [])
    {
        $this->user = $user;
    }

    protected function view(string $name, array $data = [], string $layout = 'admin'): void
    {
        View::render($name, $data + ['user' => $this->user], $layout);
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function requirePost(): void
    {
        if (!$this->isPost()) {
            redirect('/admin/');
        }
        csrf_check();
    }
}
