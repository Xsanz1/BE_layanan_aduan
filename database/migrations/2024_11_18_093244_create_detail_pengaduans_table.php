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
        Schema::create('detail_pengaduan', function (Blueprint $table) {
            $table->id('id_detail_pengaduan');
            $table->foreignId('pengaduan_id')->constrained('pengaduan', 'id_pengaduan')->onDelete('cascade');
            $table->foreignId('panel_id')->constrained('data_panels', 'id_panel')->onDelete('cascade');
            $table->foreignId('pju_id')->constrained('data_pjus', 'id_pju')->onDelete('cascade');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pengaduan');
    }
};
