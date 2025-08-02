<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            // 'phone_number' => $this->faker->optional()->phoneNumber(),
            // 'bio' => $this->faker->optional()->sentence(10),
            'avatar' => $this->faker->optional()->imageUrl(100, 100, 'people'), // Placeholder avatar
            'email_verified_at' => $this->faker->dateTimeThisYear(),
            'remember_token' => Str::random(10),
            'created_at' => $this->faker->dateTimeThisYear(),
            'updated_at' => $this->faker->dateTimeThisYear(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            if (\Spatie\Permission\Models\Role::where('name', 'user')->exists()) {
                $user->assignRole('user');
            }
        });
    }

    public function admin()
    {
        return $this->afterCreating(function (User $user) {
            if (\Spatie\Permission\Models\Role::where('name', 'admin')->exists()) {
                $user->assignRole('admin');
            }
        });
    }
}