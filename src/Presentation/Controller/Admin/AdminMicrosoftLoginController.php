<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Support\Session;
use TheNetworg\OAuth2\Client\Provider\Azure;
use App\Infrastructure\Persistence\Connection;
use PDO;

final class AdminMicrosoftLoginController
{
    public function __construct(private array $config) {}

    private function getProvider(): Azure
    {
        return new Azure([
            'clientId'                => $this->config['GRAPH_CLIENT_ID'],
            'clientSecret'            => $this->config['GRAPH_CLIENT_SECRET'],
            'redirectUri'             => $this->config['GRAPH_REDIRECT_URI'],
            'tenant'                  => $this->config['GRAPH_TENANT_ID'],
            'urlAuthorize'            => null, // usa padrão da lib
            'urlAccessToken'          => null,
            'urlResourceOwnerDetails' => null,
        ]);
    }

    public function redirectToMicrosoft(): void
    {
        $provider = $this->getProvider();

        $authUrl = $provider->getAuthorizationUrl([
            'scope' => [
                'openid',
                'profile',
                'email',
                'offline_access',
                'User.Read',
            ],
        ]);

        // salva estado na sessão para proteção CSRF
        Session::put('ms_oauth_state', $provider->getState());

        header('Location: ' . $authUrl);
        exit;
    }

    public function handleCallback(): void
    {
        $provider = $this->getProvider();

        $state  = $_GET['state'] ?? '';
        $code   = $_GET['code'] ?? null;
        $stored = Session::get('ms_oauth_state');

        if (empty($state) || $state !== $stored) {
            Session::forget('ms_oauth_state');
            http_response_code(400);
            echo 'Estado inválido na autenticação Microsoft.';
            return;
        }

        if ($code === null) {
            http_response_code(400);
            echo 'Código de autorização ausente.';
            return;
        }

        try {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);

            $user = $provider->getResourceOwner($token);
            $data = $user->toArray();

            // Dados do Azure AD
            $azureOid    = $data['id'] ?? $data['oid'] ?? null;
            $azureTenant = $data['tid'] ?? null;
            $email       = $data['userPrincipalName'] ?? $data['mail'] ?? $data['email'] ?? null;
            $name        = $data['displayName'] ?? $data['name'] ?? '';

            $this->config['audit']->systemAction([
                'action'  => 'ADMIN_LOGIN_MICROSOFT_DENIED',
                'summary' => 'Conta Microsoft não autorizada tentou acesso administrativo.',
                'details' => [
                    'azure_oid' => $azureOid,
                    'azure_upn' => $email,
                ],
            ]);

            if (!$email) {
                http_response_code(403);
                echo 'Não foi possível obter o e-mail/UPN da conta Microsoft.';
                return;
            }

            // Busca admin no banco
            /** @var PDO $pdo */
            $pdo = $this->config['pdo'];

            // Primeiro tenta por ms_object_id, se existir
            $sql = "SELECT * FROM admin_users WHERE ms_object_id = :oid LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':oid' => $azureOid]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$admin) {
                // fallback: tenta por email
                $sql = "SELECT * FROM admin_users WHERE email = :email LIMIT 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':email' => $email]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                // se achou por email, atualiza campos Azure
                if ($admin) {
                    $update = $pdo->prepare(
                        "UPDATE admin_users
                         SET ms_object_id = :oid,
                             ms_tenant_id = :tenant,
                             ms_upn = :upn
                         WHERE id = :id"
                    );
                    $update->execute([
                        ':oid'    => $azureOid,
                        ':tenant' => $azureTenant,
                        ':upn'    => $email,
                        ':id'     => $admin['id'],
                    ]);
                }
            }

            if (!$admin) {
                http_response_code(403);
                echo 'Sua conta Microsoft não está autorizada no NimbusDocs (admin_users).';
                return;
            }

            // (opcional) validar tenant
            if (!empty($admin['ms_tenant_id']) && $admin['ms_tenant_id'] !== $azureTenant) {
                http_response_code(403);
                echo 'Tenant Microsoft não autorizado para este administrador.';
                return;
            }

            // Cria sessão de admin (mantendo o mesmo formato usado no login local)
            Session::put('admin', [
                'id'        => (int)$admin['id'],
                'name'      => $admin['full_name'] ?? $admin['name'] ?? $name,
                'email'     => $admin['email'],
                'role'      => $admin['role'] ?? 'admin',
                'login_via' => 'microsoft',
            ]);

            $this->config['audit']->adminAction([
                'actor_id'    => (int)$admin['id'],
                'actor_name'  => $admin['full_name'] ?? $admin['name'] ?? $name,
                'action'      => 'ADMIN_LOGIN_SUCCESS',
                'summary'     => 'Login via Microsoft realizado com sucesso.',
                'details'     => [
                    'method'      => 'microsoft',
                    'azure_oid'   => $azureOid,
                    'azure_upn'   => $email,
                    'azure_tenant' => $azureTenant,
                ],
            ]);

            Session::forget('ms_oauth_state');

            header('Location: /admin/dashboard');
            exit;
        } catch (\Throwable $e) {
            // Aqui você também pode logar via Monolog se quiser
            http_response_code(500);
            echo 'Erro ao processar login Microsoft: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }
}
