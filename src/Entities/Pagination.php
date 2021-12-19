<?php
declare(strict_types=1);

namespace Kernel\Entities;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Exception;
use IteratorAggregate;
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
class Pagination extends Entity implements ArrayAccess, Countable, IteratorAggregate
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


    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        throw new Exception("The pagination object is read-only");
    }


    /**
     * @param mixed $offset
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }


    /**
     * @param mixed $offset
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        if (isset($this->items[$offset])) {
            unset($this->items[$offset]);
        }
    }


    /**
     * @param mixed $offset
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->items[$offset])
            ? $this->items[$offset]
            : null;
    }


    /**
     * Get the current items count
     *
     * @return int
     */
    public function count() : int
    {
        return count($this->items);
    }


    /**
     * Return the iterator
     *
     * @return ArrayIterator
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new ArrayIterator(
            $this->items ? $this->items->asArray() : []
        );
    }
}
