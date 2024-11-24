<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPengaduan extends Model
{
    use HasFactory;

    protected $table = 'detail_pengaduan';
    protected $primaryKey = 'id_detail_pengaduan';

    public $timestamps = true;

    protected $fillable = [
        'pengaduan_id',
        'pju_id',
        'panel_id',
    ];

    /**
     * Relasi ke tabel PJU (Many-to-One).
     */
    public function pju()
    {
        return $this->belongsTo(Pju::class, 'pju_id', 'id_pju');
    }

    /**
     * Relasi ke tabel Panel (Many-to-One).
     */
    public function panel()
    {
        return $this->belongsTo(Panel::class, 'panel_id', 'id_panel');
    }

    /**
     * Relasi ke tabel Pengaduan (Many-to-One).
     */
    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id', 'id_pengaduan');
    }
}
