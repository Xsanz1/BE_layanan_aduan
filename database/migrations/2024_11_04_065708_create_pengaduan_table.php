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
            $table->string('nomor_pengaduan')->unique();
            $table->string('pelapor');
            $table->enum('kondisi_masalah', ['Tiang', 'Panel', '1 Line']);
            $table->string('lokasi');
            $table->string('foto_pengaduan')->nullable();
            $table->date('tanggal_pengaduan');
            $table->string('jam_pengaduan');
            $table->string('keterangan_masalah');
            $table->string('foto_penanganan')->nullable();
            $table->string('uraian_masalah')->nullable();
            $table->time('jam_penyelesaian')->nullable();
            $table->date('tanggal_penyelesaian')->nullable();
            $table->string('durasi_penyelesaian')->nullable();
            $table->string('penyelesaian_masalah')->nullable();
            $table->enum('status', ['Pending', 'Selesai', 'Proses']);
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
