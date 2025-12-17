<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Infrastructure\Notification\GraphMailService;
use App\Infrastructure\Persistence\MySqlSettingsRepository;
use App\Infrastructure\Persistence\MySqlAdminUserRepository;
use App\Infrastructure\Persistence\MySqlPortalUserRepository;
use App\Infrastructure\Persistence\MySqlNotificationOutboxRepository;

final class NotificationService
{
    public function __construct(
        private GraphMailService $mail,
        private MySqlSettingsRepository $settings,
        private MySqlAdminUserRepository $adminUsers,
        private MySqlPortalUserRepository $portalUsers,
        private MySqlNotificationOutboxRepository $outbox,
    ) {}

    private function isEnabled(string $key, bool $default = true): bool
    {
        $value = $this->settings->get($key);
        if ($value === null) {
            return $default;
        }
        return $value === '1' || strtolower((string)$value) === 'true';
    }

    private function renderTemplate(string $name, array $data): string
    {
        ob_start();
        extract($data);
        require __DIR__ . "/../../Presentation/Email/{$name}.php";
        return (string) ob_get_clean();
    }

    // -----------------------------------------------------------------
    // 1) Novo documento geral publicado
    // -----------------------------------------------------------------
    /**
     * @param array $doc   general_documents + category_name
     */
    public function notifyNewGeneralDocument(array $doc): void
    {
        if (!$this->isEnabled('notifications.general_documents.enabled')) {
            return;
        }

        $users = $this->portalUsers->getActiveUsers(); // implementa esse método se ainda não existir

        if (!$users) {
            return;
        }

        foreach ($users as $user) {
            $this->outbox->enqueue([
                'type'            => 'NEW_GENERAL_DOCUMENT',
                'recipient_email' => $user['email'],
                'recipient_name'  => $user['full_name'] ?? $user['name'] ?? null,
                'subject'         => 'Novo documento disponível: ' . ($doc['title'] ?? ''),
                'template'        => 'new_general_document',
                'payload_json'    => json_encode(['doc' => $doc, 'user' => $user], JSON_UNESCAPED_UNICODE),
                'max_attempts'    => 5,
            ]);
        }
    }

    // -----------------------------------------------------------------
    // 2) Novo comunicado publicado
    // -----------------------------------------------------------------
    public function notifyNewAnnouncement(array $announcement): void
    {
        if (!$this->isEnabled('notifications.announcements.enabled')) {
            return;
        }

        $users = $this->portalUsers->getActiveUsers();
        if (!$users) {
            return;
        }

        foreach ($users as $user) {
            $this->outbox->enqueue([
                'type'            => 'NEW_ANNOUNCEMENT',
                'recipient_email' => $user['email'],
                'recipient_name'  => $user['full_name'] ?? $user['name'] ?? null,
                'subject'         => '[NimbusDocs] Novo comunicado: ' . ($announcement['title'] ?? ''),
                'template'        => 'new_announcement',
                'payload_json'    => json_encode(['announcement' => $announcement, 'user' => $user], JSON_UNESCAPED_UNICODE),
                'max_attempts'    => 5,
            ]);
        }
    }

    // -----------------------------------------------------------------
    // 3) Submissão recebida (usuário -> admins)
    // -----------------------------------------------------------------
    public function notifySubmissionReceived(array $submission, array $portalUser): void
    {
        if (!$this->isEnabled('notifications.submission_received.enabled')) {
            return;
        }

        $admins = $this->adminUsers->allActiveAdmins(); // método para ADMIN/SUPER_ADMIN ativos

        if (!$admins) {
            return;
        }

        foreach ($admins as $admin) {
            $this->outbox->enqueue([
                'type'            => 'SUBMISSION_RECEIVED',
                'recipient_email' => $admin['email'],
                'recipient_name'  => $admin['full_name'] ?? $admin['name'] ?? null,
                'subject'         => '[NimbusDocs] Nova submissão recebida',
                'template'        => 'submission_received',
                'payload_json'    => json_encode(['submission' => $submission, 'user' => $portalUser, 'admin' => $admin], JSON_UNESCAPED_UNICODE),
                'max_attempts'    => 5,
            ]);
        }
    }

    // -----------------------------------------------------------------
    // 4) Mudança de status da submissão (admin -> usuário)
    // -----------------------------------------------------------------
    public function notifySubmissionStatusChanged(
        array $submission,
        array $portalUser,
        string $oldStatus,
        string $newStatus
    ): void {
        if (!$this->isEnabled('notifications.submission_status_changed.enabled')) {
            return;
        }

        $html = $this->renderTemplate('submission_status_changed', [
            'submission' => $submission,
            'user'       => $portalUser,
            'oldStatus'  => $oldStatus,
            'newStatus'  => $newStatus,
        ]);

        $subject = sprintf(
            '[NimbusDocs] Atualização da sua submissão: %s (%s → %s)',
            $submission['title'] ?? ('#' . $submission['id']),
            $oldStatus,
            $newStatus
        );

        $this->outbox->enqueue([
            'type'            => 'SUBMISSION_STATUS_CHANGED',
            'recipient_email' => $portalUser['email'],
            'recipient_name'  => $portalUser['full_name'] ?? $portalUser['name'] ?? null,
            'subject'         => $subject,
            'template'        => 'submission_status_changed',
            'payload_json'    => json_encode([
                'submission' => $submission,
                'user'       => $portalUser,
                'oldStatus'  => $oldStatus,
                'newStatus'  => $newStatus,
            ], JSON_UNESCAPED_UNICODE),
            'max_attempts'    => 5,
        ]);
    }

    // -----------------------------------------------------------------
    // 5) Token criado (enviar link de acesso)
    // -----------------------------------------------------------------
    public function notifyTokenCreated(array $portalUser, array $token): void
    {
        if (!$this->isEnabled('notifications.token_created.enabled')) {
            return;
        }

        $html = $this->renderTemplate('token_created', [
            'user'  => $portalUser,
            'token' => $token,
        ]);

        $this->outbox->enqueue([
            'type'            => 'TOKEN_CREATED',
            'recipient_email' => $portalUser['email'],
            'recipient_name'  => $portalUser['full_name'] ?? $portalUser['name'] ?? null,
            'subject'         => '[NimbusDocs] Seu link de acesso ao portal',
            'template'        => 'token_created',
            'payload_json'    => json_encode(['user' => $portalUser, 'token' => $token], JSON_UNESCAPED_UNICODE),
            'max_attempts'    => 5,
        ]);
    }

    // -----------------------------------------------------------------
    // 6) Token expirado
    // -----------------------------------------------------------------
    public function notifyTokenExpired(array $portalUser, array $token): void
    {
        if (!$this->isEnabled('notifications.token_expired.enabled')) {
            return;
        }

        $html = $this->renderTemplate('token_expired', [
            'user'  => $portalUser,
            'token' => $token,
        ]);

        $this->outbox->enqueue([
            'type'            => 'TOKEN_EXPIRED',
            'recipient_email' => $portalUser['email'],
            'recipient_name'  => $portalUser['full_name'] ?? $portalUser['name'] ?? null,
            'subject'         => '[NimbusDocs] Link de acesso expirado',
            'template'        => 'token_expired',
            'payload_json'    => json_encode(['user' => $portalUser, 'token' => $token], JSON_UNESCAPED_UNICODE),
            'max_attempts'    => 5,
        ]);
    }

    // -----------------------------------------------------------------
    // 7) Usuário pré-cadastrado (envia instruções)
    // -----------------------------------------------------------------
    public function notifyUserPrecreated(array $portalUser, ?array $token = null): void
    {
        if (!$this->isEnabled('notifications.user_precreated.enabled')) {
            return;
        }

        $html = $this->renderTemplate('user_precreated', [
            'user'  => $portalUser,
            'token' => $token,
        ]);

        $this->outbox->enqueue([
            'type'            => 'USER_PRECREATED',
            'recipient_email' => $portalUser['email'],
            'recipient_name'  => $portalUser['full_name'] ?? $portalUser['name'] ?? null,
            'subject'         => '[NimbusDocs] Acesso ao portal NimbusDocs',
            'template'        => 'user_precreated',
            'payload_json'    => json_encode(['user' => $portalUser, 'token' => $token], JSON_UNESCAPED_UNICODE),
            'max_attempts'    => 5,
        ]);
    }
}
