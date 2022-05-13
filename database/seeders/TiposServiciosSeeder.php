<?php

namespace Database\Seeders;

use App\Models\TipoServicio;
use Illuminate\Database\Seeder;

class TiposServiciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoServicio::create([
            'nombre' => "Eventos",
            ]);

        TipoServicio::create([
            'nombre' => "Alimentos",
        ]);
    }
}
