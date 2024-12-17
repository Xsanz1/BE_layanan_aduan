<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WablasService
{
    protected $apiToken;
    protected $baseUrl;
    protected $groupId; // Predefined group ID or phone number

    public function __construct()
    {
        $this->apiToken = env('WABLAS_API_TOKEN', 'xjG4dvzmFhDREvqYRAC7jjB4ZHydoaU4ki7qVkSTEHYDr8tf4ordWKULaxhO8gYl');
        $this->baseUrl = 'https://tegal.wablas.com/api/v2';
        $this->groupId = env('WABLAS_GROUP_ID', '120363373291153768'); // Set this in .env or hardcode here
    }

    /**
     * Send a message to the predefined group or phone.
     *
     * @param string $message The message content
     * @return mixed Wablas API response
     */
    public function sendMessageToGroup($message, $imageUrl)
    {
        $url = "{$this->baseUrl}/send-message";

        // Payload untuk mengirim pesan dengan gambar
        $payload = [
            "data" => [
                [
                    'phone' => $this->groupId, // Menggunakan ID grup WhatsApp
                    'message' => $message, // Pesan teks yang ingin dikirim
                    'image' => $imageUrl, // URL gambar yang bisa diakses
                    'isGroup' => 'true', // Menunjukkan bahwa pesan ini ditujukan ke grup
                ]
            ]
        ];

        // Inisialisasi cURL untuk mengirim permintaan
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: {$this->apiToken}",
            "Content-Type: application/json"
        ]);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payload));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($curl);
        curl_close($curl);

        // Log respons dari API untuk debugging
        Log::info('Wablas API response:', ['response' => $result]);

        return json_decode($result, true); // Mengembalikan respons API yang sudah didekode
    }
}
