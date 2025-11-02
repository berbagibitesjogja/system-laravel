<?php

namespace App\Console\Commands;

use App\Http\Controllers\BotController;
use App\Http\Controllers\ReportController;
use App\Models\Donation\Donation;
use App\Traits\SendWhatsapp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DailyReportDocument extends Command
{
    use SendWhatsapp;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:daily';

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
        $donation = Donation::where('reported', null)->where('status', 'selesai')->first();
        if (!$donation) {
            return true;
        }
        try {
            $fileName = ReportController::createReport($donation);
            $relativePath = storage_path('app/public/reports/') . $fileName;
            Storage::disk('google')->put('foods/Arsip Berita Acara/' . $donation->sponsor->name . "/" . $fileName, File::get($relativePath));
            $fileSize = filesize($relativePath);
            Storage::disk('public')->delete('reports/' . $fileName);
            $donation->reported = "sudah";
            $donation->save();
            $this->send('120363315008311976@g.us', "Berhasil membuat laporan\n\n" . "Nama File : " . $fileName . "\nUkuran file : " . round($fileSize / 1024) . " kb", 'SECOND');
        } catch (\Throwable $th) {
            $this->send('120363399651067268@g.us', "[ERROR]\n\nNeed GDRIVE Refresh Token", 'SECOND');
        }
    }
}
