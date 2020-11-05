<?php
declare(strict_types=1);

namespace Kernel\Entities;

use Maer\Entity\Collection;
use Maer\Entity\Entity;

/**
 * @property int $total
 * @property int|null $previous
 * @property int|null $next
 * @property int $pages
 * @property int $currentPage
 * @property array|Collection $items
 */
class Pagination extends Entity
{
    protected int $total = 0;
    protected ?int $previous = null;
    protected ?int $next  = null;
    protected int $pages = 0;
    protected int $currentPage = 0;
    protected ?Collection $items = null;


    /**
     * Make sure we always get a valid balue from $items
     *
     * @param array $params
     *
     * @return void
     */
    protected function modifier(array &$params)
    {
        $items = $params['items'] ?? null;

        if (is_array($items)) {
            $items = new Collection($items);
        }

        if ($items instanceof Collection === false) {
            $params['items'] = new Collection;
        }
    }


    /**
     * Check if a page is the current page
     *
     * @param int $page
     *
     * @return bool
     */
    public function isCurrent(int $page): bool
    {
        return $page == $this->currentPage;
    }
}
