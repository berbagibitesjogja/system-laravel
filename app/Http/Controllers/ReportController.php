<?php

namespace App\Http\Controllers;

use App\Models\Donation\Donation;
use App\Models\Donation\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ReportGenerator;

class ReportController extends Controller
{
    use ReportGenerator;

    public function index()
    {
        $sponsors = Sponsor::all();
        return view('pages.report.index', compact('sponsors'));
    }

    public function downloadMonthly(string $code)
    {
        $record = DB::table('report_keys')->where('code', $code)->first();
        if (!$record) {
            return redirect('https://berbagibitesjogja.com');
        }
        $filePath = storage_path('app/public/monthly/' . $record->filename);
        return response()->download($filePath, $record->filename);
    }

    public function clean()
    {
        $path = storage_path() . '/app/public/reports/';
        $this->cleanupReports($path);
        return redirect()->route('report.index')->with('success', 'Berhasil menghapus semua file');
    }

    public function download(Request $request)
    {
        $sponsor = Sponsor::where('id', $request->sponsor_id)->first();
        $this->generateBulkReport($sponsor, $request->startDate, $request->endDate, $request->all(), $request->all());
        
        // Note: Logic in original 'download' was complex and specific (mixing creation and array retrieval).
        // For simplicity and safety while using traits, I've delegated the creation part. 
        // But original code had:
        // $volunteerName = $request["receiver-" . $donation->id]; 
        // which wasn't used in template replacement in the original code shown! 
        // Original code: $templateProcessor->setValue... only standard fields. 
        // So I can safely use the logic in generateBulkReport.
        
        $path = storage_path() . '/app/public/reports';
        $files = \Illuminate\Support\Facades\File::allFiles($path);
        $reportFiles = [];
        foreach ($files as $file) {
            $reportFiles[] = $file->getFilename();
        }
        return view('pages.report.download', compact('reportFiles'));
    }

    public function createReport(Donation $donation)
    {
        return $this->generateSingleReport($donation);
    }


    public function createMonthlyReport($sponsor, $bulan, $year = null)
    {
        return $this->generateMonthlyReportData($sponsor, $bulan, $year);
    }


    public function getDonations(Sponsor $sponsor, $start, $end)
    {
        $donations = $sponsor->donation()->with(['foods', 'heroes'])->whereBetween('take', [$start, $end])->get();
        $totalWeight = 0;
        $totalFood = 0;
        $totalHero = 0;
        $totalAction = $donations->count();
        foreach ($donations as $item) {
            $total = $item->foods->sum('weight');
            $item->foodWeight = $total;
            $totalWeight += $total;
            $total = $item->foods->count();
            $item->foodQuantity += $total;
            $totalFood += $total;
            $total = $item->heroes->sum('quantity');
            $item->heroQuantity += $total;
            $totalHero += $total;
        }
        return response()->json(compact('totalWeight', 'totalFood', 'totalHero', 'totalAction', 'donations'));
    }
}
