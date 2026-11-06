<?php

class View
{
    public static function render(string $view, array $data = [], string $layout = 'public'): void
    {
        extract($data);
        ob_start();
        require BASE_PATH . '/views/' . $view . '.php';
        $content = ob_get_clean();
        require BASE_PATH . '/views/layouts/' . $layout . '.php';
    }
}
