<?php

namespace App\QueryFilter\SuggestionTitle;

use App\QueryFilter\Filter;

class SuggestionTitleName extends Filter
{
    protected function applyFilter($builder)
    {
        return $builder->where('name', 'like', '%'.request($this->filterName()).'%');
    }
}
