<?php

namespace App\Filters;

use App\Filters\QueryFilter;

class HomeFilter extends QueryFilter
{
    protected $filterable = array (
  0 => 'customer_id',
);

    // Example filter method
    public function filterName($name)
    {
        return $this->builder->where('name', 'like', '%' . $name . '%');
    }
}
