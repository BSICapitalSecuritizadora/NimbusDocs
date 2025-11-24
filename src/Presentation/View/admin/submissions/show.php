<?php foreach ($files as $f): ?>
    <li>
        <?= htmlspecialchars($f['original_name'], ENT_QUOTES, 'UTF-8') ?>
        <a href="/admin/files/<?= (int)$f['id'] ?>/download"
            class="ms-2 small">
            (baixar)
        </a>
    </li>
<?php endforeach; ?>