<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Infrastructure\Notification\GraphMailService;
use App\Support\Url;

final class NotificationService
{
    public function __construct(
        private GraphMailService $mailer,
        private array $config
    ) {}

    /**
     * Nova submissão criada pelo usuário no portal
     */
    public function portalNewSubmission(array $portalUser, array $submission): void
    {
        $enabled = $this->config['notifications']['portal']['new_submission'] ?? false;
        if (!$enabled) {
            return;
        }

        $toEmail = $portalUser['email'] ?? null;
        if (!$toEmail) {
            return;
        }

        $subject = 'Confirmação de envio — NimbusDocs';
        $submissionId = (int)$submission['id'];

        $portalUrl   = Url::portal("/submissions/{$submissionId}");
        $userName    = $portalUser['full_name'] ?? $portalUser['email'];

        $title = $submission['title'] ?? 'Envio de informações';

        $html = <<<HTML
<p>Olá, {$this->e($userName)}.</p>

<p>Recebemos sua submissão no <strong>NimbusDocs</strong> com o seguinte detalhe:</p>

<ul>
  <li><strong>ID:</strong> #{$submissionId}</li>
  <li><strong>Título:</strong> {$this->e($title)}</li>
</ul>

<p>Você pode acompanhar o andamento pelo portal:</p>
<p><a href="{$portalUrl}">Acessar minha submissão</a></p>

<p>Atenciosamente,<br>Equipe NimbusDocs</p>
HTML;

        $this->mailer->sendMail($toEmail, $userName, $subject, $html);
    }

    /**
     * Status da submissão alterado por um admin
     */
    public function portalSubmissionStatusChanged(
        array $portalUser,
        array $submission,
        string $oldStatus,
        string $newStatus
    ): void {
        $enabled = $this->config['notifications']['portal']['status_change'] ?? false;
        if (!$enabled) {
            return;
        }

        $toEmail = $portalUser['email'] ?? null;
        if (!$toEmail) {
            return;
        }

        $subject = 'Atualização de status — NimbusDocs';
        $submissionId = (int)$submission['id'];
        $portalUrl    = Url::portal("/submissions/{$submissionId}");
        $userName     = $portalUser['full_name'] ?? $portalUser['email'];

        $title = $submission['title'] ?? 'Envio de informações';

        $html = <<<HTML
<p>Olá, {$this->e($userName)}.</p>

<p>O status da sua submissão no <strong>NimbusDocs</strong> foi atualizado:</p>

<ul>
  <li><strong>ID:</strong> #{$submissionId}</li>
  <li><strong>Título:</strong> {$this->e($title)}</li>
  <li><strong>Status anterior:</strong> {$this->e($oldStatus)}</li>
  <li><strong>Novo status:</strong> {$this->e($newStatus)}</li>
</ul>

<p>Você pode conferir os detalhes no portal:</p>
<p><a href="{$portalUrl}">Ver submissão</a></p>

<p>Atenciosamente,<br>Equipe NimbusDocs</p>
HTML;

        $this->mailer->sendMail($toEmail, $userName, $subject, $html);
    }

    /**
     * Admin anexou documentos de resposta para o usuário
     */
    public function portalSubmissionResponseUploaded(
        array $portalUser,
        array $submission
    ): void {
        $enabled = $this->config['notifications']['portal']['response_upload'] ?? false;
        if (!$enabled) {
            return;
        }

        $toEmail = $portalUser['email'] ?? null;
        if (!$toEmail) {
            return;
        }

        $subject      = 'Novos documentos disponíveis — NimbusDocs';
        $submissionId = (int)$submission['id'];
        $portalUrl    = Url::portal("/submissions/{$submissionId}");
        $userName     = $portalUser['full_name'] ?? $portalUser['email'];
        $title        = $submission['title'] ?? 'Envio de informações';

        $html = <<<HTML
<p>Olá, {$this->e($userName)}.</p>

<p>Foram disponibilizados novos documentos relacionados à sua submissão no <strong>NimbusDocs</strong>:</p>

<ul>
  <li><strong>ID:</strong> #{$submissionId}</li>
  <li><strong>Título:</strong> {$this->e($title)}</li>
</ul>

<p>Você pode acessar os documentos diretamente pelo portal:</p>
<p><a href="{$portalUrl}">Acessar submissão e documentos</a></p>

<p>Atenciosamente,<br>Equipe NimbusDocs</p>
HTML;

        $this->mailer->sendMail($toEmail, $userName, $subject, $html);
    }

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
