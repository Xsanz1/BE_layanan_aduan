<?php

namespace App\Http\Controllers;

use App\Services\WablasService;
use Illuminate\Http\Request;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\Storage;

class WhatsAppGroupController extends Controller
{
    protected $wablasService;

    public function __construct(WablasService $wablasService)
    {
        $this->wablasService = $wablasService;
    }

    /**
     * Send a message to a predefined WhatsApp group.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        // Validasi input
        $request->validate([
            'pengaduan_id' => 'required|exists:pengaduan,id_pengaduan',
        ]);
    
        // Ambil data pengaduan berdasarkan ID
        $pengaduan = Pengaduan::find($request->input('pengaduan_id'));
    
        // Ambil data yang ingin dikirim
        $dataToSend = [
            'nomor_pengaduan' => $pengaduan->nomor_pengaduan,
            'pelapor' => $pengaduan->pelapor,
            'kondisi_masalah' => $pengaduan->kondisi_masalah,
            'lokasi' => $pengaduan->lokasi,
            'tanggal_pengaduan' => $pengaduan->tanggal_pengaduan,
            'jam_pengaduan' => $pengaduan->jam_pengaduan,
            'keterangan_masalah' => $pengaduan->keterangan_masalah,
            'status' => $pengaduan->status,
            // Tambahkan data lainnya sesuai kebutuhan
        ];
    
        // Cek apakah ada foto pengaduan yang akan dikirim
        $fotoUrl = null;
        if ($pengaduan->foto_pengaduan) {
            // Ambil URL gambar (public URL)
            $fotoUrl = url($pengaduan->foto_pengaduan);  // http://your-domain.com/storage/uploads/your-file.jpg
        }
    
        // Format pesan teks yang lebih terstruktur dan rapi
        $message  = "Nomor Pengaduan: " . $dataToSend['nomor_pengaduan'] . "\n";
        $message .= "Pelapor: " . $dataToSend['pelapor'] . "\n";
        $message .= "Kondisi Masalah: " . $dataToSend['kondisi_masalah'] . "\n";
        $message .= "Lokasi: " . $dataToSend['lokasi'] . "\n";
        $message .= "Tanggal Pengaduan: " . $dataToSend['tanggal_pengaduan'] . "\n";
        $message .= "Jam Pengaduan: " . $dataToSend['jam_pengaduan'] . "\n";
        $message .= "Keterangan Masalah: " . $dataToSend['keterangan_masalah'] . "\n";
        $message .= "Status: " . $dataToSend['status'] . "\n";
    
        // Tambahkan URL gambar jika ada
        if ($fotoUrl) {
            $message .= "\n Foto Pengaduan: " . $fotoUrl . "\n";
        }
    
        // Kirim pesan ke grup WhatsApp dengan gambar (jika ada)
        $response = $this->wablasService->sendMessageToGroup($message, $fotoUrl);
    
        if ($response && isset($response['status']) && $response['status'] == 'success') {
            return response()->json(['success' => true, 'data' => $response], 200);
        }
    
        return response()->json(['success' => false, 'message' => 'Failed to send message', 'data' => $response], 500);
    }
    
}
