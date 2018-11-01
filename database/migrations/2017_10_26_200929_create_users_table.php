<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('document')->unique;
            $table->string('name');
            $table->string('password');
            $table->integer('status');
            $table->string('profile'); 
            $table->string('profileName'); 
            $table->string('brand');
            $table->string('brand_id');
            $table->boolean('is_new');
            $table->string('clasification');
            $table->string('stencil_status');
            $table->boolean('stencil_locked');
            $table->boolean('is_locked');
            $table->string('code_zone');
            $table->string('active_zone');
            $table->string('mail_plain');
            $table->string('external_password'); //Campo que guarda la clave de 3.0 de la asesora
            $table->string('external_user'); //Campo que almacena el usuario de 3.0 de la asesora
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
