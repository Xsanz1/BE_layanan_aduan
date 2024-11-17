<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panel extends Model
{
    use HasFactory;

    protected $table = 'data_panels';
    
    protected $primaryKey = 'id_panel';

    protected $fillable = [
        'lapisan', 'no_app', 'longitude', 'latitude', 'abd_no', 'no_pondasi_tiang',
        'line1_120w', 'line1_120w_2l', 'line1_90w', 'line1_60w', 'line2_120w', 
        'line2_120w_2l', 'line2_90w', 'line2_60w', 'jumlah_pju', 'total_daya_beban', 
        'daya_app', 'daya_terpakai', 'arus_beban', 'nama_jalan', 'desa_kel', 
        'kecamatan', 'idpel', 'no_kwh', 'no_kunci', 'magnetik_kontaktor', 'timer', 
        'mcb_kwh', 'terminal_block', 'rccb', 'pilot_lamp'
    ];
}