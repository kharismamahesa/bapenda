<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survei', function (Blueprint $table) {
            $table->increments('id_survei');
            $table->string('no_hp', 30)->nullable();
            $table->string('email', 255)->nullable();
            $table->text('alamat')->nullable();
            $table->string('nama', 100)->nullable();
            $table->string('jenis_kelamin', 1)->nullable();
            $table->integer('usia')->nullable();
            $table->string('pendidikan', 10)->nullable();
            $table->string('pekerjaan', 255)->nullable();
            $table->string('id_uptup', 10)->nullable();
            $table->integer('tanya1')->nullable();
            $table->integer('tanya2')->nullable();
            $table->integer('tanya3')->nullable();
            $table->integer('tanya4')->nullable();
            $table->integer('tanya5')->nullable();
            $table->integer('tanya6')->nullable();
            $table->integer('tanya7')->nullable();
            $table->integer('tanya8')->nullable();
            $table->integer('tanya9')->nullable();
            $table->integer('tanya21')->nullable();
            $table->integer('tanya22')->nullable();
            $table->integer('tanya23')->nullable();
            $table->integer('tanya24')->nullable();
            $table->integer('tanya25')->nullable();
            $table->dateTime('created_date')->nullable();
            $table->string('app', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survei');
    }
};
