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
            ->orderBy('tanggal_pengaduan', 'asc')  // Mengurutkan berdasarkan tanggal_pengaduan terbaru
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
            // Periksa jika panel ada
            $panel = $details->first()->panel;

            // Jika tidak ada panel, log atau kembalikan pesan yang lebih informatif
            if (!$panel) {
                return [
                    'panel_id' => null,
                    'message' => 'Panel tidak ditemukan untuk detail ini.'
                ];
            }

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
                        'id_pju' => $detail->pju_id,
                        'panel_id' => $detail->panel_id,
                        'lapisan' => $detail->pju->lapisan ?? null, // Gunakan null jika pju tidak ada
                        'no_tiang_lama' => $detail->pju->no_tiang_lama ?? null,
                        'no_tiang_baru' => $detail->pju->no_tiang_baru ?? null,
                        'nama_jalan' => $detail->pju->nama_jalan ?? null,
                        'kecamatan' => $detail->pju->kecamatan ?? null,
                        'tinggi_tiang' => $detail->pju->tinggi_tiang ?? null,
                        'jenis_tiang' => $detail->pju->jenis_tiang ?? null,
                        'spesifikasi_tiang' => $detail->pju->spesifikasi_tiang ?? null,
                        'daya_lampu' => $detail->pju->daya_lampu ?? null,
                        'status_jalan' => $detail->pju->status_jalan ?? null,
                        'tanggal_pemasangan_tiang' => $detail->pju->tanggal_pemasangan_tiang ?? null,
                        'tanggal_pesangan_lampu' => $detail->pju->tanggal_pemasangan_lampu ?? null,
                        'lifetime_tiang' => $detail->pju->lifetime_tiang ?? null,
                        'lifetime_lampu' => $detail->pju->lifetime_lampu ?? null,
                        'rekomendasi_tiang' => $detail->pju->rekomendasi_tiang ?? null,
                        'rekomendasi_lampu' => $detail->pju->rekomendasi_lampu ?? null,
                        'longitude' => $detail->pju->longitude ?? null,
                        'latitude' => $detail->pju->latitude ?? null,
                    ];
                })
            ];
        });

        // Filter untuk menghapus elemen null
        $groupedDetails = $groupedDetails->filter(function ($value) {
            return $value !== null;
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
                'jam_penanganan' => $pengaduan->jam_penanganan,
                'tanggal_penanganan' => $pengaduan->tanggal_penanganan,
                'durasi_penanganan' => $pengaduan->durasi_penanganan,
                'penanganan_masalah' => $pengaduan->penanganan_masalah,
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
            'jam_aduan' => 'required|date_format:H:i',
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
        $jamAduan = Carbon::now($timezone)->format('H:i');
        $jamPenginformasian = Carbon::now($timezone)->format('H:i');
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
            'jam_aduan' => $jamAduan,
            'jam_penginformasian' => $jamPenginformasian,
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
        if ($pengaduan->detailPengaduans->isNotEmpty()) {
            foreach ($pengaduan->detailPengaduans as $detail) {
                $message .= "Id Panel: " . ($detail->panel ? $detail->panel->panel_id : 'N/A') . "\n";
            }
        } else {
            $message .= "Id Panel: N/A\n";
        }
        if ($pengaduan->detailPengaduans->isNotEmpty()) {
            foreach ($pengaduan->detailPengaduans as $detail) {
                $message .= "Id Tiang: " . ($detail->pju ? $detail->pju->pju_id : 'N/A') . "\n";
            }
        } else {
            $message .= "Id Tiang: N/A\n";
        }
        $message .= "Lokasi: " . $pengaduan->lokasi . "\n";
        $message .= "Tanggal Pengaduan: " . $pengaduan->tanggal_pengaduan . "\n";
        $message .= "Jam Aduan: " . $pengaduan->jam_aduan . "\n";
        $message .= "Jam Penginformasian: " . $pengaduan->jam_pengaduan . "\n";
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
                'penanganan_masalah' => 'nullable|string',
                'pencegahan_masalah' => 'nullable|string',
                'penyelesaian_masalah' => 'nullable|string',
                'pengelompokan_masalah' => 'nullable|string|in:Eksternal,Internal',
                'status' => 'required|string|in:Pending,Selesai,Proses',
            ]);

            Log::info("Input request: ", $request->all());

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

            if ($request->filled('penanganan_masalah')) {
                $updateData['penanganan_masalah'] = $request->penanganan_masalah;
            }

            if ($request->filled('pencegahan_masalah')) {
                $updateData['pencegahan_masalah'] = $request->pencegahan_masalah;
            }

            if ($request->filled('pengelompokan_masalah')) {
                $updateData['pengelompokan_masalah'] = $request->pengelompokan_masalah;
            }

            if ($request->filled('penyelesaian_masalah')) {
                $updateData['penyelesaian_masalah'] = $request->penyelesaian_masalah;
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

            // Jika status menjadi Selesai, update waktu penanganan
            // Jika status menjadi Selesai, update waktu penanganan
            if ($request->status === 'Selesai') {
                $timezone = 'Asia/Jakarta';
                $jampenanganan = Carbon::now($timezone)->format('H:i');
                $tanggalpenanganan = Carbon::now($timezone)->format('Y-m-d');

                $jamPengaduan = Carbon::parse($pengaduan->tanggal_pengaduan . ' ' . $pengaduan->jam_aduan, $timezone);
                $jampenangananCarbon = Carbon::parse($tanggalpenanganan . ' ' . $jampenanganan, $timezone);
                $durasipenanganan = $jamPengaduan->diff($jampenangananCarbon);

                // Mengakumulasi durasi hari menjadi jam
                $totalJam = ($durasipenanganan->d * 24) + $durasipenanganan->h; // Mengubah hari menjadi jam dan menambahkannya
                $totalMenit = $durasipenanganan->i;

                // Format durasi dalam jam dan menit
                $durasipenangananFormatted = sprintf('%d jam, %d menit', $totalJam, $totalMenit);

                $updateData['jam_penyelesaian'] = $jampenanganan;
                $updateData['tanggal_penyelesaian'] = $tanggalpenanganan;
                $updateData['durasi_penyelesaian'] = $durasipenangananFormatted;

                Log::info("Durasi penanganan dihitung: " . $updateData['durasi_penyelesaian']);
            }

            // Update data pengaduan
            $pengaduan->update($updateData);

            Log::info("Data pengaduan berhasil diperbarui: ", $pengaduan->toArray());

            $pengaduan->load('detailPengaduans.pju', 'detailPengaduans.panel');

            // Mengirim pesan ke WhatsApp setelah pengaduan dibuat
            $message = "Uraian_masalah: " . $pengaduan->uraian_masalah . "\n";
            $message .= "penanganan Masalah: " . $pengaduan->penanganan_masalah . "\n";
            $message .= "Pencegahan Masalah: " . $pengaduan->pencegahan_masalah . "\n";
            $message .= "Pengelompokan Masalah: " . $pengaduan->pengelompokan_masalah . "\n";
            $message .= "Tanggal Penanganan: " . $pengaduan->tanggal_penanganan . "\n";
            $message .= "Jam penanganan: " . $pengaduan->jam_penanganan . "\n";
            $message .= "Durasi penanganan: " . $pengaduan->durasi_penanganan . "\n";
            $message .= "Status: " . $pengaduan->status . "\n";

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
            if (in_array($item->status, ['Selesai', 'In Progress'])) {
                $data['unresolved_monthly_counts'][$monthIndex]++;
            }
        }

        return response()->json($data);
    }

    public function exportExcel(Request $request)
    {
        $month = $request->query('month');
        $year = $request->query('year');

        // Validate month and year
        if (!$month || !$year) {
            return response()->json(['error' => 'Bulan dan tahun tidak valid'], 400);
        }

        // Validate month and year format
        if (!is_numeric($month) || !is_numeric($year) || $month < 1 || $month > 12 || $year < 2000) {
            return response()->json(['error' => 'Bulan atau tahun tidak valid'], 400);
        }

        // Filter complaints based on month and year
        $pengaduans = Pengaduan::whereMonth('tanggal_pengaduan', $month)
            ->whereYear('tanggal_pengaduan', $year)
            ->get();

        // Check if there are any complaints to export
        if ($pengaduans->isEmpty()) {
            return response()->json(['message' => 'Tidak ada pengaduan untuk bulan dan tahun ini'], 404);
        }

        // Send data to export
        return Excel::download(new PengaduanExport($pengaduans), 'pengaduan.xlsx');
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
        if ($pengaduans->isEmpty()) {
            return response()->json(['message' => 'Tidak ada data pengaduan'], 400);
        }

        $phpWord = new PhpWord();

        // Mengatur font default menjadi Times New Roman
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(13); // Ukuran font default

        // Mengulangi setiap pengaduan untuk membuat laporan
        foreach ($pengaduans as $index => $pengaduan) {
            // Buat section baru setiap dua pengaduan
            if ($index % 2 == 0) {
                $section = $phpWord->addSection();

                // Menambahkan paragraf style untuk center
                $phpWord->addParagraphStyle('centered', ['align' => 'center']);

                // Menambahkan bagian judul dan memusatkan teks
                $titleStyle = ['bold' => true, 'size' => 16];
                $section->addText("Dokumentasi Pengaduan dan penanganan", $titleStyle, 'centered');
                $section->addTextBreak(1); // Menambahkan jarak setelah judul
            }

            // Tambahkan garis pembatas di setiap awal pengaduan
            $section->addLine([
                'weight' => 2, // Ketebalan garis
                'width' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(20), // Lebar garis dalam cm (20 cm = halaman penuh)
                'height' => 0, // Tinggi garis tetap 0 untuk horizontal
                'color' => '000000', // Warna hitam
                'align' => 'center', // Pastikan garis tetap di tengah
            ]);

            $section->addText("Nomor Laporan: " . $pengaduan->nomor_pengaduan); // Mengambil nomor laporan
            $panel = $detailPengaduans->firstWhere('pengaduan_id', $pengaduan->id_pengaduan);
            $section->addText("Panel Nomor: " . ($panel ? $panel->panel_id : 'Tidak ada panel')); // Mengambil panel_id jika ada

            // Menambahkan tabel untuk pengaduan dan penanganan
            $section->addTextBreak(1); // Memberikan jarak antara teks dan tabel
            $table = $section->addTable(['borderSize' => 12]);

            // Menambahkan header tabel dengan teks yang dipusatkan secara horizontal dan vertikal
            $table->addRow();
            $table->addCell(6000, [
                'valign' => 'center',  // Vertikal tengah
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, // Horizontal tengah
            ])->addText("Pengaduan", [
                'bold' => true,
                'align' => 'center', // Teks di tengah
            ], [
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
            ]);

            $table->addCell(6000, [
                'valign' => 'center',  // Vertikal tengah
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, // Horizontal tengah
            ])->addText("penanganan", [
                'bold' => true,
                'align' => 'center', // Teks di tengah
            ], [
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
            ]);

            // Menambahkan gambar di bawah deskripsi
            $table->addRow();
            $cell1 = $table->addCell(6000, ['valign' => 'center', 'align' => 'center']);
            if ($pengaduan->foto_pengaduan) {
                $imagePath = public_path('storage/' . $pengaduan->foto_pengaduan);
                if (file_exists($imagePath)) {
                    $cell1->addImage($imagePath, [
                        'width' => 100,
                        'height' => 100,
                        'wrappingStyle' => 'inline',
                        'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                    ]);
                } else {
                    $cell1->addText('Gambar pengaduan tidak ditemukan');
                }
            } else {
                $cell1->addText('Tidak ada gambar pengaduan');
            }

            $cell2 = $table->addCell(6000);
            if ($pengaduan->foto_penanganan) {
                $imagePath = public_path('storage/' . $pengaduan->foto_penanganan);
                if (file_exists($imagePath)) {
                    $cell2->addImage($imagePath, [
                        'width' => 100,
                        'height' => 100,
                        'wrappingStyle' => 'inline',
                        'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                    ]);
                } else {
                    $cell2->addText('Gambar penanganan tidak ditemukan');
                }
            } else {
                $cell2->addText('Tidak ada gambar penanganan');
            }

            $section->addTextBreak(2);

            // Tambahkan page break setelah setiap 2 pengaduan
            if (($index + 1) % 2 == 0) {
                $section->addPageBreak();
            }
        }

        // Menyimpan file Word
        $fileName = 'Laporan_Pengaduan_' . date('Y-m-d') . '.docx';
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
    public function exportTemplate()
    {
        return Excel::download(new PengaduanExport, 'template_pengaduan.xlsx');
    }
}
