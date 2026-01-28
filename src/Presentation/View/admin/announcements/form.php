<?php

/** @var array $data */
/** @var string $csrfToken */
/** @var string $mode */

$isEdit = ($mode === 'edit');
$action = $isEdit
    ? "/admin/announcements/{$data['id']}" 
    : "/admin/announcements";

$title = $isEdit ? 'Editar Comunicado' : 'Novo Comunicado';
$subtitle = $isEdit ? 'Atualize as informações do aviso' : 'Crie uma nova mensagem para os usuários';

$startsDate = $data['starts_at'] ? substr($data['starts_at'], 0, 10) : '';
$endsDate   = $data['ends_at']   ? substr($data['ends_at'],   0, 10) : '';
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="/admin/announcements" class="text-decoration-none">
            <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
                <i class="bi bi-megaphone-fill text-white"></i>
            </div>
        </a>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-muted mb-0 small"><?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>
    <a href="/admin/announcements" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-list-ul me-1"></i>
        Visualizar Listagem
    </a>
</div>

<form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    
    <div class="row">
        <!-- Main Column -->
        <div class="col-lg-8">
            <div class="nd-card mb-4">
                <div class="nd-card-header d-flex align-items-center gap-2">
                    <i class="bi bi-card-text" style="color: var(--nd-gold-500);"></i>
                    <h5 class="nd-card-title mb-0">Conteúdo do Aviso</h5>
                </div>
                <div class="nd-card-body">
                    <!-- Title -->
                    <div class="mb-4">
                        <label class="nd-label">Título da Mensagem <span class="text-danger">*</span></label>
                        <div class="nd-input-group">
                            <input type="text"
                                name="title"
                                class="nd-input"
                                required
                                placeholder="Ex: Manutenção Programada"
                                value="<?= htmlspecialchars($data['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                style="padding-left: 2.5rem;">
                            <i class="bi bi-type-h1 nd-input-icon"></i>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="mb-4">
                        <label class="nd-label">Mensagem Detalhada <span class="text-danger">*</span></label>
                        <textarea name="body"
                            rows="8"
                            class="nd-input w-100"
                            required
                            placeholder="Descreva o comunicado..." 
                            style="resize: none;"><?= htmlspecialchars($data['body'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                         <div class="form-text small text-end">Suporta formatação simples.</div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 d-none d-lg-flex">
                 <a href="/admin/announcements" class="nd-btn nd-btn-outline">
                    <i class="bi bi-x-lg me-1"></i> Cancelar
                </a>
                <button type="submit" class="nd-btn nd-btn-primary">
                    <i class="bi bi-check-lg me-1"></i>
                    Salvar Publicação
                </button>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Settings Card -->
            <div class="nd-card mb-4">
                <div class="nd-card-header d-flex align-items-center gap-2">
                    <i class="bi bi-sliders2" style="color: var(--nd-gold-500);"></i>
                    <h5 class="nd-card-title mb-0">Configurações</h5>
                </div>
                <div class="nd-card-body">
                     <!-- Priority -->
                    <div class="mb-4">
                        <label class="nd-label mb-2">Nível de Prioridade</label>
                        <div class="nd-input-group">
                            <select name="level" class="nd-input form-select" style="padding-left: 2.5rem;">
                                <?php
                                $levels = [
                                    'info' => ['label' => 'Informativo', 'icon' => 'bi-info-circle'],
                                    'success' => ['label' => 'Positivo', 'icon' => 'bi-check-circle'],
                                    'warning' => ['label' => 'Atenção', 'icon' => 'bi-exclamation-triangle'],
                                    'danger' => ['label' => 'Urgente', 'icon' => 'bi-exclamation-octagon']
                                ];
                                foreach ($levels as $key => $val):
                                    $selected = (($data['level'] ?? 'info') === $key) ? 'selected' : '';
                                ?>
                                    <option value="<?= $key ?>" <?= $selected ?>>
                                        <?= $val['label'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="bi bi-flag nd-input-icon"></i>
                        </div>
                    </div>

                    <hr class="text-muted opacity-25">

                    <!-- Status Switch -->
                    <div class="mb-0">
                         <div class="form-check form-switch p-0 m-0 d-flex justify-content-between align-items-center">
                            <label class="form-check-label text-dark fw-medium" for="is_active">
                                Publicar Imediatamente
                            </label>
                            <input class="form-check-input ms-0" 
                                style="width: 3em; height: 1.5em;"
                                type="checkbox" 
                                id="is_active" 
                                name="is_active" 
                                value="1" 
                                <?= ((int)($data['is_active'] ?? 1) === 1) ? 'checked' : '' ?>>
                        </div>
                        <div class="small text-muted mt-1">
                            Se desativado, ficará como rascunho invisível.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vigency Card -->
            <div class="nd-card mb-4">
                 <div class="nd-card-header d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-range" style="color: var(--nd-navy-500);"></i>
                    <h5 class="nd-card-title mb-0 text-muted small text-uppercase">Programação de Vigência</h5>
                </div>
                <div class="nd-card-body bg-light">
                    <div class="mb-3">
                        <label class="nd-label">Início da Exibição</label>
                        <div class="nd-input-group">
                            <input type="date"
                                name="starts_at"
                                class="nd-input bg-white"
                                style="padding-left: 2.5rem;"
                                value="<?= htmlspecialchars($startsDate, ENT_QUOTES, 'UTF-8') ?>">
                            <i class="bi bi-calendar-event nd-input-icon"></i>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="nd-label">Fim da Exibição</label>
                         <div class="nd-input-group">
                            <input type="date"
                                name="ends_at"
                                class="nd-input bg-white"
                                style="padding-left: 2.5rem;"
                                value="<?= htmlspecialchars($endsDate, ENT_QUOTES, 'UTF-8') ?>">
                            <i class="bi bi-calendar-x nd-input-icon"></i>
                        </div>
                         <div class="form-text small">Deixe em branco para exibir indefinidamente.</div>
                    </div>
                </div>
            </div>

             <!-- Mobile Friendly Actions -->
            <div class="d-flex justify-content-end gap-2 d-lg-none pb-4">
                 <a href="/admin/announcements" class="nd-btn nd-btn-outline w-50">
                    Cancelar
                </a>
                <button type="submit" class="nd-btn nd-btn-primary w-50">
                    Salvar
                </button>
            </div>

        </div>
    </div>
</form>