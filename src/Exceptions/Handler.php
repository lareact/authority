<?php


namespace Golly\Authority\Exceptions;


use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class Handler
 * @package Golly\Authority\Exceptions
 */
class Handler extends ExceptionHandler
{

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse|RedirectResponse|Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json([
                'success' => false,
                'message' => trans('auth.unauthenticated'),
                'errors' => []
            ], 401)
            : redirect()->guest($exception->redirectTo() ?? route('login'));
    }

    /**
     * @param Request $request
     * @param ValidationException $exception
     * @return JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage(),
            'errors' => $exception->errors(),
        ], $exception->status);
    }

    /**
     * Convert the given exception to an array.
     *
     * @param Throwable $e
     * @return array
     */
    protected function convertExceptionToArray(Throwable $e)
    {
        if ($e instanceof ApiException) {
            $message = $e->getErrorMessage();
        } else {
            $message = $this->isHttpException($e)
                ? $e->getMessage()
                : '服务器错误, 请联系管理员';
        }
        return config('app.debug') ? [
            'success' => false,
            'message' => $message,
            'errors' => [],
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->map(function ($trace) {
                return Arr::except($trace, ['args']);
            })->all()
        ] : [
            'success' => false,
            'message' => $message,
            'errors' => []
        ];
    }
}
