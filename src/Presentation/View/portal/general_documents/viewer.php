<?php
$ext = strtolower(pathinfo($doc['file_original_name'], PATHINFO_EXTENSION));
$streamUrl = "/portal/documents/general/{$doc['id']}/stream";
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0"><?= htmlspecialchars($doc['title']) ?></h1>
            <p class="text-muted small mb-0"><?= htmlspecialchars($doc['description'] ?? '') ?></p>
        </div>
        <a href="<?= $streamUrl ?>?download=1" class="btn btn-primary">
            <i class="bi bi-download me-1"></i> Baixar Arquivo
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0 bg-light text-center d-flex align-items-center justify-content-center" style="min-height: 600px;">
            <?php if ($ext === 'pdf'): ?>
                <iframe src="<?= $streamUrl ?>" width="100%" height="600px" style="border:none;"></iframe>

            <?php elseif (in_array($ext, ['png', 'jpg', 'jpeg'])): ?>
                <img src="<?= $streamUrl ?>" class="img-fluid" style="max-height: 600px;" alt="Visualização do Documento">

            <?php else: ?>
                <div class="alert alert-warning m-5">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Pré-visualização indisponível para este formato (<strong><?= strtoupper($ext) ?></strong>).
                    <br><br>
                    <a href="<?= $streamUrl ?>?download=1" class="btn btn-primary btn-sm">Realizar Download</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-3">
        <a href="/portal/documents/general" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Voltar ao Acervo
        </a>
    </div>
</div>