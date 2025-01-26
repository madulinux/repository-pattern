<?php

namespace MaduLinux\RepositoryPattern\Criteria;

use Illuminate\Database\Eloquent\Builder;

abstract class BaseCriteria implements Criteria
{
    /**
     * Apply the criteria to the query
     *
     * @param Builder $query
     * @return Builder
     */
    abstract public function apply(Builder $query): Builder;
}
