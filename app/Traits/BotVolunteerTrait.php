<?php

namespace App\Traits;

use App\Http\Controllers\BotController;
use App\Http\Controllers\ReportController;
use App\Models\AppConfiguration;
use App\Models\Donation\Donation;
use App\Models\Donation\Sponsor;
use App\Models\Heroes\Hero;
use App\Models\Volunteer\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


trait BotVolunteerTrait
{
    use SendWhatsapp;

    protected function giveDocumentation($message)
    {
        $message = str_replace('@BOT dokumentasi ', '', $message);
        $message = explode(' ', $message);
        $donation = Donation::find(str_replace('#', '', $message[0]));
        $donation->update(["media" => $message[1]]);
        $this->send('120363313399113112@g.us', 'Terimakasih dokumentasinya', 'SECOND');
    }

    protected function notificationForDocumentation(Donation $donation)
    {
        $message = "*[NEED FOR DOCUMENTATION]*\n\nKode : #" . $donation->id . "\n" . \Carbon\Carbon::parse($donation->take)->isoFormat('D MMMM Y') . "\n\nHalo tim medinfo, kita ada aksi dari " . $donation->sponsor->name . " nih. Minta bantuannya buat kirimin link dokumentasi yaa biar tim Food lebih mudah dalam mencari dokumentasinya. Caranya ketik *@BOT dokumentasi <KODE> <LINK>*\n\nContoh : @BOT dokumentasi #35 https://drive/com";
        $this->send('120363313399113112@g.us', $message, 'SECOND');
    }
    protected function createReimburse($user, $reimburse)
    {
        $amount = "Rp " . number_format($reimburse->amount, 0, ',', '.');
        $this->send($user->phone, "Reimburse sebesar $amount sedang diajukan", AppConfiguration::useWhatsapp());
        $this->send(AppConfiguration::getReimburseContact(), "Reimburse sebesar {$amount} sedang diajukan oleh {$user->name} melalui {$reimburse->method} dengan nomor {$reimburse->target}", 'SECOND');
    }

    protected function replyHero($sender, $message)
    {
        $code = substr(str_replace('@BOT balas ', '', $message), 0, 6);
        $hero = Hero::where('code', $code)->where('status', 'belum')->first();
        if ($hero) {
            $message = substr(str_replace('@BOT balas ', '', $message), 7);
            $this->send($hero->phone, $message . "\n\n_dikirim menggunakan bot_", AppConfiguration::useWhatsapp());
            $this->send($sender, 'Berhasil mengirimkan balasan kepada ' . $hero->name, 'SECOND');
        }
    }

    protected function getSponsorList($sender)
    {
        $sponsors = Sponsor::all();

        $text = "Daftar Donatur BBJ\n\n";
        foreach ($sponsors as $sponsor) {
            $text .= "#{$sponsor->id} - {$sponsor->name}\n";
        }
        $this->send($sender, $text, AppConfiguration::useWhatsapp());
    }
    protected function getStatus($sender)
    {
        $this->send($sender, 'Bot bisa digunakan', AppConfiguration::useWhatsapp());
    }

    protected function createMonthly($sender, $message)
    {
        $hasil = explode(" ", $message);
        $sponsor = Sponsor::find($hasil[3]);
        $month = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $month = $month[$hasil[4]];
        try {
            $year = "20" . $hasil[5];
        } catch (\Throwable $th) {
            $year = now()->year;
        }
        $filename = ReportController::createMonthlyReport($sponsor, $hasil[4], $year);
        $code = uniqid();
        DB::table('report_keys')->insert(compact('filename', 'code'));
        $link = route('monthlyReport', compact('code'));
        $res = "✅ *Berhasil membuat laporan bulanan!*\n\n"
            . "📌 Donatur: *{$sponsor->name}*\n"
            . "📅 Bulan: *{$month} {$year}*\n\n"
            . "⬇️ Silakan download di sini:\n{$link}\n\n"
            . "⚠️ _Link hanya bisa dipakai selama 5 menit, setelahnya hangus_";
        dispatch(function () use ($code) {
            $row = DB::table('report_keys')->where('code', $code)->first();
            if ($row) {
                Storage::delete('public/monthly/' . $row->filename);
                DB::table('report_keys')->where('code', $row->code)->delete();
            }
        })->delay(now()->addMinutes(5));
        $this->send($sender, $res, AppConfiguration::useWhatsapp());
    }
    protected function sendToPemkot($payload)
    {
        return Http::post("https://berbagibitesjogja.com/pemkot/from-fonnte", $payload);
    }
    protected function getRecap()
    {
        $volunteers = User::withCount('attendances')
            ->where('role', 'member')
            ->having('attendances_count', '>', 0)
            ->orderBy('attendances_count', 'desc')
            ->orderBy('name')
            ->get();
        $text  = "*📊 Rekap Kontribusi Volunteer*\n";
        $text .= "_Sejak 7 September 2025 (Batch 4)_\n\n";

        foreach ($volunteers as $item) {
            $name = $this->shortenName($item->name);
            $text .= "- {$name} — {$item->attendances_count}\n";
        }

        $text .= "\n👉 *Nama yang belum tercantum menandakan belum terdapat keikutsertaan dalam kegiatan.*";
        $text .= "\n\n💪 Tetap semangat berkontribusi, setiap aksi kecil kalian punya dampak besar untuk lingkungan dan sesama 🌱";

        BotController::sendForPublic('120363350581821641@g.us', $text, AppConfiguration::useWhatsapp());
    }
    function shortenName(string $name): string
    {
        return strlen($name) > 20 ? (count($p = explode(' ', $name, 2)) > 1 ? strtoupper($p[0][0]) . ". " . $p[1] : substr($name, 0, 20)) : $name;
    }
}
