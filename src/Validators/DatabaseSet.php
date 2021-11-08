<?php
namespace Kernel\Validators;

use Database\Connection;
use Maer\Validator\Sets\AbstractSet;

class DatabaseSet extends AbstractSet
{
    /**
     * @var Connection
     */
    protected Connection $db;


    /**
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }


    /**
     * Check if a value is unique in the database
     *
     * @param string|int $input
     * @param string $table
     * @param string $column
     * @param string|int|null $except
     * @param string|null $exceptColumn
     *
     * @return string|bool
     */
    public function dbUnique(string|int $input, string $table, string $column, string|int|null $except = null, ?string $exceptColumn = null): string|bool
    {
        $query = $this->db->table($table)
            ->where($column, $input);

        if ($except !== null && $exceptColumn !== null) {
            $query->where($exceptColumn, '!=', $except);
        }

        return $query->count() === 0
            ?: "There alread is a record with the value $input";
    }


    /**
     * Check if a value exists in the database
     *
     * @param string|int $input
     * @param string $table
     * @param string $column
     *
     * @return string|bool
     */
    public function dbContains(string|int $input, string $table, string $column): string|bool
    {
        $matches = $this->db->table($table)
            ->where($column, $input)
            ->count();

        return $matches > 0
            ?: "Invalid value";
    }


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'dbUnique' => [$this, 'dbUnique'],
        ];
    }
}
