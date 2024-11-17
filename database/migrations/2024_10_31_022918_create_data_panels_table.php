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
        Schema::create('data_panels', function (Blueprint $table) {
            $table->id('id_panel');
            $table->string('lapisan');
            $table->string('no_app');
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->string('abd_no')->nullable();
            $table->string('no_pondasi_tiang')->nullable();
            $table->integer('line1_120w')->nullable();
            $table->integer('line1_120w_2l')->nullable();
            $table->integer('line1_90w')->nullable();
            $table->integer('line1_60w')->nullable();
            $table->integer('line2_120w')->nullable();
            $table->integer('line2_120w_2l')->nullable();
            $table->integer('line2_90w')->nullable();
            $table->integer('line2_60w')->nullable();
            $table->integer('jumlah_pju')->nullable();
            $table->integer('total_daya_beban')->nullable();
            $table->integer('daya_app')->nullable();
            $table->string('daya_terpakai')->nullable();
            $table->string('arus_beban')->nullable();
            $table->string('nama_jalan')->nullable();
            $table->string('desa_kel')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('idpel')->nullable();
            $table->string('no_kwh')->nullable();
            $table->string('no_kunci')->nullable();
            $table->string('magnetik_kontaktor')->nullable();
            $table->string('timer')->nullable();
            $table->string('mcb_kwh')->nullable();
            $table->string('terminal_block')->nullable();
            $table->string('rccb')->nullable();
            $table->string('pilot_lamp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_panels');
    }
};