<?php

namespace MaduLinux\RepositoryPattern\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait BulkOperations
{
    /**
     * Insert multiple records
     *
     * @param array $records
     * @return bool
     */
    public function insert(array $records): bool
    {
        $this->trigger('creating', $records);
        $result = $this->model->insert($records);
        $this->trigger('created', $records);
        $this->flushCache();
        return $result;
    }

    /**
     * Update multiple records
     *
     * @param array $values
     * @param array $conditions
     * @return int
     */
    public function bulkUpdate(array $values, array $conditions): int
    {
        $this->trigger('updating', [$values, $conditions]);
        $result = $this->model->where($conditions)->update($values);
        $this->trigger('updated', [$values, $conditions]);
        $this->flushCache();
        return $result;
    }

    /**
     * Delete multiple records
     *
     * @param array $ids
     * @return int
     */
    public function bulkDelete(array $ids): int
    {
        $this->trigger('deleting', $ids);
        $result = $this->model->destroy($ids);
        $this->trigger('deleted', $ids);
        $this->flushCache();
        return $result;
    }

    /**
     * Upsert records
     *
     * @param array $values
     * @param array|string $uniqueBy
     * @param array|null $update
     * @return bool
     */
    public function upsert(array $values, $uniqueBy, ?array $update = null): bool
    {
        $this->trigger('creating', $values);
        $result = $this->model->upsert($values, $uniqueBy, $update);
        $this->trigger('created', $values);
        $this->flushCache();
        return $result;
    }

    /**
     * Chunk records and process them
     *
     * @param int $count
     * @param callable $callback
     * @return bool
     */
    public function chunk(int $count, callable $callback): bool
    {
        return $this->model->chunk($count, $callback);
    }
}
