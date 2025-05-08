<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\FlashCardSetDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FlashCardSet>
 */
class FlashCardSetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(rand(3, 10)), // 隨機標題（3-10 個單字）
            'description' => $this->faker->optional(0.7)->paragraph(), // 70% 機率有描述
            'author' => fn (array $attributes) => User::find($attributes['user_id'])->username,
            'isPublic' => $this->faker->boolean(), // 隨機公開狀態
            'user_id' => User::factory(), // 關聯使用者（在 Seeder 中覆蓋）
        ];
    }

    public function addDetails(int $min, int $max)
    {
        return $this->afterCreating(function ($flashCardSet) use ($min, $max) {
            $count = rand($min, $max);

            $words = []; // 用來確保單字不重複（大小寫不敏感）

            for ($i = 0; $i < $count; $i++) {
                // 生成不重複的單字
                do {
                    $word = fake()->sentence(rand(1, 2));
                } while (in_array(strtolower($word), $words));

                // 將單字加入已使用清單
                $words[] = strtolower($word);

                $details[] = [
                    'flash_card_set_id' => $flashCardSet->id,
                    'word' => $word,
                    'word_description' => fake()->sentence(rand(5, 20)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // 批量插入 details
            FlashCardSetDetail::insert($details);
        });
    }
}
