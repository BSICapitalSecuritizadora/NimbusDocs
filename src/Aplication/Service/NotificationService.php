<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Infrastructure\Notification\GraphMailService;

class NotificationService
{
    public function __construct(private GraphMailService $mail) {}

    public function notifyGeneralDocument(array $doc, array $users): void
    {
        foreach ($users as $user) {
            $email = $user['email'];
            $name  = $user['full_name'];

            $html = $this->renderTemplate('new_general_document', [
                'doc' => $doc,
                'user' => $user,
            ]);

            $this->mail->sendMail(
                to: $email,
                subject: "Novo documento disponível: {$doc['title']}",
                htmlBody: $html
            );
        }
    }

    private function renderTemplate(string $name, array $data): string
    {
        ob_start();
        extract($data);
        require __DIR__ . "/../../Presentation/Email/{$name}.php";
        return ob_get_clean();
    }
}
