<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Portal\Auth;

use App\Infrastructure\Persistence\MySqlPortalAccessTokenRepository;
use App\Infrastructure\Persistence\MySqlPortalUserRepository;
use App\Support\AuditLogger;
use App\Support\Csrf;
use App\Support\Session;
use Respect\Validation\Validator as v;

final class PortalLoginController
{
    private MySqlPortalAccessTokenRepository $tokenRepo;
    private MySqlPortalUserRepository $userRepo;
    private AuditLogger $audit;

    public function __construct(private array $config)
    {
        $this->tokenRepo = new MySqlPortalAccessTokenRepository($config['pdo']);
        $this->userRepo  = new MySqlPortalUserRepository($config['pdo']);
        $this->audit     = new AuditLogger($config['pdo']);
    }

    public function showLoginForm(array $vars = []): void
    {
        $pageTitle   = 'Acesso ao Portal';
        $contentView = __DIR__ . '/../../../View/portal/login.php';
        $viewData    = [
            'csrfToken' => Csrf::token(),
            'flash'     => [
                'error' => Session::getFlash('error'),
                'success' => Session::getFlash('success'),
            ],
        ];

        // Render view directly (standalone page)
        extract($viewData);
        require $contentView;
    }

    public function handleLogin(array $vars = []): void
    {
        $post  = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/portal/login');
        }

        $identifier = trim($post['identifier'] ?? '');
        $password   = (string)($post['password'] ?? '');
        $code       = strtoupper(trim($post['access_code'] ?? ''));

        if ($code === '') {
            Session::flash('error', 'Informe um código de acesso válido.');
            $this->redirect('/portal/login');
        }

        $this->loginWithCode($code);
    }

    public function logout(array $vars = []): void
    {
        Session::forget('portal_user');
        session_regenerate_id(true);

        Session::flash('success', 'Você saiu do portal.');
        $this->redirect('/portal/login');
    }

    private function redirect(string $path): void
    {
        session_write_close();
        header('Location: ' . $path);
        exit;
    }

    private function loginWithCode(string $code): void
    {
        if (!v::stringType()->length(4, 64)->validate($code)) {
            Session::flash('error', 'Informe um código de acesso válido.');
            $this->redirect('/portal/login');
        }
        // Tenta um token válido
        $row = $this->tokenRepo->findValidWithUserByCode($code);

        // Se não válido, tenta buscar token pela string para ver se está expirado e notificar
        if (!$row) {
            $tokenOnly = $this->tokenRepo->findByCode($code);
            if ($tokenOnly && strtotime($tokenOnly['expires_at']) < time()) {
                $portalUser = $this->userRepo->findById((int)$tokenOnly['portal_user_id']);
                if ($portalUser && isset($this->config['notification'])) {
                    try {
                        $this->config['notification']->notifyTokenExpired($portalUser, $tokenOnly);
                    } catch (\Throwable $t) {
                        error_log('Falha ao enviar notificação de token expirado: ' . $t->getMessage());
                    }
                }
            }
            $this->audit->log('PORTAL_USER', null, 'PORTAL_LOGIN_CODE_FAILED', 'PORTAL_ACCESS_TOKEN', null, ['code' => $code]);
            Session::flash('error', 'Código inválido ou expirado.');
            $this->redirect('/portal/login');
        }

        $portalUser = [
            'id'              => (int)$row['user_id'],
            'full_name'       => $row['user_full_name'],
            'email'           => $row['user_email'],
            'document_number' => $row['user_document_number'],
            'phone_number'    => $row['user_phone_number'],
        ];

        $ip  = $_SERVER['REMOTE_ADDR'] ?? '';
        $ua  = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $this->tokenRepo->markAsUsed((int)$row['token_id'], $ip, $ua);
        $this->userRepo->recordLastLogin((int)$row['user_id'], 'ACCESS_CODE');

        session_regenerate_id(true);
        Session::put('portal_user', $portalUser);

        $logger = $this->config['portal_access_logger'] ?? null;
        if ($logger) {
            $logger->log((int)$portalUser['id'], 'LOGIN', 'portal', null);
        }

        $this->audit->log('PORTAL_USER', (int)$row['user_id'], 'PORTAL_LOGIN_SUCCESS_CODE', 'PORTAL_ACCESS_TOKEN', (int)$row['token_id']);

        Session::flash('success', 'Login efetuado com sucesso.');
        $this->redirect('/portal');
    }
}
