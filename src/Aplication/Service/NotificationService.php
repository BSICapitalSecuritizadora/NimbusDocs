<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Infrastructure\Email\GraphMailService;
use App\Infrastructure\Persistence\MySqlSettingsRepository;
use App\Infrastructure\Persistence\MySqlAdminUserRepository;
use App\Infrastructure\Persistence\MySqlPortalUserRepository;

final class NotificationService
{
    public function __construct(
        private GraphMailService $mail,
        private MySqlSettingsRepository $settings,
        private MySqlAdminUserRepository $adminUsers,
        private MySqlPortalUserRepository $portalUsers,
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

        $users = $this->portalUsers->allActive(); // implementa esse método se ainda não existir

        if (!$users) {
            return;
        }

        foreach ($users as $user) {
            $html = $this->renderTemplate('new_general_document', [
                'doc'  => $doc,
                'user' => $user,
            ]);

            $this->mail->sendMailHtml(
                to: $user['email'],
                subject: 'Novo documento disponível: ' . $doc['title'],
                htmlBody: $html,
            );
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

        $users = $this->portalUsers->allActive();
        if (!$users) {
            return;
        }

        foreach ($users as $user) {
            $html = $this->renderTemplate('new_announcement', [
                'announcement' => $announcement,
                'user'         => $user,
            ]);

            $this->mail->sendMailHtml(
                to: $user['email'],
                subject: '[NimbusDocs] Novo comunicado: ' . $announcement['title'],
                htmlBody: $html,
            );
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
            $html = $this->renderTemplate('submission_received', [
                'submission' => $submission,
                'user'       => $portalUser,
                'admin'      => $admin,
            ]);

            $this->mail->sendMailHtml(
                to: $admin['email'],
                subject: '[NimbusDocs] Nova submissão recebida',
                htmlBody: $html,
            );
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

        $this->mail->sendMailHtml(
            to: $portalUser['email'],
            subject: $subject,
            htmlBody: $html,
        );
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

        $this->mail->sendMailHtml(
            to: $portalUser['email'],
            subject: '[NimbusDocs] Seu link de acesso ao portal',
            htmlBody: $html,
        );
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

        $this->mail->sendMailHtml(
            to: $portalUser['email'],
            subject: '[NimbusDocs] Link de acesso expirado',
            htmlBody: $html,
        );
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

        $this->mail->sendMailHtml(
            to: $portalUser['email'],
            subject: '[NimbusDocs] Acesso ao portal NimbusDocs',
            htmlBody: $html,
        );
    }
}
