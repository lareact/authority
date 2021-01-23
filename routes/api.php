<?php

use Golly\Authority\Http\Controllers\ApiController;
use Golly\Authority\Http\Controllers\AuthController;
use Golly\Authority\Http\Controllers\PasswordController;
use Golly\Authority\Http\Controllers\PermissionController;
use Golly\Authority\Http\Controllers\RoleController;
use Golly\Authority\Http\Controllers\UserController;
use Illuminate\Routing\Router;

/**
 * @var Router $router
 */
$router->get('version', [ApiController::class, 'version'])->name('version');
$router->post('password/forgot', [PasswordController::class, 'forgot'])->middleware('throttle:1,1');
$router->post('password/reset', [PasswordController::class, 'reset']);
$router->group([
    'prefix' => 'auth'
], function (Router $router) {
    $router->post('register', [AuthController::class, 'register'])->name('register');
    $router->post('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('email.verify');
    $router->post('login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login');
    $router->post('logout', [AuthController::class, 'logout'])->name('logout');
    $router->get('me', [AuthController::class, 'me'])->name('auth.me');
    $router->put('password', [AuthController::class, 'password'])->name('auth.password');
});

$router->group([
    'middleware' => ['auth:golly']
], function (Router $router) {
    $router->put(
        'users/{id}/roles',
        [UserController::class, 'assignRoles']
    )->middleware('role:admin')->name('user.roles');
    $router->put('users/{id}/permissions', [UserController::class, 'assignPermissions']);
    $router->resource('users', UserController::class);
    $router->post('users/{id}/children', [UserController::class, 'storeChild'])->name('create.children');
    $router->put('roles/{id}/permissions', [RoleController::class, 'assignPermissions']);
    $router->resource('roles', RoleController::class);
    $router->resource('permissions', PermissionController::class);
});
