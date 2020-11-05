<?php
declare(strict_types=1);

namespace Kernel\Contracts;

use Kernel\Abstracts\AbstractEntity;
use Kernel\Entities\Pagination;
use Maer\Entity\Collection;

interface RepositoryInterface
{
    /**
     * Insert an item
     *
     * @param AbstractEntity $item
     *
     * @return AbstractEntity|null
     */
    public function insert(AbstractEntity $item): ?AbstractEntity;


    /**
     * Get items
     *
     * @return Collection
     */
    public function get(array $filters = []): Collection;


    /**
     * Get items for a specific page (pagination)
     *
     * @param int $page
     * @param int $perPage
     * @param array $filters
     *
     * @return Pagination
     */
    public function forPage(int $page = 1, int $perPage = 10, array $filters = []): Pagination;


    /**
     * Get an item by id
     *
     * @param mixed $id
     * @param array $filters
     *
     * @return AbstractEntity|null
     */
    public function byId($id, array $filters = []): ?AbstractEntity;


    /**
     * Update an item
     *
     * @param AbstractEntity $item
     *
     * @return AbstractEntity|null
     */
    public function update(AbstractEntity $item): ?AbstractEntity;


    /**
     * Delete an item
     *
     * @param mixed $id
     *
     * @return bool
     */
    public function delete($id): bool;
}
