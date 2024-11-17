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
            'pelapor'             => $row['pelapor'] ?? null,
            'nomor_pengaduan'     => $row['nomor_pengaduan'] ?? null,
            'kondisi_masalah'     => $row['kondisi_masalah'] ?? null,
            'keterangan_masalah'  => $row['keterangan_masalah'] ?? null,
            'id_tiang'            => $row['id_tiang'] ?? null,
            'id_panel'            => $row['id_panel'] ?? null,
            'jam_pengaduan'       => isset($row['jam_pengaduan']) ? Carbon::parse($row['jam_pengaduan'])->setTimezone('Asia/Jakarta')->format('H:i') : null,
            'tanggal_pengaduan'   => isset($row['tanggal_pengaduan']) ? Carbon::parse($row['tanggal_pengaduan'])->setTimezone('Asia/Jakarta')->format('Y-m-d') : null,
            'jam_penyelesaian'    => isset($row['jam_penyelesaian']) ? Carbon::parse($row['jam_penyelesaian'])->setTimezone('Asia/Jakarta')->format('H:i:s') : null,
            'tanggal_penyelesaian' => isset($row['tanggal_penyelesaian']) ? Carbon::parse($row['tanggal_penyelesaian'])->setTimezone('Asia/Jakarta')->format('Y-m-d') : null,
            'durasi_penyelesaian' => $row['durasi_penyelesaian'] ?? null,
            'lokasi'              => $row['lokasi'] ?? null,
            'foto'                => $row['foto'] ?? null,
            'status'              => $row['status'] ?? 'pending',
        ]);
    }
    private function parseTime($time, $format = 'H:i')
    {
        return $time && Carbon::hasFormat($time, $format)
            ? Carbon::parse($time)->setTimezone('Asia/Jakarta')->format($format)
            : null;
    }

    private function parseDate($date, $format = 'Y-m-d')
    {
        return $date && Carbon::hasFormat($date, $format)
            ? Carbon::parse($date)->setTimezone('Asia/Jakarta')->format($format)
            : null;
    }
}
