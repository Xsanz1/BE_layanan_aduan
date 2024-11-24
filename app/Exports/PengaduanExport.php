<?php

namespace App\Exports;

use App\Models\Pengaduan;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Facades\Log;


class PengaduanExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Ambil semua data pengaduan dengan relasi
        $pengaduans = Pengaduan::with(['detailPengaduans.pju', 'detailPengaduans.panel'])->get();
        Log::info("Relasi detailPengaduans:", $pengaduans->toArray());

        // Format data untuk ekspor
        $rows = [];
        foreach ($pengaduans as $pengaduan) {
            foreach ($pengaduan->detailPengaduans as $detail) {
                $rows[] = [
                    'id_pengaduan' => $pengaduan->id_pengaduan,
                    'nomor_pengaduan' => $pengaduan->nomor_pengaduan,
                    'pelapor' => $pengaduan->pelapor,
                    'kondisi_masalah' => $pengaduan->kondisi_masalah,
                    'lokasi' => $pengaduan->lokasi,
                    'foto_pengaduan' => $pengaduan->foto_pengaduan,
                    'tanggal_pengaduan' => $pengaduan->tanggal_pengaduan,
                    'jam_pengaduan' => $pengaduan->jam_pengaduan,
                    'keterangan_masalah' => $pengaduan->keterangan_masalah,
                    'foto_penanganan' => $pengaduan->foto_penanganan,
                    'uraian_masalah' => $pengaduan->uraian_masalah,
                    'tanggal_penyelesaian' => $pengaduan->tanggal_penyelesaian,
                    'jam_penyelesaian' => $pengaduan->jam_penyelesaian,
                    'durasi_penyelesaian' => $pengaduan->durasi_penyelesaian,
                    'penyelesaian_masalah' => $pengaduan->penyelesaian_masalah,
                    'panel_id' => $detail->panel ? $detail->panel->id_panel : 'N/A',
                    'pju_tiang' => $detail->pju ? $detail->pju->no_tiang_baru : 'N/A',
                    'status' => $pengaduan->status,
                ];
            }
        }
        return collect($rows);
    }
    public function headings(): array
    {
        return [
            'Nomor Pengaduan',
            'Pelapor',
            'Kondisi Masalah',
            'Lokasi',
            'Foto Pengaduan',
            'Tanggal Pengaduan',
            'Jam Pengaduan',
            'Keterangan Masalah',
            'Foto Penanganan',
            'Uraian Masalah',
            'Tanggal Penyelesaian',
            'Jam Penyelesaian',
            'Durasi Penyelesaian',
            'Penyelesaian Masalah',
            'Panel ID',
            'PJU ID (No Tiang Baru)',
            'Status',
        ];
    }
};

    // public function drawings()
    // {
    //     $drawings = [];
    //     $pengaduanData = Pengaduan::all();

    //     foreach ($pengaduanData as $index => $pengaduan) {
    //         if ($pengaduan->foto_pengaduan && Storage::disk('public')->exists($pengaduan->foto_pengaduan)) {
    //             $drawing = new Drawing();
    //             $drawing->setName('Foto Pengaduan');
    //             $drawing->setDescription('Foto Pengaduan');
    //             $drawing->setPath(public_path('storage/' . $pengaduan->foto_pengaduan));
    //             $drawing->setHeight(50);
    //             $drawing->setCoordinates('E' . ($index + 2)); // Tempatkan gambar di kolom 'E'
    //             $drawings[] = $drawing;
    //         }
    //     }

    //     return $drawings;
    // }

//     public function registerEvents(): array
//     {
//         return [
//             AfterSheet::class => function (AfterSheet $event) {
//                 // Aktifkan AutoSize untuk kolom
//                 foreach (range('A', 'Q') as $column) {
//                     $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
//                 }
//             },
//         ];
//     }
// }