<?php

use App\Http\Resources\PostResource;
use App\Models\Post;
use Database\Factories\PostFactory;
use Inertia\Testing\AssertableInertia;
use function Pest\Laravel\get;


it('should return the correct component', function () {
    get(route('posts.index'))
        ->assertComponent('Posts/Index');
});


it('passes posts to the view', function () {
    $posts = PostFactory::new()->count(3)->create();

    get(route('posts.index'))
        ->assertHasPaginatedResource('posts', PostResource::collection($posts->reverse()));
});

it('can show a post', function () {
    \Pest\Laravel\withoutExceptionHandling();
    $post = Post::factory()->create();

    get(route('posts.show', $post))
        ->assertComponent('Posts/Show')
        ->assertInertia(fn (AssertableInertia $inertia) => $inertia->component('Posts/Show', true));
});

it('passes a post to the view', function () {
    \Pest\Laravel\withoutExceptionHandling();
    $post = Post::factory()->create();

    $post->load('user');
    get(route('posts.show', $post))
        ->assertHasResource('post', PostResource::make($post));

});
