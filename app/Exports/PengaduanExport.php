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

class PengaduanExport implements FromCollection, WithHeadings, WithDrawings, WithEvents
{
    public function collection()
    {
        return Pengaduan::all([
            'pelapor',
            'nomor_pengaduan',
            'kondisi_masalah',
            'keterangan_masalah',
            'id_tiang',
            'id_panel',
            'jam_pengaduan',
            'tanggal_pengaduan',
            'jam_penyelesaian',
            'tanggal_penyelesaian',
            'durasi_penyelesaian',
            'lokasi',
            'foto',
            'status',
        ]);
    }

    public function headings(): array
    {
        return [
            'Pelapor',
            'Nomor Pengaduan',
            'Kondisi Masalah',
            'Keterangan Masalah',
            'ID Tiang',
            'ID Panel',
            'Jam Pengaduan',
            'Tanggal Pengaduan',
            'Jam Penyelesaian',
            'Tanggal Penyelesaian',
            'Durasi Penyelesaian',
            'Lokasi',
            'Foto',
            'Status',
        ];
    }

    public function drawings()
    {
        $drawings = [];
        $pengaduanData = Pengaduan::all();

        foreach ($pengaduanData as $index => $pengaduan) {
            if ($pengaduan->foto && Storage::disk('public')->exists($pengaduan->foto)) {
                $drawing = new Drawing();
                $drawing->setName('Foto');
                $drawing->setDescription('Foto Aduan');
                $drawing->setPath(public_path('storage/' . $pengaduan->foto));
                $drawing->setHeight(50);
                $drawing->setCoordinates('M' . ($index + 2)); // Menempatkan gambar di kolom 'M', menyesuaikan urutan kolom
                $drawings[] = $drawing;
            }
        }

        return $drawings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Mengaktifkan AutoSize pada semua kolom hingga kolom M
                foreach (range('A', 'N') as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
