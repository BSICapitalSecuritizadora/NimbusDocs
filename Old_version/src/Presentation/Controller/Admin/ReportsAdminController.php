<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlSubmissionReportRepository;
use App\Support\Auth;
use App\Support\CsvResponse;

final class ReportsAdminController
{
    private MySqlSubmissionReportRepository $reports;

    public function __construct(private array $config)
    {
        $this->reports = new MySqlSubmissionReportRepository($config['pdo']);
    }

    public function submissionsReport(): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        // filtros padrões: últimos 30 dias
        $defaultFrom = (new \DateTimeImmutable('-30 days'))->format('Y-m-d');
        $defaultTo   = (new \DateTimeImmutable('today'))->format('Y-m-d');

        $filters = [
            'status'    => $_GET['status']    ?? '',
            'email'     => $_GET['email']     ?? '',
            // Usa null coalescing para evitar warnings quando a chave não existe
            'from_date' => $_GET['from_date'] ?? $defaultFrom,
            'to_date'   => $_GET['to_date']   ?? $defaultTo,
        ];

        $kpis        = $this->reports->kpis($filters);
        $byDay       = $this->reports->byDay($filters);
        $ranking     = $this->reports->rankingUsers($filters);
        $submissions = $this->reports->listSubmissions($filters);

        // preparar dados do gráfico por dia
        $labels = [];
        $values = [];
        foreach ($byDay as $row) {
            $labels[] = (new \DateTimeImmutable($row['day']))->format('d/m');
            $values[] = (int)$row['total'];
        }

        $pageTitle   = 'Relatório de submissões';
        $contentView = __DIR__ . '/../../View/admin/reports/submissions.php';

        $viewData = [
            'filters'     => $filters,
            'kpis'        => $kpis,
            'chartLabels' => $labels,
            'chartValues' => $values,
            'ranking'     => $ranking,
            'submissions' => $submissions,
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function submissionsReportExportCsv(): void
    {
        Auth::requireRole('ADMIN', 'SUPER_ADMIN');

        $filters = [
            'status'   => $_GET['status']    ?? '',
            'email'    => $_GET['email']     ?? '',
            'from_date' => $_GET['from_date'] ?? '',
            'to_date'  => $_GET['to_date']   ?? '',
        ];

        $rows = $this->reports->listSubmissions($filters);

        $headers = [
            'ID',
            'Usuário',
            'E-mail',
            'Título',
            'Status',
            'Criado em',
            'Atualizado em',
        ];

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                $r['id'],
                $r['user_name']  ?? '',
                $r['user_email'] ?? '',
                $r['title']      ?? '',
                $r['status']     ?? '',
                $r['created_at'] ?? '',
                $r['updated_at'] ?? '',
            ];
        }

        CsvResponse::send('relatorio_submissoes', $headers, $data);
    }
}
