<?php

namespace App\Http\Controllers;

use App\Models\AppConfiguration;
use App\Models\Volunteer\Reimburse;
use App\Traits\BotVolunteerTrait;
use App\Traits\SendWhatsapp;
use Gemini\Data\Blob;
use Gemini\Enums\MimeType;
use Gemini;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReimburseController extends Controller
{
    use SendWhatsapp, BotVolunteerTrait;
    public function index()
    {
        if (Auth::user()->role != "super") {
            return back();
        }
        $reimburse = Reimburse::with('user')->latest()->get();
        return view('pages.reimburse.index', compact('reimburse'));
    }
    public function create()
    {
        $reimburse = Auth::user()->reimburses;
        return view('pages.reimburse.create', compact('reimburse'));
    }
    public function store(Request $request)
    {
        $request->validate([
            "file" => 'file|image',
            "method" => 'required|string',
            "target" => 'required|string',
        ]);
        $file = $request->file('file');
        $filePath = $file->getRealPath();

        try {
            $client = Gemini::client(config('gemini.api_key'));
            $result = $client->generativeModel("gemini-3-flash")
                ->generateContent(["Berikan saya jawaban berupa total harga yang ada pada gambar berikut. hanya dalam bentuk integer tanpa formatting. apabila gambar yang diterima bukan merupakan invoice maka hanya hasilkan 0 tanpa formatting", new Blob(
                    mimeType: MimeType::IMAGE_JPEG,  // or IMAGE_PNG
                    data: base64_encode(file_get_contents($filePath))
                )])
                ->text();
            if ($result != "0") {
                $path = $file->store('reimburse', 'public');
                $reimburse = Reimburse::create(["amount" => (int) $result, "user_id" => Auth::id(), "file" => $path, "method" => $request->method, "target" => $request->target]);
                $this->createReimburse(Auth::user(), $reimburse);
                return back()->with("success", "Reimbursement submitted!");
            }
        } catch (\Throwable $th) {
            logs()->info(json_encode($th));
            logs()->info(json_encode($th->getMessage()));
            BotController::sendForPublic('120363399651067268@g.us', "[ERROR] Reimburse Store\n" . $th->getMessage());
        }
        return back()->with("error", "Reimbursement failed!");
    }

    public function destroy(Reimburse $reimburse)
    {
        $this->send($reimburse->user->phone, "Reimburse ditolak", AppConfiguration::useWhatsapp());
        Storage::disk('public')->delete($reimburse->file);
        $reimburse->delete();
        return back()->with("success", "Reimbursement canceled!");
    }
    public function update(Reimburse $reimburse)
    {
        $am = "Rp " . number_format($reimburse->amount, 0, ',', '.');
        $this->send($reimburse->user->phone, "Reimburse sebesar {$am} telah diberikan", AppConfiguration::useWhatsapp());
        $reimburse->update(["done" => true]);
        return back()->with("success", "Reimbursement success!");
    }
}
