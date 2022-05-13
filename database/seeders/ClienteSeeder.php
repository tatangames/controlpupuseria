<?php

namespace Database\Seeders;

use App\Models\Clientes;
use App\Models\Usuarios;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Usuarios::create([
            'nombre' => 'Jonathan Moran',
            'usuario' => 'tatan',
            'password' => bcrypt('admin'),
            'activo' => '1'
        ])->assignRole('Super-Admin');

        Usuarios::create([
            'nombre' => 'Juan Perez',
            'usuario' => 'juan',
            'password' => bcrypt('admin'),
            'activo' => '1'
        ])->assignRole('Revisador');


    }
}
