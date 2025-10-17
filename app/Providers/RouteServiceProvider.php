<?php

namespace App\Providers;

use App\Helpers\Constants;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(config('ratelimit.auth'))
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'status'     => Constants::FAILED,
                        'data' => [
                            'message'     => Constants::ERROR_TOO_MANY_REQUEST,
                            'retry_after' => $headers['Retry-After'] ?? config('ratelimit.auth'),
                        ]
                    ], JsonResponse::HTTP_TOO_MANY_REQUESTS, $headers);
                });
        });

        RateLimiter::for('drug-search', function (Request $request) {
            return Limit::perMinute(config('ratelimit.drug_search'))
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'status'     => Constants::FAILED,
                        'data' => [
                            'message'     => Constants::ERROR_TOO_MANY_REQUEST,
                            'retry_after' => $headers['Retry-After'] ?? config('ratelimit.drug_search'),
                        ]
                    ], JsonResponse::HTTP_TOO_MANY_REQUESTS, $headers);
                });
        });

        RateLimiter::for('user-drugs', function (Request $request) {
            return Limit::perMinute(config('ratelimit.user_drugs'))
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'status'     => Constants::FAILED,
                        'data' => [
                            'message'     => Constants::ERROR_TOO_MANY_REQUEST,
                            'retry_after' => $headers['Retry-After'] ?? config('ratelimit.user_drugs'),
                        ]
                    ], JsonResponse::HTTP_TOO_MANY_REQUESTS, $headers);
            });
        });
    }
}
