<?php

namespace App\Jobs;

use App\Traits\SendWhatsapp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSurveyWhatsappMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use SendWhatsapp;

    public int $tries = 3;
    public int $timeout = 120;

    /**
     * @var array<int, int>
     */
    public array $backoff = [60, 300, 900];

    public function __construct(
        public readonly string $phone,
        public readonly string $message,
    ) {}

    public function handle(): void
    {
        $this->send($this->phone, $this->message);
    }
}
