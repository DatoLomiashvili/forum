<?php

use App\Http\Resources\PostResource;
use Database\Factories\PostFactory;
use Inertia\Testing\AssertableInertia;
use function Pest\Laravel\get;


it('should return the correct component', function () {
    get(route('posts.index'))
        ->assertInertia(fn (AssertableInertia $inertia) => $inertia
        ->component('Posts/Index', true)
        );
});


it('passes posts to the view', function () {
    $posts = PostFactory::new()->count(3)->create();

    get(route('posts.index'))
        ->assertHasPaginatedResource('posts', PostResource::collection($posts->reverse()));
});
