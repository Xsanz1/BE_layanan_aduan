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
            $table->id('id_pengaduan'); // ID otomatis untuk setiap pengaduan
            $table->string('pelapor'); // Nama pelapor
            $table->string('nomor_pengaduan')->unique(); // Nomor pengaduan yang unik
            $table->enum('kondisi_masalah', ['Tiang', 'Panel', '1 Line']);
            $table->string('keterangan_masalah');
            $table->unsignedBigInteger('id_tiang'); // ID Tiang
            $table->unsignedBigInteger('id_panel'); // ID Panel
            $table->time('jam_pengaduan'); // Jam pengaduan
            $table->date('tanggal_pengaduan'); // Tanggal pengaduan
            $table->time('jam_penyelesaian')->nullable();
            $table->date('tanggal_penyelesaian')->nullable();
            $table->string('durasi_penyelesaian')->nullable();
            $table->string('lokasi'); // Lokasi pengaduan
            $table->string('foto') -> nullable(); // Path foto pengaduan, nullable jika tidak ada
            $table->enum('status', ['Pending', 'Selesai', 'Proses']); // Status pengaduan

            // Indexing jika diperlukan
            $table->index(['id_tiang', 'id_panel']); // Combine indexing for better performance

            $table->timestamps(); // Menyimpan waktu dibuat dan diperbarui
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
