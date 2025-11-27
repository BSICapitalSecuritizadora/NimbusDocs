<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlAdminUserRepository;
use App\Support\Session;
use TheNetworg\OAuth2\Client\Provider\Azure;

final class AdminMicrosoftAuthController
{
    private Azure $provider;
    private MySqlAdminUserRepository $adminRepo;

    public function __construct(private array $config)
    {
        $ms = $config['ms_admin_auth'];

        $this->provider = new Azure([
            'clientId'          => $ms['client_id'],
            'clientSecret'      => $ms['client_secret'],
            'redirectUri'       => $ms['redirect_uri'],
            'tenant'            => $ms['tenant_id'],
            'defaultEndPointVersion' => '2.0',
        ]);

        $this->adminRepo = new MySqlAdminUserRepository($config['pdo']);
    }

    private function requireMsConfigured(): void
    {
        $ms = $this->config['ms_admin_auth'];
        if (
            empty($ms['tenant_id']) ||
            empty($ms['client_id']) ||
            empty($ms['client_secret']) ||
            empty($ms['redirect_uri'])
        ) {
            http_response_code(500);
            echo 'Autenticação Microsoft não configurada.';
            exit;
        }
    }

    public function redirectToProvider(array $vars = []): void
    {
        $this->requireMsConfigured();

        $authorizationUrl = $this->provider->getAuthorizationUrl([
            'scope' => [
                'openid',
                'profile',
                'email',
                'offline_access',
            ],
        ]);

        // Armazena o state na sessão para proteção CSRF
        Session::put('ms_oauth_state', $this->provider->getState());

        header('Location: ' . $authorizationUrl);
        exit;
    }

    public function handleCallback(array $vars = []): void
    {
        $this->requireMsConfigured();

        $state = $_GET['state'] ?? '';
        $code  = $_GET['code']  ?? null;

        $savedState = Session::get('ms_oauth_state');
        Session::forget('ms_oauth_state');

        if (!$code || !$savedState || $state !== $savedState) {
            http_response_code(400);
            echo 'Requisição inválida (state mismatch ou código ausente).';
            return;
        }

        try {
            $token = $this->provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);

            $resourceOwner = $this->provider->getResourceOwner($token);
            $data = $resourceOwner->toArray();

            // Dependendo da conta, o email pode vir em "mail" ou "userPrincipalName"
            $email = $data['mail'] ?? $data['userPrincipalName'] ?? null;
            $name  = $data['displayName'] ?? '';

            if (!$email) {
                http_response_code(403);
                echo 'Não foi possível obter o e-mail da conta Microsoft.';
                return;
            }

            // Valida domínio, se configurado
            $allowedDomains = $this->config['ms_admin_auth']['allowed_domains'] ?? '';
            if ($allowedDomains) {
                $domains = array_map('trim', explode(',', $allowedDomains));
                $emailDomain = substr(strrchr($email, "@"), 1);

                if (!in_array($emailDomain, $domains, true)) {
                    http_response_code(403);
                    echo 'Domínio não autorizado para acesso administrativo.';
                    return;
                }
            }

            // Procura admin_users por e-mail
            $adminUser = $this->adminRepo->findActiveByEmail($email);
            if (!$adminUser) {
                http_response_code(403);
                echo 'Você não possui permissão administrativa no NimbusDocs.';
                return;
            }

            // Cria sessão admin
            Session::put('admin', [
                'id'          => $adminUser['id'],
                'name'        => $adminUser['full_name'] ?? $adminUser['name'] ?? $name,
                'email'       => $adminUser['email'],
                'auth_driver' => 'microsoft',
            ]);

            $this->config['audit']->log(
                (int)$admin['id'],
                'admin.login.microsoft',
                'admin_user',
                (int)$admin['id'],
                [
                    'email'       => $admin['email'],
                    'azure_oid'   => $azureOid,
                    'azure_upn'   => $email,
                    'azure_tenant' => $azureTenant,
                ]
            );

            header('Location: /admin');
            exit;
        } catch (\Throwable $e) {
            http_response_code(500);
            echo 'Erro na autenticação Microsoft: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }
}
