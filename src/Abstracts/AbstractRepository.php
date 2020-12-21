<?php
declare(strict_types=1);

namespace Kernel\Abstracts;

use Database\Connection;
use Database\Query\Builder;
use DateTime;
use DateTimeZone;
use Kernel\Entities\Pagination;
use Maer\Entity\Collection;

abstract class AbstractRepository
{
    /**
     * @var Connection $db
     */
    protected Connection $db;

    /**
     * Name of the table
     *
     * @var string
     */
    protected string $table = '';

    /**
     * Entity to convert into
     *
     * @var string
     */
    protected string $entity;


    /**
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }


    /**
     * Insert an item
     *
     * @param AbstractEntity $item
     *
     * @return AbstractEntity|null
     */
    public function insert(AbstractEntity $item): ?AbstractEntity
    {
        if ($item->has('createdAt') && empty($item->createdAt)) {
            $item->createdAt = $this->getDate();
        }

        if ($item->has('updatedAt') && empty($item->updatedAt)) {
            $item->updatedAt = $this->getDate();
        }

        $id = $this->table()
            ->insertGetId($item->dbData());

        return $id
            ? $this->byId($id)
            : null;
    }


    /**
     * Get items
     *
     * @return Collection
     */
    public function get(array $filters = []): Collection
    {
        $result = $this->baseQuery($filters)
            ->get();

        /**
         * @var Collection
         */
        $items = $this->resultIntoEntity($result, true);

        return $items;
    }


    /**
     * Get items for a specific page (pagination)
     *
     * @param int $page
     * @param int $perPage
     * @param array $filters
     *
     * @return Pagination
     */
    public function forPage(int $page = 1, int $perPage = 10, array $filters = []): Pagination
    {
        // Normalize the page and perPage values
        $page = $page < 1 ? 1 : $page;

        if ($perPage < 1 || $perPage > 100) {
            $perPage = 10;
        }

        /**
         * @var Builder
         */
        $query = $this->baseQuery($filters);

        // Get the total amount of items
        $total = $query->count();

        // Calculate how many pages there are
        $pages = $total ? ceil($total / $perPage) : 0;

        // Calculate how many items to skip
        $skip = ($page - 1) * $perPage;

        // Get the items for the requested page
        $items = $query->forPage($page, $perPage)->get();

        return new Pagination([
            'total'       => $total,
            'previous'    => $page > 1 ? $page - 1 : null,
            'next'        => $page < $pages ? $page + 1 : null,
            'pages'       => $pages,
            'currentPage' => $page > $perPage ? $perPage : $page,
            'items'       => $this->resultIntoEntity($items),
        ]);
    }


    /**
     * Get an item by id
     *
     * @param mixed $id
     * @param array $filters
     *
     * @return AbstractEntity|null
     */
    public function byId($id, array $filters = []): ?AbstractEntity
    {
        return $this->oneByColumnValue($id, 'id', $filters);
    }


    /**
     * Get an item by a column value
     *
     * @param [type] $value
     * @param string $column
     * @param array $filters
     *
     * @return AbstractEntity|null
     */
    protected function oneByColumnValue($value, string $column, array $filters = []): ?AbstractEntity
    {
        $result = $this->baseQuery($filters)
            ->where($column, $value)
            ->first();

        /**
         * @var AbstractEntity|null
         */
        $item = $this->resultIntoEntity($result);

        return $item;
    }


    /**
     * Update an item
     *
     * @param AbstractEntity $item
     *
     * @return AbstractEntity|null
     */
    public function update(AbstractEntity $item): ?AbstractEntity
    {
        if ($item->has('updatedAt')) {
            $item->updatedAt = $this->getDate();
        }

        /**
         * @var \PDOStatement
         */
        $stmt = $this->table()
            ->where('id', $item->id)
            ->update($item->dbData());

        return $stmt->rowCount()
            ? $this->byId($item->id)
            : null;
    }


    /**
     * Delete an item
     *
     * @param mixed $id
     *
     * @return bool
     */
    public function delete($id): bool
    {
        $query = $this->table()
            ->where('id', $id);

        if ($this->entity && $this->entity::usesSoftDelete()) {
            $query->update([
                'deletedAt' => $this->getDate(),
            ]);
        } else {
            $query->delete();
        }

        return $this->byId($id) === null;
    }


    /**
     * Get the table
     *
     * @return Builder
     */
    protected function table(): Builder
    {
        return $this->db->table($this->table);
    }


    /**
     * Get the base query
     *
     * @param array $filters
     *
     * @return Builder
     */
    protected function baseQuery(array $filters = []): Builder
    {
        $query = $this->table();

        if (is_array($filters['orderBy'] ?? null)) {
            foreach ($filters['orderBy'] as $col => $order) {
                $query->orderBy($col, $order);
            }
        }

        if (is_array($filters['ids'] ?? null)) {
            $query->whereIn('id', $filters['ids']);
        }

        $includeDeleted = $filters['includeDeleted'] ?? false;
        $onlyDeleted    = $filters['onlyDeleted'] ?? false;
        if ($onlyDeleted === false && $this->entity::usesSoftDelete() && $includeDeleted !== true) {
            $query->where('deletedAt', null);
        }

        $onlyDeleted = $filters['onlyDeleted'] ?? false;
        if ($this->entity::usesSoftDelete() && $onlyDeleted === true) {
            $query->where('deletedAt', '!=', null);
        }

        return $query;
    }


    /**
     * Convert results into entities
     *
     * @param mixed $result
     * @param bool $asCollection
     *
     * @return void
     */
    protected function resultIntoEntity($result, bool $asCollection = false)
    {
        if ($this->entity === null || empty($result)) {
            return $asCollection ? new Collection : null;
        }

        return $this->entity::make($result);
    }


    /**
     * Get date
     *
     * @param bool $utc
     *
     * @return string
     */
    protected function getDate(bool $utc = true): string
    {
        $timezone = $utc ? new DateTimeZone('UTC') : null;
        $dateTime = new DateTime('now', $timezone);

        return $dateTime->format('Y-m-d H:i:s');
    }
}
