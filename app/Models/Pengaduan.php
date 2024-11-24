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
        'nomor_pengaduan',
        'pelapor',
        'kondisi_masalah',
        'lokasi',
        'foto_pengaduan',
        'tanggal_pengaduan',
        'jam_pengaduan',
        'keterangan_masalah',
        'foto_penanganan',
        'uraian_masalah',
        'tanggal_penyelesaian',
        'jam_penyelesaian',
        'durasi_penyelesaian',
        'penyelesaian_masalah',
        'status',
    ];

    /**
     * Relasi ke tabel detail_pengaduan (one-to-many).
     */
    public function detailPengaduans()
    {
        return $this->hasMany(DetailPengaduan::class, 'pengaduan_id', 'id_pengaduan');
    }

    /**
     * Relasi ke tabel PJU melalui DetailPengaduan.
     */
    public function pjus()
    {
        return $this->hasManyThrough(
            Pju::class, // Model tujuan
            DetailPengaduan::class, // Model perantara
            'pengaduan_id', // Foreign key pada DetailPengaduan
            'id_pju', // Foreign key pada Pju
            'id_pengaduan', // Local key pada Pengaduan
            'pju_id' // Local key pada DetailPengaduan
        );
    }

    /**
     * Relasi ke tabel Panel melalui DetailPengaduan.
     */
    public function panels()
    {
        return $this->hasManyThrough(
            Panel::class, // Model tujuan
            DetailPengaduan::class, // Model perantara
            'pengaduan_id', // Foreign key pada DetailPengaduan
            'id_panel', // Foreign key pada Panel
            'id_pengaduan', // Local key pada Pengaduan
            'panel_id' // Local key pada DetailPengaduan
        );
    }
}
