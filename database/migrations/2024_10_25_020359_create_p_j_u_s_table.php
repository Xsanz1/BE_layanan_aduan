<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pjus', function (Blueprint $table) {
            $table->id();
            $table->integer('Lapisan');
            $table->integer('No_App');
            $table->integer('No_Tiang_lama');
            $table->integer('No_tiang_baru');
            $table->string('Nama_Jalan');
            $table->string('kecamatan');
            $table->float('Tinggi_Tiang_m');
            $table->string('Jenis_Tiang');
            $table->integer('Daya_lampu_w');
            $table->string('Status_Jalan');
            $table->string('longtidute');
            $table->string('lattidute');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pjus');
    }
};