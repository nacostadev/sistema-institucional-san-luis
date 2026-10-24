<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_usuario' => fake()->unique()->userName(),
            'dni' => fake()->numerify('########'),
            'correo' => fake()->unique()->safeEmail(),
            'nombres' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'password' => static::$password ??= Hash::make('password'),
            'estado' => 'activo',
        ];
    }
}
