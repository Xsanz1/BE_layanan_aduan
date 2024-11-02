<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak menggunakan konvensi penamaan
    protected $table = 'pengaduan';

    // Tentukan kolom yang dapat diisi secara massal
    protected $fillable = [
        'pelapor',
        'nomor_pengaduan',
        'masalah',
        'id_tiang',
        'id_panel',
        'jam_pengaduan',
        'tanggal_pengaduan',
        'lokasi',
        'foto',
        'status',
    ];

    // Jika ada relasi yang ingin didefinisikan, misalnya relasi dengan model lain
    // Anda bisa menambahkannya di sini
}
