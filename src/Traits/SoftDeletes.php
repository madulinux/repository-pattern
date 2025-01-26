<?php

namespace MaduLinux\RepositoryPattern\Traits;

use Illuminate\Database\Eloquent\SoftDeletes as EloquentSoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

trait SoftDeletes
{
    /**
     * Include soft deleted records
     *
     * @return self
     */
    public function withTrashed(): self
    {
        if (in_array(EloquentSoftDeletes::class, class_uses_recursive($this->model))) {
            $this->query->withTrashed();
        }
        return $this;
    }

    /**
     * Get only soft deleted records
     *
     * @return self
     */
    public function onlyTrashed(): self
    {
        if (in_array(EloquentSoftDeletes::class, class_uses_recursive($this->model))) {
            $this->query->onlyTrashed();
        }
        return $this;
    }

    /**
     * Restore soft deleted records
     *
     * @param int|string|array $ids
     * @return bool
     */
    public function restore($ids): bool
    {
        if (!in_array(EloquentSoftDeletes::class, class_uses_recursive($this->model))) {
            return false;
        }

        if (is_array($ids)) {
            return $this->model->whereIn('id', $ids)->restore();
        }

        $record = $this->find($ids);
        if (!$record) {
            return false;
        }

        return $record->restore();
    }

    /**
     * Force delete record
     *
     * @param int|string $id
     * @return bool
     */
    public function forceDelete($id): bool
    {
        if (!in_array(EloquentSoftDeletes::class, class_uses_recursive($this->model))) {
            return $this->delete($id);
        }

        $record = $this->find($id);
        if (!$record) {
            return false;
        }

        return $record->forceDelete();
    }
}
