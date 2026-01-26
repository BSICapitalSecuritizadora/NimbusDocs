<?php
/**
 * Pagination Partial - Truncated
 * 
 * Expected variables:
 * - int $page (current page)
 * - int $pages (total pages)
 * - string $baseUrl (base URL for pagination links, e.g., "/admin/submissions" or "/portal/submissions")
 * - array $queryParams (optional additional query params to preserve)
 */

$page = (int)($page ?? 1);
$pages = (int)($pages ?? 1);
$baseUrl = $baseUrl ?? '';
$queryParams = $queryParams ?? [];

if ($pages <= 1) {
    return; // No pagination needed
}

// Build query string from additional params
$queryString = '';
if (!empty($queryParams)) {
    $filteredParams = array_filter($queryParams, fn($v) => $v !== null && $v !== '');
    if ($filteredParams) {
        $queryString = '&' . http_build_query($filteredParams);
    }
}

/**
 * Generate page numbers with ellipsis
 * Shows: 1 ... (current-1) current (current+1) ... last
 */
function getPaginationRange(int $current, int $total, int $delta = 2): array
{
    $range = [];
    $rangeWithDots = [];
    
    $left = $current - $delta;
    $right = $current + $delta;
    
    for ($i = 1; $i <= $total; $i++) {
        if ($i === 1 || $i === $total || ($i >= $left && $i <= $right)) {
            $range[] = $i;
        }
    }
    
    $prev = null;
    foreach ($range as $i) {
        if ($prev !== null) {
            if ($i - $prev === 2) {
                // Just one number between, add it instead of dots
                $rangeWithDots[] = $prev + 1;
            } elseif ($i - $prev > 2) {
                $rangeWithDots[] = '...';
            }
        }
        $rangeWithDots[] = $i;
        $prev = $i;
    }
    
    return $rangeWithDots;
}

$paginationItems = getPaginationRange($page, $pages);
?>

<nav aria-label="Paginação">
    <ul class="pagination pagination-sm justify-content-end mb-0">
        <!-- Anterior -->
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $baseUrl ?>?page=<?= max(1, $page - 1) ?><?= $queryString ?>" aria-label="Anterior">
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>
        
        <!-- Números com Truncamento -->
        <?php foreach ($paginationItems as $item): ?>
            <?php if ($item === '...'): ?>
                <li class="page-item disabled">
                    <span class="page-link">…</span>
                </li>
            <?php else: ?>
                <li class="page-item <?= $item === $page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $baseUrl ?>?page=<?= $item ?><?= $queryString ?>">
                        <?= $item ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <!-- Próximo -->
        <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $baseUrl ?>?page=<?= min($pages, $page + 1) ?><?= $queryString ?>" aria-label="Próximo">
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    </ul>
</nav>
