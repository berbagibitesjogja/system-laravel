<?php

namespace App\Console\Commands;

use App\Traits\SendWhatsapp;
use Illuminate\Console\Command;

class SendStartWar extends Command
{
    use SendWhatsapp;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'follow-up:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $output = "🚨 *WAR TUGAS VOLUNTEER RESMI DIBUKA!* 🚨\n\n"
        ."Silakan langsung akses dan pilih tugas melalui link berikut:\n"
        ."🔗berbagibitesjogja.com/war\n"
        ."🔗berbagibitesjogja.com/war\n"
        ."🔗berbagibitesjogja.com/war\n\n"
        ."Yuk segera isi sebelum kuota penuh. Terimakasih atas partisipasi dan kontribusi teman-teman untuk berbagi kebaikan✨";
        $this->send('120363350581821641@g.us', $output);
        return 0;
    }
}
