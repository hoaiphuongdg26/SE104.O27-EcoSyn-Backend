<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class QueryFilter
{
    /**
     * @var Request
     */
    public $request;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @var array
     */
    protected $search = [];

    /**
     * @var $builder
     */
    protected $builder;

    /**
     * @var string|null
     */
    protected $orderField = null;

    /**
     * @var string
     */
    protected $orderType = 'desc';

    /**
     * @var $filterable
     */
    protected $filterable;

    /**
     * QueryFilter constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->filters = $this->request->all();
    }

    /**
     * @param Builder $builder
     * @param array $filterFields
     * @param array $orderFields
     * @return Builder
     */
    public function apply(Builder $builder, array $filterFields, array $orderFields = [])
    {
        $this->builder = $builder;
        $this->orderFields = $orderFields;

        foreach ($this->filters as $name => $value)
        {
            $method = 'filter' . Str::studly($name);

            if (is_null($value) || $value == '') {
                continue;
            }

            if (method_exists($this, $method)) {
                $this->{$method}($value);
                continue;
            }

            if (empty($this->filterable) || !is_array($this->filterable)) {
                continue;
            }

            if (in_array($name, $this->filterable)) {
                $this->builder->where($name, $value);
                continue;
            }

            if (key_exists($name, $this->filterable)) {
                $this->builder->where($this->filterable[$name], $value);
                continue;
            }
        }
        foreach (['daily', 'monthly', 'yearly'] as $time) {
            if (isset($this->filters[$time])) {
                $this->applyTimeFilter($time, $this->filters[$time]);
            }
        }
        if (isset($this->filters['start_date']) && isset($this->filters['end_date'])) {
            $this->builder->whereBetween('updated_at', [Carbon::parse($this->filters['start_date']), Carbon::parse($this->filters['end_date'])]);
        }
        return $this->builder;
    }
    /**
     * Apply time filter based on the provided date or year.
     *
     * @param string $time
     * @param string $value
     * @return void
     */
    protected function applyTimeFilter($time, $value)
    {
        switch ($time) {
            case 'daily':
                $this->builder->whereDate('updated_at', Carbon::parse($value));
                break;
            case 'monthly':
                $this->builder->whereYear('updated_at', Carbon::parse($value)->year)
                    ->whereMonth('updated_at', Carbon::parse($value)->month);
                break;
            case 'yearly':
                $this->builder->whereYear('updated_at', $value);
                break;
        }
    }
}

