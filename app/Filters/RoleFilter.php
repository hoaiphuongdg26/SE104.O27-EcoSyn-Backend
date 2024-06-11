<?php

namespace App\Filters;

use App\Filters\QueryFilter;

class RoleFilter extends QueryFilter
{
    protected $filterable = array (
);

    // Example filter method
    public function filterName($name)
    {
        return $this->builder->where('name', 'like', '%' . $name . '%');
    }
}
