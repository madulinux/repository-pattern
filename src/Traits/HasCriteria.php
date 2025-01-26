<?php

namespace MaduLinux\RepositoryPattern\Traits;

use MaduLinux\RepositoryPattern\Criteria\Criteria;
use Illuminate\Database\Eloquent\Builder;

trait HasCriteria
{
    protected array $criteria = [];

    public function pushCriteria($criteria)
    {
        if (is_array($criteria)) {
            $this->criteria = array_merge($this->criteria, $criteria);
        } else {
            $this->criteria[] = $criteria;
        }
        return $this;
    }

    public function popCriteria()
    {
        return array_pop($this->criteria);
    }

    public function getCriteria(): array
    {
        return $this->criteria;
    }

    public function resetCriteria()
    {
        $this->criteria = [];
        return $this;
    }

    protected function applyCriteria(Builder $query): Builder
    {
        foreach ($this->criteria as $criteria) {
            if ($criteria instanceof Criteria) {
                $query = $criteria->apply($query);
            }
        }
        return $query;
    }
}