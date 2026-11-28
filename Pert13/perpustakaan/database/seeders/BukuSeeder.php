<?php

namespace Database\Seeders;

use App\Models\Buku;
use Illuminate\Database\Seeder;

class BukuSeeder extends Seeder
{
    public function run(): void
    {
        Buku::firstOrCreate(
            ['isbn' => '978-602-03-2478-4'],
            [
                'title' => 'Laskar Pelangi',
                'nama_penulis' => 'Andrea Hirata',
                'jumlah_eksamplar' => 10,
            ]
        );

        Buku::factory(20)->create();
    }
}
