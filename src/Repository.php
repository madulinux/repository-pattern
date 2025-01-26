<?php

namespace MaduLinux\RepositoryPattern;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use MaduLinux\RepositoryPattern\DataTransferObjects\PaginationParameters;
use MaduLinux\RepositoryPattern\Traits\Cacheable;
use MaduLinux\RepositoryPattern\Traits\HasEvents;
use MaduLinux\RepositoryPattern\Traits\SoftDeletes;
use MaduLinux\RepositoryPattern\Traits\BulkOperations;
use MaduLinux\RepositoryPattern\Traits\Transactional;
use MaduLinux\RepositoryPattern\Traits\CustomCacheKey;
use MaduLinux\RepositoryPattern\Traits\HasCriteria;

abstract class Repository
{
    use Cacheable,
        HasEvents,
        SoftDeletes,
        BulkOperations,
        Transactional,
        HasCriteria,
        CustomCacheKey {
            Cacheable::getCacheKey insteadof CustomCacheKey;
            CustomCacheKey::getCacheKey as getCustomCacheKey;
        }

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;

    /**
     * Get model instance
     *
     * @return Model
     */
    abstract protected function getModel(): Model;

    /**
     * Get all records
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->remember(__FUNCTION__, [], function () {
            return $this->model->all();
        });
    }

    /**
     * Find record by ID
     *
     * @param int|string $id
     * @return Model|null
     */
    public function find($id): ?Model
    {
        return $this->remember(__FUNCTION__, func_get_args(), function () use ($id) {
            return $this->model->find($id);
        });
    }

    /**
     * Find record by ID or fail
     *
     * @param int|string $id
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id): Model
    {
        return $this->remember(__FUNCTION__, func_get_args(), function () use ($id) {
            return $this->model->findOrFail($id);
        });
    }

    /**
     * Create new record
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        $this->trigger('creating', $data);
        $result = $this->model->create($data);
        $this->trigger('created', $result);
        $this->flushCache();
        return $result;
    }

    /**
     * Update record
     *
     * @param int|string $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }

        $this->trigger('updating', [$id, $data]);
        $result = $record->update($data);
        $this->trigger('updated', [$id, $data]);
        $this->flushCache();
        return $result;
    }

    /**
     * Delete record
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }

        $this->trigger('deleting', $id);
        $result = $record->delete();
        $this->trigger('deleted', $id);
        $this->flushCache();
        return $result;
    }

    /**
     * Get paginated records
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->remember(__FUNCTION__, func_get_args(), function () use ($perPage) {
            return $this->model->paginate($perPage);
        });
    }

    /**
     * Search records
     *
     * @param string $query
     * @param array $columns
     * @return Collection
     */
    public function search(string $query, array $columns = ['*']): Collection
    {
        return $this->remember(__FUNCTION__, func_get_args(), function () use ($query, $columns) {
            return $this->model->where(function ($q) use ($query, $columns) {
                foreach ($columns as $column) {
                    if ($column !== '*') {
                        $q->orWhere($column, 'LIKE', "%{$query}%");
                    }
                }
            })->get();
        });
    }

    /**
     * Search with pagination
     *
     * @param string $query
     * @param array $params
     * @return LengthAwarePaginator
     */
    public function searchPaginated(string $query, array $params = []): LengthAwarePaginator
    {
        return $this->remember(__FUNCTION__, func_get_args(), function () use ($query, $params) {
            $perPage = $params['per_page'] ?? 15;
            $columns = $params['columns'] ?? ['*'];

            return $this->model->where(function ($q) use ($query, $columns) {
                foreach ($columns as $column) {
                    if ($column !== '*') {
                        $q->orWhere($column, 'LIKE', "%{$query}%");
                    }
                }
            })->paginate($perPage);
        });
    }

    /**
     * Get filtered records
     *
     * @param array $filters
     * @param array $params
     * @return LengthAwarePaginator
     */
    public function getFiltered(array $filters, array $params = []): LengthAwarePaginator
    {
        return $this->remember(__FUNCTION__, func_get_args(), function () use ($filters, $params) {
            $query = $this->model->query();
            $perPage = $params['per_page'] ?? 15;

            foreach ($filters as $field => $filter) {
                if (is_array($filter) && isset($filter['operator'], $filter['value'])) {
                    $query->where($field, $filter['operator'], $filter['value']);
                } elseif (is_array($filter)) {
                    $query->whereIn($field, $filter);
                } else {
                    $query->where($field, $filter);
                }
            }

            if (isset($params['sort_by'])) {
                $direction = $params['sort_direction'] ?? 'asc';
                $query->orderBy($params['sort_by'], $direction);
            }

            if (isset($params['with'])) {
                $query->with($params['with']);
            }

            return $query->paginate($perPage);
        });
    }


    /**
     * Get paginated records
     *
     * @param PaginationParameters $params
     * @return LengthAwarePaginator
     */
    public function getPaginated(PaginationParameters $params): LengthAwarePaginator
    {
        $query = $this->model->query();
        // Handle filtering
        if (isset($params->filters)) {
            // filter only filterable fields
            $filters = array_intersect_key($params->filters, array_flip($params->filterable_fields));
            if (!empty($filters)) {
                foreach ($filters as $field => $filter) {
                    if (is_array($filter) && isset($filter['operator'], $filter['value'])) {
                        $query->where($field, $filter['operator'], $filter['value']);
                    } elseif (is_array($filter)) {
                        $query->whereIn($field, $filter);
                    } else {
                        $query->where($field, $filter);
                    }
                }
            }
        }

        // handle search
        if (isset($params->search)) {
            $query->where(function ($query) use ($params) {
                foreach ($params->searchable_fields as $field) {
                    $query->orWhere($field, 'like', '%' . $params->search . '%');
                }
            });
        }

        $sort_by = $params->sort_by ?? 'id';
        $sort_direction = $params->sort_direction ?? 'asc';

        // sort_by only $params->sortableFields
        if (isset($params->sort_by) && in_array($params->sort_by, $params->sortable_fields)) {
            $query->orderBy($sort_by, $sort_direction);
        }

        // Handle pagination
        return $query->paginate($params->per_page, $params->columns, $params->page_name, $params->page);
    }

    protected function newQuery(): Builder
    {
        $query = $this->model->newQuery();
        return $this->applyCriteria($query);
    }
}
