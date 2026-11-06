<?php

class EmployeeController extends Controller
{
    public function index(): void
    {
        $old = [
            'full_name' => '', 'position' => '', 'phone' => '', 'email' => '', 'hire_date' => '',
            'username' => '', 'role' => 'housekeeping',
        ];

        if ($this->isPost()) {
            csrf_check();
            foreach ($old as $key => $value) {
                $old[$key] = post($key);
            }
            try {
                EmployeeService::create($old + ['password' => (string) ($_POST['password'] ?? '')], $this->user['id']);
                flash('Сотрудник добавлен');
                redirect('/admin/?page=employees');
            } catch (InvalidArgumentException $e) {
                flash($e->getMessage(), 'error');
            }
        }

        $this->view('admin/employees', ['title' => 'Сотрудники', 'employees' => Employee::all(), 'old' => $old]);
    }

    public function toggle(): void
    {
        $this->requirePost();
        try {
            EmployeeService::toggle((int) post('id'), $this->user['id']);
            flash('Состояние учётной записи изменено');
        } catch (InvalidArgumentException $e) {
            flash($e->getMessage(), 'error');
        }
        redirect('/admin/?page=employees');
    }
}
