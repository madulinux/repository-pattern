<?php

namespace Tests\Stubs;

use Illuminate\Database\Eloquent\Model;
use MaduLinux\RepositoryPattern\Repository;

class TestRepository extends Repository
{
    protected $model;

    public function __construct(?Model $model = null)
    {
        $this->model = $model ?? new TestModel();
    }

    protected function getModel(): Model
    {
        return $this->model;
    }

    public function getCacheKeyTest(string $method, array $args = []): string
    {
        return $this->getCacheKey($method, $args);
    }

    public function getCacheTime(): int
    {
        return $this->cacheTime;
    }

    public function getCacheTags(): array
    {
        return $this->cacheTags;
    }
}
