<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use SplFileInfo;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    private static Collection $fixtures;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => str(fake()->sentence())->beforeLast('.')->title(),
            'body' => Collection::times(4, fn() => fake()->realText(1250))->join(PHP_EOL . PHP_EOL)
        ];
    }

    public function withFixture(): self
    {
        $posts = self::getFixtures()
        ->map(fn (string $contents) => str($contents)->explode(PHP_EOL, 2))
        ->map(fn (Collection $parts) => [
            'title' => str($parts[0])->trim()->after('# '),
            'body' => str($parts[1])->trim(),
        ]);


        return $this->sequence(...$posts);
    }

    private static function getFixtures(): Collection
    {
        return self::$fixtures ??= collect(File::files(database_path('factories/fixtures/posts')))
            ->map(fn (SplFileInfo $fileInfo) => $fileInfo->getContents());
    }
}
