<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\FlashCardSet;
use App\Models\FlashCardSetDetail;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->has(
            FlashCardSet::factory()
                ->count(60)
                ->addDetails(10, 200)
            )->create([
                'username' => 'abcd1234'
            ]);

        User::factory()->count(10)->create()->each(function ($user) {
            FlashCardSet::factory()
                ->count(rand(0, 3))
                ->create([
                    'user_id' => $user->id,
                    'author' => $user->username
                ])
                ->each(function () {
                    FlashCardSet::factory()->addDetails(10, 200);
                });
        });
    }
}
