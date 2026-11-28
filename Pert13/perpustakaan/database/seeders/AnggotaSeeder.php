<?php

namespace Database\Seeders;

use App\Models\Anggota;
use Illuminate\Database\Seeder;

class AnggotaSeeder extends Seeder
{
    public function run(): void
    {
        Anggota::firstOrCreate(
            ['id_anggota' => 'AGT-0001'],
            ['nama_anggota' => 'Budi Santoso']
        );

        Anggota::factory(15)->create();
    }
}
