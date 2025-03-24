<?php

use App\Models\Post;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

beforeEach(function () {
   $this->validData = [
       'title' => fake()->words(5, true),
       'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad atque aut, eos eum facere inventore mollitia nisi possimus tempore? A asperiores cum debitis dolor dolores esse id iure libero magni mollitia officia quam, quas, quis quod reprehenderit repudiandae saepe ullam voluptatibus. Aperiam dolores iure modi nisi vero. Corporis, perspiciatis, quam.'
   ];
});

it('requires authentication', function () {
   post(route('posts.store'))
       ->assertRedirectToRoute('login');
});

it('stores a post', function () {
    $user = User::factory()->create();


    actingAs($user)
        ->post(route('posts.store'), $this->validData);

    $this->assertDatabaseHas(Post::class, [
        ...$this->validData,
        'user_id' => $user->id
    ]);
});

it('redirects to the post show page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('posts.store'), $this->validData)
        ->assertRedirect(Post::latest('id')->first()->showRoute());
});

it('requires a valid data', function (array $badData, array|string $errors) {
    actingAs(User::factory()->create())
        ->post(route('posts.store'), [...$this->validData, ...$badData])
        ->assertInvalid($errors);
})
->with([
    [['title' => null], 'title'],
    [['title' => true], 'title'],
    [['title' => 1], 'title'],
    [['title' => 1.5], 'title'],
    [['title' => str_repeat('a', 256)], 'title'],
    [['title' => str_repeat('a', 9)], 'title'],
    [['body' => null], 'body'],
    [['body' => true], 'body'],
    [['body' => 1], 'body'],
    [['body' => 1.5], 'body'],
    [['body' => str_repeat('a', 10_001)], 'body'],
    [['body' => str_repeat('a', 99)], 'body'],
]);

