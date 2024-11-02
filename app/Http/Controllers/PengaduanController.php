<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
            'masalah' => 'required|string',
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

        // Generate nomor_pengaduan: YYYYMMDD-RANDOMNUMBER
        $datePart = Carbon::now($timezone)->format('Ymd');
        $randomNumber = random_int(1000, 9999); // Generates a random 4-digit number
        $nomorPengaduan = $datePart . $randomNumber;

        // Handle the image file
        $fotoPath = '';
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/uploads'), $fileName);
            $fotoPath = 'uploads/' . $fileName;
        }

        // Create a new pengaduan entry in the database
        $pengaduan = Pengaduan::create([
            'pelapor' => $request->pelapor,
            'nomor_pengaduan' => $nomorPengaduan,
            'masalah' => $request->masalah,
            'id_tiang' => $request->id_tiang,
            'id_panel' => $request->id_panel,
            'jam_pengaduan' => $jamPengaduan,
            'tanggal_pengaduan' => $tanggalPengaduan,
            'lokasi' => $request->lokasi,
            'foto' => $fotoPath,
            'status' => $request->status,
        ]);

        return response()->json($pengaduan, 201);
    }

    public function update(Request $request, $id)
    {
        // Find pengaduan by ID
        $pengaduan = Pengaduan::find($id);
        if (!$pengaduan) {
            return response()->json(['message' => 'Pengaduan tidak ditemukan.'], 404); // Pengaduan not found
        }

        // Validate input (skip nomor_pengaduan uniqueness check as it's an update)
        $request->validate([
            'pelapor' => 'required|string|max:255',
            'masalah' => 'required|string',
            'id_tiang' => 'required|integer',
            'id_panel' => 'required|integer',
            'lokasi' => 'required|string',
            'foto' => 'nullable|file',
            'status' => 'required|string',
        ]);

        // Handle the image file
        $fotoPath = $pengaduan->foto;
        if ($request->hasFile('foto')) {
            if ($fotoPath && file_exists(public_path('storage/' . $fotoPath))) {
                unlink(public_path('storage/' . $fotoPath)); // Delete old image
            }

            $file = $request->file('foto');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/uploads'), $fileName);
            $fotoPath = 'uploads/' . $fileName;
        }

        // Set timezone (defaulting to 'Asia/Jakarta' if not provided)
        $timezone = $request->input('timezone', 'Asia/Jakarta');

        // Automatically set to the current date and time
        $jamPengaduan = Carbon::now($timezone)->format('H:i'); // 24-hour format
        $tanggalPengaduan = Carbon::now($timezone)->format('Y-m-d');

        // Update fields in the database
        $pengaduan->pelapor = $request->pelapor;
        $pengaduan->masalah = $request->masalah;
        $pengaduan->id_tiang = $request->id_tiang;
        $pengaduan->id_panel = $request->id_panel;
        $pengaduan->jam_pengaduan = $jamPengaduan;
        $pengaduan->tanggal_pengaduan = $tanggalPengaduan;
        $pengaduan->lokasi = $request->lokasi;
        $pengaduan->foto = $fotoPath;
        $pengaduan->status = $request->status;

        // Save changes
        $pengaduan->save();

        return response()->json($pengaduan, 200);
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
        return response()->json(['message' => 'Pengaduan berhasil dihapus.']);
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
        ]);
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
}
