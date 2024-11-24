<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PJU extends Model
{
    use HasFactory;

    protected $table = 'data_pjus';

    protected $primaryKey = 'id_pju';

    protected $fillable = [
        'panel_id',
        'lapisan',
        'no_tiang_lama',
        'no_tiang_baru',
        'nama_jalan',
        'kecamatan',
        'tinggi_tiang',
        'jenis_tiang',
        'spesifikasi_tiang',
        'daya_lampu',
        'status_jalan',
        'tanggal_pemasangan_tiang',
        'tanggal_pemasangan_lampu',
        'lifetime_tiang',
        'lifetime_lampu',
        'rekomendasi_tiang',
        'rekomendasi_lampu',
        'longitude',
        'latitude',
    ];

    public function panel()
    {
        return $this->belongsTo(Panel::class, 'panel_id', 'id_panel');
    }
    
}