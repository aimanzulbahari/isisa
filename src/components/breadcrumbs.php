<?php
declare(strict_types=1);

/**
 * Breadcrumb component (the path shown at the top of a page).
 *
 * The path comes from routes/breadcrumbs-route.php, for the route that is open now.
 * A page can also set its own path BEFORE including this file:
 *
 *   $breadcrumbs = [
 *       ['label' => 'Dashboard', 'route' => 'dashboard'],
 *       ['label' => 'Users'],
 *   ];
 *
 * If there is no path for the page, nothing is printed.
 */
$crumbs = $breadcrumbs ?? breadcrumbTrail();
$last   = count($crumbs) - 1;

if ($crumbs !== []):
?>
<nav aria-label="breadcrumb" class="breadcrumb-wrapper animate-in">
    <ol class="breadcrumb breadcrumb-custom">
        <?php foreach ($crumbs as $i => $crumb): ?>
            <?php
            $label  = e($crumb['label']);
            $icon   = $i === 0 ? '<i class="bi bi-house" aria-hidden="true"></i> ' : '';
            $isLast = $i === $last;
            ?>
            <?php if ($isLast): ?>
                <!-- Current page: not a link -->
                <li class="breadcrumb-item active" aria-current="page"><?= $icon ?><span><?= $label ?></span></li>
            <?php elseif (!empty($crumb['route'])): ?>
                <li class="breadcrumb-item">
                    <a href="<?= e(routeUrl($crumb['route'])) ?>"><?= $icon ?><span><?= $label ?></span></a>
                </li>
            <?php else: ?>
                <!-- A group without a page of its own: plain text -->
                <li class="breadcrumb-item"><?= $icon ?><span><?= $label ?></span></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>
<?php endif; ?>
