<?php

namespace App\Providers;

use App\AI\ScopeReview\FakeScopeReviewGenerator;
use App\AI\ScopeReview\ScopeReviewGenerator;
use App\AI\ScopeReview\ScopeReviewOutputValidator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ScopeReviewOutputValidator::class);

        $this->app->bind(ScopeReviewGenerator::class, function ($app) {
            return match (config('ai.provider')) {
                'fake' => new FakeScopeReviewGenerator(
                    $app->make(ScopeReviewOutputValidator::class),
                ),
                default => throw new \RuntimeException(
                    'Provider AI belum tersedia untuk mode yang dipilih.',
                ),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
