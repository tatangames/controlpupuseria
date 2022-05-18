<?php

namespace Database\Seeders;

use App\Models\Horario;
use Illuminate\Database\Seeder;

class HorarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Horario::create([
            'hora1' => "15:00:00",
            'hora2' => "22:00:00",
            'dia' => 1,
            'cerrado' => 0,
            'nombre' => 'Domingo'
        ]);

        Horario::create([
            'hora1' => "15:00:00",
            'hora2' => "22:00:00",
            'dia' => 2,
            'cerrado' => 0,
            'nombre' => 'Lunes'
        ]);

        Horario::create([
            'hora1' => "15:00:00",
            'hora2' => "22:00:00",
            'dia' => 3,
            'cerrado' => 1,
            'nombre' => 'Martes'
        ]);

        Horario::create([
            'hora1' => "15:00:00",
            'hora2' => "22:00:00",
            'dia' => 4,
            'cerrado' => 0,
            'nombre' => 'Miercoles'
        ]);

        Horario::create([
            'hora1' => "15:00:00",
            'hora2' => "22:00:00",
            'dia' => 5,
            'cerrado' => 0,
            'nombre' => 'Jueves'
        ]);

        Horario::create([
            'hora1' => "15:00:00",
            'hora2' => "22:00:00",
            'dia' => 6,
            'cerrado' => 0,
            'nombre' => 'Viernes'
        ]);

        Horario::create([
            'hora1' => "15:00:00",
            'hora2' => "22:00:00",
            'dia' => 7,
            'cerrado' => 0,
            'nombre' => 'Sabado'
        ]);
    }
}
