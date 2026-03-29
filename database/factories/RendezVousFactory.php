<?php

namespace Database\Factories;

use App\Models\RendezVous;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RendezVousFactory extends Factory
{
    protected $model = RendezVous::class;

    public function definition(): array
    {
        return [
            'client_id' => User::factory()->client(),
            'employe_id' => User::factory()->employe(),
            'date' => fake()->date(),
            'heure' => fake()->time('H:i'),
            'duree' => 30,
            'motif' => fake()->sentence(),
            'status' => 'en_attente',
        ];
    }

    public function confirme(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirme',
        ]);
    }

    public function refuse(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refuse',
        ]);
    }

    public function enAttente(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'en_attente',
        ]);
    }
}