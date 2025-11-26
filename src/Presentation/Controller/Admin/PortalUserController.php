<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Persistence\MySqlPortalUserRepository;
use App\Infrastructure\Persistence\MySqlPortalAccessTokenRepository;
use App\Support\AuditLogger;
use App\Support\Csrf;
use App\Support\Session;
use App\Support\RandomToken;
use Respect\Validation\Validator as v;
use DateInterval;
use DateTimeImmutable;


final class PortalUserController
{
    private MySqlPortalUserRepository $repo;
    private MySqlPortalAccessTokenRepository $tokenRepo;
    private AuditLogger $audit;

    public function __construct(private array $config)
    {
        $this->repo      = new MySqlPortalUserRepository($config['pdo']);
        $this->tokenRepo = new MySqlPortalAccessTokenRepository($config['pdo']);
        $this->audit     = new AuditLogger($config['pdo']);
    }

    private function requireAdmin(): array
    {
        $admin = Session::get('admin');
        if (!$admin) {
            http_response_code(403);
            echo '403 - Não autorizado';
            exit;
        }

        return $admin;
    }

    public function index(array $vars = []): void
    {
        $this->requireAdmin();

        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;

        $pagination = $this->repo->paginate($page, $perPage);

        $pageTitle   = 'Usuários Finais - NimbusDocs';
        $contentView = __DIR__ . '/../../View/admin/portal_users/index.php';
        $viewData    = [
            'pagination' => $pagination,
            'csrfToken'  => Csrf::token(),
            'flash'      => [
                'success' => Session::getFlash('success'),
                'error'   => Session::getFlash('error'),
            ],
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function showCreateForm(array $vars = []): void
    {
        $this->requireAdmin();

        $pageTitle   = 'Novo Usuário Final';
        $contentView = __DIR__ . '/../../View/admin/portal_users/form.php';
        $viewData    = [
            'mode'      => 'create',
            'user'      => null,
            'errors'    => Session::getFlash('errors') ?? [],
            'old'       => Session::getFlash('old') ?? [],
            'csrfToken' => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function store(array $vars = []): void
    {
        $admin = $this->requireAdmin();

        $post  = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/portal-users/create');
        }

        $data = [
            'full_name'       => trim($post['full_name'] ?? ''),
            'email'           => trim($post['email'] ?? ''),
            'document_number' => $this->normalizeCpf($post['document_number'] ?? ''),
            'phone_number'    => trim($post['phone_number'] ?? ''),
            'external_id'     => trim($post['external_id'] ?? ''),
            'notes'           => trim($post['notes'] ?? ''),
            'status'          => $post['status'] ?? 'INVITED',
            'password'        => (string)($post['password'] ?? ''),
            'password_confirmation' => (string)($post['password_confirmation'] ?? ''),
        ];

        $errors = $this->validateData($data, true);

        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', $data);
            $this->redirect('/admin/portal-users/create');
        }

        $newId = $this->repo->create([
            'full_name'       => $data['full_name'],
            'email'           => $data['email'],
            'document_number' => $data['document_number'],
            'phone_number'    => $data['phone_number'],
            'external_id'     => $data['external_id'],
            'notes'           => $data['notes'],
            'status'          => $data['status'],
        ]);

        $this->audit->log('ADMIN', (int)$admin['id'], 'PORTAL_USER_CREATED', 'PORTAL_USER', $newId);

        Session::flash('success', 'Usuário final criado com sucesso.');
        $this->redirect('/admin/portal-users');
    }

    public function showEditForm(array $vars = []): void
    {
        $admin = $this->requireAdmin();

        $id   = (int)($vars['id'] ?? 0);
        $user = $this->repo->findById($id);

        if (!$user) {
            Session::flash('error', 'Usuário não encontrado.');
            $this->redirect('/admin/portal-users');
        }

        $tokens = $this->tokenRepo->listRecentByUser($id, 10);

        $pageTitle   = 'Editar Usuário Final';
        $contentView = __DIR__ . '/../../View/admin/portal_users/form.php';
        $viewData    = [
            'mode'      => 'edit',
            'user'      => $user,
            'tokens'    => $tokens,
            'errors'    => Session::getFlash('errors') ?? [],
            'old'       => Session::getFlash('old') ?? [],
            'csrfToken' => Csrf::token(),
        ];

        require __DIR__ . '/../../View/admin/layouts/base.php';
    }

    public function update(array $vars = []): void
    {
        $admin = $this->requireAdmin();

        $id    = (int)($vars['id'] ?? 0);
        $post  = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect("/admin/portal-users/{$id}/edit");
        }

        $data = [
            'full_name'       => trim($post['full_name'] ?? ''),
            'email'           => trim($post['email'] ?? ''),
            'document_number' => $this->normalizeCpf($post['document_number'] ?? ''),
            'phone_number'    => trim($post['phone_number'] ?? ''),
            'external_id'     => trim($post['external_id'] ?? ''),
            'notes'           => trim($post['notes'] ?? ''),
            'status'          => $post['status'] ?? 'INVITED',
            'password'        => (string)($post['password'] ?? ''),
            'password_confirmation' => (string)($post['password_confirmation'] ?? ''),
        ];

        $errors = $this->validateData($data, false);

        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', $data);
            $this->redirect("/admin/portal-users/{$id}/edit");
        }

        $update = [
            'full_name'       => $data['full_name'],
            'email'           => $data['email'],
            'document_number' => $data['document_number'],
            'phone_number'    => $data['phone_number'],
            'external_id'     => $data['external_id'],
            'notes'           => $data['notes'],
            'status'          => $data['status'],
        ];

        $this->repo->update($id, $update);
        $this->audit->log('ADMIN', (int)$admin['id'], 'PORTAL_USER_UPDATED', 'PORTAL_USER', $id);

        Session::flash('success', 'Usuário final atualizado com sucesso.');
        $this->redirect('/admin/portal-users');
    }

    public function deactivate(array $vars = []): void
    {
        $admin = $this->requireAdmin();

        $id    = (int)($vars['id'] ?? 0);
        $post  = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect('/admin/portal-users');
        }

        $this->repo->deactivate($id);
        $this->audit->log('ADMIN', (int)$admin['id'], 'PORTAL_USER_DEACTIVATED', 'PORTAL_USER', $id);

        Session::flash('success', 'Usuário final desativado com sucesso.');
        $this->redirect('/admin/portal-users');
    }

    // ----------------- helpers -----------------

    private function validateData(array $data, bool $isCreate): array
    {
        $errors = [];

        if (!v::stringType()->length(3, 190)->validate($data['full_name'])) {
            $errors['full_name'] = 'Nome deve ter pelo menos 3 caracteres.';
        }

        if ($data['email'] !== '' && !v::email()->length(1, 190)->validate($data['email'])) {
            $errors['email'] = 'E-mail inválido.';
        }

        if ($data['status'] && !in_array($data['status'], ['ACTIVE', 'INACTIVE', 'INVITED', 'BLOCKED'], true)) {
            $errors['status'] = 'Status inválido.';
        }

        if ($data['document_number'] !== '' && !$this->isValidCpf($data['document_number'])) {
            $errors['document_number'] = 'CPF inválido.';
        }

        return $errors;
    }

    private function normalizeCpf(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    private function isValidCpf(string $cpf): bool
    {
        $cpf = $this->normalizeCpf($cpf);

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $sum = 0;
            for ($i = 0; $i < $t; $i++) {
                $sum += (int)$cpf[$i] * (($t + 1) - $i);
            }
            $digit = ((10 * $sum) % 11) % 10;
            if ((int)$cpf[$t] !== $digit) {
                return false;
            }
        }

        return true;
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    public function generateToken(array $vars = []): void
    {
        $admin = $this->requireAdmin();

        $id   = (int)($vars['id'] ?? 0);
        $post = $_POST;
        $token = $post['_token'] ?? '';

        if (!Csrf::validate($token)) {
            Session::flash('error', 'Sessão expirada. Tente novamente.');
            $this->redirect("/admin/portal-users/{$id}/edit");
        }

        $user = $this->repo->findById($id);
        if (!$user) {
            Session::flash('error', 'Usuário não encontrado.');
            $this->redirect('/admin/portal-users');
        }

        // validade escolhida
        $validity = $post['validity'] ?? '24h';
        $intervalSpec = match ($validity) {
            '1h'  => 'PT1H',
            '24h' => 'P1D',
            '7d'  => 'P7D',
            default => 'P1D',
        };

        $now        = new DateTimeImmutable('now');
        $expiresAt  = $now->add(new DateInterval($intervalSpec));
        $code       = RandomToken::shortCode(12); // 12 caracteres

        // opcional: revogar tokens pendentes anteriores
        $this->tokenRepo->revokePendingTokens($id);

        $tokenId = $this->tokenRepo->createToken($id, $code, $expiresAt);
        $this->audit->log('ADMIN', (int)$admin['id'], 'PORTAL_USER_TOKEN_GENERATED', 'PORTAL_ACCESS_TOKEN', $tokenId, [
            'expires_at' => $expiresAt->format(DateTimeImmutable::ATOM),
        ]);

        Session::flash(
            'success',
            sprintf(
                'Código de acesso gerado: %s (válido até %s)',
                $code,
                $expiresAt->format('d/m/Y H:i')
            )
        );

        $this->redirect("/admin/portal-users/{$id}/edit");
    }
}
