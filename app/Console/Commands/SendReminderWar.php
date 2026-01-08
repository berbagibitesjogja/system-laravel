<?php

namespace App\Console\Commands;

use App\Models\FormJob;
use App\Traits\SendWhatsapp;
use Carbon\Carbon;
use Illuminate\Console\Command;
use SebastianBergmann\Environment\Console;

class SendReminderWar extends Command
{
    use SendWhatsapp;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'follow-up:war';

    /**
     * The console command description.
    *
    * @var string
    */
    protected $description = 'Send weekly volunteer reminder (WAR)';

    /**
     * Execute the console command.
    */
    public function handle()
    {
        $datas = FormJob::all()->pluck('data');
        $upcoming = collect($datas);
        
        $output = "📣 *REMINDER WAR TUGAS VOLUNTEER* 📣\n\n";
        $output .= "Hai teman-teman volunteer 👋\n";
        $output .= "Kami ingin mengingatkan bahwa war tugas volunteer akan diadakan pada:\n";
        $output .= "🕖 *Hari Senin, pukul: 19.00 WIB*\n\n";
        $output .= "Berikut kebutuhan tugas yang akan dibuka pada war nanti:\n";

        foreach ($upcoming as $d) {
            $dateFormatted = Carbon::parse($d['date'])->translatedFormat('l, d F Y');
            
            $output .= "📅 *{$dateFormatted}*\n";
            $output .= "📍 Donor: *{$d['sponsor']}*\n";
            $output .= "🎯 Tujuan: *{$d['receiver']}*\n";
            $output .= "🧩 Kebutuhan tim:\n";
            
            foreach ($d['jobs'] as $job) {
                $output .= "- {$job['name']} ({$job['place']}) — {$job['need']} orang\n";
            }

            $output .= "\n";
        }
        
        $output .= "👉 Apply tugas dapat dilakukan melalui link war.berbagibitesjogja.com pada waktu yang telah ditentukan.";
        
        $this->send('120363350581821641@g.us', $output);

        return 0;
    }
}
