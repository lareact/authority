<?php


namespace Golly\Authority\Http\Controllers;


use Exception;
use Golly\Authority\Actions\CreateUserAction;
use Golly\Authority\Actions\LoginAction;
use Golly\Authority\Actions\UpdatePasswordAction;
use Golly\Authority\Exceptions\ValidationException;
use Golly\Authority\Http\Requests\VerifyEmailRequest;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class AuthController
 * @package Golly\Authority\Http\Controllers
 */
class AuthController extends ApiController
{

    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:golly', [
            'except' => ['login', 'verifyEmail', 'register']
        ]);
    }

    /**
     * @param Request $request
     * @param CreateUserAction $action
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request, CreateUserAction $action)
    {
        $action->create($request->json());

        return $this->sendNoContent();
    }

    /**
     * @param VerifyEmailRequest $request
     * @return JsonResponse
     */
    public function verifyEmail(VerifyEmailRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->sendNoContent();
        }
        $request->user()->markEmailAsVerified();
        event(new Verified($request->user()));

        return $this->sendNoContent(202);
    }

    /**
     * @param Request $request
     * @param LoginAction $action
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request, LoginAction $action)
    {
        $certification = $action->login($request);

        return $this->sendArray($certification);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function logout(Request $request)
    {
        $request->user()->getAccessToken()->delete();

        return $this->sendNoContent();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function info(Request $request)
    {
        return $this->sendArray([
            'user' => $request->user()->asArray(),
            'abilities' => $request->user()->getAccessToken()->abilities ?? []
        ]);
    }

    /**
     * 修改密码后，自动登出
     *
     * @param Request $request
     * @param UpdatePasswordAction $action
     * @return JsonResponse
     * @throws ValidationException
     */
    public function password(Request $request, UpdatePasswordAction $action)
    {
        $action->update($request->user(), $request->json());

        return $this->logout($request);
    }


}

