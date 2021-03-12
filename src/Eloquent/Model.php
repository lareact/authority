<?php


namespace Golly\Authority\Eloquent;

use DateTimeInterface;
use Golly\Authority\Eloquent\Traits\Filterable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * Class Model
 * @package Golly\Authority\Eloquent
 * @mixin QueryBuilder
 */
class Model extends LaravelModel
{
    use Filterable;

    /**
     * 进一步封装常用的函数
     *
     * @param Builder $query
     * @return QueryBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new QueryBuilder($query);
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
