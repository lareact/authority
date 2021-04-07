<?php


namespace Golly\Authority\Http\Middleware;


use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class AntJsonMiddleware
 * @package Golly\Authority\Http\Middleware
 */
class AntJsonMiddleware
{

    /**
     * @var array
     */
    protected $invalid = ['undefined'];

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');
        $request->headers->set('Content-Type', 'application/json');
        if ($request->isMethod('GET')) {
            $this->handleQueries($request->query);
        }
        $response = $next($request);
        // 返回解析
        if ($response instanceof JsonResponse) {
            /* @var array $data */
            $data = $response->getData(true);
            $data['success'] = $response->isSuccessful();
            $response->setData($data);
        }

        return $response;
    }

    /**
     * 处理Query参数
     *
     * @param ParameterBag $bag
     */
    protected function handleQueries(ParameterBag $bag)
    {
        // 过滤无效数据
        $params = collect($bag->all())->filter(function ($value) {
            $value = trim($value);
            return !in_array($value, $this->invalid);
        });
        $bag->replace(array_merge([
            'page' => $params->pull('current', 1),
            'perPage' => $params->pull('pageSize')
        ], $params->toArray()));
    }
}
