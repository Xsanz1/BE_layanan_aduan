<?php

namespace App\Imports;

use App\Models\Pengaduan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class PengaduanImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Pengaduan([
            'pelapor'                => $row['pelapor'] ?? null,
            'nomor_pengaduan'        => $row['nomor_pengaduan'] ?? null,
            'kondisi_masalah'        => $row['kondisi_masalah'] ?? null,
            'lokasi'                 => $row['lokasi'] ?? null,
            'foto_pengaduan'         => $row['foto_pengaduan'] ?? null,
            'tanggal_pengaduan'      => $this->parseDate($row['tanggal_pengaduan']),
            'jam_pengaduan'          => $this->parseTime($row['jam_pengaduan']),
            'keterangan_masalah'     => $row['keterangan_masalah'] ?? null,
            'foto_penanganan'        => $row['foto_penanganan'] ?? null,
            'uraian_masalah'         => $row['uraian_masalah'] ?? null,
            'tanggal_penyelesaian'   => $this->parseDate($row['tanggal_penyelesaian']),
            'jam_penyelesaian'       => $this->parseTime($row['jam_penyelesaian'], 'H:i:s'),
            'durasi_penyelesaian'    => $row['durasi_penyelesaian'] ?? null,
            'penyelesaian_masalah'   => $row['penyelesaian_masalah'] ?? null,
            'status'                 => $row['status'] ?? 'Pending',
        ]);
    }

    private function parseTime($time, $format = 'H:i')
    {
        return $time ? Carbon::parse($time)->setTimezone('Asia/Jakarta')->format($format) : null;
    }

    private function parseDate($date, $format = 'Y-m-d')
    {
        return $date ? Carbon::parse($date)->setTimezone('Asia/Jakarta')->format($format) : null;
    }
}
