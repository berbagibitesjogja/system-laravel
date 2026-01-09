<?php

namespace App\Traits;

use App\Models\Donation\Donation;
use App\Models\Donation\Sponsor;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Http\Controllers\BotController;

trait ReportGenerator
{
    public function generateSingleReport(Donation $donation)
    {
        try {
            $sponsor = $donation->sponsor;
            $templateProcessor = new TemplateProcessor(public_path() . '/templates/default.docx');
            $donationDate = Carbon::parse($donation->take)->isoFormat('dddd, D MMMM Y');
            $templateProcessor->setValue('year', Carbon::now()->isoFormat('Y'));
            $templateProcessor->setValue('createdDate', Carbon::now()->isoFormat('D-M-Y'));
            $templateProcessor->setValue('sponsorName', $sponsor->name);
            $templateProcessor->setValue('donationDate', $donationDate);
            $templateProcessor->setValue('foodTotal', round($donation->foods->sum('weight') / 100) / 10);
            
            if ($donation->partner_id != null) {
                $heroes = $donation->partner->heroes;
            } else {
                $heroes = $donation->heroes;
            }
            
            $templateProcessor->setValue('totalHeroes', $heroes->sum('quantity'));
            $templateProcessor->cloneRow('heroesName', $heroes->count());
            
            for ($i = 1; $i < $heroes->count() + 1; $i++) {
                $templateProcessor->setValue("heroesName#$i", $heroes[$i - 1]->name);
                if ($heroes[$i - 1]->quantity > 1) {
                    $templateProcessor->setValue("heroesFaculty#$i", $heroes[$i - 1]->quantity . " Orang");
                } else {
                    $templateProcessor->setValue("heroesFaculty#$i", $heroes[$i - 1]->faculty->name . " (" . $heroes[$i - 1]->faculty->university->name . ")");
                }
            }

            $foods = $donation->foods;
            $templateProcessor->cloneRow('foodName', $foods->count());
            for ($i = 1; $i < $foods->count() + 1; $i++) {
                $templateProcessor->setValue("foodName#$i", $foods[$i - 1]->name . ($foods[$i - 1]->expired ? '(basi)' : ''));
                $templateProcessor->setValue("foodWeight#$i", round($foods[$i - 1]->weight / 100) / 10);
            }
            
            $uniq = substr(bin2hex(random_bytes(2)), 0, 3);
            $filename = storage_path() . '/app/public/reports/' . 'Laporan ' . $sponsor->name . ' Tanggal ' . Carbon::parse($donation->take)->isoFormat('D MMMM Y') . " $uniq";
            $templateProcessor->saveAs($filename . '.docx');
            
            return basename($filename . '.docx');
        } catch (\Throwable $th) {
            BotController::sendForPublic('120363399651067268@g.us', $th->getMessage(), 'SECOND');
        }
    }

    public function generateMonthlyReportData($sponsor, $bulan, $year = null)
    {
        $year ??= now()->year;
        $months = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $month = $months[$bulan];
        $donations = $sponsor->donation()->with(['heroes', 'foods'])->whereMonth('take', $bulan)->whereYear('take', $year)->get();

        $templateProcessor = new TemplateProcessor(public_path() . '/templates/monthly.docx');
        $templateProcessor->setValue('monthYear', $month . " " . $year);
        $templateProcessor->setValue('sponsorName', $sponsor->name);
        $templateProcessor->setValue('createdDate', Carbon::now()->isoFormat('D-M-Y'));

        $templateProcessor->cloneBlock('block_donation', $donations->count(), true, true);

        $heroesTotal = 0;
        $foodsTotal = 0;

        foreach ($donations as $index => $donation) {
            $i = $index + 1; // block index

            $donationDate = Carbon::parse($donation->take)->isoFormat('dddd, D MMMM Y');
            $templateProcessor->setValue("donationDate#{$i}", $donationDate);

            // HEROES
            if ($donation->partner_id != null) {
                $heroes = $donation->partner->heroes;
            } else {
                $heroes = $donation->heroes;
            }
            $templateProcessor->setValue("totalHeroes#{$i}", $heroes->sum('quantity'));

            // clone row heroesName dalam setiap block
            $templateProcessor->cloneRow("heroesName#{$i}", $heroes->count());
            foreach ($heroes as $hIndex => $hero) {
                $h = $hIndex + 1;
                $templateProcessor->setValue("heroesName#{$i}#{$h}", $hero->name);
                if ($hero->quantity > 1) {
                    $templateProcessor->setValue("heroesFaculty#{$i}#{$h}", $hero->quantity . " Orang");
                } else {
                    $templateProcessor->setValue("heroesFaculty#{$i}#{$h}", $hero->faculty->name . " (" . $hero->faculty->university->name . ")");
                }
            }

            // FOODS
            $foods = $donation->foods;
            $heroesTotal += $heroes->sum('quantity');
            $foodsTotal += $foods->sum('weight');
            $templateProcessor->setValue("foodTotal#{$i}", round($foods->sum('weight') / 100) / 10);
            $templateProcessor->cloneRow("foodName#{$i}", $foods->count());
            foreach ($foods as $fIndex => $food) {
                $f = $fIndex + 1;
                $templateProcessor->setValue("foodName#{$i}#{$f}", $food->name . ($food->expired ? '(basi)' : ''));
                $templateProcessor->setValue("foodWeight#{$i}#{$f}", round($food->weight / 100) / 10);
            }
        }
        $templateProcessor->setValue("foodsTotal", round($foodsTotal / 100) / 10);
        $templateProcessor->setValue("heroesTotal", $heroesTotal);
        $templateProcessor->setValue("year", now()->year);
        $uniq = substr(bin2hex(random_bytes(2)), 0, 3);
        $filename = storage_path() . '/app/public/monthly/' . 'Laporan ' . $sponsor->name . ' Bulan ' . $month . " $year $uniq";
        $templateProcessor->saveAs($filename . '.docx');
        
        return basename($filename . '.docx');
    }

    public function generateBulkReport(Sponsor $sponsor, $startDate, $endDate, $receiverData, $roleData)
    {
         $donations = $sponsor->donation()->with(['foods', 'heroes'])->whereBetween('take', [$startDate, $endDate])->get();
        foreach ($donations as $donation) {
            $templateProcessor = new TemplateProcessor('templates/default.docx');
            $donationDate = Carbon::parse($donation->take)->isoFormat('dddd, D MMMM Y');
            $templateProcessor->setValue('year', Carbon::now()->isoFormat('Y'));
            $templateProcessor->setValue('createdDate', Carbon::now()->isoFormat('D-M-Y'));
            $templateProcessor->setValue('sponsorName', $sponsor->name);
            $templateProcessor->setValue('donationDate', $donationDate);
            $templateProcessor->setValue('foodTotal', round($donation->foods->sum('weight') / 100) / 10);
            
            if ($donation->partner_id != null) {
                $heroes = $donation->partner->heroes;
            } else {
                $heroes = $donation->heroes;
            }
            
            $templateProcessor->setValue('totalHeroes', $heroes->sum('quantity'));
            $templateProcessor->cloneRow('heroesName', $heroes->count());
            
            for ($i = 1; $i < $heroes->count() + 1; $i++) {
                $templateProcessor->setValue("heroesName#$i", $heroes[$i - 1]->name);
                if ($heroes[$i - 1]->quantity > 1) {
                    $templateProcessor->setValue("heroesFaculty#$i", $heroes[$i - 1]->quantity . " Orang");
                } else {
                    $templateProcessor->setValue("heroesFaculty#$i", $heroes[$i - 1]->faculty->name . " (" . $heroes[$i - 1]->faculty->university->name . ")");
                }
            }

            $foods = $donation->foods;
            $templateProcessor->cloneRow('foodName', $foods->count());
            for ($i = 1; $i < $foods->count() + 1; $i++) {
                $templateProcessor->setValue("foodName#$i", $foods[$i - 1]->name);
                $templateProcessor->setValue("foodWeight#$i", round($foods[$i - 1]->weight / 100) / 10);
            }
            $filename = storage_path() . '/app/public/reports/' . 'Laporan ' . $sponsor->name . ' Tanggal ' . Carbon::parse($donation->take)->isoFormat('D MMMM Y');
            $templateProcessor->saveAs($filename . '.docx');
        }
    }

    public function cleanupReports(string $path)
    {
        $files = File::allFiles($path);
        foreach ($files as $file) {
            unlink($path . '/' . $file->getFilename());
        }
    }
}
