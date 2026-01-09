<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHeroRequest;
use App\Models\Donation\Donation;
use App\Models\Donation\Food;
use App\Models\Heroes\Backup;
use App\Models\Heroes\Hero;
use App\Models\Heroes\University;
use App\Models\Volunteer\Faculty;
use App\Models\Volunteer\User;
use App\Traits\DashboardAnalytics;
use App\Traits\TwoWayEncryption;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class HeroController extends Controller implements HasMiddleware
{
    use TwoWayEncryption, DashboardAnalytics;
    
    public static function middleware(): array
    {
        return [
            new Middleware('guest', only: ['create', 'cancel']),
            new Middleware('auth', only: ['index', 'backups', 'contributor', 'show', 'update', 'restore', 'destroy', 'faculty']),
        ];
    }

    public function index()
    {
        $heroes = Hero::with(['faculty', 'donation'])->paginate(100);
        $donations = Donation::where('status', 'aktif')->get();
        $faculties = Faculty::all();

        return view('pages.hero.index', compact('donations', 'heroes', 'faculties'));
    }

    public function backups()
    {
        $backups = Backup::orderBy('updated_at', 'desc')->paginate(30);

        return view('pages.hero.backups', compact('backups'));
    }

    public function getJsonData($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);

        return $data;
    }

    public function create()
    {
        $donations = Donation::where('status', 'aktif')->get();
        $donations_sum = Donation::all()->count();
        $foods = round(Food::all()->sum('weight') / 1000);
        $heroes = Hero::all()->sum('quantity');

        $lastData = $this->getDonationAnalytics(5); // HeroController used 5 months back

        return view('pages.form', compact('donations', 'donations_sum', 'foods', 'heroes', 'lastData'));
    }

    public function contributor(Request $request)
    {
        try {
            $donation = Donation::find($request['donation_id']);
            if ($donation->remain < $request['quantity']) {
                return back();
            }
            Hero::create([
                'name' => $request['name'],
                'faculty_id' => $request['faculty_id'],
                'donation_id' => $request['donation_id'],
                'quantity' => $request['quantity'],
                'status' => 'sudah',
            ]);
            // Remain update handled by Observer

            $donation->save(); 
            
            return back()->with('success', 'Berhasil menambahkan kontributor');
        } catch (\Throwable $th) {
            return back()->with('error', 'Gagal menambahkan kontributor');
        }
    }

    public function store(StoreHeroRequest $request)
    {
        // Validation handled by FormRequest
        
        $code = $this->generate();
        
        Hero::create([
            'name' => $request['name'],
            'phone' => '62' . $request['phone'], // Prefix added
            'faculty_id' => $request['faculty'],
            'donation_id' => $request['donation'],
            'code' => $code,
            'status' => 'belum',
            // Quantity defaults to 1? Not specified in original code, so implicit in DB or null.
            // Observer handles decrement using `quantity ?? 1`.
        ]);
        
        // Remain update handled by Observer.
        
        $donation = Donation::find($request['donation']); // Reload to get fresh state? Or just trust observer.
        session(['donation' => $donation->id]);
        session(['code' => $this->encryptData($request['name'])]);

        return back()->with('success', 'Berhasil mendaftar');
    }

    public function show(Hero $hero)
    {
        return view('pages.hero.show');
    }

    public function update(Request $request, Hero $hero)
    {
        $hero->status = 'sudah';
        $hero->save();

        return back()->with('success', 'Hero telah datang');
    }

    public function destroy(Hero $hero)
    {
        Backup::create([
            'name' => $hero->name,
            'phone' => $hero->phone,
            'faculty_id' => $hero->faculty_id,
            'donation_id' => $hero->donation_id,
            'code' => $hero->code,
        ]);
        
        $hero->delete();

        return back()->with('success', 'Hero batal mengambil');
    }

    public function generate()
    {
        $characters = '1234567890';
        $charactersLength = strlen($characters);
        $uniqueString = '';

        for ($i = 0; $i < 6; $i++) {
            $index = rand(0, $charactersLength - 1);
            $uniqueString .= $characters[$index];
        }

        return $uniqueString;
    }

    public function faculty(Faculty $faculty)
    {
        $heroes = $faculty->heroes()->paginate(50);

        return view('pages.hero.faculty', compact('heroes'));
    }

    public function cancel(Request $request)
    {
        $hero = Hero::where('donation_id', session('donation'))->where('code', session('code'))->first();
        // Observer 'deleted' will increment donation remain.
        // Original code: $donation->remain + 1, save, delete.
        // Observer does exactly that.
        
        if ($hero) {
             $hero->delete();
        }
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Terimakasih telah membatalkan');
    }

    public function getFaculties(University $university)
    {
        return response()->json($university->fakultas);
    }
}
