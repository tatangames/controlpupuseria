<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformacionAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('informacion_admin', function (Blueprint $table) {
            $table->id();

            // problema sino cae el codigo al correo para que se contacte con el administrador
            $table->string('mensaje', 300)->nullable();

            // para cuando usuario esta bloqueado
            $table->string('mensaje_bloqueo', 300)->nullable();

            // cerrado por evento
            $table->boolean('cerrado');
            $table->string('mensaje_cerrado', 300)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('informacion_admin');
    }
}
