<?php
declare(strict_types=1);

/**
 * Pagination helpers.
 *
 * Example:
 *   $page  = paginate($totalRows, currentPage(), 10);
 *   $sql  .= " LIMIT {$page['per_page']} OFFSET {$page['offset']}";
 *   // then show the links with src/components/pagination.php using $page
 */

if (!function_exists('currentPage')) {
    /** Page number from ?page=2 (never lower than 1). */
    function currentPage(string $param = 'page'): int
    {
        return max(1, toInt($_GET[$param] ?? 1, 1));
    }
}

if (!function_exists('paginate')) {
    /**
     * Work out everything needed to show a page of results.
     *
     * @return array{total:int, per_page:int, current:int, last:int, offset:int, from:int, to:int,
     *               has_prev:bool, has_next:bool, pages:int[]}
     */
    function paginate(int $total, int $page = 1, int $perPage = 10, int $window = 2): array
    {
        $perPage = max(1, min($perPage, 100));  // Never allow huge pages
        $last    = max(1, (int) ceil($total / $perPage));
        $page    = min(max(1, $page), $last);   // Keep the page inside the range
        $offset  = ($page - 1) * $perPage;

        return [
            'total'    => $total,
            'per_page' => $perPage,
            'current'  => $page,
            'last'     => $last,
            'offset'   => $offset,
            'from'     => $total === 0 ? 0 : $offset + 1,
            'to'       => min($offset + $perPage, $total),
            'has_prev' => $page > 1,
            'has_next' => $page < $last,
            // Page numbers to show: the current page and a few on each side
            'pages'    => range(max(1, $page - $window), min($last, $page + $window)),
        ];
    }
}

if (!function_exists('pageUrl')) {
    /** The current URL with ?page= changed. Other filters in the URL are kept. */
    function pageUrl(int $page, string $param = 'page'): string
    {
        $query = $_GET;
        $query[$param] = $page;

        $path = strtok((string) ($_SERVER['REQUEST_URI'] ?? ''), '?');
        return $path . '?' . http_build_query($query);
    }
}
