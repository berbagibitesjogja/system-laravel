<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait SendWhatsapp
{
    protected function send($target, $message, $from = 'FIRST')
    {
        return true;
        if (str_ends_with($target, '@g.us')) {
            $target = '120363350581821641@g.us';
        }
        Http::post(env('WHATSAPP_ENDPOINT', 'https://api.fonnte.com') . '/send', [
            'target' => $target,
            'message' => $message,
        ]);
    }
    protected function mentionAll($target)
    {
        return true;
        Http::post(env('WHATSAPP_ENDPOINT', 'https://api.fonnte.com') . '/mention-all', [
            'target' => $target
        ]);
    }
}
