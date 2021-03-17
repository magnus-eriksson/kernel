<?php
declare(strict_types = 1);

namespace Kernel\Abstracts;

use Maer\Entity\Entity;

abstract class AbstractEntity extends Entity
{
    /**
     * Get all values that should be stored in the database
     *
     * @return array
     */
    public function dbData(): array
    {
        return $this->asArray(['id']);
    }


    /**
     * Check if the entity uses soft deletes
     *
     * @return bool
     */
    public static function usesSoftDelete(): bool
    {
        return property_exists(static::class, 'deletedAt');
    }
}
