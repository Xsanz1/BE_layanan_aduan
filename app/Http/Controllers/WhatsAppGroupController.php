<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppGroupController extends Controller
{
    // Simpan pesan yang akan dikirim ke dalam database dan kirim ke WA Blas API
    public function sendMessage(Request $request)
    {
        // Validasi input dari request
        $validated = $request->validate([
            'group_id' => 'required|string',  // ID grup dari WA Blas
            'message' => 'required|string'    // Konten pesan
        ]);

        // Simpan ke dalam database
        $whatsAppGroup = WhatsAppGroup::create([
            'group_id' => $validated['group_id'],
            'message' => $validated['message']
        ]);

        // Kirim pesan menggunakan API WA Blas
        $response = Http::withToken('bVNoCXrUG6Vh4SBAFNpt5AeNysMMDqTznvvyaIUp4G0D41gA468CHAu0waD1QXoi')
            ->post('https://tegal.wablas.com/api/v2/send-message', [
                'phone' => $validated['group_id'], // ID grup
                'message' => $validated['message'] // Isi pesan
            ]);

        // Periksa apakah pesan berhasil dikirim
        if ($response->successful()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Message sent and saved successfully',
                'data' => $whatsAppGroup
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send message',
                'details' => $response->json()
            ], 400);
        }
    }
}
