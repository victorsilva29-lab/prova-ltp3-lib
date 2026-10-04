<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('autores', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('nacionalidade', 100);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('autores');
    }
};
