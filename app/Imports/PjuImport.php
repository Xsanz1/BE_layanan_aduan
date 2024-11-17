<?php

namespace App\Imports;

use App\Models\Pju;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PjuImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Pju([
            'Lapisan'        => (int) $row['lapisan'],
            'No_App'         => (int) $row['no_app'],
            'No_Tiang_lama'  => (int) $row['no_tiang_lama'],
            'No_tiang_baru'  => (int) $row['no_tiang_baru'],
            'Nama_Jalan'     => $row['nama_jalan'],
            'kecamatan'      => $row['kecamatan'],
            'Tinggi_Tiang_m' => (float) $row['tinggi_tiang_m'],
            'Jenis_Tiang'    => $row['jenis_tiang'],
            'Daya_lampu_w'   => (int) $row['daya_lampu_w'],
            'Status_Jalan'   => $row['status_jalan'],
            'longtidute'     => $row['longtidute'],
            'lattidute'      => $row['lattidute'],
        ]);
    }
}
