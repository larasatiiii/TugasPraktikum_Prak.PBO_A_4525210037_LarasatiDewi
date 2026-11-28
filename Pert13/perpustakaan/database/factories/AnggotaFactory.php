<?php

namespace Database\Factories;

use App\Models\Anggota;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnggotaFactory extends Factory
{
    protected $model = Anggota::class;

    public function definition(): array
    {
        return [
            'id_anggota' => $this->faker->unique()->numerify('AGT-####'),
            'nama_anggota' => $this->faker->name(),
        ];
    }
}
