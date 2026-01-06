<?php

/** @var array $data */
/** @var string $csrfToken */
/** @var string $mode */

$isEdit = ($mode === 'edit');
$action = $isEdit
    ? "/admin/announcements/{$data['id']}" // No update method on controller? usually PUT or POST to specific route. Assuming controller handles it.
    : "/admin/announcements";

// Controller seems to use same route for update? 
// Checking controller logic would be ideal but sticking to current form action structure and just styling:
if ($isEdit) {
    // Usually update routes might differ or use method spoofing. 
    // The previous form action was "/admin/announcements/{id}" likely mapping to an update route via POST.
}

$title = $isEdit ? 'Editar Comunicado' : 'Novo Comunicado';
$subtitle = $isEdit ? 'Atualize as informações do comunicado' : 'Crie uma nova mensagem para os usuários';

$startsDate = $data['starts_at'] ? substr($data['starts_at'], 0, 10) : '';
$endsDate   = $data['ends_at']   ? substr($data['ends_at'],   0, 10) : '';
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="nd-avatar nd-avatar-lg" style="background: var(--nd-navy-600);">
            <i class="bi bi-megaphone text-white"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 fw-bold" style="color: var(--nd-navy-900);"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-muted mb-0 small"><?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>
    <a href="/admin/announcements" class="nd-btn nd-btn-outline nd-btn-sm">
        <i class="bi bi-arrow-left me-1"></i>
        Voltar
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="nd-card">
            <div class="nd-card-header d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square" style="color: var(--nd-gold-500);"></i>
                <h5 class="nd-card-title mb-0">Informações do Comunicado</h5>
            </div>

            <div class="nd-card-body">
                <form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="mb-4">
                        <label class="nd-label">Título</label>
                        <div class="nd-input-group">
                            <input type="text"
                                name="title"
                                class="nd-input"
                                required
                                placeholder="E.g. Manutenção Programada"
                                value="<?= htmlspecialchars($data['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                style="padding-left: 2.5rem;">
                            <i class="bi bi-type-h1 nd-input-icon"></i>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="nd-label">Mensagem</label>
                        <textarea name="body"
                            rows="5"
                            class="nd-input"
                            required
                            placeholder="Digite o conteúdo do comunicado aqui..."><?= htmlspecialchars($data['body'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <div class="row gx-3">
                        <div class="col-md-6 mb-4">
                            <label class="nd-label">Nível de Prioridade</label>
                            <div class="nd-input-group">
                                <select name="level" class="nd-input form-select" style="padding-left: 2.5rem;">
                                    <?php
                                    $levels = ['info' => 'Informativo', 'success' => 'Sucesso', 'warning' => 'Alerta', 'danger' => 'Crítico'];
                                    foreach ($levels as $key => $label):
                                        $selected = (($data['level'] ?? 'info') === $key) ? 'selected' : '';
                                    ?>
                                        <option value="<?= $key ?>" <?= $selected ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="bi bi-flag nd-input-icon"></i>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4 d-flex align-items-center">
                            <div class="form-check nd-form-check mt-3">
                                <input class="form-check-input"
                                    type="checkbox"
                                    id="is_active"
                                    name="is_active"
                                    value="1"
                                    <?= ((int)($data['is_active'] ?? 1) === 1) ? 'checked' : '' ?>>
                                <label class="form-check-label text-dark fw-medium" for="is_active">
                                    Comunicado Ativo
                                </label>
                                <div class="small text-muted" style="font-size: 0.75rem;">Visível no portal durante o período</div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 mb-4 rounded" style="background: var(--nd-gray-50); border: 1px dashed var(--nd-gray-300);">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-calendar-range me-2" style="color: var(--nd-navy-500);"></i>
                            <h6 class="fw-bold mb-0 text-dark">Período de Exibição (Opcional)</h6>
                        </div>
                        <div class="row gx-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="nd-label">Início</label>
                                <input type="date"
                                    name="starts_at"
                                    class="nd-input"
                                    value="<?= htmlspecialchars($startsDate, ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="nd-label">Fim</label>
                                <input type="date"
                                    name="ends_at"
                                    class="nd-input"
                                    value="<?= htmlspecialchars($endsDate, ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="/admin/announcements" class="nd-btn nd-btn-outline">
                            Cancelar
                        </a>
                        <button type="submit" class="nd-btn nd-btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            Salvar Comunicado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>