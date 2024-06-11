<?php

namespace App\Filters;

use App\Filters\QueryFilter;

class ReportFilter extends QueryFilter
{
    protected $filterable = array (
  0 => 'customer_id',
  1 => 'description',
  2 => 'status',
  3 => 'deleted',
);

    // Example filter method
    public function filterName($name)
    {
        return $this->builder->where('name', 'like', '%' . $name . '%');
    }
}
