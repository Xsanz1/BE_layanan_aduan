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
            'pelapor'                => $row['pelapor'],
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
            'jam_penyelesaian'       => $this->parseTimePenyelesaian($row['jam_penyelesaian'], 'H:i:s'),
            'durasi_penyelesaian'    => $row['durasi_penyelesaian'] ?? null,
            'penyelesaian_masalah'   => $row['penyelesaian_masalah'] ?? null,
            'status'                 => $row['status'] ?? null,
        ]);
    }

    private function parseTime($time, $format = 'H:i')
    {
        // Menangani waktu dan mengubahnya ke format yang sesuai
        return $time ? Carbon::parse($time)->setTimezone('Asia/Jakarta')->format($format) : null;
    }

    private function parseDate($date, $format = 'Y-m-d')
    {
        // Cek jika tanggal adalah angka (serial date Excel)
        if (is_numeric($date)) {
            // Konversi angka menjadi tanggal Excel menggunakan Carbon
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date))
                ->setTimezone('Asia/Jakarta')
                ->format($format);
        }
    
        // Menangani tanggal dalam format mm/dd/yyyy dan mengubahnya ke format yang sesuai
        return $date ? Carbon::createFromFormat('m/d/Y', $date)->setTimezone('Asia/Jakarta')->format($format) : null;
    }
    
    private function parseTimePenyelesaian($time, $format = 'H:i')
{
    // Menangani format waktu yang ada di database (misal: 19:22:00, 21.23.00, dll)
    if ($time) {
        // Jika waktu mengandung titik, ganti titik dengan colon
        $time = str_replace(':', ':', $time);

        // Cek jika format waktu sudah benar
        try {
            return Carbon::parse($time)->setTimezone('Asia/Jakarta')->format($format);
        } catch (\Exception $e) {
            // Menangani jika terjadi kesalahan parsing
            return null;
        }
    }

    return null;
}

}
