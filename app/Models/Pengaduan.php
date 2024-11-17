<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasFactory;

    protected $table = 'pengaduan';
    protected $primaryKey = 'id_pengaduan';
    protected $fillable = [
        'pelapor',
        'nomor_pengaduan',
        'kondisi_masalah',
        'keterangan_masalah',
        'id_tiang',        // Foreign key to PJU
        'id_panel',        // Foreign key to Panel
        'jam_pengaduan',
        'tanggal_pengaduan',
        'jam_penyelesaian',
        'tanggal_penyelesaian',
        'durasi_penyelesaian',
        'lokasi',
        'foto',
        'status',
    ];

    /**
     * Relasi ke tabel PJU.
     */
    public function pju()
    {
        return $this->belongsTo(PJU::class, 'id_tiang'); // 'id' adalah primary key di tabel pju
    }

    /**
     * Relasi ke tabel Panel.
     */
    public function panel()
    {
        return $this->belongsTo(Panel::class, 'id_panel'); // 'id' adalah primary key di tabel panel
    }
}
