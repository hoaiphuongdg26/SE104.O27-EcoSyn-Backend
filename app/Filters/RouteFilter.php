<?php

namespace App\Filters;

use App\Filters\QueryFilter;

class RouteFilter extends QueryFilter
{
    protected $filterable = array (
  0 => 'route_id',
  1 => 'start_time',
  2 => 'end_time',
);

    // Example filter method
    public function filterName($name)
    {
        return $this->builder->where('name', 'like', '%' . $name . '%');
    }
}
