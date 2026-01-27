<?php
/**
 * Pagination Partial
 * 
 * @var string $baseUrl
 * @var array $queryParams
 * @var int $page
 * @var int $pages
 */

$queryParams = $queryParams ?? [];

function getPageUrl($url, $params, $p) {
    $params['page'] = $p;
    return $url . '?' . http_build_query($params);
}
?>

<nav aria-label="Navegação de página" class="nd-pagination">
    <!-- Previous -->
    <div class="nd-page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
        <?php if ($page > 1): ?>
            <a href="<?= htmlspecialchars(getPageUrl($baseUrl, $queryParams, $page - 1)) ?>" class="nd-page-link" aria-label="Anterior">
                <i class="bi bi-chevron-left" aria-hidden="true"></i>
            </a>
        <?php else: ?>
            <span class="nd-page-link" aria-hidden="true">
                <i class="bi bi-chevron-left"></i>
            </span>
        <?php endif; ?>
    </div>

    <!-- Numbers -->
    <?php
    $start = max(1, $page - 2);
    $end = min($pages, $page + 2);

    if ($start > 1) {
        echo '<div class="nd-page-item"><a href="' . htmlspecialchars(getPageUrl($baseUrl, $queryParams, 1)) . '" class="nd-page-link">1</a></div>';
        if ($start > 2) {
            echo '<div class="nd-page-item disabled"><span class="nd-page-link">...</span></div>';
        }
    }

    for ($i = $start; $i <= $end; $i++):
    ?>
        <div class="nd-page-item <?= ($i == $page) ? 'active' : '' ?>">
            <a href="<?= htmlspecialchars(getPageUrl($baseUrl, $queryParams, $i)) ?>" class="nd-page-link">
                <?= $i ?>
            </a>
        </div>
    <?php endfor; ?>

    <?php
    if ($end < $pages) {
        if ($end < $pages - 1) {
            echo '<div class="nd-page-item disabled"><span class="nd-page-link">...</span></div>';
        }
        echo '<div class="nd-page-item"><a href="' . htmlspecialchars(getPageUrl($baseUrl, $queryParams, $pages)) . '" class="nd-page-link">' . $pages . '</a></div>';
    }
    ?>

    <!-- Next -->
    <div class="nd-page-item <?= ($page >= $pages) ? 'disabled' : '' ?>">
        <?php if ($page < $pages): ?>
            <a href="<?= htmlspecialchars(getPageUrl($baseUrl, $queryParams, $page + 1)) ?>" class="nd-page-link" aria-label="Próxima">
                <i class="bi bi-chevron-right" aria-hidden="true"></i>
            </a>
        <?php else: ?>
            <span class="nd-page-link" aria-hidden="true">
                <i class="bi bi-chevron-right"></i>
            </span>
        <?php endif; ?>
    </div>
</nav>
