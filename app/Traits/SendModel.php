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
        $url = AppConfiguration::where('key', "MODEL_ENDPOINT")->first()->value;
        $response = Http::timeout(60)->post($url . '/chat', [
            'message' => $text,
        ]);
        $data = $response->json();
        return $data['response'];
    }
}
