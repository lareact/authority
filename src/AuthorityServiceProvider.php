<?php


namespace Golly\Authority;


use Golly\Authority\Auth\AccessTokenGuard;
use Golly\Authority\Commands\ClearInactiveTokenCommand;
use Golly\Authority\Contracts\ExtraQueryInterface;
use Golly\Authority\Http\Middleware\AntJsonMiddleware;
use Golly\Authority\Http\Middleware\PermissionMiddleware;
use Golly\Authority\Http\Middleware\RateLimiterMiddleware;
use Golly\Authority\Http\Middleware\RoleMiddleware;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Throwable;

/**
 * Class AuthorityServiceProvider
 * @package Golly\Authority
 */
class AuthorityServiceProvider extends ServiceProvider
{

    /**
     * The middleware aliases.
     *
     * @var array
     */
    protected $middlewareAliases = [
        'role' => RoleMiddleware::class,
        'permission' => PermissionMiddleware::class,
        'ant.json' => AntJsonMiddleware::class
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 资源
        $this->configurePublishing();
        // 路由
        $this->configureRoutes();
        // 中间件别名
        $this->aliasMiddleware();
        // 中间件优先级
        $this->configureMiddleware();
        // 自定义鉴权
        $this->configureAuthGuard();
        // 支持的命令行
        $this->commands([
            ClearInactiveTokenCommand::class
        ]);
        // 添加额外参数
        $this->app->afterResolving(ExtraQueryInterface::class, function ($resolved) {
            $resolved->addExtraData();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        config([
            'auth.guards.golly' => array_merge([
                'driver' => 'golly',
                'provider' => 'users',
            ], config('auth.guards.golly', [])),
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../config/authority.php', 'authority');
    }

    /**
     * Configure the publishable resources offered by the package.
     *
     * @return void
     */
    protected function configurePublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/authority.php' => config_path('authority.php'),
            ], 'authority-config');

            $this->publishes([
                __DIR__ . '/../database/migrations/' => database_path('migrations'),
            ], 'authority-migrations');
        }
    }

    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(__DIR__ . '/../routes/api.php');
    }

    /**
     * Extend AccessToken Auth.
     * @return void
     */
    protected function configureAuthGuard()
    {
        Auth::extend('golly', function ($app, $name, array $config) {
            $guard = new AccessTokenGuard(
                $app['auth']->createUserProvider($config['provider']),
                $app['request']
            );
            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }


    /**
     * Alias the middleware.
     *
     * @return void
     */
    protected function aliasMiddleware()
    {
        $router = $this->app['router'];

        $method = method_exists($router, 'aliasMiddleware') ? 'aliasMiddleware' : 'middleware';

        foreach ($this->middlewareAliases as $alias => $middleware) {
            $router->$method($alias, $middleware);
        }
    }

    /**
     * 处理中间件优先级
     *
     * @return void
     */
    protected function configureMiddleware()
    {
        try {
            $kernel = $this->app->make(Kernel::class);
            $kernel->prependToMiddlewarePriority(AntJsonMiddleware::class);
        } catch (Throwable $e) {

        }
    }
}
