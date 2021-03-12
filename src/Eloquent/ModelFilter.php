<?php


namespace Golly\Authority\Eloquent;


use Golly\Authority\Contracts\FilterInterface;
use Golly\Authority\Exceptions\FilterException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class ModelFilter
 * @package Golly\Authority\Eloquent
 * @mixin QueryBuilder
 */
class ModelFilter implements FilterInterface
{
    /**
     * @var QueryBuilder
     */
    protected $query;

    /**
     * @var array
     */
    protected $input = [];

    /**
     * 存在的索引
     *
     * @var array
     */
    protected $indexes = [];

    /**
     * @param QueryBuilder $query
     * @param array $input
     * @return QueryBuilder
     * @throws FilterException
     */
    public function handle(QueryBuilder $query, array $input): QueryBuilder
    {
        $this->query = $query;
        $this->input = $input;
        $input = $this->handleInputPriorities($input);
        // 关联关系&&排序
        $with = Arr::pull($input, 'with');
        $order = Arr::pull($input, 'order');
        // 过滤项
        foreach ($input as $key => $value) {
            $method = $this->getKeyMethod($key);
            if ($this->isCallable($method)) {
                $this->$method($value);
            }
        }
        if ($with) {
            if (method_exists($this, 'interceptWith')) {
                $this->interceptWith($with);
            } else {
                $this->with($with);
            }
        }
        if ($order) {
            $this->order($order);
        }

        return $this->query;
    }

    /**
     * @param string $value
     * @return void
     */
    public function with(string $value)
    {
        $relations = explode(',', $value);
        if (method_exists($this, 'handleWith')) {
            $this->handleWith($relations);
        }

        $this->query->with($relations);
    }

    /**
     * @param string $value
     * @return void
     */
    public function order(string $value)
    {
        $orders = explode(',', $value);
        foreach ($orders as $order) {
            if (empty($order)) {
                continue;
            }
            [$column, $direction] = str_pad(explode(':', $order), 2, 'desc');
            $this->query->orderBy($column, $direction);
        }
    }

    /**
     * 处理参数优先级
     *
     * @param array $input
     * @return array
     */
    protected function handleInputPriorities(array $input)
    {
        if (empty($this->indexes)) {
            return $input;
        }
        $keys = array_intersect_key(array_flip($this->indexes), $input);

        return array_replace($keys, $input);
    }

    /**
     * 替换ID并转为驼峰
     *
     * @param string $key
     * @return mixed
     */
    protected function getKeyMethod(string $key)
    {
        $key = preg_replace('/^(.*)_id$/', '$1', $key);

        return Str::camel(str_replace('.', '_', $key));
    }

    /**
     * 是否存在该函数
     *
     * @param $method
     * @return bool
     */
    protected function isCallable($method)
    {
        return method_exists($this, $method);
    }

}
