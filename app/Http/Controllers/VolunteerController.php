<?php

namespace App\Http\Controllers;

use App\Models\Donation\Donation;
use App\Models\Donation\Food;
use App\Models\FormJob;
use App\Models\Heroes\Hero;
use App\Models\Heroes\University;
use App\Models\Volunteer\Availability;
use App\Models\Volunteer\Division;
use App\Models\Volunteer\Notify;
use App\Models\Volunteer\Precence;
use App\Models\Volunteer\User;
use App\Traits\SendWhatsapp;
use App\Traits\TwoWayEncryption;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class VolunteerController extends Controller
{
    use SendWhatsapp, TwoWayEncryption;
    public function home()
    {
        if (! Auth::user()) {
            return redirect()->action([HeroController::class, 'create']);
        }

        $currentDate = Carbon::now();
        $fourMonthsAgo = Carbon::parse(Carbon::now()->format('Y-m'))->subMonths(9);

        $donations = Donation::where('charity', 0)->whereBetween('take', [$fourMonthsAgo, $currentDate])->with(['foods', 'heroes'])->get();
        $groupedData = $donations->groupBy(function ($donation) {
            return Carbon::parse($donation->take)->format('Y-m');
        });
        $lastData = [];
        foreach ($groupedData as $key => $item) {
            $hero_count = 0;
            $food_count = 0;

            foreach ($item as $data) {
                $hero_count += $data->heroes->sum('quantity');
                $food_count += $data->foods->where('expired', '0')->sum('weight') / 1000;
            }
            $lastData[] = [
                'bulan' => Carbon::parse($key)->format('F'),
                'heroes' => $hero_count,
                'foods' => $food_count,
            ];
        }
        $user = Auth::user();
        $donations = Donation::where('charity', 0)->get();
        $precence = Precence::where('status', 'active')->count();
        $foods = Food::whereIn('donation_id', $donations->pluck('id'))->where('expired', '0')->get();
        $heroes = Hero::all();
        return view('pages.volunteer.home', compact('user', 'donations', 'foods', 'heroes', 'lastData', 'precence'));
    }

    public function index()
    {
        if (Auth::user()->role == 'member') {
            return redirect()->route('volunteer.home');
        }
        $users = User::with(['attendances', 'division'])->get();

        return view('pages.volunteer.index', compact('users'));
    }

    public function create()
    {
        $divisions = Division::all();
        $universities = University::where('variant', 'student')->get();

        return view('pages.volunteer.create', compact('divisions', 'universities'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request['phone'] = str_replace('08', '628', $request->phone);
            $user = User::create($request->all());
            $id = $user->id;
            $data = [];
            for ($day = 1; $day <= 7; $day++) {
                for ($hour = 7; $hour <= 21; $hour++) {
                    $data[] = [
                        "user_id" => $id,
                        "day" => $day,
                        "hour" => $hour,
                        "minute" => 0,
                        "code" => $day . $hour . "0"
                    ];
                    $data[] = [
                        "user_id" => $id,
                        "day" => $day,
                        "hour" => $hour,
                        "minute" => 30,
                        "code" => $day . $hour . "5"
                    ];
                }
            }
            Availability::insert($data);
            $user->availabilities()->update(["created_at" => now(), "updated_at" => now()]);
            DB::commit();
            return redirect()->route('volunteer.index')->with('success', 'Berhasil menambahkan volunteer');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('volunteer.index')->with('error', $th->getMessage());
        }
    }

    public function show(User $volunteer)
    {
        $divisions = Division::all();

        return view('pages.volunteer.show', compact('volunteer', 'divisions'));
    }

    public function update(Request $request, User $volunteer)
    {
        try {
            $volunteer->update($request->all());

            return redirect()->action([VolunteerController::class, 'logout'])->with('success', 'Berhasil mengubah data user');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal mengubah data user');
        }
    }

    public function destroy(User $volunteer)
    {
        try {
            $volunteer->delete();

            return back()->with('success', 'Berhasil menghapus user');
        } catch (\Throwable $th) {
            return back()->with('error', 'Gagal menghapus user');
        }
    }

    public function login()
    {
        return redirect()->route('auth.google');
    }

    public function logout(Request $request)
    {
        $volunteer = User::find(Auth::user()->id);
        activity()
            ->causedBy($volunteer)
            ->performedOn($volunteer)
            ->createdAt(now())
            ->event('authentication')
            ->log('Logout');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('volunteer.home');
    }

    public function authenticate(Request $request)
    {
        $user = Socialite::driver('google')->user();
        $volunteer = User::where('email', $user->email)->first();
        if (session('phone')) {
            $phone = session('phone');
            session()->forget('phone');

            if (! str_ends_with($user->email, 'mail.ugm.ac.id')) {
                return redirect()->route('volunteer.home')->with('error', 'Email tidak valid');
            }
            try {

                $now = now();
                $code = $this->encryptData("{$user->name},{$user->email},{$user->phone},{$now}");
                $text = rawurlencode(
                    "> Verifikasi\n\n" .
                        "Halo Minje! 👋\n" .
                        "Aku ingin mengaktifkan fitur *Dapatkan Notifikasi* untuk info donasi BBJ.\n\n" .
                        "Kode Verifikasi: _{$code}_"
                );
                return redirect("https://wa.me/6285117773642?text={$text}");
            } catch (\Throwable $th) {
                return redirect()->route('volunteer.home')->with('error', 'Anda sudah terdaftar');
            }
        }
        if (! $volunteer) {
            return redirect()->route('volunteer.home')->with('error', 'Anda tidak terdaftar');
        }
        if (session('job')) {
            $entry = session('entry');
            $jobId = session('job');
            session()->forget(['entry', 'job']);


            $apply = FormJob::whereId($entry)->first();

            if (!$apply) {
                return redirect()->away('https://war.berbagibitesjogja.com');
            }

            $data = collect($apply['data']);
            $jobs = collect($data->get('jobs'));
            $jobItemIndex = $jobs->search(fn($j) => $j['id'] == $jobId);

            if ($jobItemIndex === false) {
                return redirect()->away('https://war.berbagibitesjogja.com');
            }

            $jobItem = $jobs[$jobItemIndex];

            // Check division restriction
            if (!empty($jobItem['division']) && $jobItem['division'] != $volunteer->division->name) {
                return redirect()->away('https://war.berbagibitesjogja.com');
            }

            // Work with persons
            $persons = collect($jobItem['persons']);
            $alreadyExists = str_contains(json_encode($jobs), $volunteer->phone);

            if (!$alreadyExists && $persons->count() < $jobItem['need']) {
                // Add the volunteer
                $persons->push([
                    'name' => $volunteer->name,
                    'phone' => $volunteer->phone,
                ]);

                // Update persons in jobItem
                $jobItem['persons'] = $persons;

                // Update job in jobs list
                $jobs[$jobItemIndex] = $jobItem;

                // Save back to data
                $data['jobs'] = $jobs;

                // Save to DB
                $apply->data = $data;
                $apply->save();
                $message = $volunteer->name
                    . " berhasil mendaftar"
                    . "\n\nDonatur: " . $data->get('sponsor')
                    . "\nPenerima: " . $data->get('receiver')
                    . "\nTanggal: " . $data->get('date')
                    . "\nTugas: " . $jobItem['name'];
                $messageV = "Kamu berhasil mendaftar"
                    . "\n\nDonatur: " . $data->get('sponsor')
                    . "\nPenerima: " . $data->get('receiver')
                    . "\nTanggal: " . $data->get('date')
                    . "\nTugas: " . $jobItem['name'];
                dispatch(function () use ($volunteer, $message, $messageV) {
                    $this->send('120363330280278639@g.us', $message);
                    $this->send($volunteer->phone, $messageV);
                });
            }
            return redirect()->away('https://war.berbagibitesjogja.com');
        }
        if (session('unjob')) {
            $entry = session('entry');
            $jobId = session('unjob');
            session()->forget(['unjob', 'entry']);


            $apply = FormJob::whereId($entry)->first();

            if (!$apply) {
                return redirect()->away('https://war.berbagibitesjogja.com');
            }

            $data = collect($apply['data']);
            $jobs = collect($data->get('jobs'));
            $jobItemIndex = $jobs->search(fn($j) => $j['id'] == $jobId);

            if ($jobItemIndex === false) {
                return redirect()->away('https://war.berbagibitesjogja.com');
            }

            $jobItem = $jobs[$jobItemIndex];

            // Work with persons
            $persons = collect($jobItem['persons']);

            // Remove the person (unapply)
            $updatedPersons = $persons->reject(function ($person) use ($volunteer) {
                return $person['phone'] === $volunteer->phone;
            })->values(); // reindex the array

            // Save only if there's a change
            if ($updatedPersons->count() !== $persons->count()) {
                $jobItem['persons'] = $updatedPersons;
                $jobs[$jobItemIndex] = $jobItem;
                $data['jobs'] = $jobs;
                $apply->data = $data;
                $apply->save();
            }
            $message = $volunteer->name
                . " membatalkan ikut"
                . "\n\nDonatur: " . $data->get('sponsor')
                . "\nPenerima: " . $data->get('receiver')
                . "\nTanggal: " . $data->get('date')
                . "\nTugas: " . $jobItem['name'];
            $messageV = "Kamu membatalkan"
                . "\n\nDonatur: " . $data->get('sponsor')
                . "\nPenerima: " . $data->get('receiver')
                . "\nTanggal: " . $data->get('date')
                . "\nTugas: " . $jobItem['name'];
            dispatch(function () use ($volunteer, $message, $messageV) {
                $this->send('120363330280278639@g.us', $message);
                $this->send($volunteer->phone, $messageV);
            });
            return redirect()->away('https://war.berbagibitesjogja.com');
        }



        $volunteer->name = $user->name;
        $volunteer->photo = $user->avatar;
        $volunteer->save();
        Auth::login($volunteer);
        activity()
            ->causedBy($volunteer)
            ->performedOn($volunteer)
            ->createdAt(now())
            ->event('authentication')
            ->log('Login');

        return redirect()->intended('/')->with('success', 'Berhasil login');
    }

    public function applyJob(Request $request, string $entry, string $job)
    {
        session()->put('job', $job);
        session()->put('entry', $entry);
        return redirect()->route('auth.google');
    }
    public function unapplyJob(Request $request, string $entry, string $job)
    {
        session()->put('unjob', $job);
        session()->put('entry', $entry);
        return redirect()->route('auth.google');
    }
}
