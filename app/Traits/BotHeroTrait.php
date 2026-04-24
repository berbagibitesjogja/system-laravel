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

    protected function r(array $arr)
    {
        return $arr[array_rand($arr)];
    }

    protected function jitter($min = 10, $max = 60)
    {
        return rand($min, $max);
    }

    protected function greeting($name)
    {
        return $this->r([
            "Halo",
            "Halo pahlawan",
            "Hai",
            "Hi",
            "Assalamualaikum",
            "Hey"
        ]) . " {$name} " . $this->r(["🌱", "✨", "💚", "🙌"]);
    }

    protected function noise()
    {
        return $this->r([
            "",
            "\n\n💚 BBJ",
            "\n\n_Terima kasih_ 🙏",
            "\n\nSalam BBJ🌿",
            "\n\n🤖"
        ]);
    }

    protected function verifyFoodHeroes($sender, $text)
    {
        preg_match('/_(.*?)_/', $text, $match);
        $code = $match[1] ?? null;
        $name = $this->decryptData($code);

        $activeDonation = Donation::whereStatus('aktif')->pluck('id');
        $hero = Hero::whereName($name)->whereIn('donation_id', $activeDonation)->first();

        if (!$hero) {
            return $this->send($sender, 'Maaf signature key tidak valid');
        }

        $message =
            "🎉 {$this->greeting($hero->name)}\n\n" .
            "Terima kasih sudah jadi Food Hero 🌱\n" .
            "Kode kamu: *{$hero->code}*\n\n" .
            "📅 " . Carbon::parse($hero->donation->take)->format('d F Y') . "\n" .
            "⏰ " . str_pad($hero->donation->hour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($hero->donation->minute, 2, '0', STR_PAD_LEFT) . "\n" .
            "📍 {$hero->donation->location}\n" .
            "🗺 {$hero->donation->maps}\n\n" .
            ($hero->donation->message ? "📝 *Pesan Khusus:* {$hero->donation->message}\n\n" : "") .
            "Harap tunjukkan kode ini saat pengambilan ya. Semoga bermanfaat dan tidak terbuang 🌿💚" .
            $this->noise();

        $this->send($sender, $message);
    }

    protected function verifyNotify($sender, $text)
    {
        preg_match('/_(.*?)_/', $text, $match);
        $code = $match[1] ?? null;

        $data = explode(',', $this->decryptData($code));

        [$name, $email, $phone] = $data;

        try {
            Notify::create(compact('name', 'email', 'phone'));

            $this->send(
                $sender,
                "{$this->greeting($name)}\n\nNotifikasi aktif 🌱\nKamu akan dapat info donasi otomatis." .
                $this->noise()
            );
        } catch (\Throwable $th) {
            $this->send(
                $sender,
                "Hai {$name}, verifikasi berhasil! 🎉\n\n" .
                    "Fitur notifikasi BBJ kamu sudah aktif. Kamu akan otomatis menerima info donasi ketika tersedia 🌱\n\n" .
                    "Catatan: Notifikasi ini berlaku *satu kali*. Setelah menerima notifikasi, kamu perlu daftar lagi jika ingin mendapatkan pemberitahuan berikutnya 😊" .
                    $this->noise()
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

        $allHero = Hero::where('donation_id', $activeDonation->id)
            ->get(['name', 'code'])
            ->shuffle();

        $message = "📋 Daftar Hero Hari Ini\n"
            . "_Jumlah: {$allHero->count()}_\n";

        foreach ($allHero as $hero) {
            $message .= "\n{$hero->name}\n{$hero->code}\n";
        }

        $this->send($sender, $message, 'SECOND');
    }

    protected function getAllNotYetHero($sender)
    {
        $activeDonation = Donation::where('status', 'aktif')->first();

        $notyetHero = Hero::where('donation_id', $activeDonation->id)
            ->where('status', 'belum')
            ->get(['name', 'code'])
            ->shuffle();

        $message = "⏳ Belum Diambil\n"
            . "_Jumlah: {$notyetHero->count()}_\n";

        foreach ($notyetHero as $hero) {
            $message .= "\n{$hero->name}\n{$hero->code}\n";
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

        $heroes = Hero::where('donation_id', $activeDonation->id)
            ->get(['name', 'phone'])
            ->shuffle();

        $delay = 10;

        foreach ($heroes as $hero) {

            $templates = [
                "{$this->greeting($hero->name)}\n\n🍽 Makanan sudah siap diambil\n",
                "📢 {$hero->name}, info BBJ!\n\n📍 {$activeDonation->location}\nJangan lupa ambil ya 🌱",
                "{$this->greeting($hero->name)}\n\nSurplus food sudah tersedia ✨\n📍 {$activeDonation->location}"
            ];

            $message = "Ada kabar baik nih ✨ Surplus food dari *Berbagi Bites Jogja* sudah bisa diambil:\n\n"
                . "⏰ *Waktu:* " . str_pad($activeDonation->hour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($activeDonation->minute, 2, '0', STR_PAD_LEFT) . "\n"
                . "📍 *Lokasi:* {$activeDonation->location}\n"
                . "{$activeDonation->maps}\n\n"
                . "✅ Jangan lupa datang ya, semoga bermanfaat dan jangan sampai terbuang 🌱💚\n\n"
                . "⚠️ Catatan:\n"
                . "- Jika tidak bisa mengambil, mohon konfirmasi 🙏\n"
                . "- Makanan yang tidak diambil hingga waktu yang ditentukan akan dialihkan ke Food Heroes lain\n\n";

            $message = $this->r($templates) . $this->noise();

            dispatch(function () use ($hero, $message) {
                $this->send($hero->phone, $message, AppConfiguration::useWhatsapp());
            })->delay(now()->addSeconds($delay));

            $delay += $this->jitter(10, 40);
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

        $notyetHero = Hero::where('donation_id', $activeDonation->id)
            ->where('status', 'belum')
            ->get(['name', 'phone'])
            ->shuffle();

        $jam = str_replace('@BOT ingatkan hero yang belum ', '', $jam);

        $delay = 5;

        foreach ($notyetHero as $hero) {

            $message =
                "{$this->greeting($hero->name)}\n\n"
                . "Kami dari *Berbagi Bites Jogja* ingin mengingatkan kembali untuk mengambil makanan di:\n"
                . "📍 {$activeDonation->location}\n"
                . "{$activeDonation->maps}\n\n"
                . "Batas pengambilan sampai pukul {$jam} yaa 🙏\n\n"
                . "Mohon segera hadir agar makanan tidak terbuang dan bisa kamu manfaatkan 🌱💚\n\n"
                . "⚠️ Catatan:\n"
                . "- Jika tidak bisa mengambil, mohon konfirmasi 🙏\n"
                . "- Makanan yang tidak diambil hingga waktu yang ditentukan akan dialihkan ke Food Heroes lain\n\n"
                . $this->noise();

            dispatch(function () use ($hero, $message) {
                $this->send($hero->phone, $message, AppConfiguration::useWhatsapp());
            })->delay(now()->addSeconds($delay));

            $delay += $this->jitter(5, 20);
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

        $gemini = new ChatController();
        $response = $gemini->chat($text);

        if (!str_starts_with($response[0], 'Maaf')) {
            $this->send($sender, $response[0], 'SECOND');
        }
    }

    protected function sendNotification(Donation $donation, string $hour)
    {
        $notif = Notify::all()->shuffle();

        $date = Carbon::parse($donation->take)->locale('id');
        $formatted = $date->translatedFormat('l, d F Y');

        $delay = 10;

        foreach ($notif as $hero) {

            $message =
                "📢 {$donation->sponsor->name}\n"
                . "{$donation->quota} orang\n"
                . "{$formatted}\n"
                . "{$hour} WIB\n"
                . "{$donation->location}\n"
                . "{$donation->maps}\n"
                . ($donation->message ? "\n\n📝 *Pesan Khusus:* {$donation->message}" : "")
                . "\n\n🦸🏻 Ayo, jadi Food Heroes dan bantu BBJ menyelamatkan bumi dengan daftar di sini https://berbagibitesjogja.com/form"
                . $this->noise();

            dispatch(function () use ($hero, $message) {
                $this->send($hero->phone, $message, AppConfiguration::useWhatsapp());
            })->delay(now()->addSeconds($delay));

            $delay += $this->jitter(10, 35);
        }

        Notify::truncate();
    }
}
