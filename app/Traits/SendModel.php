<?php

namespace App\Traits;

use App\Models\AppConfiguration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait SendModel
{
    protected function askModel($text)
    {
        $url = AppConfiguration::where('key', 'MODEL_ENDPOINT')->value('value');

        if (!$url) {
            return 'Maaf, layanan model belum dikonfigurasi.';
        }

        $response = Http::timeout(60)->post(rtrim($url, '/') . '/chat', [
            'message' => $text,
        ]);

        if (!$response->successful()) {
            return 'Maaf, layanan model sedang tidak tersedia.';
        }

        $data = $response->json();

        return $data['response'] ?? 'Maaf, layanan model sedang tidak tersedia.';
    }
}
