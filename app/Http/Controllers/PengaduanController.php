<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\PengaduanExport;
use App\Imports\PengaduanImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class PengaduanController extends Controller
{
    // Menampilkan daftar pengaduan
    public function view()
    {
        $pengaduan = Pengaduan::all();
        return response()->json($pengaduan);
    }

    // Menyimpan pengaduan baru ke database
    public function create(Request $request)
    {
        $request->validate([
            'pelapor' => 'required|string|max:255',
            'kondisi_masalah' => 'required|string', // Enum validation
            'keterangan_masalah' => 'required|string',
            'id_tiang' => 'required|integer',
            'id_panel' => 'required|integer',
            'lokasi' => 'required|string',
            'foto' => 'nullable|file',
            'status' => 'required|string',
        ]);

        // Set timezone and format date and time
        $timezone = 'Asia/Jakarta';
        $jamPengaduan = Carbon::now($timezone)->format('H:i'); // 24-hour format
        $tanggalPengaduan = Carbon::now($timezone)->format('Y-m-d');
        $yearMonthPart = Carbon::now($timezone)->format('Ymd'); // Format YYYYMM

        // Count existing pengaduan for the current month and set sequential number
        $countThisMonth = Pengaduan::whereYear('tanggal_pengaduan', Carbon::now($timezone)->year)
            ->whereMonth('tanggal_pengaduan', Carbon::now($timezone)->month)
            ->count() + 1;
        $nomorUrut = str_pad($countThisMonth, 4, '0', STR_PAD_LEFT);
        $nomorPengaduan = $yearMonthPart . '-' . $nomorUrut;

        // Handle the main photo
        $fotoPath = '';
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/uploads'), $fileName);
            $fotoPath = 'uploads/' . $fileName;
        }

        // Handle the kondisi_lapangan photo
        $kondisiLapanganPath = '';
        if ($request->hasFile('kondisi_lapangan')) {
            $file = $request->file('kondisi_lapangan');
            $fileName = time() . '_kondisi_' . $file->getClientOriginalName();
            $file->move(public_path('storage/uploads'), $fileName);
            $kondisiLapanganPath = 'uploads/' . $fileName;
        }

        // Create a new pengaduan entry in the database
        $pengaduan = Pengaduan::create([
            'pelapor' => $request->pelapor,
            'nomor_pengaduan' => $nomorPengaduan,
            'kondisi_masalah' => $request->kondisi_masalah,
            'keterangan_masalah' => $request->keterangan_masalah,
            'id_tiang' => $request->id_tiang,
            'id_panel' => $request->id_panel,
            'jam_pengaduan' => $jamPengaduan,
            'tanggal_pengaduan' => $tanggalPengaduan,
            'jam_penyelesaian' => null,
            'tanggal_penyelesaian' => null,
            'durasi_penyelesaian' => null,
            'lokasi' => $request->lokasi,
            'foto' => $fotoPath,
            'status' => $request->status,
        ]);

        return response()->json($pengaduan, 201);
    }


    public function update(Request $request, $id)
    {
        $pengaduan = Pengaduan::find($id);
        if (!$pengaduan) {
            return response()->json(['message' => 'Pengaduan tidak ditemukan.'], 404);
        }

        // Dynamic validation: only validate 'foto' as file if a new file is uploaded
        $request->validate([
            'pelapor' => 'required|string|max:255',
            'kondisi_masalah' => 'required|string',
            'keterangan_masalah' => 'required|string',
            'id_tiang' => 'required|integer',
            'id_panel' => 'required|integer',
            'lokasi' => 'required|string',
            'foto' => $request->hasFile('foto') ? 'file' : 'nullable', // Only validate as file if present
            'status' => 'required|string',
        ]);

        // Update foto hanya jika ada file baru yang diupload
        $fotoPath = $pengaduan->foto;
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($fotoPath && file_exists(public_path('storage/' . $fotoPath))) {
                unlink(public_path('storage/' . $fotoPath));
            }

            $file = $request->file('foto');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/uploads'), $fileName);
            $fotoPath = 'uploads/' . $fileName;
        }

        // Update kondisi_lapangan hanya jika ada file baru yang diupload
        $kondisiLapanganPath = $pengaduan->kondisi_lapangan;
        if ($request->hasFile('kondisi_lapangan')) {
            // Hapus kondisi_lapangan lama jika ada
            if ($kondisiLapanganPath && file_exists(public_path('storage/' . $kondisiLapanganPath))) {
                unlink(public_path('storage/' . $kondisiLapanganPath));
            }

            $file = $request->file('kondisi_lapangan');
            $fileName = time() . '_kondisi_' . $file->getClientOriginalName();
            $file->move(public_path('storage/uploads'), $fileName);
            $kondisiLapanganPath = 'uploads/' . $fileName;
        }

        // Hanya perbarui waktu penyelesaian dan durasi jika status diubah menjadi "Selesai"
        if ($request->status === 'Selesai') {
            $timezone = 'Asia/Jakarta';
            $jamPenyelesaian = Carbon::now($timezone)->format('H:i:s'); // Waktu penyelesaian dengan detik
            $tanggalPenyelesaian = Carbon::now($timezone)->format('Y-m-d');

            // Menghitung durasi penyelesaian
            $jamPengaduan = Carbon::parse($pengaduan->tanggal_pengaduan . ' ' . $pengaduan->jam_pengaduan, $timezone);
            $jamPenyelesaianCarbon = Carbon::parse($tanggalPenyelesaian . ' ' . $jamPenyelesaian, $timezone);
            $durasiPenyelesaian = $jamPengaduan->diff($jamPenyelesaianCarbon);

            // Format durasi menjadi string
            $durasiFormatted = sprintf(
                '%d hari, %d jam, %d menit, %d detik',
                $durasiPenyelesaian->d,
                $durasiPenyelesaian->h,
                $durasiPenyelesaian->i,
                $durasiPenyelesaian->s
            );

            // Set penyelesaian data
            $pengaduan->jam_penyelesaian = $jamPenyelesaian;
            $pengaduan->tanggal_penyelesaian = $tanggalPenyelesaian;
            $pengaduan->durasi_penyelesaian = $durasiFormatted;
        }

        // Update fields lainnya
        $pengaduan->pelapor = $request->pelapor;
        $pengaduan->kondisi_masalah = $request->kondisi_masalah;
        $pengaduan->keterangan_masalah = $request->keterangan_masalah;
        $pengaduan->id_tiang = $request->id_tiang;
        $pengaduan->id_panel = $request->id_panel;
        $pengaduan->lokasi = $request->lokasi;
        $pengaduan->foto = $fotoPath; // Gunakan path yang diperbarui
        $pengaduan->status = $request->status;

        // Simpan perubahan
        $pengaduan->save();

        return response()->json($pengaduan, 201);
    }




    // Menghapus pengaduan dari database
    public function destroy($id)
    {
        $pengaduan = Pengaduan::find($id);
        if (!$pengaduan) {
            return response()->json(['message' => 'Pengaduan tidak ditemukan.'], 404); // Not found response
        }

        $fotoPath = $pengaduan->foto;
        unlink('storage/' . $fotoPath);

        $pengaduan->delete();
        return response()->json(['message' => 'Pengaduan berhasil dihapus.'], 201);
    }
    public function count()
    {
        // Get total number of Pengaduan
        $totalPengaduan = Pengaduan::count();

        // Get counts for completed and pending status
        $totalCompleted = Pengaduan::where('status', 'Selesai')->count();
        $totalPending = Pengaduan::where('status', 'Pending')->count();

        // Return counts in response
        return response()->json([
            'total_pengaduan' => $totalPengaduan,
            'total_completed' => $totalCompleted,
            'total_pending' => $totalPending,
        ], 201);
    }

    public function monthlyCount()
    {
        // Initialize an array to hold counts for each month
        $data = [
            'months' => [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            ],
            'total_monthly_counts' => array_fill(0, 12, 0),      // Initialize total counts for each month
            'unresolved_monthly_counts' => array_fill(0, 12, 0), // Initialize unresolved counts for each month
        ];

        // Get current year using PHP's date function
        $currentYear = date('Y');

        // Fetch all complaints for the current year
        $pengaduan = Pengaduan::whereYear('tanggal_pengaduan', $currentYear)->get();

        // Calculate total monthly counts
        foreach ($pengaduan as $item) {
            $monthIndex = date('n', strtotime($item->tanggal_pengaduan)) - 1; // Get month index (0-based)
            $data['total_monthly_counts'][$monthIndex]++;

            // Count unresolved complaints (e.g., statuses 'Pending' or 'In Progress')
            if (in_array($item->status, ['Pending', 'In Progress'])) {
                $data['unresolved_monthly_counts'][$monthIndex]++;
            }
        }

        return response()->json($data);
    }
    public function exportToExcel()
    {
        try {
            return Excel::download(new PengaduanExport, 'pengaduan_' . now()->format('Ymd_His') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Error saat melakukan ekspor: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengekspor data. Silakan coba lagi.'], 500);
        }
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        // Simpan foto (optional jika diunggah secara bersamaan)
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('foto_pengaduan', 'public');
        }

        Excel::import(new PengaduanImport, $request->file('file'));

        return response()->json(['sukses menambahkan data'], 201);
    }
}
