<?php

namespace App\Console\Commands;

use App\Models\FormJob;
use App\Traits\SendWhatsapp;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendFollowUpAll extends Command
{
    use SendWhatsapp;

    protected $signature = 'follow-up:all';
    public function handle()
    {
        $datas = FormJob::all()->pluck('data');
        $data = collect($datas);
        $now = Carbon::now();

        if ($now->hour < 12) {
            $tomorrow = $now->toDateString(); // hari ini
        } else {
            $tomorrow = $now->addDay()->toDateString(); // besok
        }
        $d = $data->firstWhere('date', $tomorrow);
        if ($d) {
            $output = '';
            $output .= "📢 *Pemberitahuan Tugas Volunteer*\n\n";
            $output .= "📍 Donor: *{$d['sponsor']}*\n";
            $output .= "🎯 Tujuan: *{$d['receiver']}*\n\n";
            $output .= "Berikut kebutuhan tim dan jumlah orang yang dibutuhkan:\n";

            foreach ($d['jobs'] as $job) {
                $output .= "- {$job['name']} ({$job['place']}) — {$job['need']} orang\n";
            }

            $output .= "\n👥 *Daftar yang sudah bertugas:*\n";
            foreach ($d['jobs'] as $job) {
                $names = array_map(fn($p) => "{$p['name']} ({$p['phone']})", $job['persons']);
                $namesStr = !empty($names) ? implode("\n", $names) : '-';
                $output .= "\n🧩 {$job['name']}:\n{$namesStr}\n";
            }

            $output .= "\n⚠️ Bagi teman-teman volunteer yang ingin ikut berkontribusi di kegiatan ini, "
                . "silakan isi jadwal tugas melalui link berikut ya:\n"
                . "👉 berbagibitesjogja.com/war\n\n"
                . "Terima kasih atas semangat dan partisipasinya 🌱";

            $this->send('120363350581821641@g.us', $output);
        }
        return 0;
    }
}
