<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rekams', function (Blueprint $table) {
            $table->id();
            $table->integer('laporan')->default('1');
            $table->timestamps();
            $table->integer('id_pasien')->references('id')->on('pasiens');
            $table->string('id_antrian')->references('id')->on('antrians');
            $table->date('tanggalperiksa')->nullable();
            $table->string('keluhan');
            $table->string('diagnosa')->nullable();
            $table->integer('id_obat')->references('id')->on('obat')->nullable();
            $table->string('jumlahobat')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('rawat')->nullable();
            $table->string('lamabaru')->nullable();
            $table->string('darah')->nullable();
            $table->string('berat')->nullable();
            $table->string('pinggang')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rekams');
    }
};
