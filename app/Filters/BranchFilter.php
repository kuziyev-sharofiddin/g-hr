<?php

namespace App\Filters;

use LaravelLegends\EloquentFilter\Filters\ModelFilter;

class BranchFilter extends ModelFilter
{
    /**
     * The rules of filter
     *
     * @see https://github.com/LaravelLegends/eloquent-filter#what-does-it-do
     * @return array
     */
    public function getFilterables(): array
    {
        return [
            'id' => ['exact', 'not_equal'],
            'name' => ['exact', 'like', 'not_equal'],
            'address' => ['like'],
            'phone_number' => ['exact', 'like'],
            'target' => ['like'],
            'state_id' => ['exact', 'not_equal'],
            'region_id' => ['exact', 'not_equal'],
            'search' => ['like'],
        ];
    }

    /**
     * Override the search filter to use Searchable trait
     * This allows both ModelFilter and Searchable trait to work together
     */
    public function search($value)
    {
        return $this->builder->search($value);
    }
}
