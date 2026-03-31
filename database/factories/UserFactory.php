<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'current_team_id' => null,
            'role' => 'client',
            'tva_number' => null,
            'duree_creneau' => 90,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'tva_number' => null,
            'duree_creneau' => null,
        ]);
    }

    public function client(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'client',
            'tva_number' => null,
            'duree_creneau' => null,
        ]);
    }

    public function employe(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'employe',
            'tva_number' => null,
            'duree_creneau' => 30,
        ]);
    }

    public function societe(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'client',
            'tva_number' => fake()->numerify('BE0#########'),
        ]);
    }

    public function withPersonalTeam(callable $callback = null): static
    {
        if (! Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(fn (array $attributes, User $user) => [
                    'name' => $user->name . '\'s Team',
                    'user_id' => $user->id,
                    'personal_team' => true,
                ])
                ->when(is_callable($callback), $callback),
            'ownedTeams'
        );
    }
}