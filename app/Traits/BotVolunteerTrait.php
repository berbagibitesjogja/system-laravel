<?php

namespace App\Traits;

use App\Http\Controllers\BotController;
use App\Http\Controllers\ReportController;
use App\Models\AppConfiguration;
use App\Models\Donation\Donation;
use App\Models\Donation\Sponsor;
use App\Models\Heroes\Hero;
use App\Models\Volunteer\Reimburse;
use App\Models\Volunteer\User;
use Gemini;
use Gemini\Data\Blob;
use Gemini\Enums\MimeType;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


trait BotVolunteerTrait
{
    use SendWhatsapp, SendModel;

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
        $url = Storage::disk('public')->url($reimburse->file);
        $amount = "Rp " . number_format($reimburse->amount, 0, ',', '.');
        $this->send(
            '6285740297985',
            // AppConfiguration::getReimburseContact(),
            "📌 *PENGAJUAN REIMBURSE BARU*\n\n"
                . "👤 *Nama* : {$user->name}\n"
                . "💰 *Nominal* : Rp {$amount}\n"
                . "💳 *Metode* : {$reimburse->method}\n"
                . "🎯 *Tujuan* : {$reimburse->target}\n\n"
                . "🧾 *Kode Reimburse* : {$reimburse->id}\n\n"
                . "_Silakan lakukan pembayaran dan kirimkan bukti pembayaran_\n"
                . "_dengan caption gambar:_\n"
                . "*Payment {$reimburse->id}*\n",
            $url
        );
        $this->send(
            '6289512289613',
            // AppConfiguration::getReimburseContact(),
            "📌 *PENGAJUAN REIMBURSE BARU*\n\n"
                . "👤 *Nama* : {$user->name}\n"
                . "💰 *Nominal* : Rp {$amount}\n"
                . "💳 *Metode* : {$reimburse->method}\n"
                . "🎯 *Tujuan* : {$reimburse->target}\n\n"
                . "🧾 *Kode Reimburse* : {$reimburse->id}\n\n"
                . "_Silakan lakukan pembayaran dan kirimkan bukti pembayaran_\n"
                . "_dengan caption gambar:_\n"
                . "*Payment {$reimburse->id}*\n",
            $url
        );
        $this->send(
            $user->phone,
            "✅ *Pengajuan Reimburse Berhasil*\n\n"
                . "💰 *Nominal* : Rp {$amount}\n"
                . "🧾 *Kode Reimburse* : {$reimburse->id}\n\n"
                . "Pengajuan reimburse kamu sedang kami proses.\n"
                . "Mohon ditunggu ya, terima kasih 🙏"
        );

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

    protected function getReplyFromVolunteer($volunteer, $text, $media)
    {
        if (strtolower($text) == 'reimburse') {
            // $this->send($volunteer->phone, 'Maaf sedang perbaikan');
            $this->send($volunteer->phone,"Reimburse\nNominal : ex. 100.000\nMetode : ex. BCA\nTujuan : ex.12345\nKeterangan : ex. Beli truk\n\n*nominal tanpa Rp ataupun koma, hanya . sebagai pemisah 0");
        } elseif (str_starts_with($text, 'Reimburse')) {
            // $this->send($volunteer->phone, 'Maaf sedang perbaikan');
            $data = $this->parseReimburseMessage($text);
            // $client = Gemini::client(config('gemini.api_key'));
            // $result = $client->generativeModel("models/gemini-2.5-flash")
            //     ->generateContent(["Berikan saya jawaban berupa total harga yang ada pada gambar berikut. hanya dalam bentuk integer tanpa formatting. apabila gambar yang diterima bukan merupakan invoice maka hanya hasilkan 0 tanpa formatting", new Blob(
            //         mimeType: MimeType::IMAGE_JPEG,  // or IMAGE_PNG
            //         data: base64_encode(Http::get($media)->body())
            //     )])
            //     ->text();
            $result = str_replace(['Rp', '.', ',','rp'],'', $data['amount']);
            if ($result != "0") {
                $tmp = tempnam(sys_get_temp_dir(), 'reimburse_');
                file_put_contents($tmp, Http::get($media)->body());

                $path = Storage::disk('public')->putFile(
                    'reimburse',
                    new File($tmp)
                );

                $reimburse = Reimburse::create([
                    'amount' => (int) $result,
                    'user_id' => $volunteer->id,
                    'file' => $path,
                    'method' => $data['method'],
                    'target' => $data['target'],
                    'notes' => $data['notes'],
                ]);
                $this->createReimburse($volunteer, $reimburse);
            }
        } elseif (str_starts_with($text, 'Payment')) {
            // $this->send($volunteer->phone, 'Maaf sedang perbaikan');
                        $code = str_replace('Payment ','',$text);
                        $reimburse = Reimburse::find($code);
            $tmp = tempnam(sys_get_temp_dir(), 'payment_');
            file_put_contents($tmp, Http::get($media)->body());

            $path = Storage::disk('public')->putFile(
                'payment',
                new File($tmp)
                );
            $reimburse->update(['payment'=>$path,'done'=>true]);
            $this->send(
                $reimburse->user->phone,
                "🎉 *Reimburse Telah Dibayarkan*\n\n"
                . "💰 *Nominal* : Rp {$reimburse->amount}\n"
                . "🧾 *Kode Reimburse* : {$reimburse->id}\n\n"
                . "Dana reimburse sudah kami transfer.\n"
                . "Silakan cek dan terima kasih 🙏",
                $media
                );
                
                $this->send(
                '6285740297985',
                // AppConfiguration::getReimburseContact(),
                "✅ *REIMBURSE SELESAI*\n\n"
                . "👤 *Nama* : {$reimburse->user->name}\n"
                . "💰 *Nominal* : Rp {$reimburse->amount}\n"
                . "💳 *Metode* : {$reimburse->method}\n"
                . "🎯 *Tujuan* : {$reimburse->target}\n"
                . "🧾 *Kode Reimburse* : {$reimburse->id}\n\n"
                . "Status: *TELAH DIBAYARKAN*\n"
                . "Terima kasih atas proses reimburse-nya 🙏"
                );
            $this->send(
                '6289512289613',
                "✅ *REIMBURSE SELESAI*\n\n"
                . "👤 *Nama* : {$reimburse->user->name}\n"
                . "💰 *Nominal* : Rp {$reimburse->amount}\n"
                . "💳 *Metode* : {$reimburse->method}\n"
                . "🎯 *Tujuan* : {$reimburse->target}\n"
                . "🧾 *Kode Reimburse* : {$reimburse->id}\n\n"
                . "Status: *TELAH DIBAYARKAN*\n"
                . "Terima kasih atas proses reimburse-nya 🙏"
                , $media
                );
                
        }else{
            $reply = $this->askModel($text);
            $this->send($sender, $reply);
        }
    }

    private function parseReimburseMessage(string $message): array
    {
        preg_match_all('/^(Metode|Tujuan|Keterangan|Nominal)\s*:\s*(.+)$/mi', $message, $matches);

        return [
            'method' => $matches[2][array_search('Metode', $matches[1])] ?? null,
            'target' => $matches[2][array_search('Tujuan', $matches[1])] ?? null,
            'notes' => $matches[2][array_search('Keterangan', $matches[1])] ?? null,
            'amount' => $matches[2][array_search('Nominal', $matches[1])] ?? null,
        ];
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
