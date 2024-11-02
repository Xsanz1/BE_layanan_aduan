<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panel extends Model
{
    use HasFactory;

    protected $table = 'panels';

    protected $fillable = [
        'lapisan',
        'no_app',
        'longitude',
        'latitude',
        'abd_no',
        'no_pondasi_tiang',
        'line_1_120w',
        'line_1_120w_2l',
        'line_1_90w',
        'line_1_60w',
        'line_2_120w',
        'line_2_120w_2l',
        'line_2_90w',
        'line_2_60w',
        'jumlah_pju',
        'total_daya_beban_w',
        'daya_app',
        'daya_terpakai',
        'arus_beban',
        'nama_jalan',
        'desa_kel',
        'kecamatan',
        'idpel',
        'no_kwh',
        'no_kunci',
        'magnetik_kontaktor',
        'timer',
        'mcb_kwh',
        'terminal_block',
        'rccb',
        'pilot_lamp',
    ];
}
