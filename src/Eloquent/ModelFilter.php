<?php


namespace Golly\Authority\Eloquent;


use Golly\Authority\Contracts\FilterInterface;
use Golly\Authority\Exceptions\FilterException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * Class ModelFilter
 * @package Golly\Authority\Eloquent
 * @mixin QueryBuilder
 */
class ModelFilter implements FilterInterface
{
    use ForwardsCalls;

    /**
     * @var QueryBuilder
     */
    protected $query;

    /**
     * 存在的索引
     *
     * @var array
     */
    protected $indexes = [];

    /**
     * @param QueryBuilder $query
     * @param array $params
     * @return QueryBuilder
     * @throws FilterException
     */
    public function handle(QueryBuilder $query, array $params): QueryBuilder
    {
        $this->query = $query;
        $params = $this->handleParamPriorities($params);
        // 关联关系&&排序
        $with = Arr::pull($params, 'with');
        $order = Arr::pull($params, 'order');
        // 过滤项
        foreach ($params as $key => $value) {
            $method = $this->getKeyMethod($key);
            if ($this->isCallable($method)) {
                $result = $this->$method($value);
                if ($result instanceof QueryBuilder) {
                    $this->query = $result;
                } else {
                    throw new FilterException(sprintf('过滤器函数（%s）存在错误', $method));
                }
            }
        }
        if ($with) {
            if (method_exists($this, 'interceptWith')) {
                $this->query = $this->interceptWith($with);
            } else {
                $this->query = $this->with($with);
            }
        }
        if ($order) {
            $this->query = $this->order($order);
        }

        return $this->query;
    }

    /**
     * @param string $value
     * @return QueryBuilder
     */
    public function with(string $value)
    {
        $relations = explode(',', $value);
        if (method_exists($this, 'handleWith')) {
            return $this->handleWith($relations);
        }

        return $this->query->with($relations);
    }

    /**
     * @param string $value
     * @return QueryBuilder
     */
    public function order(string $value)
    {
        $orders = explode(',', $value);
        foreach ($orders as $order) {
            if (empty($order)) {
                continue;
            }
            [$column, $direction] = str_pad(explode(':', $order), 2, 'desc');
            $this->query = $this->query->orderBy($column, $direction);
        }

        return $this->query;
    }

    /**
     * 处理参数优先级
     *
     * @param array $params
     * @return array
     */
    protected function handleParamPriorities(array $params)
    {
        if (empty($this->indexes)) {
            return $params;
        }
        $keys = array_intersect_key(array_flip($this->indexes), $params);

        return array_replace($keys, $params);
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

    /**
     * @param $method
     * @param $arguments
     * @return QueryBuilder
     * @throws FilterException
     */
    public function __call($method, $arguments)
    {
        $query = $this->forwardCallTo($this->query, $method, $arguments);
        if ($query instanceof QueryBuilder) {
            $this->query = $query;
            return $query;
        } else {
            throw new FilterException('过滤器函数"' . $method . '"存在错误');
        }
    }

}
