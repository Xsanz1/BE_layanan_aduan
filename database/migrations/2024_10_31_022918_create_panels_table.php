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
        Schema::create('panels', function (Blueprint $table) {
            $table->id();
            $table->string('lapisan')->nullable();
            $table->string('no_app', 50)->nullable();
            $table->decimal('longitude', 10, 5)->nullable();
            $table->decimal('latitude', 10, 5)->nullable();
            $table->string('abd_no', 100)->nullable();
            $table->integer('no_pondasi_tiang')->nullable();
            $table->integer('line_1_120w')->nullable();
            $table->integer('line_1_120w_2l')->nullable();
            $table->integer('line_1_90w')->nullable();
            $table->integer('line_1_60w')->nullable();
            $table->integer('line_2_120w')->nullable();
            $table->integer('line_2_120w_2l')->nullable();
            $table->integer('line_2_90w')->nullable();
            $table->integer('line_2_60w')->nullable();
            $table->integer('jumlah_pju')->nullable();
            $table->integer('total_daya_beban_w')->nullable();
            $table->integer('daya_app')->nullable();
            $table->decimal('daya_terpakai', 5, 2)->nullable();
            $table->decimal('arus_beban', 8, 2)->nullable();
            $table->string('nama_jalan', 255)->nullable();
            $table->string('desa_kel', 255)->nullable();
            $table->string('kecamatan', 255)->nullable();
            $table->string('idpel', 20)->nullable();
            $table->string('no_kwh', 20)->nullable();
            $table->string('no_kunci', 20)->nullable();
            $table->string('magnetik_kontaktor', 50)->nullable();
            $table->string('timer', 50)->nullable();
            $table->string('mcb_kwh', 50)->nullable();
            $table->string('terminal_block', 50)->nullable();
            $table->string('rccb', 50)->nullable();
            $table->string('pilot_lamp', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panels');
    }
};
