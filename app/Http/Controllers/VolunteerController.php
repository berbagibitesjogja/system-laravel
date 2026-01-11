<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVolunteerRequest;
use App\Models\Donation\Donation;
use App\Models\Donation\Food;
use App\Models\Donation\Sponsor;
use App\Models\Heroes\Hero;
use App\Models\Heroes\University;
use App\Models\Volunteer\Availability;
use App\Models\Volunteer\Division;
use App\Models\Volunteer\Precence;
use App\Models\Volunteer\User;
use App\Traits\DashboardAnalytics;
use App\Traits\JobApplicationHandler;
use App\Traits\SendWhatsapp;
use App\Traits\TwoWayEncryption;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class VolunteerController extends Controller
{
    use SendWhatsapp, TwoWayEncryption, DashboardAnalytics, JobApplicationHandler;

    public function home()
    {
        if (! Auth::user()) {
            return redirect()->action([HeroController::class, 'create']);
        }

        $lastData = $this->getDonationAnalytics(11);

        $user = Auth::user();
        $donations = Donation::where('charity', 0)->get();
        $precence = Precence::where('status', 'active')->count();
        $foods = Food::whereIn('donation_id', $donations->pluck('id'))->where('expired', '0')->get();
        $heroes = Hero::all();
        $activities = \Spatie\Activitylog\Models\Activity::with(['causer', 'subject'])
            ->latest()
            ->limit(10)
            ->get();

        return view('pages.volunteer.home', compact('user', 'donations', 'foods', 'heroes', 'lastData', 'precence', 'activities'));
    }

    public function hallOfFame()
    {
        $topVolunteers = User::where('role', 'member')
            ->withCount('attendances')
            ->having('attendances_count', '>', 0)
            ->orderBy('attendances_count', 'desc')
            ->limit(10)
            ->get();

        $topSponsors = Sponsor::withCount('donation')
            ->having('donation_count', '>', 0)
            ->orderBy('donation_count', 'desc')
            ->limit(5)
            ->get();

        return view('pages.volunteer.hall-of-fame', compact('topVolunteers', 'topSponsors'));
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

    public function store(StoreVolunteerRequest $request)
    {
        try {
            DB::beginTransaction();
            // User creation triggers Observer which handles availability generation
            $user = User::create($request->all());
            
            DB::commit();
            return redirect()->route('volunteer.index')->with('success', 'Berhasil menambahkan volunteer');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('volunteer.index')->with('error', $th->getMessage());
        }
    }

    public function profile()
    {
        $volunteer = Auth::user();
        $divisions = Division::all();
        $activities = \Spatie\Activitylog\Models\Activity::causedBy($volunteer)
            ->latest()
            ->limit(5)
            ->get();
        $myAttendance = $volunteer->attendances()->count();
        $rank = User::where('role', 'member')
            ->withCount('attendances')
            ->having('attendances_count', '>', $myAttendance)
            ->count() + 1;

        return view('pages.volunteer.show', compact('volunteer', 'divisions', 'activities', 'rank'));
    }
    public function show(User $volunteer)
    {
        if (Auth::user()->role == 'member') {
            return redirect()->route('volunteer.home');
        }
        $divisions = Division::all();
        $activities = \Spatie\Activitylog\Models\Activity::causedBy($volunteer)
            ->latest()
            ->limit(5)
            ->get();

        // Calculate Rank
        $myAttendance = $volunteer->attendances()->count();
        $rank = User::where('role', 'member')
            ->withCount('attendances')
            ->having('attendances_count', '>', $myAttendance)
            ->count() + 1;

        return view('pages.volunteer.show', compact('volunteer', 'divisions', 'activities', 'rank'));
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
                $code = $this->encryptData("{$user->name},{$user->email},{$phone},{$now}");
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
        $volunteer->name = $user->name;
        $volunteer->photo = $user->avatar;
        $volunteer->save();
        
        if (session('job')) {
            $entry = session('entry');
            $jobId = session('job');
            session()->forget(['entry', 'job']);
            return $this->handleJobApplication($volunteer, $entry, $jobId);
        }
        if (session('unjob')) {
            $entry = session('entry');
            $jobId = session('unjob');
            session()->forget(['unjob', 'entry']);
            return $this->handleJobUnapplication($volunteer, $entry, $jobId);
        }
        
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
