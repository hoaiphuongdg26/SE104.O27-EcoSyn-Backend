<?php

namespace App\Filters;

use App\Filters\QueryFilter;

class PostFilter extends QueryFilter
{
    protected $filterable = array (
  0 => 'title',
  1 => 'content',
  2 => 'image_url',
  3 => 'staff_id',
  4 => 'deleted',
);

    // Example filter method
    public function filterName($name)
    {
        return $this->builder->where('name', 'like', '%' . $name . '%');
    }
}
