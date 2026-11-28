<?php

namespace Database\Factories;

use App\Models\Buku;
use Illuminate\Database\Eloquent\Factories\Factory;

class BukuFactory extends Factory
{
    protected $model = Buku::class;

    public function definition(): array
    {
        return [
            'isbn' => $this->faker->unique()->isbn13(),
            'title' => $this->faker->sentence(3),
            'nama_penulis' => $this->faker->name(),
            'jumlah_eksamplar' => $this->faker->numberBetween(1, 20),
        ];
    }
}
