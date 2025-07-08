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
            'client_id' => User::factory(),
            'employe_id' => User::factory(),
            'date' => $this->faker->date(),
            'heure' => $this->faker->time('H:i'),
            'duree' => 30,
            'motif' => $this->faker->sentence(),
            'status' => 'en_attente',
        ];
    }
}