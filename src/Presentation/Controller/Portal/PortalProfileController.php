<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Portal;

use App\Infrastructure\Persistence\MySqlPortalUserRepository;
use App\Support\Auth;
use App\Support\Csrf;
use App\Support\Session;

final class PortalProfileController
{
    private MySqlPortalUserRepository $userRepo;

    public function __construct(private array $config)
    {
        $this->userRepo = new MySqlPortalUserRepository($config['pdo']);
    }

    public function edit(): void
    {
        $userSession = Auth::requirePortalUser();
        $user = $this->userRepo->findById((int)$userSession['id']);

        if (!$user) {
            Session::flash('error', 'Usuário não encontrado.');
            header('Location: /portal/logout');
            exit;
        }

        $pageTitle   = 'Meu Perfil';
        $contentView = __DIR__ . '/../../View/portal/profile.php';
        
        $viewData = [
            'user'      => $user,
            'csrfToken' => Csrf::token(),
            'flash'     => [
                'success' => Session::getFlash('success'),
                'error'   => Session::getFlash('error'),
            ]
        ];

        require __DIR__ . '/../../View/portal/layouts/base.php';
    }

    public function update(): void
    {
        $userSession = Auth::requirePortalUser();
        $id = (int)$userSession['id'];

        if (!Csrf::validate($_POST['_token'] ?? '')) {
            Session::flash('error', 'Token de segurança inválido.');
            header('Location: /portal/profile');
            exit;
        }

        $fullName = trim($_POST['full_name'] ?? '');
        $phone    = trim($_POST['phone_number'] ?? '');
        
        // Basic validation
        if (strlen($fullName) < 3) {
            Session::flash('error', 'O nome deve ter pelo menos 3 caracteres.');
            header('Location: /portal/profile');
            exit;
        }

        // Update DB
        $this->userRepo->update($id, [
            'full_name'    => $fullName,
            'phone_number' => $phone,
        ]);

        // Refresh Session
        $updatedUser = $this->userRepo->findById($id);
        if ($updatedUser) {
            Session::put('portal_user', [
                'id'              => (int)$updatedUser['id'],
                'full_name'       => $updatedUser['full_name'],
                'email'           => $updatedUser['email'],
                'document_number' => $updatedUser['document_number'],
                'phone_number'    => $updatedUser['phone_number'],
            ]);
        }

        Session::flash('success', 'Perfil atualizado com sucesso!');
        header('Location: /portal/profile');
        exit;
    }
}
