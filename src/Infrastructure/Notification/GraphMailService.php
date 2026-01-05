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

    private Client $http;
    private Logger $logger;
    private ?string $lastError = null;

    public function __construct(array $config, Logger $logger)
    {
        $this->tenantId     = $config['GRAPH_TENANT_ID'] ?? '';
        $this->clientId     = $config['GRAPH_CLIENT_ID'] ?? '';
        $this->clientSecret = $config['GRAPH_CLIENT_SECRET'] ?? '';
        $this->from         = $config['MAIL_FROM'] ?? '';
        $this->fromName     = $config['MAIL_FROM_NAME'] ?? 'NimbusDocs';

        // Não lançar exceções em 4xx/5xx automaticamente; vamos tratar/responder
        $this->http   = new Client([
            'http_errors' => false,
            'timeout'     => 15,
        ]);
        $this->logger = $logger;
    }

    private function getAccessToken(): string
    {
        $url = "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token";

        try {
            $response = $this->http->post($url, [
                'form_params' => [
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type'    => 'client_credentials',
                    'scope'         => 'https://graph.microsoft.com/.default',
                ]
            ]);

            $status = $response->getStatusCode();
            $body   = (string)$response->getBody();
            if ($status !== 200) {
                $this->logger->error('Graph OAuth token: resposta não-200', [
                    'status' => $status,
                    'body'   => $body,
                ]);
                return '';
            }

            $data = json_decode($body, true);
            return $data['access_token'] ?? '';
        } catch (\Throwable $e) {
            $this->logger->error('Graph OAuth token: exceção', [
                'exception' => $e->getMessage(),
            ]);
            return '';
        }
    }

    public function sendMail(string $to, string $subject, string $htmlBody): bool
    {
        $this->lastError = null;

        // Se não tiver config preenchida, nem tenta enviar
        if ($this->tenantId === '' || $this->clientId === '' || $this->clientSecret === '' || $this->from === '') {
            $this->lastError = 'Configuração de e-mail incompleta (.env).';
            $this->logger->warning('GraphMailService: configuração incompleta, e-mail não enviado.');
            return false;
        }

        try {
            $token = $this->getAccessToken();
            if ($token === '') {
                $this->lastError = 'Falha ao obter token de acesso do Microsoft Graph.';
                $this->logger->error('GraphMailService: token vazio, não foi possível enviar e-mail.');
                return false;
            }

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
                ],
                "saveToSentItems" => false,
            ];

            $response = $this->http->post($url, [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type'  => 'application/json',
                ],
                'body' => json_encode($payload, JSON_UNESCAPED_UNICODE),
            ]);

            $status = $response->getStatusCode();
            if ($status === 202) {
                return true;
            }

            $body = (string)$response->getBody();
            $errorData = json_decode($body, true);
            $errorMsg = $errorData['error']['message'] ?? 'Erro desconhecido ao enviar e-mail';
            $this->lastError = "Graph API retornou status {$status}: {$errorMsg}";

            $this->logger->error('Graph sendMail: falha no envio', [
                'status' => $status,
                'body'   => $body,
                'to'     => $to,
                'from'   => $this->from,
            ]);
            return false;
        } catch (\Throwable $e) {
            $this->lastError = 'Exceção: ' . $e->getMessage();
            $this->logger->error('GraphMailService: erro ao enviar e-mail', [
                'exception' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }
}
