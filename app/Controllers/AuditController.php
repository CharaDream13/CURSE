<?php

class AuditController extends Controller
{
    public function index(): void
    {
        $this->view('admin/audit', ['title' => 'Журнал действий', 'logs' => Audit::recent(200)]);
    }
}
