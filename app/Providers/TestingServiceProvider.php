<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;
use Inertia\Inertia;
use Inertia\Testing\AssertableInertia;

class TestingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        if (!$this->app->runningUnitTests()) {
            return;
        }

        AssertableInertia::macro(name: 'hasResource', macro: function (string $key, JsonResource $resource) {
            /** @var AssertableInertia $this */
            $this->has($key);

            expect($this->prop($key))->toEqual($resource->response()->getData(true));
        });

        AssertableInertia::macro(name: 'hasPaginatedResource', macro: function (string $key, ResourceCollection $collection) {
            /** @var AssertableInertia $this */
            $this->hasResource("$key.data", $collection);

            expect($this->prop($key))->toHaveKeys(['data', 'links', 'meta']);
        });

        TestResponse::macro('assertHasResource', function (string $key, JsonResource $resource) {
            /** @var TestResponse $this */
            return $this->assertInertia(fn (AssertableInertia $inertia) => $inertia->hasResource($key, $resource));
        });

        TestResponse::macro('assertHasPaginatedResource', function (string $key, ResourceCollection $collection) {
            /** @var TestResponse $this */
            return $this->assertInertia(fn (AssertableInertia $inertia) => $inertia->hasPaginatedResource($key, $collection));
        });

        TestResponse::macro('assertComponent', function (string $component) {
            /** @var TestResponse $this */
            return $this->assertInertia(fn (AssertableInertia $inertia) => $inertia->component($component));
        });
    }
}
