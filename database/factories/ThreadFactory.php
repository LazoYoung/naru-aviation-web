<?php

namespace Database\Factories;

use App\Enums\Category;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ThreadFactory extends Factory
{
    protected $model = Thread::class;

    public function definition(): array {
        $category = $this->faker->numberBetween(0, count(Category::cases()) - 1);

        return [
            'title' => $this->faker->word(),
            'category' => $category,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'view' => 0,
            'user_id' => User::factory(),
            'event_id' => null,
        ];
    }
}
