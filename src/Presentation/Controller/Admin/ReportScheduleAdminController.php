<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlReportScheduleRepository;
use App\Support\Auth;

final class ReportScheduleAdminController
{
    private MySqlReportScheduleRepository $scheduleRepo;

    public function __construct(private array $config)
    {
        $this->scheduleRepo = new MySqlReportScheduleRepository($config['pdo']);
    }

    private function requireAdmin(): array
    {
        return Auth::requireAdmin();
    }

    public function index(): void
    {
        $admin = $this->requireAdmin();
        $schedules = $this->scheduleRepo->findAll();

        $pageTitle = 'Relatórios Agendados';
        $contentView = __DIR__ . '/../../View/admin/reports/schedules/index.php';

        $viewData = [
            'admin' => $admin,
            'schedules' => $schedules,
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function store(): void
    {
        $admin = $this->requireAdmin();

        $reportType = $_POST['report_type'] ?? '';
        $frequency = $_POST['frequency'] ?? 'WEEKLY';
        $emailsRaw = $_POST['recipient_emails'] ?? '';

        if (!$reportType || !$emailsRaw) {
            $_SESSION['flash_error'] = 'Preencha todos os campos obrigatórios.';
            header('Location: /admin/reports/schedules');
            exit;
        }

        // Clean and validate emails
        $emailList = array_map('trim', explode(',', $emailsRaw));
        $validEmails = array_filter($emailList, fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL));

        if (empty($validEmails)) {
            $_SESSION['flash_error'] = 'Forneça pelo menos um email válido.';
            header('Location: /admin/reports/schedules');
            exit;
        }

        // Calculate next run date (defaulting to next hour for safety if today, or properly based on frequency)
        // Simplification for MVP: run it tomorrow at 08:00 AM. In background script we'll update it properly.
        $nextRunAt = date('Y-m-d 08:00:00', strtotime('tomorrow'));

        $this->scheduleRepo->create([
            'report_type' => $reportType,
            'frequency' => $frequency,
            'recipient_emails' => json_encode(array_values($validEmails), JSON_UNESCAPED_UNICODE),
            'next_run_at' => $nextRunAt,
            'is_active' => 1,
        ]);

        $_SESSION['flash_success'] = 'Agendamento criado com sucesso!';
        header('Location: /admin/reports/schedules');
        exit;
    }

    public function toggleActive(int $id): void
    {
        $admin = $this->requireAdmin();

        $schedule = $this->scheduleRepo->findById($id);
        if ($schedule) {
            $newStatus = $schedule['is_active'] ? false : true;
            $this->scheduleRepo->toggleActive($id, $newStatus);
            $_SESSION['flash_success'] = 'Status do agendamento alterado.';
        }

        header('Location: /admin/reports/schedules');
        exit;
    }

    public function delete(int $id): void
    {
        $admin = $this->requireAdmin();

        $schedule = $this->scheduleRepo->findById($id);
        if ($schedule) {
            $this->scheduleRepo->delete($id);
            $_SESSION['flash_success'] = 'Agendamento excluído com sucesso.';
        }

        header('Location: /admin/reports/schedules');
        exit;
    }
}
