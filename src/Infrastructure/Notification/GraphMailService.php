<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use GuzzleHttp\Client;
use Monolog\Logger;

final class GraphMailService
{
    private string $tenantId;
    private string $clientId;
    private string $clientSecret;
    private string $from;
    private string $fromName;
    private Client $client;
    private Logger $logger;

    public function __construct(array $config, Logger $logger)
    {
        $this->tenantId     = $config['GRAPH_TENANT_ID'];
        $this->clientId     = $config['GRAPH_CLIENT_ID'];
        $this->clientSecret = $config['GRAPH_CLIENT_SECRET'];
        $this->from         = $config['MAIL_FROM'];
        $this->fromName     = $config['MAIL_FROM_NAME'];

        $this->client = new Client();
        $this->logger = $logger;
    }

    private function getAccessToken(): string
    {
        $url = "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token";

        $response = $this->client->post($url, [
            'form_params' => [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type'    => 'client_credentials',
                'scope'         => 'https://graph.microsoft.com/.default',
            ]
        ]);

        $data = json_decode((string)$response->getBody(), true);
        return $data['access_token'];
    }

    public function sendMail(string $to, string $subject, string $htmlBody): void
    {
        try {
            $token = $this->getAccessToken();

            $url = "https://graph.microsoft.com/v1.0/users/{$this->from}/sendMail";

            $payload = [
                "message" => [
                    "subject" => $subject,
                    "body" => [
                        "contentType" => "HTML",
                        "content"     => $htmlBody
                    ],
                    "from" => [
                        "emailAddress" => [
                            "address" => $this->from,
                            "name"    => $this->fromName
                        ]
                    ],
                    "toRecipients" => [
                        [
                            "emailAddress" => [
                                "address" => $to
                            ]
                        ]
                    ]
                ]
            ];

            $this->client->post($url, [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode($payload)
            ]);
        } catch (\Throwable $e) {
            $this->logger->error("Erro ao enviar email via Graph: " . $e->getMessage());
        }
    }
}
