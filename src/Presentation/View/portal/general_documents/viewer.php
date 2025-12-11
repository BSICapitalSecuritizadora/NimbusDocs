<?php
$ext = strtolower(pathinfo($doc['file_original_name'], PATHINFO_EXTENSION));
$streamUrl = "/portal/documents/general/{$doc['id']}/stream";
?>

<div class="container py-4">
    <h1 class="h4 mb-3"><?= htmlspecialchars($doc['title']) ?></h1>
    <p class="text-muted"><?= htmlspecialchars($doc['description'] ?? '') ?></p>

    <div class="card">
        <div class="card-body" style="min-height: 600px;">
            <?php if ($ext === 'pdf'): ?>
                <iframe src="<?= $streamUrl ?>" width="100%" height="600px" style="border:none;"></iframe>

            <?php elseif (in_array($ext, ['png', 'jpg', 'jpeg'])): ?>
                <img src="<?= $streamUrl ?>" class="img-fluid" alt="Imagem">

            <?php else: ?>
                <div class="alert alert-warning">
                    Este tipo de arquivo não pode ser visualizado diretamente.
                    <br>
                    <a href="<?= $streamUrl ?>" class="btn btn-primary mt-3">Baixar documento</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <a href="/portal/documents/general" class="btn btn-outline-secondary mt-3">← Voltar</a>
</div>