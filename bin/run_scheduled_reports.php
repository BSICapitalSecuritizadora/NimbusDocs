<?php

declare(strict_types=1);

/**
 * NimbusDocs - Scheduled Reports Background Worker
 * This script is intended to be executed via crontab (e.g. hourly)
 * It sweeps the `report_schedules` table, generates the PDF/CSV, and emails recipients.
 */

// Basic CLI safeguards
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

require_once __DIR__ . '/../vendor/autoload.php';

// Initialize core services similar to bootstrap
$appConfig = require __DIR__ . '/../config/app.php';
$logger = $appConfig['logger'];

use App\Infrastructure\Persistence\MySqlReportScheduleRepository;
use App\Infrastructure\Persistence\MySqlPortalSubmissionRepository;
use App\Infrastructure\Service\MailService;
use Mpdf\Mpdf;

try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']};charset=utf8mb4",
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD'],
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

    $scheduleRepo = new MySqlReportScheduleRepository($pdo);
    $submissionRepo = new MySqlPortalSubmissionRepository($pdo);
    
    // Inject dependencies into MailService based on the existing pattern
    $mailService = new MailService($appConfig['mailer'], $appConfig['mail_logger']);

    $dueSchedules = $scheduleRepo->findDueSchedules();

    if (empty($dueSchedules)) {
        $logger->info("Scheduled Reports: No reports due to run at this time.");
        exit(0);
    }

    $logger->info(sprintf("Scheduled Reports: Found %d reports due.", count($dueSchedules)));

    foreach ($dueSchedules as $schedule) {
        $logger->info("Processing schedule ID: " . $schedule['id']);
        
        $recipients = json_decode($schedule['recipient_emails'], true);
        if (!$recipients || !is_array($recipients)) {
            $logger->warning("Skipping schedule ID {$schedule['id']}: No valid recipient emails.");
            continue;
        }

        $reportPath = '';
        $fileName = '';

        // Generate the Report Payload
        if ($schedule['report_type'] === 'submissions') {
            $logger->info("Generating Submissions Report...");
            
            // 1. Gather Data (simplified logic - total dump of the last 30 days or active ones)
            $filters = []; // You could add date logic here based on frequency
            $generator = $submissionRepo->getExportCursor($filters);
            $items = iterator_to_array($generator);

            // 2. Format HTML for PDF
            $html = "<h1>Relatório de Submissões</h1><p>Gerado em: " . date('d/m/Y H:i') . "</p>";
            $html .= "<table border='1' cellpadding='5' cellspacing='0' width='100%'>
                        <thead><tr><th>Cod</th><th>Status</th><th>Solicitante</th><th>Data</th></tr></thead>
                        <tbody>";
            foreach ($items as $item) {
                $html .= "<tr>
                            <td>{$item['reference_code']}</td>
                            <td>{$item['status']}</td>
                            <td>{$item['user_name']}</td>
                            <td>{$item['submitted_at']}</td>
                          </tr>";
            }
            $html .= "</tbody></table>";

            // 3. Generate PDF
            $mpdf = new Mpdf([
                'tempDir' => __DIR__ . '/../storage/app/tmp',
                'format'  => 'A4'
            ]);
            $mpdf->WriteHTML($html);
            
            $fileName = "Relatorio_Submissoes_" . date('Ymd_Hi') . ".pdf";
            $reportPath = __DIR__ . '/../storage/app/tmp/' . $fileName;
            
            // Save to disk temporarily
            $mpdf->Output($reportPath, \Mpdf\Output\Destination::FILE);
            
            $logger->info("PDF Generated at: $reportPath");
        } else {
            $logger->warning("Unknown report type: " . $schedule['report_type']);
            continue;
        }

        // Send Email
        if ($reportPath && file_exists($reportPath)) {
            $logger->info("Dispatching emails to " . count($recipients) . " recipients.");
            
            // MailService uses send($to, $subject, $body, $template, $attachments)
            // But since this varies heavily in implementation based on the application's config, 
            // we will build a raw PHPMailer or adapt to the existing MailService.
            // Based on earlier analysis, MailService has a standard shape. Let's try sending it carefully.
            
            foreach ($recipients as $email) {
                // Read file to memory for attachment
                $attachmentData = [
                    $reportPath => $fileName
                ];
                
                // Fire off
                $mailService->send(
                    $email,
                    "Relatório Agendado - NimbusDocs",
                    "Olá! Segue em anexo o relatório agendado na plataforma NimbusDocs.",
                    'basic', // Assuming a basic template exists
                    [], // data replacements
                    $attachmentData
                );
            }

            // Cleanup temp file
            unlink($reportPath);

            // Update Timestamps
            $now = date('Y-m-d H:i:s');
            // Calculate next run
            $nextRunStr = match ($schedule['frequency']) {
                'DAILY'   => '+1 day',
                'WEEKLY'  => '+1 week',
                'MONTHLY' => '+1 month',
                default   => '+1 week'
            };
            // Calculate NEXT run based on the scheduled run time to avoid drift, or simply relative to now.
            // Using relative to old next_run_at to keep cadence
            $nextRunTs = strtotime($nextRunStr, strtotime($schedule['next_run_at']));
            // If the worker was down, fast forward past now
            while ($nextRunTs <= time()) {
                $nextRunTs = strtotime($nextRunStr, $nextRunTs);
            }
            $nextRunAt = date('Y-m-d H:i:s', $nextRunTs);

            $scheduleRepo->updateRunTimes($schedule['id'], $now, $nextRunAt);
            
            $logger->info("Schedule ID {$schedule['id']} completed successfully. Next run updated to {$nextRunAt}");
        }
    }

    $logger->info("Scheduled Reports execution finished.");
    echo "Done.\n";
    exit(0);

} catch (\Throwable $e) {
    echo "Error processing scheduled reports: " . $e->getMessage() . "\n";
    $logger->error("Scheduled Reports Error: " . $e->getMessage());
    exit(1);
}
