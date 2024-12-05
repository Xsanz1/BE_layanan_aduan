<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\PengaduanExport;
use App\Imports\PengaduanImport;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Models\DetailPengaduan;
use Throwable;
use App\Services\WablasService;
use Exception;

class PengaduanController extends Controller

{
    protected $wablasService;

    public function __construct(WablasService $wablasService)
    {
        $this->wablasService = $wablasService;
    }
    // Menampilkan daftar pengaduan
    // Menampilkan daftar pengaduan
    public function get_pengaduan(Request $request)
    {
        // Mengambil bulan dan tahun dari request, jika tidak ada maka default ke bulan dan tahun saat ini
        $currentMonth = $request->input('month', now()->month); // Menggunakan bulan saat ini jika tidak ada input bulan
        $currentYear = $request->input('year', now()->year);  // Menggunakan tahun saat ini jika tidak ada input tahun

        $pengaduan = Pengaduan::with(['detailPengaduans.pju', 'detailPengaduans.panel'])
            ->whereMonth('tanggal_pengaduan', $currentMonth) // Memfilter berdasarkan bulan saat ini
            ->whereYear('tanggal_pengaduan', $currentYear)  // Memfilter berdasarkan tahun saat ini
            ->orderBy('tanggal_pengaduan', 'desc')  // Mengurutkan berdasarkan tanggal_pengaduan terbaru
            ->get();

        return response()->json($pengaduan, 200);
    }

    public function get_detail_pengaduan($id_pengaduan)
    {
        $pengaduan = Pengaduan::with(['detailPengaduans.pju', 'detailPengaduans.panel'])
            ->find($id_pengaduan);

        if (!$pengaduan) {
            return response()->json(['message' => 'Pengaduan tidak ditemukan.'], 404);
        }

        // Kelompokkan tiang berdasarkan panel
        $groupedDetails = $pengaduan->detailPengaduans->groupBy('panel_id')->map(function ($details) {
            $panel = $details->first()->panel;

            return [
                'panel_id' => $panel->id_panel,
                'lapisan' => $panel->lapisan,
                'no_app' => $panel->no_app,
                'longitude' => $panel->longitude,
                'latitude' => $panel->latitude,
                'abd_no' => $panel->abd_no,
                'no_pondasi_tiang' => $panel->no_pondasi_tiang,
                'line1_120w' => $panel->line1_120w,
                'line1_120w_2l' => $panel->line1_120w_2l,
                'line1_90w' => $panel->line1_90w,
                'line1_60w' => $panel->line1_60w,
                'line2_120w' => $panel->line2_120w,
                'line2_120w_2l' => $panel->line2_120w_2l,
                'line2_90w' => $panel->line2_90w,
                'line2_60w' => $panel->line2_60w,
                'jumlah_pju' => $panel->jumlah_pju,
                'total_daya_beban' => $panel->total_daya_beban,
                'daya_app' => $panel->daya_app,
                'daya_terpakai' => $panel->daya_terpakai,
                'arus_beban' => $panel->arus_beban,
                'nama_jalan' => $panel->nama_jalan,
                'desa_kel' => $panel->desa_kel,
                'kecamatan' => $panel->kecamatan,
                'idpel' => $panel->idpel,
                'no_kwh' => $panel->no_kwh,
                'no_kunci' => $panel->no_kunci,
                'magnetik_kontaktor' => $panel->magnetik_kontaktor,
                'timer' => $panel->timer,
                'mcb_kwh' => $panel->mcb_kwh,
                'terminal_block' => $panel->terminal_block,
                'rccb' => $panel->rccb,
                'pilot_lamp' => $panel->pilot_lamp,
                'tiangs' => $details->map(function ($detail) {
                    return [
                        'id_pju' => $detail->pju->id_pju,
                        'panel_id' => $detail->panel_id,
                        'lapisan' => $detail->pju->lapisan,
                        'no_tiang_lama' => $detail->pju->no_tiang_lama,
                        'no_tiang_baru' => $detail->pju->no_tiang_baru,
                        'nama_jalan' => $detail->pju->nama_jalan,
                        'kecamatan' => $detail->pju->kecamatan,
                        'tinggi_tiang' => $detail->pju->tinggi_tiang,
                        'jenis_tiang' => $detail->pju->jenis_tiang,
                        'spesifikasi_tiang' => $detail->pju->spesifikasi_tiang,
                        'daya_lampu' => $detail->pju->daya_lampu,
                        'status_jalan' => $detail->pju->status_jalan,
                        'tanggal_pemasangan_tiang' => $detail->pju->tanggal_pemasangan_tiang,
                        'tanggal_pesangan_lampu' => $detail->pju->tanggal_pemasangan_lampu,
                        'lifetime_tiang' => $detail->pju->lifetime_tiang,
                        'lifetime_lampu' => $detail->pju->lifetime_lampu,
                        'rekomendasi_tiang' => $detail->pju->rekomendasi_tiang,
                        'rekomendasi_lampu' => $detail->pju->rekomendasi_lampu,
                        'longitude' => $detail->pju->longitude,
                        'latitude' => $detail->pju->latitude,
                    ];
                })
            ];
        });

        return response()->json([
            'message' => 'Pengaduan ditemukan.',
            'data_pengaduan' => [
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
                'jam_penyelesaian' => $pengaduan->jam_penyelesaian,
                'tanggal_penyelesaian' => $pengaduan->tanggal_penyelesaian,
                'durasi_penyelesaian' => $pengaduan->durasi_penyelesaian,
                'penyelesaian_masalah' => $pengaduan->penyelesaian_masalah,
                'status' => $pengaduan->status,
                'detail_pengaduans' => $groupedDetails->values()
            ]
        ]);
    }

    // Membuat pengaduan baru
    public function create_pengaduan(Request $request)
    {
        // Validasi awal
        $request->validate([
            'pelapor' => 'required|string|max:255',
            'kondisi_masalah' => 'required|string',
            'panel_id' => 'required|integer|exists:data_panels,id_panel', // Panel wajib dipilih
            'lokasi' => 'required|string',
            'foto_pengaduan' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'keterangan_masalah' => 'required|string',
        ]);

        // Jika kondisi masalah mengandung kata 'Panel', maka pju_id opsional
        if (str_contains($request->kondisi_masalah, 'Panel')) {
            $request->validate([
                'pju_id' => 'nullable|array', // Array PJU ID menjadi opsional
                'pju_id.*' => 'nullable|exists:data_pjus,id_pju', // Setiap PJU ID valid jika diisi
            ]);
        }

        // Validasi: Pastikan panel belum terhubung dengan pengaduan aktif
        $existingPengaduanPanel = Pengaduan::whereHas('detailPengaduans', function ($query) use ($request) {
            $query->where('panel_id', $request->panel_id);
        })->whereIn('status', ['Pending', 'Progress'])->first();

        if ($existingPengaduanPanel) {
            return response()->json([
                'message' => 'Panel ini tidak dapat dipilih karena sedang terhubung dengan pengaduan yang belum selesai.',
                'pengaduan_id' => $existingPengaduanPanel->id_pengaduan,
                'status' => $existingPengaduanPanel->status
            ], 400);
        }

        // Validasi: Pastikan setiap PJU belum terhubung dengan pengaduan aktif
        $existingPjus = [];
        if ($request->pju_id) {
            foreach ($request->pju_id as $pjuId) {
                $existingPengaduanPju = Pengaduan::whereHas('detailPengaduans', function ($query) use ($pjuId) {
                    $query->where('pju_id', $pjuId);
                })->whereIn('status', ['Pending', 'Progress'])->first();

                if ($existingPengaduanPju) {
                    $existingPjus[] = [
                        'pju_id' => $pjuId,
                        'pengaduan_id' => $existingPengaduanPju->id_pengaduan,
                        'status' => $existingPengaduanPju->status,
                    ];
                }
            }

            if (!empty($existingPjus)) {
                return response()->json([
                    'message' => 'Beberapa PJU tidak dapat dipilih karena sedang terhubung dengan pengaduan yang belum selesai.',
                    'details' => $existingPjus
                ], 400);
            }
        }

        // Set timezone dan format waktu
        $timezone = 'Asia/Jakarta';
        $jamPengaduan = Carbon::now($timezone)->format('H:i');
        $tanggalPengaduan = Carbon::now($timezone)->format('Y-m-d');
        $nomorPengaduan = Carbon::now($timezone)->format('Ymd') . '-' . str_pad(Pengaduan::count() + 1, 4, '0', STR_PAD_LEFT);

        // Upload foto jika ada
        $fotoPath = null;
        if ($request->hasFile('foto_pengaduan')) {
            $file = $request->file('foto_pengaduan');

            // Cek apakah file benar-benar ada
            if ($file->isValid()) {
                $fileName = $file->getClientOriginalName();
                $file->move(public_path('storage/uploads'), $fileName);
                $fotoPath = 'uploads/' . $fileName; // Lokasi file di server
            } else {
                return response()->json([
                    'message' => 'Foto pengaduan tidak valid.',
                ], 400);
            }
        } else {
            $fotoPath = null;
        }


        // Buat data utama pengaduan
        $pengaduan = Pengaduan::create([
            'nomor_pengaduan' => $nomorPengaduan,
            'pelapor' => $request->pelapor,
            'kondisi_masalah' => $request->kondisi_masalah,
            'lokasi' => $request->lokasi,
            'foto_pengaduan' => $fotoPath,
            'tanggal_pengaduan' => $tanggalPengaduan,
            'jam_pengaduan' => $jamPengaduan,
            'keterangan_masalah' => $request->keterangan_masalah,
            'status' => 'Pending',
        ]);

        // Masukkan detail pengaduan
        $details = [];
        if ($request->pju_id && is_array($request->pju_id)) {
            foreach ($request->pju_id as $pjuId) {
                $details[] = [
                    'pengaduan_id' => $pengaduan->id_pengaduan,
                    'panel_id' => $request->panel_id,
                    'pju_id' => $pjuId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        } else {
            $details[] = [
                'pengaduan_id' => $pengaduan->id_pengaduan,
                'panel_id' => $request->panel_id,
                'pju_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert ke tabel detail_pengaduan
        DetailPengaduan::insert($details);

        // Load relasi untuk response
        $pengaduan->load('detailPengaduans.panel', 'detailPengaduans.pju');

        // Mengirim pesan ke WhatsApp setelah pengaduan dibuat
        $message = "Nomor Pengaduan: " . $pengaduan->nomor_pengaduan . "\n";
        $message .= "Pelapor: " . $pengaduan->pelapor . "\n";
        $message .= "Kondisi Masalah: " . $pengaduan->kondisi_masalah . "\n";
        $message .= "Lokasi: " . $pengaduan->lokasi . "\n";
        $message .= "Tanggal Pengaduan: " . $pengaduan->tanggal_pengaduan . "\n";
        $message .= "Jam Pengaduan: " . $pengaduan->jam_pengaduan . "\n";
        $message .= "Keterangan Masalah: " . $pengaduan->keterangan_masalah . "\n";
        $message .= "Status: " . $pengaduan->status . "\n";

        if ($pengaduan->foto_pengaduan) {
            $fotoUrl = url('uploads/' . $pengaduan->foto_pengaduan); // Menyusun URL gambar
            $response = $this->wablasService->sendMessageToGroup($message, $fotoUrl); // Kirim gambar
        } else {
            $response = $this->wablasService->sendMessageToGroup($message, null); // Kirim tanpa gambar
        }

        return response()->json($pengaduan->load('detailPengaduans.panel', 'detailPengaduans.pju'), 200);
    }



    // Memperbarui pengaduan
    public function update_pengaduan(Request $request, $id_pengaduan)
    {
        try {
            Log::info("Memulai proses update pengaduan dengan ID: $id_pengaduan");

            // Ambil data pengaduan beserta relasinya
            $pengaduan = Pengaduan::with(['detailPengaduans.pju', 'detailPengaduans.panel'])->find($id_pengaduan);

            if (!$pengaduan) {
                Log::error("Pengaduan dengan ID: $id_pengaduan tidak ditemukan.");
                return response()->json(['message' => 'Pengaduan tidak ditemukan.'], 404);
            }

            Log::info("Data pengaduan ditemukan: ", $pengaduan->toArray());

            // Validasi input
            $request->validate([
                'uraian_masalah' => 'nullable|string',
                'penyelesaian_masalah' => 'nullable|string',
                'pencegahan_masalah' => 'nullable|string',
                'pengelompokan_masalah' => 'nullable|string|in:Eksternal,Internal',
                'status' => 'required|string|in:Pending,Selesai,Proses',
            ]);

            // Validasi file hanya jika field file dikirimkan
            if ($request->hasFile('foto_penanganan')) {
                $request->validate([
                    'foto_penanganan' => 'file|mimes:jpeg,png,jpg|max:2048',
                ]);
            }

            Log::info("Input request: ", $request->all());

            // Siapkan data untuk diupdate
            $updateData = [
                'status' => $request->status,
            ];

            if ($request->filled('uraian_masalah')) {
                $updateData['uraian_masalah'] = $request->uraian_masalah;
            }

            if ($request->filled('penyelesaian_masalah')) {
                $updateData['penyelesaian_masalah'] = $request->penyelesaian_masalah;
            }

            if ($request->filled('pencegahan_masalah')) {
                $updateData['pencegahan_masalah'] = $request->pencegahan_masalah;
            }

            if ($request->filled('pengelompokan_masalah')) {
                $updateData['pengelompokan_masalah'] = $request->pengelompokan_masalah;
            }

            // Proses upload foto jika ada
            if ($request->hasFile('foto_penanganan')) {
                Log::info("Proses upload file foto penanganan.");
                $file = $request->file('foto_penanganan');
                $fileName = $file->getClientOriginalName();
                $file->move(public_path('storage/uploads'), $fileName);
                $updateData['foto_penanganan'] = 'uploads/' . $fileName;
                Log::info("Foto berhasil diupload dengan path: " . $updateData['foto_penanganan']);
            }

            // Jika status menjadi Selesai, update waktu penyelesaian
            // Jika status menjadi Selesai, update waktu penyelesaian
            if ($request->status === 'Selesai') {
                $timezone = 'Asia/Jakarta';
                $jamPenyelesaian = Carbon::now($timezone)->format('H:i');
                $tanggalPenyelesaian = Carbon::now($timezone)->format('Y-m-d');

                $jamPengaduan = Carbon::parse($pengaduan->tanggal_pengaduan . ' ' . $pengaduan->jam_pengaduan, $timezone);
                $jamPenyelesaianCarbon = Carbon::parse($tanggalPenyelesaian . ' ' . $jamPenyelesaian, $timezone);
                $durasiPenyelesaian = $jamPengaduan->diff($jamPenyelesaianCarbon);

                // Mengakumulasi durasi hari menjadi jam
                $totalJam = ($durasiPenyelesaian->d * 24) + $durasiPenyelesaian->h; // Mengubah hari menjadi jam dan menambahkannya
                $totalMenit = $durasiPenyelesaian->i;

                // Format durasi dalam jam dan menit
                $durasiPenyelesaianFormatted = sprintf('%d jam, %d menit', $totalJam, $totalMenit);

                $updateData['jam_penyelesaian'] = $jamPenyelesaian;
                $updateData['tanggal_penyelesaian'] = $tanggalPenyelesaian;
                $updateData['durasi_penyelesaian'] = $durasiPenyelesaianFormatted;

                Log::info("Durasi penyelesaian dihitung: " . $updateData['durasi_penyelesaian']);
            }

            // Update data pengaduan
            $pengaduan->update($updateData);

            Log::info("Data pengaduan berhasil diperbarui: ", $pengaduan->toArray());

            $pengaduan->load('detailPengaduans.pju', 'detailPengaduans.panel');

            // Mengirim pesan ke WhatsApp setelah pengaduan dibuat
            $message = "Uraian_masalah: " . $pengaduan->uraian_masalah . "\n";
            $message .= "Penyelesaian Masalah: " . $pengaduan->penyelesaian_masalah . "\n";
            $message .= "Pencegahan Masalah: " . $pengaduan->pencegahan_masalah . "\n";
            $message .= "Pengelompokan Masalah: " . $pengaduan->pengelompokan_masalah . "\n";
            $message .= "Tanggal Penanganan: " . $pengaduan->tanggal_penyelesaian . "\n";
            $message .= "Jam Penyelesaian: " . $pengaduan->jam_penyelesaian . "\n";
            $message .= "Durasi Penyelesaian: " . $pengaduan->durasi_penyelesaian . "\n";

            if ($pengaduan->foto_penanganan) {
                $fotoUrl = url('uploads/' . $pengaduan->foto_penanganan); // Menyusun URL gambar
                $response = $this->wablasService->sendMessageToGroup($message, $fotoUrl); // Kirim gambar
            } else {
                $response = $this->wablasService->sendMessageToGroup($message, null); // Kirim tanpa gambar
            }

            return response()->json([
                'message' => 'Pengaduan berhasil diperbarui.',
                'data_pengaduan' => $pengaduan,
            ], 200);
        } catch (Exception $e) {
            Log::error("Terjadi kesalahan saat update pengaduan: ", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Terjadi kesalahan saat memperbarui pengaduan.'], 500);
        }
    }

    // Menghapus pengaduan
    public function delete_pengaduan($id_pengaduan)
    {
        $pengaduan = Pengaduan::find($id_pengaduan);
        if (!$pengaduan) {
            return response()->json(['message' => 'Pengaduan tidak ditemukan.'], 404);
        }

        if ($pengaduan->foto_pengaduan) {
            @unlink(public_path($pengaduan->foto_pengaduan));
        }

        if ($pengaduan->foto_penanganan) {
            @unlink(public_path($pengaduan->foto_penanganan));
        }

        $pengaduan->delete();
        return response()->json(['message' => 'Pengaduan berhasil dihapus.'], 200);
    }
    public function count_pengaduan()
    {
        try {
            $totalPengaduan = Pengaduan::count();
            $totalCompleted = Pengaduan::where('status', 'Selesai')->count();
            $totalPending = Pengaduan::where('status', 'Pending')->count();

            return response()->json([
                'total_pengaduan' => $totalPengaduan,
                'total_completed' => $totalCompleted,
                'total_pending' => $totalPending,
            ], 200);
        } catch (Throwable $e) {
            Log::error("Error fetching pengaduan count: " . $e->getMessage());
            return response()->json(['message' => 'Gagal mengambil data pengaduan.'], 500);
        }
    }
    public function monthlyCount_pengaduan()
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

    public function export_pengaduan()
    {
        try {
            Log::info("Memulai proses ekspor...");

            // Ambil semua data pengaduan
            $pengaduans = Pengaduan::with(['detailPengaduans.panel', 'detailPengaduans.pju'])->get();

            // Log jumlah data yang ditemukan
            Log::info("Jumlah data pengaduan ditemukan: " . $pengaduans->count());

            if ($pengaduans->isEmpty()) {
                return response()->json(['message' => 'Pengaduan tidak ditemukan.'], 404);
            }

            $fileName = 'pengaduan_' . now()->format('Ymd_His') . '.xlsx';
            return Excel::download(new PengaduanExport, $fileName);
        } catch (Throwable $e) {
            Log::error('Error saat ekspor data: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengekspor data.'], 500);
        }
    }
    public function import_pengaduan(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            // Simpan foto (optional jika diunggah secara bersamaan)
            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('foto_pengaduan', 'public');
            }

            // Import data Excel
            Excel::import(new PengaduanImport, $request->file('file'));

            return response()->json(['sukses menambahkan data'], 200);
        } catch (Exception $e) {
            // Log error jika terjadi kegagalan saat impor
            Log::error('Import Pengaduan Gagal: ' . $e->getMessage(), [
                'file' => $request->file('file')->getClientOriginalName(),
                'error' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Gagal mengimpor data, silakan coba lagi.'], 500);
        }
    }
    public function validatePanel($panel_id)
    {
        try {
            // Cari pengaduan aktif (Pending/In Progress) yang terkait dengan panel
            $pengaduan = Pengaduan::whereHas('detailPengaduans', function ($query) use ($panel_id) {
                $query->where('panel_id', $panel_id);
            })->whereIn('status', ['Pending', 'In Progress'])->first();

            // Jika pengaduan ditemukan, panel tidak tersedia
            if ($pengaduan) {
                return response()->json([
                    'message' => 'Panel ini tidak dapat dipilih karena sedang terhubung dengan pengaduan yang belum selesai.',
                    'pengaduan_id' => $pengaduan->id_pengaduan,
                    'status' => $pengaduan->status
                ], 400);
            }

            // Jika tidak ada pengaduan aktif, panel tersedia untuk dipilih
            return response()->json([
                'message' => 'Panel tersedia untuk dipilih.'
            ], 200);
        } catch (Exception $e) {
            // Log error dan kembalikan respons kesalahan
            Log::error("Error validating panel: " . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat memvalidasi panel.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportToWord()
    {
        // Ambil semua data pengaduan dan detail pengaduan
        $pengaduans = Pengaduan::all(); // Mengambil semua data pengaduan
        $detailPengaduans = DetailPengaduan::all(); // Mengambil semua data detail pengaduan

        // Pastikan kita punya data untuk diproses
        if ($pengaduans->isEmpty() || $detailPengaduans->isEmpty()) {
            return response()->json(['message' => 'Tidak ada data pengaduan atau penyelesaian'], 400);
        }

        $phpWord = new PhpWord();

        // Menambahkan bagian pertama (nomor laporan dan panel nomor)
        $section = $phpWord->addSection();
        $section->addText("Nomor Laporan: " . $pengaduans->first()->nomor_pengaduan); // Mengambil nomor laporan pertama
        $section->addText("Panel Nomor: " . $detailPengaduans->first()->panel_id); // Mengambil panel_id pertama

        // Menambahkan bagian tabel (Pengaduan dan Penyelesaian)
        $section->addTextBreak(1); // Memberikan jarak antara teks dan tabel
        $table = $section->addTable();

        // Menambahkan header tabel
        $table->addRow();
        $table->addCell(3000)->addText("Pengaduan");
        $table->addCell(3000)->addText("Penyelesaian");

        // Loop untuk menambahkan pengaduan dan penyelesaian ke tabel
        foreach ($pengaduans as $pengaduan) {
            // Mencari penyelesaian terkait berdasarkan ID pengaduan
            $penyelesaian = $detailPengaduans->firstWhere('pengaduan_id', $pengaduan->id_pengaduan); // Sesuaikan 'pengaduan_id' dengan kolom yang sesuai

            $table->addRow();
            $table->addCell(3000)->addText($pengaduan->deskripsi_pengaduan); // Ambil deskripsi pengaduan
            $table->addCell(3000)->addText($penyelesaian ? $penyelesaian->deskripsi_penyelesaian : 'Belum ada penyelesaian'); // Ambil deskripsi penyelesaian
        }

        // Menyimpan file Word
        $fileName = 'Laporan_Pengaduan_' . '.docx';
        $filePath = storage_path('app/public/' . $fileName);
        $phpWord->save($filePath, 'Word2007');

        // Mengirim file ke browser untuk diunduh
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function filterPengaduan(Request $request)
    {
        $currentMonth = $request->input('month');
        $currentYear = $request->input('year');

        // Log nilai bulan dan tahun yang diterima
        Log::debug("Bulan yang diterima: " . $currentMonth);
        Log::debug("Tahun yang diterima: " . $currentYear);

        try {
            // Debugging: Log query yang akan dijalankan
            $query = Pengaduan::with(['detailPengaduans.pju', 'detailPengaduans.panel'])
                ->whereMonth('tanggal_pengaduan', $currentMonth)
                ->whereYear('tanggal_pengaduan', $currentYear)
                ->orderBy('tanggal_pengaduan', 'desc');

            Log::debug("Query: " . $query->toSql()); // Log query

            $pengaduan = $query->get();

            // Jika data tidak ada
            if ($pengaduan->isEmpty()) {
                return response()->json(['message' => 'Tidak ada pengaduan untuk bulan dan tahun ini.'], 404);
            }

            return response()->json($pengaduan, 200);
        } catch (Exception $e) {
            // Log error jika terjadi exception
            Log::error('Error saat mengambil pengaduan: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
