<?php


namespace Golly\Authority\Eloquent;

use DateTimeInterface;
use Golly\Authority\Contracts\FilterInterface;
use Golly\Authority\Exceptions\FilterException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Class Model
 * @package Golly\Authority\Eloquent
 * @method QueryBuilder filter(array $params)
 * @mixin QueryBuilder
 */
class Model extends LaravelModel
{

    /**
     * @param Builder $query
     * @return QueryBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new QueryBuilder($query);
    }

    /**
     * @return Filter
     */
    public function newModelFilter()
    {
        return new Filter();
    }

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
     * @param array $params
     * @return LengthAwarePaginator
     */
    public function paginate(array $params = [])
    {
        $perPage = Arr::pull($params, 'perPage');

        return (new static())->filter($params)->paginate($perPage);
    }


    /**
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return Carbon::instance($date)->toDateTimeString();
    }
}
