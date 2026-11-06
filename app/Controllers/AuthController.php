<?php

class AuthController extends Controller
{
    public function login(): void
    {
        if (!empty($_SESSION['user_id'])) {
            redirect('/admin/');
        }

        $error = null;
        $username = '';
        if ($this->isPost()) {
            csrf_check();
            $username = post('username');
            $error = AuthService::login($username, (string) ($_POST['password'] ?? ''));
            if ($error === null) {
                redirect('/admin/');
            }
        }

        $this->view('pages/login', ['title' => 'Вход', 'error' => $error, 'username' => $username], 'public');
    }

    public function logout(): void
    {
        if ($this->isPost()) {
            csrf_check();
            AuthService::logout();
        }
        redirect('/login.php');
    }
}
