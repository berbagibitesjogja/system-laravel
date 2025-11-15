<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait SendWhatsapp
{
    // protected function send($target, $message, $from = 'FIRST')
    // {
    //     if (!str_ends_with($target, '@g.us')) {
    //         return true;
    //     }
    //     Http::post(env('WHATSAPP_ENDPOINT', 'https://api.fonnte.com') . '/send', [
    //         'target' => $target,
    //         'message' => $message,
    //     ]);
    // }

    public static function send($target, $message, $from = 'FIRST')
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'target' => $target,
                'message' => $message,
                'schedule' => 0,
                'typing' => false,
                'delay' => '2',
                'countryCode' => '62',
            ],
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . env("WHATSAPP_FONNTE_" . $from),
            ],
        ]);

        curl_exec($curl);
        curl_close($curl);
    }
    protected function mentionAll($target)
    {
        Http::post(env('WHATSAPP_ENDPOINT', 'https://api.fonnte.com') . '/mention-all', [
            'target' => $target
        ]);
    }
}
