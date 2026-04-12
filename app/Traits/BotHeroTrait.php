<?php

namespace App\Traits;

use App\Http\Controllers\ChatController;
use App\Models\AppConfiguration;
use App\Models\Donation\Donation;
use App\Models\Heroes\Hero;
use App\Models\Volunteer\Notify;
use Carbon\Carbon;

trait BotHeroTrait
{
    use SendWhatsapp, TwoWayEncryption;

    protected function verifyFoodHeroes($sender, $text)
    {
        preg_match('/_(.*?)_/', $text, $match);
        $code = $match[1] ?? null;
        $name = $this->decryptData($code);
        $activeDonation = Donation::whereStatus('aktif')->pluck('id');
        $hero = Hero::whereName($name)->whereIn('donation_id', $activeDonation)->first();
        if ($hero) {
            $message =
                "🎉 Halo {$hero->name}!\n\n" .
                "Terima kasih sudah mendaftar sebagai Food Hero BBJ 🌱✨\n" .
                "Berikut adalah *kode penukaranmu*: *{$hero->code}* 🔑\n\n" .
                "📅 Tanggal: " . Carbon::parse($hero->donation->take)->format('d F Y') . "\n" .
                "⏰ Waktu: " . str_pad($hero->donation->hour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($hero->donation->minute, 2, '0', STR_PAD_LEFT) . "\n" .
                "📍 Lokasi: {$hero->donation->location}\n" .
                "🗺️ Maps: {$hero->donation->maps}\n\n" .
                "Harap tunjukkan kode ini saat pengambilan ya. Semoga bermanfaat dan tidak terbuang 🌿💚\n\n" .
                "_Pesan otomatis dari bot BBJ 🤖_";

            $this->send($sender, $message);
        } else {
            $this->send($sender, 'Maaf signature key tidak valid');
        }
    }
    protected function verifyNotify($sender, $text)
    {
        preg_match('/_(.*?)_/', $text, $match);
        $code = $match[1] ?? null;
        $data = explode(',', $this->decryptData($code));
        $name = $data[0];
        $email = $data[1];
        $phone = $data[2];
        try {
            Notify::create(compact('name', 'email', 'phone'));
            $this->send(
                $sender,
                "Terima kasih {$name}, verifikasi berhasil! 🎉\n\n" .
                    "Fitur notifikasi BBJ kamu sudah aktif. Kamu akan otomatis menerima info donasi ketika tersedia 🌱\n\n" .
                    "Catatan: Notifikasi ini berlaku *satu kali*. Setelah menerima notifikasi, kamu perlu daftar lagi jika ingin mendapatkan pemberitahuan berikutnya 😊"
            );
        } catch (\Throwable $th) {
            $this->send(
                $sender,
                "Hai {$name}, kamu sebelumnya sudah terdaftar dan notifikasi akan kamu dapatkan ketika ada donasi, ditunggu yaa! 🎉"
            );
        }
    }
    protected function getReplyFromHeroes($hero, $text)
    {
        $message = '> Balasan Heroes' . " \n\n" . $hero->name . "\n_Kode : " . $hero->code . "_\n\n" . $text;
        $this->send('120363350581821641@g.us', $message, 'SECOND');
    }

    protected function getAllActiveHero($sender)
    {
        $activeDonation = Donation::where('status', 'aktif')->first();
        $allHero = Hero::where('donation_id', $activeDonation->id)->get(['name', 'code']);
        $message = '';
        $message = $message . 'Daftar heroes hari ini' . " \n ";
        $message = $message . '_Jumlah : ' . $allHero->count() . '_' . " \n ";
        foreach ($allHero as $hero) {
            $message = $message . " \n " . $hero->name;
            $message = $message . " \n " . $hero->code;
        }
        $this->send($sender, $message, 'SECOND');
    }

    protected function getAllNotYetHero($sender)
    {
        $activeDonation = Donation::where('status', 'aktif')->first();
        $notyetHero = Hero::where('donation_id', $activeDonation->id)->where('status', 'belum')->get(['name', 'code']);
        $message = '';
        $message = $message . 'Daftar yang belum mengambil' . " \n ";
        $message = $message . '_Jumlah : ' . $notyetHero->count() . '_' . " \n ";
        foreach ($notyetHero as $hero) {
            $message = $message . " \n " . $hero->name;
            $message = $message . " \n " . $hero->code;
        }
        $this->send($sender, $message, 'SECOND');
    }

    protected function getReplyFromFoodDonator($foodDonator, $text)
    {
        $message = '> Balasan Donasi Surplus Food' . " \n\n" . $foodDonator->name . "\n" . $foodDonator->ticket . "\n\n" . $text;
        $this->send('120363301975705765@g.us', $message, 'SECOND');
    }

    protected function reminderToday($sender)
    {
        $activeDonation = Donation::where('status', 'aktif')->first();
        $allActiveHero = Hero::where('donation_id', $activeDonation->id)->get(['name', 'phone']);
        $delay = 30;
        foreach ($allActiveHero as $hero) {
            $message = "🍽️ Halo {$hero->name}!\n\n"
                . "Ada kabar baik nih ✨ Surplus food dari *Berbagi Bites Jogja* sudah bisa diambil:\n\n"
                . "⏰ *Waktu:* " . str_pad($activeDonation->hour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($activeDonation->minute, 2, '0', STR_PAD_LEFT) . "\n"
                . "📍 *Lokasi:* {$activeDonation->location}\n"
                . "{$activeDonation->maps}\n\n"
                . "✅ Jangan lupa datang ya, semoga bermanfaat dan jangan sampai terbuang 🌱💚\n\n"
                . "⚠️ Catatan:\n"
                . "- Jika tidak bisa mengambil, mohon konfirmasi 🙏\n"
                . "- Makanan yang tidak diambil hingga waktu yang ditentukan akan dialihkan ke Food Heroes lain\n\n"
                . "_Pesan ini dikirim otomatis oleh bot BBJ 🤖_";
            $phone = $hero->phone;
            dispatch(function () use ($phone, $message) {
                $this->send($phone, $message, AppConfiguration::useWhatsapp());
            })->delay(now()->addSeconds($delay));
            $delay += 30;
        }
        $message = 'Akan mengirimkan kepada ' . $allActiveHero->count() . ' hero secara bertahap';
        $this->send($sender, $message, 'SECOND');
        $message = 'Berhasil mengirimkan kepada ' . $allActiveHero->count() . ' hero';
        dispatch(function () use ($sender, $message) {
            $this->send($sender, $message, 'SECOND');
        })->delay(now()->addSeconds($delay));
    }

    protected function reminderLastCall($jam, $sender)
    {
        $activeDonation = Donation::where('status', 'aktif')->first();
        $notyetHero = Hero::where('donation_id', $activeDonation->id)->where('status', 'belum')->get(['name', 'phone']);
        $jam = str_replace('@BOT ingatkan hero yang belum ', '', $jam);
        $delay = 10;
        foreach ($notyetHero as $hero) {
            $message = "⏰ Halo {$hero->name}!\n\n"
                . "Kami dari *Berbagi Bites Jogja* ingin mengingatkan kembali untuk mengambil makanan di:\n"
                . "📍 {$activeDonation->location}\n"
                . "{$activeDonation->maps}\n\n"
                . "Batas pengambilan sampai pukul {$jam} yaa 🙏\n\n"
                . "Mohon segera hadir agar makanan tidak terbuang dan bisa kamu manfaatkan 🌱💚\n\n"
                . "⚠️ Catatan:\n"
                . "- Jika tidak bisa mengambil, mohon konfirmasi 🙏\n"
                . "- Makanan yang tidak diambil hingga waktu yang ditentukan akan dialihkan ke Food Heroes lain\n\n"
                . "_Pesan ini dikirim otomatis oleh bot BBJ 🤖_";
            $phone = $hero->phone;
            dispatch(function () use ($phone, $message) {
                $this->send($phone, $message, AppConfiguration::useWhatsapp());
            })->delay(now()->addSeconds($delay));
            $delay += 10;
        }
        $message = 'Akan mengirimkan kepada ' . $notyetHero->count() . ' hero secara bertahap';
        $this->send($sender, $message, 'SECOND');
        $message = 'Berhasil mengirimkan kepada ' . $notyetHero->count() . ' hero';
        dispatch(function () use ($sender, $message) {
            $this->send($sender, $message, 'SECOND');
        })->delay(now()->addSeconds($delay));
    }

    protected function gemini($sender, $text)
    {
        return true;
        $gemini =  new ChatController();
        $response = $gemini->chat($text);
        $spam = str_starts_with($response[0], 'Maaf');
        if (!$spam) {
            $this->send($sender, $response[0], 'SECOND');
        }
    }

    protected function sendNotification(Donation $donation, string $hour)
    {
        $delay = 10;
        $notif = Notify::all();
        $date = Carbon::parse($donation->take)->locale('id');
        $formatted = $date->translatedFormat('l, d F Y');
        foreach ($notif as $hero) {
            $phone = $hero->phone;
                $message = "*📢 Ada donasi aktif BBJ dari {$donation->sponsor->name}!*\n\n🦸‍♂{$donation->quota} orang\n🗓$formatted\n🕧" . $hour . "WIB\n📍{$donation->location}\n{$donation->maps}";

            if (!empty($donation->message)) {
                $message .= "\n\n📝 *Pesan Khusus:* {$donation->message}";
            }

            $message .= "\n\n🦸🏻 Ayo, jadi Food Heroes dan bantu BBJ menyelamatkan bumi dengan daftar di sini https://berbagibitesjogja.com/form" . "\n\nTerima kasih 🙏\n\n_pesan ini dikirim otomatis oleh bot_";
            dispatch(function () use ($phone, $message) {
                $this->send($phone, $message, AppConfiguration::useWhatsapp());
            })->delay(now()->addSeconds($delay));
            $delay += 10;
        }
        Notify::truncate();
    }
}
