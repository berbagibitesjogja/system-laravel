<?php

namespace App\Http\Controllers;

use App\Models\FormJob;
use App\Models\Volunteer\Attendance;
use App\Models\Volunteer\Precence;
use App\Traits\SendWhatsapp;
use Ballen\Distical\Calculator;
use Ballen\Distical\Entities\LatLong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrecenceController extends Controller
{
    use SendWhatsapp;
    public function calculateDistance($startLat, $startLong, $endLat, $endLong)
    {
        $ipswich = new LatLong($startLat, $startLong);
        $london = new LatLong($endLat, $endLong);
        $distanceCalculator = new Calculator($ipswich, $london);
        $distance = $distanceCalculator->get();

        return round($distance->asKilometres() * 1000);
    }
    public function userAttendance(Request $request)
    {
        $precence = Precence::where('code', $request->precenceCode)->where('status', 'active')->get();
        if ($precence->count() == 0) {
            return response()->json(['message' => 'Presensi tidak ditemukan', 'data' => $request->all()], 404);
        }
        $precence = $precence[0];
        $user = Auth::user();
        $distance = $this->calculateDistance($request->precenceLat, $request->precenceLong, $request->userLat, $request->userLong);
        if ($distance > $precence->max_distance) {
            $this->failedAttendance($user, $precence);
            return response()->json(["message" => "Presensi tidak ditemukan", "data" => $request->all()], 404);
        }
        if (Attendance::where('user_id', $user->id)->where('precence_id', $precence->id)->get()->count() == 1) {
            return response()->json([$request->all(), $precence], 200);
        }
        try {
            Attendance::create([
                'user_id' => $user->id,
                'precence_id' => $precence->id,
                'distance' => $distance,
            ]);
        } catch (\Throwable $th) {
            $this->failedAttendance($user, $precence);
            return response()->json($th->getMessage(), 404);
        }

        return response()->json([$request->all(), $precence], 200);
    }

    private function failedAttendance($user, $precence)
    {
        $now = now()->toDateString();
        $phone = $user->phone;
        $jobs = FormJob::where('data', 'LIKE', "%$now%")->where('data', 'LIKE', "%$phone%")->count() != 0 ? 'Yes' : 'No';
        $message = "[ERROR] Presensi gagal\n\nEmail : {$user->email}\nUser ID : {$user->id}\nPrecence ID : {$precence->id}\nVerified : $jobs";
        $this->send('120363399651067268@g.us', $message);
    }

    public function index()
    {
        $precences = Precence::orderBy('status')->orderBy('created_at')->paginate(10);

        return view('pages.precence.index', compact('precences'));
    }

    public function create()
    {
        $user = Auth::user();

        return view('pages.precence.create', compact('user'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['code'] = $this->uniqueString();
        Precence::create($data);

        return redirect()->route('precence.index')->with('success', 'Berhasil membuat presensi baru');
    }

    public function show(Precence $precence)
    {
        $user = Auth::user();
        $attendances = $precence->attendance();

        return view('pages.precence.show', compact('precence', 'attendances'));
    }

    public function edit(Precence $precence)
    {
        $user = Auth::user();

        return view('pages.precence.edit', compact('precence', 'user'));
    }

    public function update(Request $request, Precence $precence)
    {
        if ($request->has('attendance_id')) {
            $attendance = Attendance::find($request->attendance_id);
            $attendance->point = $request->point;
            $attendance->save();

            return redirect()->back();
        }
        $precence->update($request->all());

        return redirect()->route('precence.index')->with('success', 'Berhasil mengubah data presensi');
    }

    public function getQrCode()
    {
        $precence = Precence::where('status', 'active')->get()[0];

        return view('pages.precence.downloadQr', compact('precence'));
    }

    public function uniqueString($length = 20)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
