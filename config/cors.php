<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Path yang diizinkan CORS, biasanya cukup dengan 'api/*' untuk semua endpoint API.

    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'], // Mengizinkan semua metode HTTP (GET, POST, PUT, DELETE, dll.) atau tentukan metode yang diperlukan.

    'allowed_origins' => ['http://localhost:3002'], // Asal (origin) yang diizinkan, sesuaikan dengan domain frontend Anda.

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Mengizinkan semua header atau bisa tentukan seperti ['Content-Type', 'X-Requested-With', 'Authorization']

    'exposed_headers' => [], // Header yang akan diekspos ke frontend

    'max_age' => 0, // Waktu cache untuk preflight requests

    'supports_credentials' => true, // Set ke true jika Anda menggunakan cookies atau session (misalnya dengan Sanctum)
];
