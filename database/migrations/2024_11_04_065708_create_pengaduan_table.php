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
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id('id_pengaduan');
            $table->string('nomor_pengaduan')->unique()->nullable();
            $table->string('pelapor')->nullable();
            $table->enum('kondisi_masalah', ['Tiang', 'Panel', '1 Line'])->nullable();
            $table->string('lokasi')->nullable();
            $table->string('foto_pengaduan')->nullable();
            $table->date('tanggal_pengaduan')->nullable();
            $table->string('jam_pengaduan')->nullable();
            $table->string('keterangan_masalah')->nullable();
            $table->string('foto_penanganan')->nullable();
            $table->string('uraian_masalah')->nullable();
            $table->time('jam_penyelesaian')->nullable();
            $table->date('tanggal_penyelesaian')->nullable();
            $table->string('durasi_penyelesaian')->nullable();
            $table->string('penyelesaian_masalah')->nullable();
            $table->string('pencegahan_masalah')->nullable();
            $table->enum('pengelompokan_masalah', ['Eksternal', 'Internal'])->nullable();
            $table->enum('status', ['Pending', 'Selesai', 'Proses'])->nullable();
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
        Schema::dropIfExists('pengaduan');
    }
};
