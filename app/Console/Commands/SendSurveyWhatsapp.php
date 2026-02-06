<?php

namespace App\Console\Commands;

use App\Jobs\SendSurveyWhatsappMessage;
use App\Traits\SendWhatsapp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendSurveyWhatsapp extends Command
{
    use SendWhatsapp;

    protected $signature = 'survey:send-wa';
    protected $description = 'Kirim pesan survey ke nomor unik dari heroes dan users (prefix 62)';

    public function handle()
    {
        $message = "‼️*BBJ BUTUH PARTISIPASI KAMU*‼️\n\n"
            . "Halo, Sobat BBJ ✨\n\n"
            . "Saat ini Ibu Marina Hardiyanti selaku kontributor program BBJ sedang melaksanakan studi Doctoral Program of Food Science, Hungarian University of Agriculture and Life Science, Budapest. Beliau membuat “Survey of nutrition, food safety, and sensory aspects in the implementation of the food donation program” yang bertujuan untuk mengetahui pengetahuan dan persepsi masyarakat penerima makanan dan relawan pengelola program donasi makanan tentang aspek yang berkaitan dengan gizi, keamanan pangan, dan sensori pada program donasi makanan.\n\n"
            . "Untuk mendukung program BBJ melalui penelitian beliau, MinJe mengharapkan bantuan dan kesediaan Sobat BBJ untuk berpartisipasi dalam pengisian survei ini nihh ✨\n\n"
            . "⏱️ Waktu pengisian sekitar 10-15 menit\n"
            . "📝 Seluruh jawaban bersifat anonim dan hanya digunakan untuk keperluan akademik.\n\n"
            . "🔗 Link survei untuk Sobat BBJ yang pernah/secara rutin menjadi penerima program donasi makanan:\n"
            . "http://ugm.id/SurveyFoodHeroes\n\n"
            . "🔗 Link survei untuk Sobat BBJ yang pernah menjadi relawan pada organisasi/program donasi makanan:\n"
            . "http://ugm.id/SurveyVolunteer\n\n"
            . "Sobat BBJ yang pernah menerima ataupun menjadi relawan dapat mengisi 2x pada form survei yang berbeda.\n\n"
            . "Terima kasih atas waktu dan  partisipasi Sobat BBJ! Mudah-mudahan urusan Sobat BBJ diberi kelancaran karena sudah membantu memudahkan jalan orang lain, dan semoga hasil survei ini nantinya dapat membawa manfaat bagi pengembangan ilmu pengetahuan✨";

        $heroPhones = DB::table('heroes')
            ->whereNotNull('phone')
            ->pluck('phone');

        $userPhones = DB::table('users')
            ->whereNotNull('phone')
            ->pluck('phone');

        $targets = $heroPhones
            ->merge($userPhones)
            ->map(fn($phone) => $this->normalizeIndonesianPhoneTo62($phone))
            ->filter()
            ->unique()
            ->values();

        if ($targets->isEmpty()) {
            $this->warn('Tidak ada nomor yang valid yang ditemukan.');
            return self::SUCCESS;
        }

        $this->info('Total target (unik, valid): ' . $targets->count());
        $delay = 10;
        foreach ($targets as $phone) {
            SendSurveyWhatsappMessage::dispatch($phone, $message)->delay(now()->addSeconds($delay));
            $this->line('Queued: ' . $phone . 'After : ' . $delay . ' sec');
            $delay += 10;
        }
        $this->send('120363399651067268@g.us', 'Akan mengirimkan broadcast kepada ' . $targets->count() . ' dengan jeda ' . $delay . ' detik');

        return self::SUCCESS;
    }

    private function normalizeIndonesianPhoneTo62($phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', (string) $phone);
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '62')) {
            $normalized = $digits;
        } elseif (str_starts_with($digits, '0')) {
            $normalized = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $normalized = '62' . $digits;
        } else {
            return null;
        }

        if (!preg_match('/^62\d+$/', $normalized)) {
            return null;
        }

        $len = strlen($normalized);
        if ($len < 10 || $len > 15) {
            return null;
        }

        return $normalized;
    }
}
