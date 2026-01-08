<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('local')) {
            Http::fake([
                'https://api.providera.com/auth' => Http::response([
                    'auth_token' => 'mock_' . md5(time()),
                ], 200),
            ]);

            Http::fake([
                'https://api.providera.com/payments' => Http::response([
                    'payment_id' => 'pay_' . md5(time()),
                ], 200),
            ]);

            Http::fake([
                'https://api.providerb.com/auth' => Http::response([
                    'bearer_token' => 'mock_' . md5(time()),
                ], 200),
            ]);

            Http::fake([
                'https://api.providerb.com/payments' => Http::response([
                    'transaction_id' => 'pay_' . md5(time()),
                ], 200),
            ]);
        }
    }
}
