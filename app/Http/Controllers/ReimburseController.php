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
    private function isAuthorizedManager($user): bool
    {
        return $user->role === 'super'
            || in_array($user->division?->name, ['Bendahara', 'Operational Manager']);
    }

    public function index()
    {
        $user = Auth::user();
        if (!$this->isAuthorizedManager($user)) {
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
            'file' => 'required|file|image',
            'method' => 'required|string',
            'target' => 'required|string',
            'notes' => 'required|string',
        ]);
        $file = $request->file('file');
        $filePath = $file->getRealPath();

        try {
            $client = Gemini::client(config('gemini.api_key'));
            $result = $client
                ->generativeModel('models/gemini-2.5-flash')
                ->generateContent([
                    'Berikan saya jawaban berupa total harga yang ada pada gambar berikut. hanya dalam bentuk integer tanpa formatting. apabila gambar yang diterima bukan merupakan invoice maka hanya hasilkan 0 tanpa formatting',
                    new Blob(
                        mimeType: MimeType::IMAGE_JPEG, // or IMAGE_PNG
                        data: base64_encode(file_get_contents($filePath)),
                    ),
                ])
                ->text();
            if ($result != '0') {
                $path = $file->store('reimburse', 'public');
                $reimburse = Reimburse::create([
                    'amount' => (int) $result,
                    'user_id' => Auth::id(),
                    'file' => $path,
                    'method' => $request->method,
                    'target' => $request->target,
                    'notes' => $request->notes,
                ]);
                $this->createReimburse(Auth::user(), $reimburse);
                return back()->with('success', 'Reimbursement submitted!');
            }
        } catch (\Throwable $th) {
            logs()->info(json_encode($th));
            logs()->info(json_encode($th->getMessage()));
            BotController::sendForPublic('120363399651067268@g.us', "[ERROR] Reimburse Store\n" . $th->getMessage());
        }
        return back()->with('error', 'Reimbursement failed!');
    }

    public function destroy(Reimburse $reimburse)
    {
        if (!$this->isAuthorizedManager(Auth::user())) {
            return back()->with('error', 'Anda tidak memiliki akses untuk tindakan ini.');
        }

        try {
            $this->send($reimburse->user->phone, 'Reimburse ditolak');
        } catch (\Throwable $th) {
            logs()->error('Reimburse Destroy WA Error: ' . $th->getMessage());
        }

        if ($reimburse->file) {
            Storage::disk('public')->delete($reimburse->file);
        }
        if ($reimburse->payment) {
            Storage::disk('public')->delete($reimburse->payment);
        }

        $reimburse->delete();
        return back()->with('success', 'Reimbursement canceled!');
    }

    public function update(Request $request, Reimburse $reimburse)
    {
        if (!$this->isAuthorizedManager(Auth::user())) {
            return back()->with('error', 'Anda tidak memiliki akses untuk tindakan ini.');
        }

        if ($reimburse->done) {
            return back()->with('error', 'Reimburse ini sudah diselesaikan sebelumnya.');
        }

        $request->validate([
            'payment' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ], [
            'payment.required' => 'Bukti transfer wajib diunggah.',
            'payment.file' => 'Berkas bukti transfer tidak valid.',
            'payment.mimes' => 'Bukti transfer harus berupa gambar (JPG, PNG, WEBP) atau PDF.',
            'payment.max' => 'Ukuran bukti transfer maksimal 5MB.',
        ]);

        $path = $request->file('payment')->store('payment', 'public');

        if ($reimburse->payment) {
            Storage::disk('public')->delete($reimburse->payment);
        }

        $reimburse->update([
            'payment' => $path,
            'done' => true,
        ]);

        $am = 'Rp ' . number_format($reimburse->amount, 0, ',', '.');
        $paymentUrl = asset('storage/' . $path);
        try {
            $this->send(
                $reimburse->user->phone,
                "Reimburse sebesar {$am} telah diberikan.\nBukti transfer: {$paymentUrl}"
            );
        } catch (\Throwable $th) {
            logs()->error('Reimburse Update WA Error: ' . $th->getMessage());
        }

        return back()->with('success', 'Reimbursement berhasil dikonfirmasi!');
    }
}
