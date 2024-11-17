<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_pjus', function (Blueprint $table) {
            $table->id('id_pju');
            $table->foreignId('panel_id')->constrained('data_panels', 'id_panel')->onDelete('cascade');
            $table->string('lapisan');
            $table->string('no_app');
            $table->integer('no_tiang_lama');
            $table->integer('no_tiang_baru');
            $table->string('nama_jalan');
            $table->string('kecamatan');
            $table->integer('tinggi_tiang');
            $table->string('jenis_tiang');
            $table->string('spesifikasi_tiang');
            $table->integer('daya_lampu');
            $table->string('status_jalan');
            $table->date('tanggal_pemasangan_tiang')->nullable();
            $table->date('tanggal_pemasangan_lampu')->nullable();
            $table->integer('lifetime_tiang')->nullable();
            $table->integer('lifetime_lampu')->nullable();
            $table->string('rekomendasi_tiang')->nullable();
            $table->string('rekomendasi_lampu')->nullable();
            $table->decimal('longitude', 10, 7);
            $table->decimal('latitude', 10, 7);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_pjus');
    }
};