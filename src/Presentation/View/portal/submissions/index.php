<?php
$items = $pagination['items'] ?? [];
?>
<h1 class="h4 mb-3">Minhas submissões</h1>

<p>Lista de submissões (placeholder).</p>

<ul>
    <?php foreach ($items as $s): ?>
        <li>
            <?= htmlspecialchars($s['title'], ENT_QUOTES, 'UTF-8') ?>
        </li>
    <?php endforeach; ?>
</ul>