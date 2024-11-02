<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PJU extends Model
{
    use HasFactory;

    protected $table = 'pjus';
    
    protected $primaryKey = 'id_pju';

    protected $fillable = [
        'Lapisan',
        'No_App',
        'No_Tiang_lama',
        'No_tiang_baru',
        'Nama_Jalan',
        'kecamatan',
        'Tinggi_Tiang_m',
        'Jenis_Tiang',
        'Daya_lampu_w',
        'Status_Jalan',
        'longtidute',
        'lattidute',
    ];
}