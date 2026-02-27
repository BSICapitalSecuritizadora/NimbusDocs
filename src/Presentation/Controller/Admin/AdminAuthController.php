<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Auth\AzureAdminAuthClient;
use App\Infrastructure\Persistence\MySqlAdminUserRepository;
use App\Support\Session;

final class AdminAuthController
{
    private MySqlAdminUserRepository $adminRepo;

    private AzureAdminAuthClient $azureClient;

    public function __construct(private array $config)
    {
        $pdo = $config['pdo'];
        $this->adminRepo = new MySqlAdminUserRepository($pdo);
        /** @var AzureAdminAuthClient $client */
        $client = $config['azure_admin_auth'];
        $this->azureClient = $client;
    }

    /**
     * GET /admin/login/microsoft
     */
    public function loginWithMicrosoft(): void
    {
        $provider = $this->azureClient->getProvider();

        $options = [
            'scope' => [
                'openid',
                'profile',
                'email',
                'User.Read',
            ],
        ];

        $authUrl = $provider->getAuthorizationUrl($options);

        // Anti-CSRF state
        Session::put('azure_oauth2_state', $provider->getState());

        header('Location: ' . $authUrl);
        exit;
    }

    /**
     * GET /admin/auth/callback (aliased from /admin/login/callback if routed)
     */
    public function loginCallback(): void
    {
        $provider = $this->azureClient->getProvider();

        $state = (string) ($_GET['state'] ?? '');
        $savedState = (string) Session::get('azure_oauth2_state');

        if (!$state || !$savedState || !hash_equals($savedState, $state)) {
            Session::flash('error', 'Falha na validação do login com a Microsoft (state inválido).');
            header('Location: /admin/login');
            exit;
        }

        if (!isset($_GET['code'])) {
            Session::flash('error', 'Código de autorização não informado pela Microsoft.');
            header('Location: /admin/login');
            exit;
        }

        try {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code'],
            ]);

            // Tenta primeiro via resource owner
            $owner = $provider->getResourceOwner($token);
            $graphUser = $owner->toArray();

            // Fallback: consulta o Graph /me com requisição autenticada (evita audience inválida)
            if (!isset($graphUser['userPrincipalName']) && !isset($graphUser['mail'])) {
                $request = $provider->getAuthenticatedRequest(
                    'GET',
                    'https://graph.microsoft.com/v1.0/me',
                    $token
                );
                $response = $provider->getParsedResponse($request);
                $graphUser = is_array($response) ? $response : $graphUser;
            }

            // Extrai dados com múltiplos fallbacks
            $mail = $graphUser['mail'] ?? null;
            $upn = $graphUser['userPrincipalName'] ?? ($graphUser['preferred_username'] ?? null);
            if (!$mail && isset($graphUser['otherMails']) && is_array($graphUser['otherMails']) && count($graphUser['otherMails']) > 0) {
                $mail = $graphUser['otherMails'][0];
            }
            $name = $graphUser['displayName'] ?? ($graphUser['name'] ?? null);
            $email = $mail ?: $upn;

            if (!$email) {
                Session::flash('error', 'Não foi possível obter o e-mail da conta Microsoft.');
                header('Location: /admin/login');
                exit;
            }

            // Valida domínio permitido, se configurado
            $allowedDomain = $this->azureClient->getAllowedDomain();
            if ($allowedDomain) {
                $domain = substr(strrchr($email, '@') ?: '', 1);
                if (strcasecmp($domain, (string) $allowedDomain) !== 0) {
                    Session::flash('error', 'Seu e-mail não pertence ao domínio autorizado.');
                    header('Location: /admin/login');
                    exit;
                }
            }

            // Vincula à tabela admin_users: tentar por OID primeiro
            $azureOid = $graphUser['id'] ?? $graphUser['oid'] ?? null;
            $adminUser = null;
            if ($azureOid) {
                // tenta localizar por ms_object_id/azure_oid via SQL direto
                $pdo = $this->config['pdo'];
                $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE ms_object_id = :oid OR azure_oid = :oid LIMIT 1');
                $stmt->execute([':oid' => $azureOid]);
                $adminUser = $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
            }

            if (!$adminUser && $email) {
                $adminUser = $this->adminRepo->findActiveByEmail($email);
            }
            if (!$adminUser) {
                Session::flash('error', 'Sua conta não está cadastrada como administradora do NimbusDocs.');
                header('Location: /admin/login');
                exit;
            }

            // Se achou por e-mail mas ainda não tem vínculo, atualiza OID/tenant/UPN
            $azureTenant = $graphUser['tenantId'] ?? $graphUser['tid'] ?? null;
            if ($adminUser && $azureOid) {
                $pdo = $this->config['pdo'];
                $upd = $pdo->prepare(
                    'UPDATE admin_users SET 
                        ms_object_id = COALESCE(ms_object_id, :oid),
                        azure_oid    = COALESCE(azure_oid, :oid),
                        ms_tenant_id = COALESCE(ms_tenant_id, :tenant),
                        azure_tenant_id = COALESCE(azure_tenant_id, :tenant),
                        ms_upn       = COALESCE(ms_upn, :upn),
                        azure_upn    = COALESCE(azure_upn, :upn),
                        updated_at   = NOW()
                     WHERE id = :id'
                );
                $upd->execute([
                    ':oid' => $azureOid,
                    ':tenant' => $azureTenant,
                    ':upn' => $email,
                    ':id' => (int) $adminUser['id'],
                ]);
            }

            // Tudo ok: inicia sessão
            session_regenerate_id(true);
            Session::put('admin', [
                'id' => (int) $adminUser['id'],
                'email' => $adminUser['email'],
                'name' => $adminUser['full_name'] ?? $adminUser['name'] ?? ($name ?? $adminUser['email']),
                'role' => $adminUser['role'] ?? 'ADMIN',
                'is_active' => $adminUser['is_active'] ?? 1,
                'oauth' => [
                    'provider' => 'azure',
                    'upn' => $upn,
                ],
            ]);

            // Limpa state
            Session::forget('azure_oauth2_state');

            header('Location: /admin/dashboard');
            exit;
        } catch (\Throwable $e) {
            error_log('Azure AD Callback Error: ' . $e->getMessage());
            Session::flash('error', 'Erro ao autenticar com a Microsoft: ' . $e->getMessage());
            header('Location: /admin/login');
            exit;
        }
    }
}
