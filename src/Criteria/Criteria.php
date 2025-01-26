<?php

namespace MaduLinux\RepositoryPattern\Criteria;

use Illuminate\Database\Eloquent\Builder;

interface Criteria
{
    /**
     * Apply the criteria to the query
     *
     * @param Builder $query
     * @return Builder
     */
    public function apply(Builder $query): Builder;
}
