<?php

/**
 * Clase Artisan Migrate para creación de la tabla logger para llevar el manejo de los logs de error
 * @author  Andres.Castellanos <andres.castellanos@softwareestrategico.com>
 * @date    Diciembre 01 de 2017
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoggersTable extends Migration {
    /**
     * Run the migrations. Para crear la tabla logger / logs de error
     * @return void
     */
    public function up() {
        Schema::create('logger', function (Blueprint $table) {
            //Definimos la estructura de la tabla
            $table->increments('id');
            $table->string('brand'); //Nombre de la marca {"CARMEL", "PCFK", "LOGUIN"}
            $table->string('adviser_document'); //Documento de identidad de la asesora
            $table->string('function'); //Nombre de la funcion o metodo fallido
            $table->text('details'); //Detalle del error   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations. Para eliminar la tabla logger /log
     * @return void
     */
    public function down() {
        Schema::dropIfExists('logger');
    }
}
