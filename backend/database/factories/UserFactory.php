<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;


class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->regexify('[a-zA-Z0-9]{8,20}'),
            'password' => Hash::make('abcd1234'),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
