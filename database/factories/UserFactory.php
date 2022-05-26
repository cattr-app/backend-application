<?php

namespace Database\Factories;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => hash('sha512', 'password'),
            'type' => UserType::EMPLOYEE,
            'role_id' => 2,
            'active' => true,
            'is_admin' => false,
            'type' => 'employee',
            'last_activity' => now()->subMinutes(random_int(1, 55)),
        ];
    }

    public function admin(): UserFactory
    {
        return $this->state(fn () => ['is_admin' => true]);
    }
}
