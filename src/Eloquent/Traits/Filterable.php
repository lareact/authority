<?php


namespace Golly\Authority\Eloquent\Traits;


use Golly\Authority\Contracts\FilterInterface;
use Golly\Authority\Eloquent\QueryBuilder;
use Golly\Authority\Eloquent\ModelFilter;
use Golly\Authority\Exceptions\FilterException;

/**
 * Trait Filterable
 * @package Golly\Authority\Eloquent
 * @method QueryBuilder filter(array $params)
 */
trait Filterable
{

    /**
     * @param QueryBuilder $query
     * @param array $params
     * @return QueryBuilder
     * @throws FilterException
     */
    public function scopeFilter(QueryBuilder $query, array $params = [])
    {
        $filter = $this->newModelFilter();
        if ($filter && $filter instanceof FilterInterface) {
            return $filter->handle($query, $params);
        }

        return $query;
    }


    /**
     * @return ModelFilter
     */
    public function newModelFilter()
    {
        return new ModelFilter();
    }
}
